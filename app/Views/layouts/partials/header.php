<?php
/**
 * Header tự viết: fixed navbar 7 mục, search overlay, account dropdown, cart theo cờ.
 * Không dùng markup/class của template DECOR; chỉ Bootstrap + class dc-.
 *
 * @var string $active
 */
$active = $active ?? '';
$siteName = setting('site_name', 'DECOR');
$nav = require dirname(__DIR__, 4) . '/config/nav.php';

$menuItems = array_values(array_filter(
    $nav['items'] ?? [],
    function ($item): bool {
        return is_array($item) && !empty($item['enabled']);
    }
));

// Chưa có auth chung: đọc session an toàn để nhóm auth thay thế sau.
$session = $_SESSION ?? [];
$user = $session['user'] ?? null;
$userId = $session['user_id'] ?? (is_array($user) ? ($user['id'] ?? null) : null);
$isLoggedIn = $userId !== null && $userId !== '' && (int)$userId > 0;
$role = $session['role'] ?? (is_array($user) ? ($user['role'] ?? '') : '');
$isAdmin = $isLoggedIn && in_array(strtolower((string)$role), ['admin', 'administrator'], true);

$accountLinks = $isLoggedIn
    ? array_merge($nav['account']['user'] ?? [], $isAdmin ? ($nav['account']['admin'] ?? []) : [])
    : ($nav['account']['guest'] ?? []);

$cartEnabled = !empty($nav['cart_enabled']);
?>
<header class="dc-header">
    <nav class="navbar navbar-expand-lg dc-navbar fixed-top" aria-label="Main navigation">
        <div class="container-fluid dc-header__inner">
            <a class="navbar-brand dc-brand" href="<?= e(url('')) ?>">
                <img class="dc-brand__logo" src="<?= e(asset('assets/images/logo.png')) ?>"
                     alt="<?= e($siteName) ?>">
            </a>

            <div class="d-none d-lg-flex dc-header__nav">
                <ul class="navbar-nav dc-nav mb-0">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                        $route = (string)$item['route'];
                        $isActive = $route !== '' && $active === $route;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $isActive ? ' active' : '' ?>"
                               href="<?= e(url($route)) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                                <?= e($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="d-none d-lg-flex dc-header__actions">
                <ul class="list-unstyled d-flex align-items-center dc-actions mb-0">
                    <li>
                        <button type="button" class="dc-icon-btn" data-dc-search-open
                                aria-controls="dc-search" aria-expanded="false" aria-label="Open search">
                            <?= icon('search') ?>
                        </button>
                    </li>
                    <li class="dropdown dc-account">
                        <button type="button" class="dc-icon-btn" id="dcAccount"
                                data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                            <?= icon('person') ?>
                            <?= icon('chevron-down', 'dc-icon-btn__chevron') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dcAccount">
                            <?php foreach ($accountLinks as $link): ?>
                                <?php
                                $enabled = !empty($link['enabled']);
                                $href = $enabled ? e(url((string)$link['route'])) : '#';
                                $class = 'dropdown-item' . ($enabled ? '' : ' disabled');
                                $attrs = $enabled ? '' : ' aria-disabled="true" tabindex="-1"';
                                ?>
                                <li><a class="<?= e($class) ?>" href="<?= $href ?>"<?= $attrs ?>>
                                    <?= e($link['label']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php if ($cartEnabled): ?>
                        <li>
                            <button type="button" class="dc-icon-btn" data-bs-toggle="offcanvas"
                                    data-bs-target="#dcCart" aria-controls="dcCart" aria-label="Shopping cart">
                                <?= icon('cart') ?>
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#dcNav" aria-controls="dcNav" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
</header>

<!-- Search overlay: ẩn bằng hidden, JS bật/tắt. -->
<div class="dc-search" id="dc-search" role="dialog" aria-modal="true" aria-labelledby="dcSearchTitle" hidden>
    <button type="button" class="btn-close dc-search__close" data-dc-search-close aria-label="Close search"></button>
    <div class="dc-search__panel">
        <h2 class="h5" id="dcSearchTitle">Search DECOR</h2>
        <form class="position-relative" role="search" method="get" action="<?= e(url('faq')) ?>">
            <label class="visually-hidden" for="dcSearchInput">Search</label>
            <input type="search" id="dcSearchInput" name="s" class="form-control dc-search__input"
                   placeholder="Type and press enter" autocomplete="off">
            <button type="submit" class="dc-search__submit" aria-label="Submit search">
                <?= icon('search') ?>
            </button>
        </form>
        <h3 class="h6 mt-4">Browse categories</h3>
        <ul class="list-unstyled d-flex flex-wrap gap-3 mb-0">
            <li><a href="#">Offices</a></li>
            <li><a href="#">Kitchen</a></li>
            <li><a href="#">Living Room</a></li>
            <li><a href="#">Dining</a></li>
            <li><a href="#">Bedroom</a></li>
        </ul>
    </div>
</div>

<!-- Menu trượt dưới 992px: cùng danh sách liên kết, không dùng dropdown để tránh cắt menu. -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="dcNav" role="dialog" aria-labelledby="dcNavLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title h5" id="dcNavLabel">Menu</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <ul class="navbar-nav dc-offcanvas-nav">
            <?php foreach ($menuItems as $item): ?>
                <?php
                $route = (string)$item['route'];
                $isActive = $route !== '' && $active === $route;
                ?>
                <li class="nav-item">
                    <a class="nav-link<?= $isActive ? ' active' : '' ?>"
                       href="<?= e(url($route)) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                        <?= e($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-4 border-top pt-3">
            <h2 class="h6 dc-offcanvas__title">Account</h2>
            <ul class="list-unstyled dc-offcanvas-account">
                <?php if ($accountLinks === []): ?>
                    <li><span class="text-muted">Account features coming soon</span></li>
                <?php else: ?>
                    <?php foreach ($accountLinks as $link): ?>
                        <?php $enabled = !empty($link['enabled']); ?>
                        <li>
                            <a href="<?= $enabled ? e(url((string)$link['route'])) : '#' ?>"
                               <?= $enabled ? '' : 'aria-disabled="true" tabindex="-1"' ?>>
                                <?= e($link['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <ul class="list-unstyled d-flex align-items-center dc-actions mt-auto pt-4">
            <li>
                <button type="button" class="dc-icon-btn" data-bs-dismiss="offcanvas" data-dc-search-open
                        aria-controls="dc-search" aria-expanded="false" aria-label="Open search">
                    <?= icon('search') ?>
                </button>
            </li>
            <?php if ($cartEnabled): ?>
                <li>
                    <button type="button" class="dc-icon-btn" data-bs-dismiss="offcanvas"
                            data-bs-toggle="offcanvas" data-bs-target="#dcCart"
                            aria-controls="dcCart" aria-label="Shopping cart">
                        <?= icon('cart') ?>
                    </button>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php if ($cartEnabled): ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="dcCart" role="dialog" aria-labelledby="dcCartLabel">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title h5" id="dcCartLabel">Your cart</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p>The cart will be updated soon.</p>
        </div>
    </div>
<?php endif; ?>
