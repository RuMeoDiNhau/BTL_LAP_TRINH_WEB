<?php

declare(strict_types=1);

final class ProductImageUpload
{
    public const MAX_BYTES = 2 * 1024 * 1024;
    private const EXTENSIONS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const RELATIVE_DIRECTORY = 'uploads/products/';

    public function store(array $file): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            throw new InvalidArgumentException('Ảnh vượt quá giới hạn 2 MB.');
        }

        $temporary = $file['tmp_name'] ?? null;
        if ($error !== UPLOAD_ERR_OK || !is_string($temporary) || !is_uploaded_file($temporary)) {
            throw new InvalidArgumentException('Không nhận được ảnh tải lên hợp lệ.');
        }
        $bytes = filesize($temporary);
        if ($bytes === false || $bytes < 1 || $bytes > self::MAX_BYTES) {
            throw new InvalidArgumentException('Chọn ảnh có dung lượng tối đa 2 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporary);
        $image = @getimagesize($temporary);
        if (!isset(self::EXTENSIONS[$mime]) || !$image || $image['mime'] !== $mime) {
            throw new InvalidArgumentException('Chỉ hỗ trợ ảnh JPG, PNG và WebP.');
        }
        if ($image[0] < 1 || $image[1] < 1 || $image[0] > 4096 || $image[1] > 4096) {
            throw new InvalidArgumentException('Kích thước ảnh tối đa là 4096 × 4096 pixel.');
        }

        $directory = dirname(__DIR__, 2) . '/public/' . self::RELATIVE_DIRECTORY;
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh sản phẩm.');
        }
        $filename = bin2hex(random_bytes(16)) . '.' . self::EXTENSIONS[$mime];
        if (!move_uploaded_file($temporary, $directory . $filename)) {
            throw new RuntimeException('Không thể lưu ảnh sản phẩm.');
        }

        return self::RELATIVE_DIRECTORY . $filename;
    }

    public function delete(?string $path): void
    {
        if ($path === null || !preg_match('~^uploads/products/[0-9a-f]{32}\.(?:jpg|png|webp)$~D', $path)) {
            return;
        }
        $file = dirname(__DIR__, 2) . '/public/' . $path;
        if (is_file($file) && !@unlink($file)) {
            error_log('Không thể xóa ảnh sản phẩm: ' . basename($file));
        }
    }
}
