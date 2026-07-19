<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Crawl dự án NOXH mỗi ngày 1 lần (03:00), lấy chi tiết giá/diện tích + tự publish.
// Upsert theo external_id nên không tạo trùng; dự án đã publish giữ nguyên published_at.
Schedule::command('crawl:projects --source=cafeland --pages=5 --details --publish')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground();
