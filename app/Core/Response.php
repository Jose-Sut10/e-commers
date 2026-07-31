<?php

namespace Core;

class Response
{
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function json(array $data): void
    {
        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }

    public static function status(int $code): void
    {
        http_response_code($code);
    }
}