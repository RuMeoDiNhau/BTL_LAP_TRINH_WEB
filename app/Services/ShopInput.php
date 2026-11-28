<?php

declare(strict_types=1);

final class ShopInput
{
    public static function text(array $source, string $key): string
    {
        $value = $source[$key] ?? '';
        return is_string($value) && mb_check_encoding($value, 'UTF-8') ? trim($value) : '';
    }

    public static function integer($value, int $min = 1, int $max = Product::MAX_UNSIGNED_INT): ?int
    {
        if (!is_string($value) && !is_int($value)) {
            return null;
        }
        $number = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max],
        ]);

        return $number === false ? null : $number;
    }

    public static function keyword(): string
    {
        return mb_substr(self::text($_GET, 'keyword'), 0, 100, 'UTF-8');
    }

    public static function page(): int
    {
        return self::integer($_GET['page'] ?? 1) ?? 1;
    }

    public static function csrfValid(): bool
    {
        $token = $_POST['_csrf'] ?? null;
        return is_string($token) && verify_csrf($token);
    }
}
