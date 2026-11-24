<?php

declare(strict_types=1);

final class AccountValidation
{
    public static function profile(array $data): array
    {
        $errors = [];
        $length = mb_strlen($data['full_name'], 'UTF-8');
        if ($length < 2 || $length > 100 || preg_match('/[\x00-\x1F\x7F]/u', $data['full_name'])) {
            $errors['full_name'] = 'Họ và tên cần từ 2 đến 100 ký tự hợp lệ.';
        }
        if (strlen($data['email']) > 150 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Vui lòng nhập email hợp lệ, tối đa 150 ký tự.';
        }
        if ($data['phone'] !== '' && !preg_match('/^\+?[0-9]{9,15}$/D', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại cần từ 9 đến 15 chữ số, có thể bắt đầu bằng +.';
        }
        return $errors;
    }

    public static function password(string $password, string $confirmation): array
    {
        $errors = [];
        if (mb_strlen($password, 'UTF-8') < 8 || strlen($password) > 72 || str_contains($password, "\0")
            || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Mật khẩu cần từ 8 ký tự, tối đa 72 byte; gồm chữ hoa, chữ thường và số.';
        }
        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'Mật khẩu nhập lại chưa khớp.';
        }
        return $errors;
    }
}
