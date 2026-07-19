<?php

namespace App\Services\Crawler;

use App\Enums\ProjectStatus;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\Http;

/**
 * Crawler danh sách dự án nhà ở xã hội trên cafeland.vn (nguồn tổng hợp toàn quốc).
 *
 * DOM: ul.projectList > li.hover-suggestions, mỗi li có:
 *   - h3 > a  : tên + link chi tiết
 *   - .wrap-img img[data-src] : ảnh
 *   - .duan-sap-mo-ban / .duan-dang-mo-ban : trạng thái
 *   - .titleProjectSeo : "Địa chỉ: ..."
 *   - a[title bắt đầu bằng chủ đầu tư] : chủ đầu tư (text "Chủ đầu tư: ...")
 *
 * ponytail: adapter riêng cho cafeland; chung shape trả về với HanoiSoXayDungCrawler
 * (name/address/investor/source_url/image/status) để command import dùng lại.
 */
class CafelandCrawler
{
    private const BASE = 'https://cafeland.vn';

    private const UA = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36';

    public function __construct(
        private readonly int $maxPages = 3,
    ) {}

    /**
     * Tải nhiều trang danh sách và trả về các dự án đã parse (đã gộp, chưa dedupe).
     *
     * @return array<int, array<string, string|null>>
     */
    public function fetch(): array
    {
        $all = [];

        for ($page = 1; $page <= $this->maxPages; $page++) {
            $url = $page === 1
                ? self::BASE.'/du-an/nha-o-xa-hoi/'
                : self::BASE."/du-an/nha-o-xa-hoi/page-{$page}/";

            $response = Http::timeout(30)->withHeaders(['User-Agent' => self::UA])->get($url);

            if (! $response->ok()) {
                break;
            }

            $items = $this->parse($response->body());

            if ($items === []) {
                break;
            }

            $all = [...$all, ...$items];
        }

        return $all;
    }

    /**
     * Tải trang chi tiết một dự án và trích diện tích + giá + mô tả.
     *
     * @return array{area_from: int|null, area_to: int|null, price_from: int|null, price_to: int|null, description: string|null}
     */
    public function fetchDetail(string $url): array
    {
        $response = Http::timeout(30)->withHeaders(['User-Agent' => self::UA])->get($url);

        return $response->ok()
            ? $this->parseDetail($response->body())
            : ['area_from' => null, 'area_to' => null, 'price_from' => null, 'price_to' => null, 'description' => null];
    }

    /**
     * Parse trang chi tiết. Tách riêng để test offline.
     *
     * @return array{area_from: int|null, area_to: int|null, price_from: int|null, price_to: int|null, description: string|null}
     */
    public function parseDetail(string $html): array
    {
        // Text phẳng để dò diện tích + giá (nội dung nằm rải trong nhiều <p>).
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');

        $areaFrom = null;
        $areaTo = null;

        // "Diện tích: 68,1-70,5m2" — lấy phần nguyên (m²), làm tròn.
        if (preg_match('/Diện tích:?\s*([\d.,]+)\s*[-–]?\s*([\d.,]*)\s*m/u', $text, $m)) {
            $areaFrom = $this->toInt($m[1]);
            $areaTo = ($m[2] !== '' && $m[2] !== $m[1]) ? $this->toInt($m[2]) : null;
        }

        [$priceFrom, $priceTo] = $this->extractPrice($text);

        // Mô tả: đoạn <p> văn bản dài đầu tiên (bỏ script/style).
        $description = null;
        if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $ps)) {
            foreach ($ps[1] as $raw) {
                $paragraph = trim(preg_replace('/\s+/', ' ', strip_tags($raw)) ?? '');

                if (mb_strlen($paragraph) >= 120 && ! str_contains($paragraph, 'function') && ! str_contains($paragraph, '$(')) {
                    $description = $paragraph;
                    break;
                }
            }
        }

        return [
            'area_from' => $areaFrom,
            'area_to' => $areaTo,
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'description' => $description,
        ];
    }

    /**
     * Trích giá bán/m² (đồng) từ text. Bắt "13,9 triệu/m2", "từ 16 - 18 triệu đồng/m²".
     * Trả [từ, đến] (đồng), null nếu không tìm thấy (→ "Chưa công bố" ở UI).
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function extractPrice(string $text): array
    {
        // Ưu tiên cụm có "giá" đứng trước để tránh bắt nhầm số khác.
        $pattern = '/giá[^.]{0,40}?([\d.,]+)\s*(?:[-–]\s*([\d.,]+)\s*)?triệu\s*(?:đồng)?\s*[\/ ]*m/iu';

        if (preg_match($pattern, $text, $m)) {
            $from = $this->toMillions($m[1]);
            $to = ! empty($m[2]) ? $this->toMillions($m[2]) : null;

            // Sanity: NOXH thực tế ~6-35 triệu/m². Ngoài khoảng → trích nhầm số khác
            // trong text tự do, coi như chưa đáng tin (UI hiển thị "Chưa công bố").
            if ($from !== null && ($from < 6_000_000 || $from > 35_000_000)) {
                return [null, null];
            }

            if ($to !== null && $to > 35_000_000) {
                $to = null;
            }

            return [$from, $to];
        }

        return [null, null];
    }

    private function toMillions(string $vietnameseNumber): ?int
    {
        // "13,9" triệu -> 13_900_000 ; "16" -> 16_000_000
        $normalized = (float) str_replace([',', ' '], ['.', ''], $vietnameseNumber);

        return $normalized > 0 ? (int) round($normalized * 1_000_000) : null;
    }

    private function toInt(string $vietnameseNumber): ?int
    {
        // "68,1" -> 68 ; "1.944" -> 1944
        $clean = str_replace('.', '', $vietnameseNumber);
        $clean = explode(',', $clean)[0];

        return is_numeric($clean) ? (int) $clean : null;
    }

    /**
     * Parse HTML một trang danh sách. Tách riêng để test offline bằng fixture.
     *
     * @return array<int, array<string, string|null>>
     */
    public function parse(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }

        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $nodes = $xpath->query("//ul[contains(@class, 'projectList')]/li");

        if ($nodes === false) {
            return [];
        }

        $projects = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $nameNode = $this->firstNode($xpath, './/h3/a', $node);

            if ($nameNode === null) {
                continue;
            }

            $name = trim($nameNode->textContent);

            if ($name === '') {
                continue;
            }

            $projects[] = [
                'name' => $name,
                'source_url' => $this->attr($xpath, './/h3/a/@href', $node),
                'image' => $this->attr($xpath, ".//*[contains(@class,'wrap-img')]//img/@data-src", $node),
                'address' => $this->addressFrom($xpath, $node),
                'investor' => $this->investorFrom($xpath, $node),
                'status' => $this->statusFrom($xpath, $node)?->value,
            ];
        }

        return $projects;
    }

    private function statusFrom(DOMXPath $xpath, DOMElement $node): ?ProjectStatus
    {
        $classNode = $this->firstNode($xpath, ".//span[contains(@class,'duan-')]/@class", $node);
        $class = $classNode !== null ? $classNode->textContent : '';

        return match (true) {
            str_contains($class, 'dang-mo-ban') => ProjectStatus::Receiving,
            str_contains($class, 'sap-mo-ban') => ProjectStatus::Upcoming,
            default => null,
        };
    }

    private function addressFrom(DOMXPath $xpath, DOMElement $node): ?string
    {
        $seo = $this->firstNode($xpath, ".//*[contains(@class,'titleProjectSeo')]", $node);
        $text = $seo !== null ? trim(preg_replace('/\s+/', ' ', $seo->textContent) ?? '') : '';

        if (preg_match('/Địa chỉ:\s*(.+)$/u', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function investorFrom(DOMXPath $xpath, DOMElement $node): ?string
    {
        // Link có text bắt đầu "Chủ đầu tư: ..."
        $links = $xpath->query('.//a', $node);

        if ($links === false) {
            return null;
        }

        foreach ($links as $link) {
            if (! $link instanceof DOMNode) {
                continue;
            }

            $text = trim($link->textContent);

            if (str_starts_with($text, 'Chủ đầu tư:')) {
                return trim(str_replace('Chủ đầu tư:', '', $text));
            }
        }

        return null;
    }

    private function attr(DOMXPath $xpath, string $query, DOMElement $context): ?string
    {
        $node = $this->firstNode($xpath, $query, $context);

        if ($node === null) {
            return null;
        }

        $value = trim($node->textContent);

        return $value !== '' ? $value : null;
    }

    private function firstNode(DOMXPath $xpath, string $query, DOMElement $context): ?DOMNode
    {
        $nodes = $xpath->query($query, $context);
        $node = ($nodes === false) ? null : $nodes->item(0);

        return $node instanceof DOMNode ? $node : null;
    }
}
