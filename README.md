# Nhà Ở Xã Hội Việt Nam

Website tra cứu dự án nhà ở xã hội (NOXH) trên toàn quốc: thông tin dự án, thời gian tiếp nhận hồ sơ, giá bán dự kiến, hướng dẫn thủ tục nộp hồ sơ. Dữ liệu crawl tự động từ nguồn công khai + nhập tay qua trang quản trị. Tối ưu SEO bằng SSR + meta động + sitemap + JSON-LD.

## Stack

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Inertia v3 + Vue 3.5 + Tailwind v4 |
| Typed routes | Laravel Wayfinder |
| Database | MySQL 8 |
| Cache / Queue / Session | Redis (predis) |
| Lưu ảnh | Cloudflare R2 (S3-compatible) |
| SSR | Inertia SSR (Node 22) |
| Test / QA | Pest 4, Larastan, Pint, ESLint |

## Yêu cầu môi trường

- PHP 8.3, Composer
- Node 22, npm
- MySQL 8
- Redis
- (tuỳ chọn) Tài khoản Cloudflare R2 để lưu ảnh

## Cài đặt lần đầu

```bash
# 1. Cài dependencies
composer install
npm install

# 2. Tạo .env
cp .env.example .env
php artisan key:generate

# 3. Điền .env: DB, Redis, ADMIN_*, R2_* (xem phần "Cấu hình .env")

# 4. Tạo database MySQL (nếu chưa có)
mysql -u root -p -e "CREATE DATABASE social_housing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Migrate + seed (tạo bảng, 34 tỉnh, admin, dữ liệu mẫu)
php artisan migrate:fresh --seed

# 6. Build frontend (kèm SSR bundle)
npm run build
```

## Cấu hình .env

```env
APP_NAME="Nhà Ở Xã Hội"
APP_URL=http://localhost:8001        # đổi theo port đang chạy

DB_CONNECTION=mysql
DB_DATABASE=social_housing
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis cho cache/queue/session
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis

# Tài khoản admin khởi tạo (AdminUserSeeder). Bỏ trống ADMIN_PASSWORD -> không seed admin.
ADMIN_NAME="Quản trị viên"
ADMIN_EMAIL=admin@noxh.test
ADMIN_PASSWORD=your_admin_password

# Cloudflare R2 lưu ảnh dự án. Bỏ trống -> ảnh giữ hotlink URL nguồn.
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=
R2_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
R2_URL=https://pub-xxx.r2.dev        # public URL bucket, hoặc domain CDN sau
```

> `.env` không commit lên git (đã trong `.gitignore`). Điền secret thật vào đây.

## Chạy ở local

Port 8000 có thể bận (app khác) — ví dụ dùng `8001`.

### Cách 1 — Dev thường (hot-reload, khuyên dùng)

```bash
# Terminal 1: Vite hot-reload
npm run dev

# Terminal 2: web server
php artisan serve --port=8001
```

Mở http://localhost:8001 — sửa code Vue thấy ngay. (SSR tắt, meta render client-side.)

### Cách 2 — Giống production (có SSR, meta SEO đầy đủ trong HTML)

```bash
npm run build                      # build lại khi sửa code

# Terminal 1: SSR server
php artisan inertia:start-ssr

# Terminal 2: web server
php artisan serve --port=8001
```

Mở http://localhost:8001. Dừng SSR: `php artisan inertia:stop-ssr`.

## Trang chính

| URL | Mô tả |
|---|---|
| `/` | Trang chủ: tìm kiếm, dự án nổi bật, danh sách tỉnh |
| `/du-an` | Danh sách dự án + lọc (tỉnh, trạng thái, từ khoá) |
| `/du-an/{slug}` | Chi tiết dự án |
| `/nha-o-xa-hoi/{tỉnh}` | Landing SEO theo tỉnh/thành |
| `/huong-dan` · `/huong-dan/{slug}` | Hướng dẫn nộp hồ sơ |
| `/sitemap.xml` | Sitemap động cho SEO |
| `/admin` | Trang quản trị (đăng nhập) |

**Đăng nhập admin:** email + mật khẩu đặt trong `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

## Crawler dữ liệu

Crawl dự án NOXH từ nguồn công khai (cafeland.vn — tổng hợp toàn quốc).

```bash
# Crawl trang danh sách + chi tiết (giá, diện tích, mô tả) rồi publish luôn
php artisan crawl:projects --source=cafeland --pages=5 --details --publish
```

Tuỳ chọn:

| Cờ | Ý nghĩa |
|---|---|
| `--source=` | `cafeland` (mặc định) hoặc `hanoi` |
| `--pages=N` | Số trang danh sách crawl |
| `--details` | Vào từng trang chi tiết lấy giá/diện tích/mô tả |
| `--publish` | Publish ngay (bỏ đi để giữ nháp, duyệt tay trong /admin) |
| `--force` | Cập nhật lại cả dự án đã có (mặc định chỉ thêm dự án MỚI) |

**Chống trùng & tiết kiệm phí R2:** dự án đã có trong DB được bỏ qua hoàn toàn — không fetch chi tiết, không upload ảnh lại. Chỉ dự án mới được xử lý.

### Lịch chạy tự động (1 lần/ngày)

Đã cấu hình trong `routes/console.php` — crawl 03:00 hằng ngày. Để lịch chạy thật trên server, thêm vào crontab:

```cron
* * * * * cd /path/to/social-housing && php artisan schedule:run >> /dev/null 2>&1
```

Dev test ngay: `php artisan schedule:work`

## Ảnh dự án + R2

- Ảnh dự án tải về R2, lưu public URL vào DB. Chưa cấu hình R2 → giữ hotlink URL nguồn (không chặn crawl).
- Chuyển các ảnh hotlink cũ lên R2 sau khi điền creds:

```bash
php artisan images:migrate-to-r2
```

- Có domain CDN sau: đổi `R2_URL` sang domain đó, chạy lại lệnh trên để cập nhật URL ảnh cũ.

## Kiểm tra chất lượng (QA)

```bash
php artisan test           # Pest (unit + feature)
vendor/bin/pint            # format PHP
vendor/bin/phpstan analyse # static analysis (Larastan)
npm run lint               # ESLint
npm run types:check        # vue-tsc
```

## Cấu trúc thư mục chính

```
app/
  Console/Commands/     crawl:projects, images:migrate-to-r2
  Http/Controllers/     public + Admin/ + Auth/
  Models/               Project, Province, Investor, ProjectImage, Guide
  Services/
    Crawler/            CafelandCrawler, HanoiSoXayDungCrawler
    ImageStore.php      tải ảnh -> R2 (fallback hotlink)
  Enums/ProjectStatus.php
resources/js/
  pages/                Home, projects/, provinces/, guides/, admin/, auth/
  components/           ProjectCard, AppHead (SEO), StatusBadge, ...
  layouts/              PublicLayout, AdminLayout
config/
  admin.php             tài khoản admin từ env
  filesystems.php       disk 'r2'
routes/
  web.php · auth.php · admin.php · console.php (schedule)
```

## Ghi chú

- **SEO:** mỗi trang có title/description/canonical/OG/JSON-LD động (`AppHead.vue`), sitemap.xml, `lang=vi`. Bật SSR để bot đọc HTML đầy đủ.
- **Địa chỉ dự án:** lưu cả địa chỉ sau sáp nhập (`address`) và trước sáp nhập (`former_address`).
- **Giá bán** là dự kiến, trích từ mô tả nguồn; không có → hiển thị "Chưa công bố".
- Deploy production: Nginx + PHP-FPM + `inertia:start-ssr` (chạy nền qua supervisor) + MySQL + Redis.
