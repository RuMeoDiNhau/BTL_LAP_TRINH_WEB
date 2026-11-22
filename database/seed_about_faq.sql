-- ============================================================================
-- PROJECT: DECOR E-COMMERCE
-- FILE   : database/seed_about_faq.sql
-- DESC   : English content for the About and FAQs pages. Run after schema.sql.
--          ON DUPLICATE KEY UPDATE makes this file safe to re-run:
--          - page_contents: UNIQUE(page_key, section_key)
--          - faqs: PRIMARY KEY(id), fixed ids 1..9
-- ============================================================================

USE `decor_db`;
SET NAMES utf8mb4;


-- ----------------------------------------------------------------------------
-- 1. ABOUT PAGE CONTENT
-- ----------------------------------------------------------------------------
INSERT INTO `page_contents`
    (`page_key`, `section_key`, `title`, `content`, `image`, `sort_order`)
VALUES
    ('about', 'intro_1', NULL,
     'DECOR is a destination for anyone who loves beautiful, comfortable living spaces.',
     NULL, 1),
    ('about', 'intro_2', NULL,
     'We believe that every small detail helps make a home feel more relaxed and welcoming.',
     NULL, 2),
    ('about', 'intro_3', NULL,
     'Our team listens first and stays with you from the initial idea to the final detail.',
     NULL, 3),

    ('about', 'journey', 'The beginning of our journey',
     'DECOR started with a simple wish: to bring thoughtful decor ideas into every home. We keep learning and listening so that every product and every recommendation suits modern everyday life.',
     'assets/images/post-thumbnail-1.jpg', 1),
    ('about', 'who_we_are', 'Who are we?',
     'We are furniture lovers who care about making a room feel warmer and easier to live in. DECOR works with care, openness and respect for each customer’s choices.',
     'assets/images/post-thumbnail-2.jpg', 2),

    ('about', 'testimonial_1', 'DECOR customer',
     'The advice was gentle and easy to follow; I felt heard from start to finish.',
     NULL, 1),
    ('about', 'testimonial_2', 'DECOR partner',
     'Small but thoughtful suggestions made my living room feel much tidier.',
     NULL, 2),
    ('about', 'testimonial_3', 'Home decor enthusiast',
     'I like how DECOR pays attention to materials and the way things feel every day.',
     NULL, 3),
    ('about', 'testimonial_4', 'DECOR community member',
     'The support after my purchase made me feel confident about shopping with DECOR.',
     NULL, 4),

    ('about', 'feature_1', 'Book An Appointment',
     'Book a consultation so we can help you in more detail.',
     NULL, 1),
    ('about', 'feature_2', 'Pick up in store',
     'Choose your pieces and collect them directly from our store.',
     NULL, 2),
    ('about', 'feature_3', 'Special packaging',
     'Every gift is wrapped with care and attention.',
     NULL, 3),
    ('about', 'feature_4', 'Free global returns',
     'Returns are easy under our standard policy.',
     NULL, 4),

    ('faq', 'intro', 'Frequently asked questions',
     'Below are the questions we hear most often. If you cannot find the answer you need, please get in touch and we will be happy to help.',
     NULL, 1)
ON DUPLICATE KEY UPDATE
    `title`      = VALUES(`title`),
    `content`    = VALUES(`content`),
    `image`      = VALUES(`image`),
    `sort_order` = VALUES(`sort_order`);


-- ----------------------------------------------------------------------------
-- 2. FREQUENTLY ASKED QUESTIONS (fixed ids 1..9)
-- ----------------------------------------------------------------------------
INSERT INTO `faqs`
    (`id`, `question`, `answer`, `sort_order`, `is_active`)
VALUES
    (1, 'Do I need advice before choosing a product?',
     'Just describe your space and preferences. We will suggest the options that suit you best.',
     1, 1),
    (2, 'Can DECOR help with room design?',
     'Yes. We are happy to discuss ideas, color palettes and layouts to make your space feel more harmonious.',
     2, 1),
    (3, 'Where can I see product samples?',
     'You can browse the pieces featured on our website or contact us for more details.',
     3, 1),
    (4, 'How is delivery time communicated?',
     'We will agree on clear delivery information before you confirm your choice.',
     4, 1),
    (5, 'Can I exchange or return an item?',
     'We want you to be happy with your purchase. If something comes up, contact us and we will guide you through our standard policy.',
     5, 1),
    (6, 'How is my personal information handled?',
     'The details you share are used only to help and contact you. We do not pass them to third parties without your consent.',
     6, 1),
    (7, 'Which payment methods are available?',
     'We support common payment methods and will confirm the details when you complete your order.',
     7, 1),
    (8, 'What should I do if an item is out of stock?',
     'Get in touch and we will let you know when a similar option that fits your needs is available.',
     8, 1),
    (9, 'How can I send feedback or a complaint?',
     'We value every comment. Just leave your message and we will respond as soon as we can.',
     9, 1)
ON DUPLICATE KEY UPDATE
    `question`   = VALUES(`question`),
    `answer`     = VALUES(`answer`),
    `sort_order` = VALUES(`sort_order`),
    `is_active`  = VALUES(`is_active`);


-- ============================================================================
-- END OF FILE
-- ============================================================================
