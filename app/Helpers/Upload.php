<?php

namespace App\Helpers;

use RuntimeException;

final class Upload
{
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public static function propertyImage(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Le téléversement de l’image a échoué.');
        }
        if (($file['size'] ?? 0) > config('upload_max_bytes')) {
            throw new RuntimeException('L’image dépasse la limite de 5 Mo.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extension = self::MIME_EXTENSIONS[$mime] ?? null;
        if ($extension === null) {
            throw new RuntimeException('Formats autorisés : JPG, PNG ou WEBP.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $directory = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Impossible de préparer le dossier image.');
        }
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            throw new RuntimeException('Impossible d’enregistrer l’image.');
        }

        return 'media/properties/' . $filename;
    }
}
