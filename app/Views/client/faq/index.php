<?php
/**
 * FAQs page: keeps the template DOM/classes. The contact form is restored with
 * unique labels/ids/names, CSRF, a honeypot and per-field error slots.
 * The template's <button><h3> was changed to <h3><button> for W3C compliance.
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

$text = static function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['content'] ?? '';
    return $value === '' ? $default : (string)$value;
};

$heading = static function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['title'] ?? '';
    return $value === '' ? $default : (string)$value;
};

$email = setting('email', 'contact@decor.vn');
$phone = setting('phone', '0901234567');

$fieldValue = static function (string $key) use ($old): string {
    return e((string)($old[$key] ?? ''));
};

$fieldClass = static function (string $key) use ($errors): string {
    return 'form-control ps-3' . (isset($errors[$key]) ? ' is-invalid' : '');
};

$fieldInvalid = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? ' aria-invalid="true"' : '';
};

$errorSpan = static function (string $key) use ($errors): string {
    $message = isset($errors[$key]) ? e((string)$errors[$key]) : '';

    return '<small class="field-error d-block" id="faq-' . e($key) . '-error" role="alert">'
        . $message . '</small>';
};
?>
<section class="page-title page-title--faq padding-large mt-5">
    <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="d-flex flex-wrap flex-column justify-content-between align-items-center">
                <h1 class="display-5 fw-bold">FAQs</h1>
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <a class="breadcrumb-item" href="<?= e(url('')) ?>">Home</a>
                    <span class="breadcrumb-item active" aria-current="page">FAQs</span>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="faqs-wrap">
    <div class="container" data-aos="fade-up">
        <div class="row g-lg-5">
            <!-- The layout already has a page-level main, so use div here. -->
            <div class="col-lg-8">
                <h2 class="fw-bold h3"><?= e($heading('intro', 'Frequently asked questions')) ?></h2>
                <p><?= nl2br(e($text(
                    'intro',
                    'Below are the questions we hear most often. If you cannot find the answer you need, please get in touch and we will be happy to help.'
                ))) ?></p>

                <?php if ($faqs === []): ?>
                    <div class="accordion" id="accordion-box">
                        <div class="accordion-item mt-3">
                            <div class="accordion-body">Our FAQs are being updated.</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordion-box">
                        <?php foreach ($faqs as $index => $faq): ?>
                            <?php
                            $faqId = (int)$faq['id'];
                            $headingId = 'faq-heading-' . $faqId;
                            $collapseId = 'faq-collapse-' . $faqId;
                            $expanded = $index === 0;
                            ?>
                            <div class="accordion-item mt-3">
                                <h3 class="accordion-header accordion-title fs-5" id="<?= e($headingId) ?>">
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
                                        <div class="faqs-box">
                                            <div class="element-box margin-xsmall d-flex align-items-center">
                                                <div class="item-title"><?= nl2br(e((string)$faq['answer'])) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="inquiry-item col-lg-4 mt-5 mt-lg-0">
                <h3 class="section-title mb-3">Ask us anything</h3>
                <p>
                    Call <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a>
                    or email <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>.
                    You can also send us a message below — we are always happy to help.
                </p>

                <?php if ($flashSuccess !== ''): ?>
                    <div class="alert alert-success" role="alert"><?= e($flashSuccess) ?></div>
                <?php endif; ?>
                <?php if ($formError !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= e($formError) ?></div>
                <?php endif; ?>

                <form id="ask-form" class="form-group flex-wrap faq-form" method="post"
                      action="<?= e(url('faq/ask')) ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="visually-hidden">
                        <label for="faq-website">Website</label>
                        <input type="text" id="faq-website" name="website" value=""
                               autocomplete="off" tabindex="-1">
                    </div>

                    <div class="form-input col-lg-12 d-flex mb-3">
                        <div class="flex-grow-1 me-3">
                            <label class="visually-hidden" for="faq-name">Your name</label>
                            <input type="text" id="faq-name" name="name"
                                   class="<?= e($fieldClass('name')) ?>" value="<?= $fieldValue('name') ?>"
                                   placeholder="Your name" required maxlength="100" autocomplete="name"
                                   aria-describedby="faq-name-error"<?= $fieldInvalid('name') ?>
                                   data-error-required="Please enter your name."
                                   data-error-maxlength="Name must be 100 characters or fewer.">
                            <?= $errorSpan('name') ?>
                        </div>
                        <div class="flex-grow-1">
                            <label class="visually-hidden" for="faq-email">Your email</label>
                            <input type="email" id="faq-email" name="email"
                                   class="<?= e($fieldClass('email')) ?>" value="<?= $fieldValue('email') ?>"
                                   placeholder="Your email" required maxlength="150" autocomplete="email"
                                   aria-describedby="faq-email-error"<?= $fieldInvalid('email') ?>
                                   data-error-required="Please enter your email address."
                                   data-error-email="Please enter a valid email address."
                                   data-error-maxlength="Email must be 150 characters or fewer.">
                            <?= $errorSpan('email') ?>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-3">
                        <label class="visually-hidden" for="faq-phone">Phone number (optional)</label>
                        <input type="tel" id="faq-phone" name="phone"
                               class="<?= e($fieldClass('phone')) ?>" value="<?= $fieldValue('phone') ?>"
                               placeholder="Phone number (optional)" maxlength="20"
                               pattern="[0-9+() .-]{0,20}" autocomplete="tel"
                               aria-describedby="faq-phone-error"<?= $fieldInvalid('phone') ?>
                               data-error-pattern="Please enter a valid phone number (20 characters or fewer).">
                        <?= $errorSpan('phone') ?>
                    </div>

                    <div class="col-lg-12 mb-3">
                        <label class="visually-hidden" for="faq-subject">Subject</label>
                        <input type="text" id="faq-subject" name="subject"
                               class="<?= e($fieldClass('subject')) ?>" value="<?= $fieldValue('subject') ?>"
                               placeholder="Subject" required maxlength="200"
                               aria-describedby="faq-subject-error"<?= $fieldInvalid('subject') ?>
                               data-error-required="Please enter a subject."
                               data-error-maxlength="Subject must be 200 characters or fewer.">
                        <?= $errorSpan('subject') ?>
                    </div>

                    <div class="col-lg-12 mb-3">
                        <label class="visually-hidden" for="faq-message">Your message</label>
                        <textarea id="faq-message" name="message" class="<?= e($fieldClass('message')) ?>"
                                  rows="8" placeholder="Your message" required maxlength="5000"
                                  aria-describedby="faq-message-error"<?= $fieldInvalid('message') ?>
                                  data-error-required="Please enter your message."
                                  data-error-maxlength="Message must be 5000 characters or fewer."><?= $fieldValue('message') ?></textarea>
                        <?= $errorSpan('message') ?>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg text-uppercase btn-rounded-none" type="submit">
                            Send message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
