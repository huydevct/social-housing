<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProvinceSeeder extends Seeder
{
    /**
     * 34 đơn vị hành chính cấp tỉnh sau sáp nhập (hiệu lực 01/07/2025):
     * 6 thành phố trực thuộc TW + 28 tỉnh.
     */
    public function run(): void
    {
        $names = [
            // Thành phố trực thuộc trung ương
            'Hà Nội', 'TP. Hồ Chí Minh', 'Hải Phòng', 'Đà Nẵng', 'Cần Thơ', 'Huế',
            // Tỉnh
            'Tuyên Quang', 'Lào Cai', 'Thái Nguyên', 'Phú Thọ', 'Bắc Ninh', 'Hưng Yên',
            'Ninh Bình', 'Quảng Ninh', 'Cao Bằng', 'Lạng Sơn', 'Lai Châu', 'Điện Biên',
            'Sơn La', 'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Trị', 'Quảng Ngãi',
            'Gia Lai', 'Khánh Hòa', 'Lâm Đồng', 'Đắk Lắk', 'Đồng Nai', 'Tây Ninh',
            'Vĩnh Long', 'Đồng Tháp', 'Cà Mau', 'An Giang',
        ];

        foreach ($names as $name) {
            Province::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
