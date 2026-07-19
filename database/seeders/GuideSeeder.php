<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            [
                'title' => 'Điều kiện mua nhà ở xã hội mới nhất',
                'excerpt' => 'Ai đủ điều kiện mua, thuê nhà ở xã hội theo Luật Nhà ở 2023 và các quy định hiện hành.',
                'body' => <<<'HTML'
<h2>1. Đối tượng được mua nhà ở xã hội</h2>
<p>Theo Luật Nhà ở 2023, các đối tượng gồm: người thu nhập thấp tại đô thị; công nhân, người lao động khu công nghiệp; cán bộ, công chức, viên chức; sĩ quan, quân nhân chuyên nghiệp; hộ nghèo, cận nghèo...</p>
<h2>2. Điều kiện về nhà ở</h2>
<p>Chưa có nhà ở thuộc sở hữu của mình, hoặc có nhà ở nhưng diện tích bình quân dưới mức tối thiểu.</p>
<h2>3. Điều kiện về thu nhập</h2>
<p>Thuộc diện không phải nộp thuế thu nhập cá nhân thường xuyên theo quy định.</p>
HTML,
            ],
            [
                'title' => 'Hồ sơ mua nhà ở xã hội gồm những giấy tờ gì?',
                'excerpt' => 'Danh sách đầy đủ giấy tờ cần chuẩn bị trong hồ sơ đăng ký mua, thuê nhà ở xã hội.',
                'body' => <<<'HTML'
<h2>Giấy tờ bắt buộc</h2>
<ul>
<li>Đơn đăng ký mua/thuê nhà ở xã hội (theo mẫu của chủ đầu tư).</li>
<li>Giấy tờ chứng minh đối tượng (quyết định tuyển dụng, hợp đồng lao động, thẻ ngành...).</li>
<li>Giấy xác nhận về điều kiện nhà ở (chưa có nhà/nhà chật) do UBND cấp xã nơi cư trú xác nhận.</li>
<li>Giấy xác nhận về điều kiện thu nhập.</li>
<li>Bản sao căn cước công dân, giấy xác nhận cư trú.</li>
</ul>
<h2>Nơi xin các giấy xác nhận</h2>
<p>Giấy xác nhận điều kiện nhà ở và cư trú xin tại UBND cấp xã/phường. Giấy xác nhận thu nhập xin tại cơ quan, đơn vị đang công tác hoặc UBND cấp xã (với lao động tự do).</p>
HTML,
            ],
            [
                'title' => 'Cách thức nộp hồ sơ nhà ở xã hội',
                'excerpt' => 'Quy trình nộp hồ sơ, thời gian tiếp nhận và cách theo dõi kết quả xét duyệt.',
                'body' => <<<'HTML'
<h2>Bước 1: Chuẩn bị hồ sơ</h2>
<p>Chuẩn bị đầy đủ giấy tờ theo danh mục. Photo công chứng các bản sao.</p>
<h2>Bước 2: Nộp hồ sơ</h2>
<p>Nộp trực tiếp tại văn phòng bán hàng của chủ đầu tư trong thời gian tiếp nhận công bố trên website Sở Xây dựng địa phương.</p>
<h2>Bước 3: Chấm điểm, xét duyệt</h2>
<p>Chủ đầu tư tổng hợp, chấm điểm theo thang điểm ưu tiên và gửi Sở Xây dựng thẩm định danh sách.</p>
<h2>Bước 4: Bốc thăm/ký hợp đồng</h2>
<p>Nếu số hồ sơ vượt số căn, tổ chức bốc thăm công khai. Người trúng ký hợp đồng mua bán.</p>
HTML,
            ],
        ];

        foreach ($guides as $i => $guide) {
            Guide::updateOrCreate(
                ['slug' => Str::slug($guide['title'])],
                [
                    'title' => $guide['title'],
                    'excerpt' => $guide['excerpt'],
                    'body' => $guide['body'],
                    'sort' => $i,
                    'published_at' => now(),
                ],
            );
        }
    }
}
