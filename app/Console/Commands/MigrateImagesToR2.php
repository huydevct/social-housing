<?php

namespace App\Console\Commands;

use App\Models\ProjectImage;
use App\Services\ImageStore;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('images:migrate-to-r2')]
#[Description('Tải các ảnh còn hotlink URL nguồn về R2 (chạy sau khi cấu hình R2 creds)')]
class MigrateImagesToR2 extends Command
{
    public function handle(ImageStore $store): int
    {
        if (! $store->isConfigured()) {
            $this->error('R2 chưa cấu hình. Điền R2_* trong .env trước.');

            return self::FAILURE;
        }

        $r2Url = rtrim((string) config('filesystems.disks.r2.url'), '/');
        $migrated = 0;

        // Ảnh đang hotlink = url không trỏ về domain R2.
        ProjectImage::query()
            ->where('url', 'not like', $r2Url.'%')
            ->chunkById(50, function ($images) use ($store, $r2Url, &$migrated): void {
                foreach ($images as $image) {
                    $newUrl = $store->store($image->url);

                    if (str_starts_with($newUrl, $r2Url)) {
                        $image->update(['url' => $newUrl]);
                        $migrated++;
                    }
                }
            });

        $this->info("Đã chuyển {$migrated} ảnh lên R2.");

        return self::SUCCESS;
    }
}
