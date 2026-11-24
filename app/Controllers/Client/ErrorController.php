<?php

declare(strict_types=1);

class ErrorController extends Controller
{
    public function show(string $message, int $status): void
    {
        $this->render('client/error', ['title' => 'Thông báo', 'message' => $message, 'user' => null], $status);
    }
}
