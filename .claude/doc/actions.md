# Actions Log — Website Nhà Ở Xã Hội

Plan gốc: `/home/huy/.claude/plans/b-n-h-y-gi-p-t-calm-brook.md`

## Stack (DONE)
- MySQL 8 (`social_housing`, user `social`/`secret`), Redis cache/queue/session qua predis (ping OK).
- ⚠️ **CHỜ USER tạo DB** (root cần mật khẩu, session không sudo được):
  `sudo mysql -e "CREATE DATABASE social_housing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER 'social'@'localhost' IDENTIFIED BY 'secret'; GRANT ALL PRIVILEGES ON social_housing.* TO 'social'@'localhost'; FLUSH PRIVILEGES;"`
- Sau đó: `php artisan migrate:fresh --seed`

## Phase 1 DB — DONE
Migrations, ProjectStatus enum, models (+@property docblocks), factories, seeders (34 tỉnh sau sáp nhập, admin@noxh.test/password, 4 dự án mẫu + 20 fake, 3 guide). Project có former_address (địa chỉ trước sáp nhập).

## Phase 2 Public backend — DONE
Home/Project/Province/Guide/Sitemap controllers + routes/web.php (slug binding). robots.txt.

## Phase 3 Frontend — DONE
PublicLayout, AppHead(SEO), ProjectCard/Filters/StatusBadge/Pagination, pages Home + projects + provinces + guides. Welcome.vue removed.

## Phase 4 SEO — DONE
AppHead: title/desc/canonical/OG/twitter/JSON-LD. JSON-LD Residence (project) + Article (guide). sitemap.xml động + blade. robots.txt.

## Phase 5 Auth+Admin — DONE
Login (no register, throttle), EnsureUserIsAdmin middleware (is_admin), admin CRUD projects+guides, FormRequests, AdminLayout, Dashboard + crawl button.

## Phase 6 Crawler — DONE
HanoiSoXayDungCrawler (DOMDocument/XPath, parse tách riêng), crawl:projects command (upsert theo external_id, import nháp chờ duyệt), fixture test (2 pass).
⚠️ XPath selector là GIẢ ĐỊNH (.project-item/.project-name...) — web tool lỗi nên chưa xem DOM thật. Phải chỉnh selector theo HTML thật của soxaydung.hanoi.gov.vn khi crawl live.

## Quality gates — PASS
- phpstan: 0 errors
- pint: passed
- vue-tsc types:check: passed
- eslint: passed
- npm run build: passed
- pest: 13 passed (crawler 2 + public 6 + admin 5), sqlite :memory:
- Bug fix: HomeController `having` không group by (vỡ trên sqlite+mysql) → đổi whereHas.

## DB — DONE
- MySQL creds: root/admin (user tự set). Laravel auto-tạo DB `social_housing`.
- `php artisan migrate:fresh --seed` chạy sạch. Fix: Province factory faker city() trùng slug → 20 dự án fake recycle tỉnh đã seed.
- Data: 34 tỉnh, 24 dự án (published), 3 guide, 1 admin.

## SSR — DONE (bật theo yêu cầu SEO)
- `@inertiajs/vite` auto-sinh SSR entry (không cần viết ssr.ts). `npm run build` giờ build cả `vite build --ssr` → `bootstrap/ssr/app.js`.
- Verify HTML thô: title/meta description/og:title/JSON-LD render đầy đủ server-side, content NHS Trung Văn xuất hiện 12 lần (không chỉ data-page). Trước SSR: chỉ title generic.
- Chạy SSR: `php artisan inertia:start-ssr` (port 13714). Node 22 OK.
- Deploy: nginx + php-fpm + `php artisan inertia:start-ssr` (supervisor/pm2) + redis + mysql. Build: `npm run build`.

## Verify end-to-end — PASS
- /, /du-an, /du-an/{slug}, /sitemap.xml đều 200. Admin chặn guest. SSR meta đầy đủ.
- 13 test pass, phpstan 0, pint/eslint/types sạch, build+build:ssr OK.

## Crawl thật — DONE (cafeland.vn)
- Nguồn: batdongsan chặn 403, soxaydung DOM chưa rõ → chọn **cafeland.vn/du-an/nha-o-xa-hoi/** (tổng hợp toàn quốc, 200 OK, phân trang `page-N/`).
- `CafelandCrawler`: parse `ul.projectList > li` (tên/link/ảnh/địa chỉ/chủ đầu tư/trạng thái) + `fetchDetail()` lấy diện tích + mô tả từ trang chi tiết. Cả 2 parse tách riêng, test fixture.
- Command mở rộng: `crawl:projects --source=cafeland --pages=N --details --publish`. Map địa chỉ→tỉnh (đoán theo tên tỉnh cuối địa chỉ).
- **Đã crawl 45 dự án** vào 17 tỉnh, 42 có chủ đầu tư, 45 có mô tả, 8 có diện tích, đã publish + hiện trên web (SSR render OK, ảnh cafeland load).
- Lệnh chạy lại định kỳ: `php artisan crawl:projects --source=cafeland --pages=5 --details` (bỏ --publish nếu muốn duyệt tay).
- Test: 15 pass (crawler 4: list+detail+blank+hanoi). phpstan 0.
- Fix phụ: date docblock Project/Guide → CarbonImmutable (app dùng Date::use(CarbonImmutable)).

## Giá bán + schedule — DONE
- Giá NOXH là dự kiến, nằm trong text mô tả → `extractPrice()` trích từ text ("13,9 triệu/m2"), KHÔNG cần web khác.
- Sanity cap 6-35 triệu/m² (loại trích nhầm, vd IA25 70 triệu → null). Ngoài range/không có → UI "Chưa công bố" (đổi format.ts từ "Liên hệ").
- Kết quả: 22/45 có giá (7,2-34,8 triệu/m²), 23 "Chưa công bố".
- **Schedule 1 ngày/lần** (routes/console.php): `crawl:projects --source=cafeland --pages=5 --details --publish` lúc 03:00, withoutOverlapping + runInBackground. Upsert theo external_id, dự án đã publish giữ nguyên.
- ⚠️ Cần cron chạy scheduler: thêm `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1` vào crontab server. (dev: `php artisan schedule:work`)
- Test crawler: 6 pass (list/detail/price/price-null/price-cap/blank/hanoi). phpstan 0.

## Ảnh dự án + R2 — DONE
- Trước: hotlink URL cafeland vào project_images.url.
- Cài `league/flysystem-aws-s3-v3`. Thêm disk `r2` (config/filesystems.php, S3-compatible, region=auto, path-style).
- Env R2 (.env + .env.example, để trống chờ user điền): R2_ACCESS_KEY_ID/SECRET/BUCKET/ENDPOINT/URL. R2_URL = public bucket url (r2.dev) hoặc domain CDN sau.
- `App\Services\ImageStore`: R2 configured → tải ảnh + upload R2 → trả public URL; chưa configured hoặc lỗi → fallback hotlink (không chặn crawl).
- Crawler dùng ImageStore khi lưu ảnh. Command mới `images:migrate-to-r2` để backfill ảnh hotlink cũ sau khi có creds.
- Test: 18 pass (+ImageStore fallback). phpstan 0.
- **User TODO**: tạo R2 bucket + điền R2_* vào .env → `php artisan images:migrate-to-r2` để chuyển 45 ảnh hiện tại. Crawl mới tự lên R2.

## Đang chạy (dev, cần dừng khi xong)
- PHP server: `php -S 127.0.0.1:8123 -t public` (PID trong /tmp/serve.log)
- SSR server: `php artisan inertia:start-ssr` (port 13714, /tmp/ssr.log)
