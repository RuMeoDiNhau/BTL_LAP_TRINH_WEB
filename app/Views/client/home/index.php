<?php
declare(strict_types=1);

$templatePath = dirname(__DIR__, 4) . '/decor-template/decor/index.html';
$html = is_file($templatePath) ? file_get_contents($templatePath) : false;
if ($html === false) {
    http_response_code(500);
    echo 'Không tải được giao diện trang chủ.';
    return;
}

$escape = static fn ($value): string => e((string) $value);
$templateBase = rtrim(base_url('/decor-template'), '/');
$hero = $sections['hero'] ?? [];
$welcome = $sections['welcome'] ?? [];
$about = $sections['about'] ?? [];

$html = str_replace(
    ['../cdn/', 'href="css/', 'href="style.css"', 'src="images/', 'src="js/', 'href="index.html"', 'href="contact.html"', 'href="about.html"', 'href="shop-four-column-wide.html"', 'href="shop.html"', 'href="../../public/login"', 'href="../../public/register"'],
    [$templateBase . '/cdn/', 'href="' . $templateBase . '/decor/css/', 'href="' . $templateBase . '/decor/style.css"', 'src="' . $templateBase . '/decor/images/', 'src="' . $templateBase . '/decor/js/', 'href="' . base_url('/') . '"', 'href="' . base_url('/contact') . '"', 'href="' . base_url('/#about') . '"', 'href="' . base_url('/contact') . '"', 'href="' . base_url('/products') . '"', 'href="' . e(base_url('/login')) . '"', 'href="' . e(base_url('/register')) . '"'],
    $html
);
$html = str_replace('<title>Decor</title>', '<title>' . $escape($settings['site_name'] ?? 'Decor') . '</title>', $html);
$html = str_replace('<a class="nav-link" href="#">Blog</a>', '<a class="nav-link" href="' . e(base_url('/contact')) . '">Contact</a>', $html);
$html = str_replace('href="shop.html"', 'href="' . e(base_url('/products')) . '"', $html);
$html = str_replace('aria-controls="offcanvasCart" ', 'aria-label="Giỏ hàng" ', $html);
$html = str_replace('data-bs-target="#offcanvasCart" data-bs-toggle="offcanvas" ', '', $html);
$html = str_replace('href="cart.html"', 'href="' . e(base_url('/cart')) . '"', $html);

$html = str_replace('Room Decors', $escape($hero['title'] ?? 'Room Decors'), $html);
$html = str_replace('Cozy Furnitures for your personal spaces', nl2br($escape($hero['content'] ?? 'Cozy Furnitures for your personal spaces')), $html);
$html = str_replace('contact@yourcompany.com', $escape($settings['email'] ?? ''), $html);
$html = str_replace('<h6 class="text-center text-uppercase">Welcome</h6>', '<h6 class="text-center text-uppercase">' . $escape($welcome['title'] ?? 'Welcome') . '</h6>', $html);

$replaceFirst = static function (string $pattern, string $replacement, string $source): string {
    $result = preg_replace_callback($pattern, static function (array $matches) use ($replacement): string {
        return ($matches[1] ?? '') . $replacement . ($matches[2] ?? '');
    }, $source, 1);
    return is_string($result) ? $result : $source;
};
$welcomeText = $escape($welcome['content'] ?? 'Our lives happen around our furniture. Every day, our bed frames, dining tables, dressers, and coffee tables support us, often without thanks and appreciation.');
$html = $replaceFirst('~(<section[^>]*id="welcome".*?<h2 class="fs-2 text-center">).*?(</h2>)~s', '$1' . $welcomeText . '$2', $html);
$aboutTitle = $escape($about['title'] ?? 'Add life to your personal space with Decor');
$html = $replaceFirst('~(<section[^>]*id="about".*?<h2[^>]*>).*?(</h2>)~s', '$1' . $aboutTitle . '$2', $html);
$aboutContent = nl2br($escape($about['content'] ?? ''));
$html = $replaceFirst('~(<section[^>]*id="about".*?<p>).*?(</p>)~s', '$1' . $aboutContent . '$2', $html);

foreach ([
    ['/decor-template/decor/images/banner-large-1.jpg', $hero['image'] ?? null],
    ['/decor-template/decor/images/about.jpg', $about['image'] ?? null],
    ['/decor-template/decor/images/logo.png', $settings['logo'] ?? null],
    ['/decor-template/decor/images/logo-footer.png', $settings['logo'] ?? null],
] as [$oldPath, $storedPath]) {
    if (is_string($storedPath) && $storedPath !== '') {
        $html = str_replace($oldPath, base_url('/' . ltrim($storedPath, '/')), $html);
    }
}

if (!empty($welcome['image'])) {
    $image = '<div class="container text-center mb-4"><img class="img-fluid" src="' . e(base_url('/' . ltrim((string) $welcome['image'], '/'))) . '" alt=""></div>';
    $html = str_replace('<section class="padding-medium" id="welcome">', '<section class="padding-medium" id="welcome">' . $image, $html);
}

$html = str_replace('</body>', '<script>document.querySelectorAll("a[href=\"wishlist.html\"]").forEach(a=>a.addEventListener("click",e=>e.preventDefault()));</script></body>', $html);
$html = str_replace('</head>', '<style>#billboard .swiper-slide{isolation:isolate}#billboard .jarallax-img{z-index:0!important;object-fit:cover}#billboard .banner-content{z-index:1}</style></head>', $html);
echo $html;
