<?php

declare(strict_types=1);

final class AvatarUpload
{
    public const MAX_BYTES = 2 * 1024 * 1024;
    private const MIME_EXTENSIONS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function store(array $file): string
    {
        if (!isset($file['error']) || !is_int($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException(in_array($file['error'] ?? null, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                ? 'Ảnh vượt quá giới hạn 2 MB.' : 'Vui lòng chọn một ảnh và thử tải lên lại.');
        }
        $tmp = $file['tmp_name'] ?? null;
        if (!is_string($tmp) || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('File tải lên không hợp lệ.');
        }
        $size = filesize($tmp);
        if (!$size || $size > self::MAX_BYTES) {
            throw new InvalidArgumentException('Ảnh phải có dung lượng lớn hơn 0 và không vượt quá 2 MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $image = @getimagesize($tmp);
        if (!isset(self::MIME_EXTENSIONS[$mime]) || !$image || $image['mime'] !== $mime) {
            throw new InvalidArgumentException('Chỉ chấp nhận ảnh JPG, PNG hoặc WebP hợp lệ.');
        }
        if ($image[0] < 1 || $image[1] < 1 || $image[0] > 4096 || $image[1] > 4096) {
            throw new InvalidArgumentException('Kích thước ảnh không được vượt quá 4096 × 4096 pixel.');
        }
        $directory = dirname(__DIR__, 2) . '/public/uploads/avatars';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu avatar.');
        }
        $name = bin2hex(random_bytes(16)) . '.' . self::MIME_EXTENSIONS[$mime];
        if (!move_uploaded_file($tmp, $directory . '/' . $name)) {
            throw new RuntimeException('Không thể lưu ảnh tải lên.');
        }
        return 'uploads/avatars/' . $name;
    }

    /** Chỉ xóa ảnh do chính bộ upload tạo trong thư mục avatars. */
    public function delete(?string $path): void
    {
        if (!$path || !preg_match('~^uploads/avatars/[a-f0-9]{32}\.(jpg|png|webp)$~D', $path)) {
            return;
        }
        $absolute = dirname(__DIR__, 2) . '/public/' . $path;
        if (is_file($absolute) && !unlink($absolute)) {
            error_log('Không thể xóa avatar cũ: ' . basename($absolute));
        }
    }
}
