<?php

namespace App\Services\Crawler;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\Http;

/**
 * Crawler cho danh sách dự án nhà ở xã hội trên cổng Sở Xây dựng Hà Nội.
 *
 * ponytail: một adapter cụ thể, chưa trừu tượng hóa interface cho tới khi có
 * nguồn thứ 2. Selector XPath phải chỉnh theo DOM thật của trang nguồn — hàm
 * parse() tách riêng để test bằng HTML fixture, không phụ thuộc mạng.
 */
class HanoiSoXayDungCrawler
{
    public function __construct(
        private readonly string $listUrl = 'https://soxaydung.hanoi.gov.vn/',
    ) {}

    /**
     * Tải HTML danh sách và trả về các dự án đã parse.
     *
     * @return array<int, array<string, string|null>>
     */
    public function fetch(): array
    {
        $response = Http::timeout(30)
            ->withHeaders(['User-Agent' => 'NOXH-Bot/1.0 (+listing aggregator)'])
            ->get($this->listUrl);

        return $this->parse($response->body());
    }

    /**
     * Parse HTML thành danh sách dự án. Tách riêng để test offline.
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
        $projects = [];

        // Mỗi dự án là một item có class chứa "project-item" (chỉnh theo DOM thật).
        $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' project-item ')]");

        if ($nodes === false) {
            return [];
        }

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $name = $this->firstText($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' project-name ')]", $node);

            if ($name === null) {
                continue;
            }

            $linkNodes = $xpath->query('.//a/@href', $node);
            $link = ($linkNodes !== false && $linkNodes->length > 0) ? $linkNodes->item(0)?->nodeValue : null;

            $projects[] = [
                'name' => $name,
                'address' => $this->firstText($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' project-address ')]", $node),
                'investor' => $this->firstText($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' project-investor ')]", $node),
                'source_url' => $link ? $this->absoluteUrl($link) : $this->listUrl,
            ];
        }

        return $projects;
    }

    private function firstText(DOMXPath $xpath, string $query, DOMElement $context): ?string
    {
        $nodes = $xpath->query($query, $context);

        $node = ($nodes === false) ? null : $nodes->item(0);

        if (! $node instanceof DOMNode) {
            return null;
        }

        $text = trim($node->textContent);

        return $text !== '' ? $text : null;
    }

    private function absoluteUrl(string $href): string
    {
        if (str_starts_with($href, 'http')) {
            return $href;
        }

        return rtrim($this->listUrl, '/').'/'.ltrim($href, '/');
    }
}
