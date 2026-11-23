<?php

namespace App\Controllers\Client;

use App\Models\Contact;
use App\Models\Faq;
use App\Models\PageContent;
use App\Models\Post;

final class PageController extends \Controller
{
    public function about(): void
    {
        $sections = PageContent::getByPage('about');
        $seo = $sections['seo'] ?? [];
        $title = trim((string)($seo['title'] ?? ''));
        $desc = trim((string)($seo['content'] ?? ''));

        $this->render('client/about/index', [
            'sections'    => $sections,
            'latestPosts' => Post::getLatest(3),
        ], [
            'title'       => $title !== '' ? $title : 'About Us | DECOR',
            'description' => $desc !== '' ? $desc : 'Learn more about DECOR, your partner for home decor and furniture.',
            'canonical'   => 'about',
            'og_image'    => 'assets/images/post-thumbnail-1.jpg',
            'active'      => 'about',
        ]);
    }

    public function faq(): void
    {
        $this->renderFaq();
    }

    /** Temporary root route: Home points to the only finished page for now. */
    public function home(): void
    {
        $this->redirect('about');
    }

    /** TEMP: serves Products/Pricing/News/Contact until their real pages exist. */
    public function comingSoon(): void
    {
        $nav = require dirname(__DIR__, 3) . '/config/nav.php';

        $uri = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        $path = '/' . trim($uri, '/');
        $base = base_url();
        if ($base !== '') {
            if ($path === $base) {
                $path = '/';
            } elseif (str_starts_with($path, $base . '/')) {
                $path = substr($path, strlen($base)) ?: '/';
            }
        }
        $route = trim($path, '/');

        $label = 'Coming Soon';
        foreach (($nav['items'] ?? []) as $item) {
            if ($route !== '' && ($item['route'] ?? '') === $route) {
                $label = (string)$item['label'];
                break;
            }
        }

        $this->render('client/coming-soon/index', ['pageTitle' => $label], [
            'title'       => $label . ' | DECOR',
            'description' => $label . ' is coming soon at DECOR.',
            'canonical'   => $route,
            'og_image'    => 'assets/images/post-thumbnail-1.jpg',
            'active'      => $route,
        ]);
    }

    /** POST /faq/ask: xử lý form "Ask us anything". */
    public function ask(): void
    {
        if (!csrf_verify()) {
            http_response_code(403);
            $this->renderFaq(['formError' => 'Security token is missing or invalid. Please try again.']);
            return;
        }

        // Honeypot: bot điền vào ô ẩn thì chặn, không ghi CSDL.
        if ((string)($_POST['website'] ?? '') !== '') {
            http_response_code(403);
            $this->renderFaq(['formError' => 'Your submission could not be processed.']);
            return;
        }

        $old = [
            'name'    => trim((string)($_POST['name'] ?? '')),
            'email'   => trim((string)($_POST['email'] ?? '')),
            'phone'   => trim((string)($_POST['phone'] ?? '')),
            'subject' => trim((string)($_POST['subject'] ?? '')),
            'message' => trim((string)($_POST['message'] ?? '')),
        ];

        $length = static function (string $value): int {
            return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
        };

        $errors = [];
        if ($old['name'] === '') {
            $errors['name'] = 'Please enter your name.';
        } elseif ($length($old['name']) > 100) {
            $errors['name'] = 'Name must be 100 characters or fewer.';
        }

        if ($old['email'] === '') {
            $errors['email'] = 'Please enter your email address.';
        } elseif (filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false || $length($old['email']) > 150) {
            $errors['email'] = 'Please enter a valid email address (150 characters or fewer).';
        }

        // Phone is optional, but must match the HTML5 pattern when filled.
        if ($old['phone'] !== '' && preg_match('/^[0-9+() .-]{0,20}$/', $old['phone']) !== 1) {
            $errors['phone'] = 'Please enter a valid phone number (20 characters or fewer).';
        }

        if ($old['subject'] === '') {
            $errors['subject'] = 'Please enter a subject.';
        } elseif ($length($old['subject']) > 200) {
            $errors['subject'] = 'Subject must be 200 characters or fewer.';
        }

        if ($old['message'] === '') {
            $errors['message'] = 'Please enter your message.';
        } elseif ($length($old['message']) > 5000) {
            $errors['message'] = 'Message must be 5000 characters or fewer.';
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->renderFaq([
                'errors'    => $errors,
                'old'       => $old,
                'formError' => 'Please correct the highlighted fields.',
            ]);
            return;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);
        $saved = Contact::create([
            'user_id' => $userId > 0 ? $userId : null,
            'name'    => $old['name'],
            'email'   => $old['email'],
            'phone'   => $old['phone'],
            'subject' => $old['subject'],
            'message' => $old['message'],
        ]);

        if (!$saved) {
            http_response_code(500);
            $this->renderFaq([
                'old'       => $old,
                'formError' => 'Sorry, we could not save your message. Please try again.',
            ]);
            return;
        }

        $_SESSION['flash_success'] = 'Thank you! Your message has been sent.';
        $this->redirect('faq');
    }

    /**
     * Dùng chung cho GET /faq và các lần render lại form có lỗi.
     * Flash success được đọc và xóa ở đây để refresh không hiện lại thông báo.
     *
     * @param array<string, mixed> $extra
     */
    private function renderFaq(array $extra = []): void
    {
        $flashSuccess = (string)($_SESSION['flash_success'] ?? '');
        unset($_SESSION['flash_success']);

        $sections = PageContent::getByPage('faq');
        $seo = $sections['seo'] ?? [];
        $title = trim((string)($seo['title'] ?? ''));
        $desc = trim((string)($seo['content'] ?? ''));

        $data = array_merge([
            'sections'     => $sections,
            'faqs'         => Faq::getActive(),
            'errors'       => [],
            'old'          => [],
            'formError'    => '',
            'flashSuccess' => $flashSuccess,
        ], $extra);

        $this->render('client/faq/index', $data, [
            'title'       => $title !== '' ? $title : 'FAQs | DECOR',
            'description' => $desc !== '' ? $desc : 'Answers to common questions about DECOR products and services.',
            'canonical'   => 'faq',
            'og_image'    => 'assets/images/post-thumbnail-2.jpg',
            'active'      => 'faq',
        ]);
    }
}
