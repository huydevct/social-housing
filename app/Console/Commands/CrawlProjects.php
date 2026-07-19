<?php

namespace App\Console\Commands;

use App\Enums\ProjectStatus;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Province;
use App\Services\Crawler\CafelandCrawler;
use App\Services\Crawler\HanoiSoXayDungCrawler;
use App\Services\ImageStore;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('crawl:projects {--source=cafeland : Nguồn crawl: cafeland|hanoi} {--pages=3 : Số trang crawl (cafeland)} {--details : Crawl thêm trang chi tiết (diện tích, mô tả)} {--publish : Publish dự án ngay thay vì để nháp} {--force : Cập nhật lại cả dự án đã có (mặc định chỉ thêm dự án mới)}')]
#[Description('Crawl danh sách dự án nhà ở xã hội từ nguồn công khai vào trạng thái nháp')]
class CrawlProjects extends Command
{
    /** @var array<int, string> Cache tên tỉnh đã seed để map từ địa chỉ. */
    private array $provinceNames = [];

    public function handle(): int
    {
        $source = (string) $this->option('source');

        [$items, $sourceName] = match ($source) {
            'cafeland' => [(new CafelandCrawler((int) $this->option('pages')))->fetch(), 'Cafeland'],
            'hanoi' => [(new HanoiSoXayDungCrawler)->fetch(), 'Sở Xây dựng Hà Nội'],
            default => [null, null],
        };

        if ($items === null) {
            $this->error("Nguồn không hỗ trợ: {$source} (dùng cafeland hoặc hanoi)");

            return self::FAILURE;
        }

        $this->info(count($items).' dự án tìm thấy từ nguồn '.$sourceName.'.');

        [$created, $skipped] = $this->import($items, (string) $sourceName);

        $this->info("Thêm mới: {$created}, bỏ qua (đã có): {$skipped}.");

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, string|null>>  $items
     * @return array{0: int, 1: int} [thêm mới, bỏ qua]
     */
    public function import(array $items, string $sourceName): array
    {
        $created = 0;
        $skipped = 0;

        $withDetails = (bool) $this->option('details');
        $publish = (bool) $this->option('publish');
        $detailCrawler = $withDetails ? new CafelandCrawler : null;

        // external_id của các dự án đã có -> để bỏ qua sớm, không fetch chi tiết / upload ảnh lại.
        $existingIds = Project::pluck('external_id')->filter()->flip();

        foreach ($items as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $externalId = $sourceName.':'.md5($item['name']);

            // Dự án đã có trong DB -> bỏ qua hoàn toàn (không fetch chi tiết, không tải lại ảnh
            // lên R2). Chỉ xử lý dự án MỚI. Dùng --force nếu muốn cập nhật lại.
            if (! $this->option('force') && $existingIds->has($externalId)) {
                $skipped++;

                continue;
            }

            $investorId = null;
            if (! empty($item['investor'])) {
                $investorId = Investor::firstOrCreate(
                    ['slug' => Str::slug($item['investor'])],
                    ['name' => $item['investor']],
                )->id;
            }

            $detail = ($detailCrawler !== null && ! empty($item['source_url']))
                ? $detailCrawler->fetchDetail($item['source_url'])
                : ['area_from' => null, 'area_to' => null, 'price_from' => null, 'price_to' => null, 'description' => null];

            $project = Project::firstOrNew(['external_id' => $externalId]);
            $wasNew = ! $project->exists;

            $project->fill([
                'name' => $item['name'],
                'address' => $item['address'] ?? $project->address,
                'investor_id' => $investorId ?? $project->investor_id,
                'source_url' => $item['source_url'] ?? $project->source_url,
                'source_name' => $sourceName,
                'area_from' => $detail['area_from'] ?? $project->area_from,
                'area_to' => $detail['area_to'] ?? $project->area_to,
                'price_from' => $detail['price_from'] ?? $project->price_from,
                'price_to' => $detail['price_to'] ?? $project->price_to,
                'description' => $detail['description'] ?? $project->description,
            ]);

            if (! empty($item['status'])) {
                $project->status = ProjectStatus::tryFrom($item['status']) ?? $project->status ?? ProjectStatus::Upcoming;
            }

            if ($wasNew) {
                $project->province_id = $this->resolveProvinceId($item['address'] ?? null);
                $project->slug = Str::slug($item['name']).'-'.Str::lower(Str::random(5));
                $project->status = $project->status ?? ProjectStatus::Upcoming;
            }

            if ($publish && $project->published_at === null) {
                $project->published_at = now();
            }

            $project->save();

            // Chỉ upload ảnh lên R2 khi dự án mới VÀ chưa có ảnh -> tránh tốn phí lặp.
            if (! empty($item['image']) && $project->images()->doesntExist()) {
                $url = app(ImageStore::class)->store($item['image']);
                $project->images()->create(['url' => $url, 'is_cover' => true]);
            }

            $created++;
        }

        return [$created, $skipped];
    }

    /**
     * Đoán tỉnh/thành từ địa chỉ (tỉnh thường ở cuối). Khớp với danh sách đã seed;
     * không khớp thì tạo mới từ đoạn cuối địa chỉ.
     */
    private function resolveProvinceId(?string $address): int
    {
        if ($this->provinceNames === []) {
            $this->provinceNames = Province::pluck('name')->all();
        }

        $haystack = Str::lower($address ?? '');

        foreach ($this->provinceNames as $name) {
            if ($haystack !== '' && str_contains($haystack, Str::lower($name))) {
                return Province::where('name', $name)->value('id');
            }
        }

        // Fallback: đoạn cuối sau dấu phẩy, hoặc "Chưa xác định".
        $tail = 'Chưa xác định';
        if ($address !== null && str_contains($address, ',')) {
            $parts = array_map('trim', explode(',', $address));
            $tail = end($parts) ?: $tail;
        }

        $province = Province::firstOrCreate(['slug' => Str::slug($tail)], ['name' => $tail]);
        $this->provinceNames[] = $province->name;

        return $province->id;
    }
}
