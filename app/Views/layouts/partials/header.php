<?php
/**
 * Header following the template DOM: preloader -> search popup -> optional cart
 * offcanvas -> navbar. Menu links come from config/nav.php; only enabled items
 * are shown. The same list is used by the desktop nav and the mobile offcanvas.
 *
 * @var string $active
 */
$active = $active ?? '';
$siteName = setting('site_name', 'DECOR');
$nav = require dirname(__DIR__, 4) . '/config/nav.php';

$menuItems = array_values(array_filter(
    $nav['items'] ?? [],
    static fn($item): bool => is_array($item) && !empty($item['enabled'])
));

// Chưa có auth chung: đọc session an toàn, các key sẽ do nhóm auth định nghĩa sau.
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
<div class="preloader text-white fs-6 text-uppercase overflow-hidden"></div>

<div class="search-popup">
    <div class="search-popup-container">
        <form role="search" method="get" class="form-group" action="<?= e(url('faq')) ?>">
            <label class="visually-hidden" for="search-form">Search</label>
            <input type="search" id="search-form" class="form-control border-0 border-bottom"
                   placeholder="Type and press enter" name="s">
            <button type="submit" class="search-submit border-0 position-absolute bg-white"
                    style="top: 15px;right: 15px;" aria-label="Search">
                <svg class="search" width="24" height="24" aria-hidden="true"><use href="#search"></use></svg>
            </button>
        </form>

        <h5 class="cat-list-title">Browse Categories</h5>
        <ul class="cat-list">
            <li class="cat-list-item"><a href="#" title="Offices">Offices</a></li>
            <li class="cat-list-item"><a href="#" title="Kitchen">Kitchen</a></li>
            <li class="cat-list-item"><a href="#" title="Living Room">Living Room</a></li>
            <li class="cat-list-item"><a href="#" title="Hall">Hall</a></li>
            <li class="cat-list-item"><a href="#" title="Dining">Dining</a></li>
            <li class="cat-list-item"><a href="#" title="Bedroom">Bedroom</a></li>
            <li class="cat-list-item"><a href="#" title="Outdoor">Outdoor</a></li>
        </ul>
    </div>
</div>

<?php if ($cartEnabled): ?>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart"
         role="dialog" aria-label="Shopping cart">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Your cart</span>
                    <span class="badge bg-primary rounded-pill">0</span>
                </h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-sm">The cart will be updated soon.</li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg text-uppercase fs-6 p-3 border-bottom fixed-top bg-white"
     aria-label="Main navigation">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= e(url('')) ?>">
            <img src="<?= e(asset('assets/images/logo.png')) ?>" class="img-fluid" alt="<?= e($siteName) ?>">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
             role="dialog" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center align-items-lg-center flex-grow-1 gap-1 gap-md-5 pe-3">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                        $itemRoute = (string)$item['route'];
                        $isActive = $itemRoute !== '' && $active === $itemRoute;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $isActive ? ' active' : '' ?>"
                               href="<?= e(url($itemRoute)) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                                <?= e($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <ul class="list-unstyled d-flex align-items-center m-0 mt-4 mt-md-0">
                    <li class="search-box">
                        <a href="#" class="search-button me-4 me-lg-0 ms-0 ms-lg-4" aria-label="Search">
                            <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><use href="#search"></use></svg>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle me-4 me-lg-0 ms-0 ms-lg-4" id="accountMenu"
                           role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false" aria-label="Account menu">
                            <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><use href="#user"></use></svg>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end list-unstyled border p-3" aria-labelledby="accountMenu">
                            <?php foreach ($accountLinks as $link): ?>
                                <?php
                                $linkEnabled = !empty($link['enabled']);
                                $linkHref = $linkEnabled ? e(url((string)$link['route'])) : '#';
                                $linkClass = 'dropdown-item item-anchor' . ($linkEnabled ? '' : ' disabled');
                                $linkAttrs = $linkEnabled ? '' : ' aria-disabled="true" tabindex="-1"';
                                ?>
                                <li>
                                    <a href="<?= $linkHref ?>" class="<?= e($linkClass) ?>"<?= $linkAttrs ?>>
                                        <?= e($link['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <?php if ($cartEnabled): ?>
                        <li>
                            <a href="#" class="me-4 me-lg-0 ms-0 ms-lg-4" aria-label="Shopping cart"
                               data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart"
                               aria-controls="offcanvasCart">
                                <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><use href="#cart"></use></svg>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>
