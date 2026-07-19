<?php

use App\Services\ImageStore;

it('hotlinks the source url when r2 is not configured', function () {
    config(['filesystems.disks.r2.key' => null]);

    $store = new ImageStore;

    expect($store->isConfigured())->toBeFalse()
        ->and($store->store('https://example.com/a.jpg'))->toBe('https://example.com/a.jpg');
});
