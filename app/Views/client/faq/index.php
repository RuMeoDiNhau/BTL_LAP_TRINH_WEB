<?php
/**
 * FAQ page: nội dung đọc từ DB (page_contents, faqs, settings). Nhãn/placeholder form giữ cố định.
 *
 * @var array<string, array<string, mixed>> $sections
 * @var array<int, array<string, mixed>> $faqs
 * @var array<string, string> $errors
 * @var array<string, string> $old
 * @var string $formError
 * @var string $flashSuccess
 */
$errors = $errors ?? [];
$old = $old ?? [];
$formError = $formError ?? '';
$flashSuccess = $flashSuccess ?? '';

$text = static function (string $key) use ($sections): string {
    return trim((string)($sections[$key]['content'] ?? ''));
};
$heading = static function (string $key) use ($sections): string {
    return trim((string)($sections[$key]['title'] ?? ''));
};

$heroTitle = $heading('hero');
$introTitle = $heading('intro');
$introText = $text('intro');
$askTitle = $heading('ask');
$askText = $text('ask');

$email = trim(setting('email', ''));
$phone = trim(setting('phone', ''));
$address = trim(setting('address', ''));

$fieldValue = static function (string $key) use ($old): string {
    return e((string)($old[$key] ?? ''));
};
$fieldClass = static function (string $key) use ($errors): string {
    return 'form-control' . (isset($errors[$key]) ? ' is-invalid' : '');
};
$fieldInvalid = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? ' aria-invalid="true"' : '';
};
$errorSpan = static function (string $key) use ($errors): string {
    $message = isset($errors[$key]) ? e((string)$errors[$key]) : '';
    return '<small class="field-error d-block" id="faq-' . e($key) . '-error" role="alert">' . $message . '</small>';
};
?>
<section class="dc-page-head">
    <div class="container">
        <h1 class="display-5 fw-bold"><?= e($heroTitle) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($heroTitle) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="dc-section dc-faq">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-8">
                <?php if ($introTitle !== ''): ?><h2 class="h3 fw-bold dc-faq__title"><?= e($introTitle) ?></h2><?php endif; ?>
                <?php if ($introText !== ''): ?><p class="dc-faq__intro mb-4"><?= nl2br(e($introText)) ?></p><?php endif; ?>

                <?php if ($faqs === []): ?>
                    <p>Our FAQs are being updated.</p>
                <?php else: ?>
                    <div class="accordion accordion-flush dc-accordion" id="accordion-box">
                        <?php foreach ($faqs as $index => $faq): ?>
                            <?php
                            $faqId = (int)$faq['id'];
                            $headingId = 'faq-heading-' . $faqId;
                            $collapseId = 'faq-collapse-' . $faqId;
                            $expanded = $index === 0;
                            ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="<?= e($headingId) ?>">
                                    <button class="accordion-button<?= $expanded ? '' : ' collapsed' ?>"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#<?= e($collapseId) ?>"
                                            aria-expanded="<?= $expanded ? 'true' : 'false' ?>"
                                            aria-controls="<?= e($collapseId) ?>">
                                        <?= e((string)$faq['question']) ?>
                                    </button>
                                </h3>
                                <div id="<?= e($collapseId) ?>"
                                     class="accordion-collapse collapse<?= $expanded ? ' show' : '' ?>"
                                     role="region" aria-labelledby="<?= e($headingId) ?>">
                                    <div class="accordion-body">
                                        <p class="mb-0"><?= nl2br(e((string)$faq['answer'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="col-lg-4">
                <?php if ($askTitle !== ''): ?><h2 class="h4 mb-3 dc-faq__title"><?= e($askTitle) ?></h2><?php endif; ?>
                <?php if ($askText !== ''): ?><p class="dc-faq__intro"><?= nl2br(e($askText)) ?></p><?php endif; ?>
                <p class="dc-faq__intro">
                    <?php if ($phone !== ''): ?>Call <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a><?php endif; ?>
                    <?php if ($phone !== '' && $email !== ''): ?> or <?php endif; ?>
                    <?php if ($email !== ''): ?>email <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
                    <?php if ($address !== ''): ?><br><?= e($address) ?><?php endif; ?>
                </p>

                <?php if ($flashSuccess !== ''): ?>
                    <div class="alert alert-success" role="alert"><?= e($flashSuccess) ?></div>
                <?php endif; ?>
                <?php if ($formError !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= e($formError) ?></div>
                <?php endif; ?>

                <form id="ask-form" class="dc-faq-form" method="post"
                      action="<?= e(url('faq/ask')) ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="visually-hidden">
                        <label for="faq-website">Website</label>
                        <input type="text" id="faq-website" name="website" value=""
                               autocomplete="off" tabindex="-1">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="faq-name">Your name</label>
                            <input type="text" id="faq-name" name="name"
                                   class="<?= e($fieldClass('name')) ?>" value="<?= $fieldValue('name') ?>"
                                   placeholder="Your name" required maxlength="100" autocomplete="name"
                                   aria-describedby="faq-name-error"<?= $fieldInvalid('name') ?>
                                   data-error-required="Please enter your name."
                                   data-error-maxlength="Name must be 100 characters or fewer.">
                            <?= $errorSpan('name') ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="faq-email">Your email</label>
                            <input type="email" id="faq-email" name="email"
                                   class="<?= e($fieldClass('email')) ?>" value="<?= $fieldValue('email') ?>"
                                   placeholder="Your email" required maxlength="150" autocomplete="email"
                                   aria-describedby="faq-email-error"<?= $fieldInvalid('email') ?>
                                   data-error-required="Please enter your email address."
                                   data-error-email="Please enter a valid email address."
                                   data-error-maxlength="Email must be 150 characters or fewer.">
                            <?= $errorSpan('email') ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="faq-phone">Phone number (optional)</label>
                            <input type="tel" id="faq-phone" name="phone"
                                   class="<?= e($fieldClass('phone')) ?>" value="<?= $fieldValue('phone') ?>"
                                   placeholder="Phone number (optional)" maxlength="20"
                                   pattern="[0-9+() .-]{0,20}" autocomplete="tel"
                                   aria-describedby="faq-phone-error"<?= $fieldInvalid('phone') ?>
                                   data-error-pattern="Please enter a valid phone number (20 characters or fewer).">
                            <?= $errorSpan('phone') ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="faq-subject">Subject</label>
                            <input type="text" id="faq-subject" name="subject"
                                   class="<?= e($fieldClass('subject')) ?>" value="<?= $fieldValue('subject') ?>"
                                   placeholder="Subject" required maxlength="200"
                                   aria-describedby="faq-subject-error"<?= $fieldInvalid('subject') ?>
                                   data-error-required="Please enter a subject."
                                   data-error-maxlength="Subject must be 200 characters or fewer.">
                            <?= $errorSpan('subject') ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="faq-message">Your message</label>
                            <textarea id="faq-message" name="message"
                                      class="<?= e($fieldClass('message')) ?>"
                                      rows="8" placeholder="Your message" required maxlength="5000"
                                      aria-describedby="faq-message-error"<?= $fieldInvalid('message') ?>
                                      data-error-required="Please enter your message."
                                      data-error-maxlength="Message must be 5000 characters or fewer."><?= $fieldValue('message') ?></textarea>
                            <?= $errorSpan('message') ?>
                        </div>

                        <div class="col-12 d-grid">
                            <button class="btn dc-btn-primary" type="submit">Send message</button>
                        </div>
                    </div>
                </form>
            </aside>
        </div>
    </div>
</section>
