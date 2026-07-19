<?php

use App\Services\Crawler\CafelandCrawler;
use App\Services\Crawler\HanoiSoXayDungCrawler;

it('parses cafeland projects from source html', function () {
    $html = file_get_contents(dirname(__DIR__).'/fixtures/cafeland-projects.html');

    $projects = (new CafelandCrawler)->parse($html);

    expect($projects)->toHaveCount(2)
        ->and($projects[0]['name'])->toBe('Marquee Homes: Dự án nhà ở xã hội tại Hải Phòng')
        ->and($projects[0]['address'])->toContain('Hải Phòng')
        ->and($projects[0]['investor'])->toBe('Công ty Cổ phần Hóa chất Vật liệu điện Bình Phát')
        ->and($projects[0]['status'])->toBe('upcoming')
        ->and($projects[0]['source_url'])->toStartWith('https://cafeland.vn/du-an/')
        ->and($projects[0]['image'])->toContain('cafeland.vn');
});

it('parses cafeland detail for area and description', function () {
    $html = file_get_contents(dirname(__DIR__).'/fixtures/cafeland-detail.html');

    $detail = (new CafelandCrawler)->parseDetail($html);

    expect($detail['area_from'])->toBe(68)
        ->and($detail['area_to'])->toBe(70)
        ->and($detail['price_from'])->toBe(16_500_000)
        ->and($detail['price_to'])->toBeNull()
        ->and($detail['description'])->toContain('Marquee Homes');
});

it('returns null price when detail has no price', function () {
    $detail = (new CafelandCrawler)->parseDetail('<html><body><p>'.str_repeat('x', 130).' không có thông tin giá.</p></body></html>');

    expect($detail['price_from'])->toBeNull()
        ->and($detail['price_to'])->toBeNull();
});

it('rejects implausible price outside social housing range', function () {
    $detail = (new CafelandCrawler)->parseDetail('<html><body><p>'.str_repeat('x', 130).' giá bán từ 70 triệu đồng/m2.</p></body></html>');

    expect($detail['price_from'])->toBeNull();
});

it('parses projects from source html', function () {
    $html = file_get_contents(dirname(__DIR__).'/fixtures/hanoi-projects.html');

    $projects = (new HanoiSoXayDungCrawler)->parse($html);

    expect($projects)->toHaveCount(2)
        ->and($projects[0]['name'])->toBe('NHS Trung Văn')
        ->and($projects[0]['address'])->toBe('Phường Đại Mỗ, TP. Hà Nội')
        ->and($projects[0]['investor'])->toBe('Công ty CP NHS')
        ->and($projects[0]['source_url'])->toBe('https://soxaydung.hanoi.gov.vn/du-an/nhs-trung-van')
        ->and($projects[1]['source_url'])->toBe('https://soxaydung.hanoi.gov.vn/du-an/rice-city');
});

it('returns empty array for blank html', function () {
    expect((new HanoiSoXayDungCrawler)->parse(''))->toBe([]);
});
