<?php

namespace App\Controllers;

final class MediaController
{
    public function propertyImage(string $filename): void
    {
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $filename)) {
            http_response_code(404);
            return;
        }
        $path = dirname(__DIR__, 2) . '/storage/uploads/' . $filename;
        if (!is_file($path)) {
            http_response_code(404);
            return;
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($path);
    }
}
