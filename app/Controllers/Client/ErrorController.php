<?php

declare(strict_types=1);

class ErrorController extends Controller
{
    public function show(string $message, int $status): void
    {
        http_response_code($status);
        $this->view('client/error', ['pageTitle' => 'Thông báo | Decor', 'message' => $message]);
    }
}
