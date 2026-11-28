<?php
$hero = $sections['hero'] ?? [];
$welcome = $sections['welcome'] ?? [];
$about = $sections['about'] ?? [];

$sectionImage = static function (array $section, ?string $fallback = null): ?string {
    $path = trim((string) ($section['image'] ?? ''));
    return $path === '' ? $fallback : base_url('/' . ltrim($path, '/'));
};

$heroTitle = trim((string) ($hero['title'] ?? ''));
$heroContent = trim((string) ($hero['content'] ?? 'Chọn nội thất và đồ trang trí phù hợp cho từng căn phòng.'));
$heroImage = $sectionImage($hero, base_url('/decor-template/decor/images/banner-large-1.jpg'));
$welcomeTitle = trim((string) ($welcome['title'] ?? ''));
$welcomeContent = trim((string) ($welcome['content'] ?? ''));
$welcomeImage = $sectionImage($welcome);
$aboutTitle = trim((string) ($about['title'] ?? ''));
$aboutContent = trim((string) ($about['content'] ?? ''));
$aboutImage = $sectionImage($about, base_url('/decor-template/decor/images/about.jpg'));
?>
<div class="shop-front shop-shell home-page">
  <section class="home-hero" id="billboard" aria-labelledby="home-title">
    <div class="home-hero__copy">
      <h1 id="home-title"><?= e($heroTitle === '' ? 'Nội thất cho không gian sống' : $heroTitle) ?></h1>
      <?php if ($heroContent !== ''): ?><p><?= nl2br(e($heroContent)) ?></p><?php endif; ?>
      <a class="shop-btn" href="<?= e(base_url('/products')) ?>">Xem sản phẩm</a>
    </div>
    <div class="home-hero__picture">
      <img src="<?= e($heroImage) ?>" alt="" fetchpriority="high">
    </div>
  </section>

  <?php if ($welcomeTitle !== '' || $welcomeContent !== '' || $welcomeImage !== null): ?>
    <section class="home-welcome" id="welcome" aria-labelledby="home-welcome-title">
      <h2 id="home-welcome-title"><?= e($welcomeTitle === '' ? 'Chào mừng đến với Decor' : $welcomeTitle) ?></h2>
      <?php if ($welcomeContent !== ''): ?><p><?= nl2br(e($welcomeContent)) ?></p><?php endif; ?>
      <?php if ($welcomeImage !== null): ?><img src="<?= e($welcomeImage) ?>" alt="" loading="lazy"><?php endif; ?>
    </section>
  <?php endif; ?>

  <?php if ($products): ?>
    <section class="home-products" id="collections" aria-labelledby="home-products-title">
      <div class="shop-section-head">
        <h2 id="home-products-title">Sản phẩm mới</h2>
        <a class="shop-link" href="<?= e(base_url('/products')) ?>">Xem tất cả sản phẩm</a>
      </div>
      <div class="shop-product-grid home-products__grid">
        <?php foreach ($products as $product): ?>
          <?php require dirname(__DIR__, 2) . '/partials/shop_product_card.php'; ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <section class="home-about" id="about" aria-labelledby="home-about-title">
    <img class="home-about__picture" src="<?= e($aboutImage) ?>" alt="" loading="lazy">
    <div class="home-about__copy">
      <h2 id="home-about-title"><?= e($aboutTitle === '' ? 'Về Decor' : $aboutTitle) ?></h2>
      <?php if ($aboutContent !== ''): ?><p><?= nl2br(e($aboutContent)) ?></p><?php endif; ?>
      <a class="shop-link" href="<?= e(base_url('/about')) ?>">Tìm hiểu về Decor</a>
    </div>
  </section>
</div>
