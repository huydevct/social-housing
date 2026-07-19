<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Lưu ảnh dự án lên R2. Nếu R2 chưa cấu hình (thiếu creds) hoặc tải/upload lỗi
 * thì trả về chính URL nguồn (hotlink) để không chặn crawl.
 *
 * ponytail: quyết định lưu R2 hay hotlink dựa vào env, không cần flag/DI thêm.
 */
class ImageStore
{
    public function isConfigured(): bool
    {
        return ! empty(config('filesystems.disks.r2.key'))
            && ! empty(config('filesystems.disks.r2.bucket'))
            && ! empty(config('filesystems.disks.r2.url'));
    }

    /**
     * Trả về URL công khai để lưu vào project_images.url.
     */
    public function store(string $sourceUrl, string $folder = 'projects'): string
    {
        if (! $this->isConfigured()) {
            return $sourceUrl;
        }

        try {
            $response = Http::timeout(30)->get($sourceUrl);

            if (! $response->ok()) {
                return $sourceUrl;
            }

            $ext = pathinfo(parse_url($sourceUrl, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION);
            $ext = $ext !== '' ? strtolower($ext) : 'jpg';
            $path = $folder.'/'.Str::uuid()->toString().'.'.$ext;

            Storage::disk('r2')->put($path, $response->body(), 'public');

            return Storage::disk('r2')->url($path);
        } catch (Throwable) {
            return $sourceUrl;
        }
    }
}
