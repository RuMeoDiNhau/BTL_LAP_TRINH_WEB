<?php
declare(strict_types=1);

$templatePath = dirname(__DIR__, 4) . '/decor-template/decor/contact.html';
$html = is_file($templatePath) ? file_get_contents($templatePath) : false;
if ($html === false) {
    http_response_code(500);
    echo 'Không tải được giao diện liên hệ.';
    return;
}

$escape = static fn ($value): string => e((string) $value);
$templateBase = rtrim(base_url('/decor-template'), '/');
$html = str_replace(
    ['../cdn/', 'href="css/', 'href="style.css"', 'src="images/', 'src="js/', 'href="index.html"', 'href="contact.html"', 'href="about.html"'],
    [$templateBase . '/cdn/', 'href="' . $templateBase . '/decor/css/', 'href="' . $templateBase . '/decor/style.css"', 'src="' . $templateBase . '/decor/images/', 'src="' . $templateBase . '/decor/js/', 'href="' . base_url('/') . '"', 'href="' . base_url('/contact') . '"', 'href="' . base_url('/#about') . '"'],
    $html
);
$html = str_replace('<title>Decor</title>', '<title>' . $escape($settings['site_name'] ?? 'Decor') . ' | Liên hệ</title>', $html);
$html = str_replace('<a class="nav-link" href="#">Blog</a>', '<a class="nav-link" href="' . e(base_url('/contact')) . '">Contact</a>', $html);

$errorMarkup = '';
foreach ($errors as $message) {
    $errorMarkup .= '<div class="alert alert-danger" role="alert">' . $escape($message) . '</div>';
}
$notice = !empty($flashSuccess)
    ? '<div class="alert alert-success" role="status">' . $escape($flashSuccess) . '</div>'
    : '';
$form = '<form action="' . e(base_url('/contact')) . '" class="form-group flex-wrap" id="contact-form" method="post">'
    . csrf_field()
    . '<div class="form-input col-lg-12 d-flex mb-3"><input class="form-control ps-3 me-3" name="name" placeholder="Họ và tên" maxlength="100" required value="' . $escape($old['name'] ?? '') . '"><input class="form-control ps-3" name="email" type="email" placeholder="Email" maxlength="150" required value="' . $escape($old['email'] ?? '') . '"></div>'
    . '<div class="col-lg-12 mb-3"><input class="form-control ps-3" name="phone" placeholder="Số điện thoại" maxlength="20" value="' . $escape($old['phone'] ?? '') . '"></div>'
    . '<div class="col-lg-12 mb-3"><input class="form-control ps-3" name="subject" placeholder="Chủ đề" maxlength="200" value="' . $escape($old['subject'] ?? '') . '"></div>'
    . '<div class="col-lg-12 mb-3"><textarea class="form-control ps-3" name="message" placeholder="Nội dung liên hệ" rows="8" maxlength="5000" required>' . $escape($old['message'] ?? '') . '</textarea></div>'
    . '<div class="d-grid"><button class="btn btn-primary btn-lg text-uppercase btn-rounded-none" type="submit">Gửi liên hệ</button></div></form>';
$html = preg_replace('~<form action="contact_form\.php".*?</form>~s', $form, $html, 1) ?? $html;
$html = str_replace('<h2>Got any questions?</h2>', $notice . $errorMarkup . '<h2>Got any questions?</h2>', $html);

$address = $escape($settings['address'] ?? 'Địa chỉ đang cập nhật');
$phone = $escape($settings['phone'] ?? '');
$email = $escape($settings['email'] ?? '');
$html = str_replace('730 Glenstone Ave 65802, Springfield, US', $address, $html);
$html = str_replace('+123 987 321', $phone, $html);
$html = str_replace('+123 123 654', '', $html);
$html = str_replace('info@yourcompany.com', $email, $html);
if (!empty($settings['logo'])) {
    $logoUrl = e(base_url('/' . ltrim((string) $settings['logo'], '/')));
    $html = str_replace('/decor-template/decor/images/logo.png', $logoUrl, $html);
    $html = str_replace('/decor-template/decor/images/logo-footer.png', $logoUrl, $html);
}
echo $html;
