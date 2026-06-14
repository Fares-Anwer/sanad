<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sanad_db');

define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'pdf']);
define('ALLOWED_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);
define('LEAFLET_CSS', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
define('LEAFLET_JS', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
define('CSRF_TOKEN_LIFETIME', 7200);
define('SITE_NAME', 'سند — Sanad');
define('SITE_URL', 'http://localhost/sanad');
define('TAILWIND_CDN', 'https://cdn.tailwindcss.com');
