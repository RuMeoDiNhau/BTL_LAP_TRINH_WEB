<?php

declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $sections = PageContent::getByPage('home');
        $settings = (new Setting())->all();

        $this->view('client/home/index', [
            'pageTitle' => (string) ($settings['site_name'] ?? 'Decor'),
            'sections' => $sections,
            'settings' => $settings,
        ], null);
    }
}
