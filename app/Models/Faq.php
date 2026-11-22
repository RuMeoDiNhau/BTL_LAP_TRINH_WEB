<?php

namespace App\Models;

final class Faq
{
    /** @return array<int, array<string, mixed>> */
    public static function getActive(): array
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT id, question, answer, sort_order
             FROM faqs
             WHERE is_active = 1
             ORDER BY sort_order, id'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
