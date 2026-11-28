<?php

declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $sections = PageContent::getByPage('home');
        $settings = (new Setting())->all();
        $logo = trim((string) ($settings['logo'] ?? ''));
        $layout = ShopView::storefront('home');
        $layout['headExtra'] .= '<link rel="stylesheet" href="' . e(asset('css/pages/home.css')) . '">' . "\n";

        $this->view('client/home/index', $layout + [
            'pageTitle' => (string) ($settings['site_name'] ?? 'Decor'),
            'brandLogo' => $logo === '' ? null : base_url('/' . ltrim($logo, '/')),
            'sections' => $sections,
            'products' => (new Product())->paginate('', 0, 'active', 1, 4)['rows'],
        ]);
    }
}
