# DRESSY - Premium Fashion E-Commerce (CodeIgniter 3 + MySQL + Bootstrap 5 + jQuery/AJAX)

## Install (XAMPP / cPanel)
1. Copy the `dressy` folder to `htdocs` (or `public_html`). Needs PHP 7.4 - 8.4 with mysqli, mbstring, gd.
2. Create a MySQL database (utf8mb4) and import, in order: `database/01_schema.sql` then `database/02_seed.sql` (demo data).
3. Edit `application/config/database.php` (hostname, username, password, database).
4. Make `application/cache/sessions`, `application/logs` and `uploads/` writable (chmod 775).
5. Sub-folder install (http://localhost/dressy/): open `.htaccess` and set `RewriteBase /dressy/`.
6. Optional: set a fixed `base_url` in `application/config/config.php` for production.

Store: `/`    Admin: `/admin`  ->  admin@dressy.com / Admin@123  (change it in Settings)
Demo customer password: `Customer@123`

## Where is everything
- Storefront: Home, Shop (filters/sort/search), Product, Cart (drawer + page), Checkout, Account, Wishlist, Track Order, Contact, CMS pages
- Order on Demand: sold-out products show a "Place demand order" button; `/demand` accepts any product request. Admin quotes price + ETA; customer tracks it.
- Design Studio `/customize`: jeans, t-shirt OR shirt, shoes (all optional) shown live on a mannequin; whole package goes to cart (full outfit = discount, set in Settings).
- Admin: Dashboard, Traffic Analytics (Facebook / Instagram / TikTok / WhatsApp / Google / Direct + country & city), Tracking Links, Orders, Demand Orders, Customers, Coupons, Products, Categories, Customizer Items, Reviews, Banners, Pages, Messages, Subscribers, Settings.

## Traffic tracking notes
- Source is detected from `utm_source`, in-app browsers (Facebook/Instagram/TikTok), and the referrer. WhatsApp hides referrers, so use **Admin > Tracking Links** (adds `?utm_source=whatsapp`).
- Country/city: uses Cloudflare headers when present, otherwise free ip-api.com (cached in `geo_cache`). Turn off in Settings.
- Each order stores the visitor's first-touch source, so the analytics page shows orders, revenue and conversion per platform.

## Notes
- Demo product images are vector placeholders - replace them from Admin > Products.
- Payments: Cash on Delivery, Bank Transfer, Easypaisa/JazzCash (manual). No card gateway is integrated; no emails are sent (add SMTP via CI email library if needed).
