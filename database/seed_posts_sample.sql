-- SAMPLE DATA: 3 published posts for the About page blog strip.
-- Chạy sau database/schema.sql và database/seed.sql (cần user id=1).
-- INSERT IGNORE nên chạy lại không nhân đôi; id cố định 9001..9003.

SET NAMES utf8mb4;

INSERT IGNORE INTO `posts`
    (`id`, `author_id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `status`, `created_at`)
VALUES
    (9001, 1,
     'Pastel ideas for a calmer living room',
     'pastel-living-room-ideas',
     'Soft tones and simple textures can make a small room feel calm, open and easy to live in.',
     '<p>Start with one soft base colour, then add a few pieces with different textures. Keep the palette light and let the furniture breathe.</p>',
     'assets/images/post-thumbnail-1.jpg',
     'published', '2026-11-20 09:00:00'),

    (9002, 1,
     'Summer decor trends worth trying',
     'summer-decor-trends',
     'Light fabrics, natural materials and a few warm accents are the easiest way to refresh a room.',
     '<p>Swap heavy cushions for linen, add a woven basket and bring in a little greenery. Small changes are often enough.</p>',
     'assets/images/post-thumbnail-2.jpg',
     'published', '2026-11-18 09:00:00'),

    (9003, 1,
     'Styling tips for small spaces',
     'small-space-styling',
     'Choose fewer pieces, use vertical space and keep walkways clear to make a compact room feel larger.',
     '<p>Multi-purpose furniture and a clear surface help a small room stay tidy. Repeat one or two colours to make the look feel intentional.</p>',
     'assets/images/post-thumbnail-3.jpg',
     'published', '2026-11-15 09:00:00');
