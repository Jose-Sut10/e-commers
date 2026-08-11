<?php
namespace App\Services;
use RuntimeException;
class ImageUploadService{
    private const MAX_SIZE = 5242880; // 5 MB

    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function store(
        array $file,
        string $directory = 'products'
    ): array {
        $this->validate($file);

        $mimeType = $this->detectMimeType(
            $file['tmp_name']
        );

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new RuntimeException(
                'El formato de imagen no está permitido.'
            );
        }

        $extension =
            self::ALLOWED_TYPES[$mimeType];

        $filename =
            bin2hex(random_bytes(16))
            . '.'
            . $extension;

        $relativeDirectory =
            'uploads/' . trim($directory, '/');

        $absoluteDirectory =
            BASE_PATH
            . '/public/'
            . $relativeDirectory;

        if (!is_dir($absoluteDirectory)) {
            if (!mkdir(
                $absoluteDirectory,
                0775,
                true
            ) && !is_dir($absoluteDirectory)) {
                throw new RuntimeException(
                    'No fue posible crear el directorio de imágenes.'
                );
            }
        }

        $destination =
            $absoluteDirectory
            . '/'
            . $filename;

        if (!move_uploaded_file(
            $file['tmp_name'],
            $destination
        )) {
            throw new RuntimeException(
                'No fue posible guardar la imagen.'
            );
        }

        return [
            'path' =>
                $relativeDirectory
                . '/'
                . $filename,

            'original_name' =>
                basename(
                    (string) $file['name']
                ),

            'mime_type' =>
                $mimeType,

            'size' =>
                (int) $file['size'],
        ];
    }

    public function delete(
        string $relativePath
    ): void {
        $relativePath = ltrim(
            $relativePath,
            '/'
        );

        /*
         * Solo permitimos eliminar archivos
         * dentro de uploads/products.
         */
        if (!str_starts_with(
            $relativePath,
            'uploads/products/'
        )) {
            return;
        }

        $path =
            BASE_PATH
            . '/public/'
            . $relativePath;

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function validate(
        array $file
    ): void {
        if (
            !isset(
                $file['error'],
                $file['tmp_name'],
                $file['size']
            )
        ) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }

        if (
            $file['error']
            !== UPLOAD_ERR_OK
        ) {
            throw new RuntimeException(
                'Ocurrió un error durante la subida de la imagen.'
            );
        }

        if (
            (int) $file['size']
            > self::MAX_SIZE
        ) {
            throw new RuntimeException(
                'La imagen no puede superar los 5 MB.'
            );
        }

        if (
            !is_uploaded_file(
                $file['tmp_name']
            )
        ) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }
    }

    private function detectMimeType(
        string $path
    ): string {
        $finfo = new \finfo(
            FILEINFO_MIME_TYPE
        );

        $mime = $finfo->file($path);

        if (!is_string($mime)) {
            throw new RuntimeException(
                'No fue posible determinar el tipo de imagen.'
            );
        }
        return $mime;
    }
}