<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Vài dự án mẫu để dựng UI/SEO. Thay bằng dữ liệu crawl/nhập tay thật sau.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $samples = [
        [
            'name' => 'Nhà ở xã hội NHS Trung Văn',
            'province' => 'Hà Nội',
            'investor' => 'Công ty CP Đầu tư Xây dựng NHS',
            'district' => 'Nam Từ Liêm',
            'address' => 'Đường Tố Hữu, phường Đại Mỗ, TP. Hà Nội',
            'former_address' => 'Phường Trung Văn, quận Nam Từ Liêm, Hà Nội',
            'status' => ProjectStatus::Receiving,
            'total_units' => 275,
            'price_from' => 19_500_000,
            'price_to' => 19_500_000,
            'area_from' => 70,
            'area_to' => 76,
            'source_name' => 'Sở Xây dựng Hà Nội',
            'source_url' => 'https://soxaydung.hanoi.gov.vn/',
        ],
        [
            'name' => 'Nhà ở xã hội Rice City Long Biên',
            'province' => 'Hà Nội',
            'investor' => 'Công ty CP BIC Việt Nam',
            'district' => 'Long Biên',
            'address' => 'Khu đô thị Thượng Thanh, TP. Hà Nội',
            'former_address' => 'Phường Thượng Thanh, quận Long Biên, Hà Nội',
            'status' => ProjectStatus::Upcoming,
            'total_units' => 600,
            'price_from' => 16_800_000,
            'price_to' => 18_200_000,
            'area_from' => 55,
            'area_to' => 69,
            'source_name' => 'Chủ đầu tư',
        ],
        [
            'name' => 'Nhà ở xã hội Thượng Thanh',
            'province' => 'Hà Nội',
            'investor' => 'Him Lam Thủ đô',
            'district' => 'Long Biên',
            'address' => 'Phường Thượng Thanh, TP. Hà Nội',
            'former_address' => 'Phường Thượng Thanh, quận Long Biên, Hà Nội',
            'status' => ProjectStatus::Closed,
            'total_units' => 1944,
            'price_from' => 17_600_000,
            'price_to' => 19_000_000,
            'area_from' => 60,
            'area_to' => 70,
            'source_name' => 'Sở Xây dựng Hà Nội',
        ],
        [
            'name' => 'Nhà ở xã hội Tân Đông Hiệp',
            'province' => 'TP. Hồ Chí Minh',
            'investor' => 'Công ty CP Đầu tư Phú Cường',
            'district' => 'Dĩ An',
            'address' => 'Phường Tân Đông Hiệp, TP. Hồ Chí Minh',
            'former_address' => 'Phường Tân Đông Hiệp, TP. Dĩ An, Bình Dương',
            'status' => ProjectStatus::Receiving,
            'total_units' => 1000,
            'price_from' => 14_500_000,
            'price_to' => 16_000_000,
            'area_from' => 45,
            'area_to' => 65,
            'source_name' => 'Chủ đầu tư',
        ],
    ];

    public function run(): void
    {
        foreach ($this->samples as $sample) {
            $province = Province::where('name', $sample['province'])->first()
                ?? Province::factory()->create(['name' => $sample['province'], 'slug' => Str::slug($sample['province'])]);

            $investor = Investor::firstOrCreate(
                ['slug' => Str::slug($sample['investor'])],
                ['name' => $sample['investor']],
            );

            Project::updateOrCreate(
                ['slug' => Str::slug($sample['name'])],
                [
                    'name' => $sample['name'],
                    'province_id' => $province->id,
                    'investor_id' => $investor->id,
                    'district' => $sample['district'],
                    'address' => $sample['address'],
                    'former_address' => $sample['former_address'] ?? null,
                    'status' => $sample['status'],
                    'application_start_at' => now()->subDays(10),
                    'application_end_at' => now()->addDays(30),
                    'total_units' => $sample['total_units'],
                    'price_from' => $sample['price_from'],
                    'price_to' => $sample['price_to'],
                    'area_from' => $sample['area_from'],
                    'area_to' => $sample['area_to'],
                    'description' => 'Dự án nhà ở xã hội '.$sample['name'].' tại '.$sample['address'].
                        '. Đối tượng mua/thuê là người thu nhập thấp, cán bộ công chức đủ điều kiện theo quy định của Luật Nhà ở.',
                    'application_guide' => 'Hồ sơ nộp trực tiếp tại văn phòng chủ đầu tư trong thời gian tiếp nhận. Xem chi tiết giấy tờ tại mục Hướng dẫn.',
                    'source_name' => $sample['source_name'],
                    'source_url' => $sample['source_url'] ?? null,
                    'published_at' => now(),
                ],
            );
        }

        // Thêm data giả để test phân trang/lọc, dùng lại tỉnh/chủ đầu tư đã có.
        $provinceIds = Province::pluck('id');
        Project::factory(20)
            ->recycle(Investor::all())
            ->create(fn (): array => ['province_id' => $provinceIds->random()]);
    }
}
