<?php
namespace App\Services;
use RuntimeException;

class ShippingGuideUploadService{
    private const MAX_FILE_SIZE = 4 * 1024 * 1024;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function upload(
        ?array $file
    ): string {

        if (!$file || !isset($file['error'])
        ) {
            throw new RuntimeException(
                'Debes adjuntar una imagen de la guía de envío.'
            );
        }

        if (
            (int) $file['error']
            === UPLOAD_ERR_NO_FILE
        ) {
            throw new RuntimeException(
                'Debes adjuntar una imagen de la guía de envío.'
            );
        }

        if (
            (int) $file['error']
            !== UPLOAD_ERR_OK
        ) {
            throw new RuntimeException(
                'No fue posible recibir la imagen de la guía.'
            );
        }

        $size =
            (int) (
                $file['size']
                ?? 0
            );

        if ($size <= 0 || $size > self::MAX_FILE_SIZE
        ) {
            throw new RuntimeException(
                'La imagen de la guía debe pesar como máximo 4 MB.'
            );
        }

        $temporaryPath =
            (string) (
                $file['tmp_name']
                ?? ''
            );

        if (
            $temporaryPath === ''
            || !is_uploaded_file(
                $temporaryPath
            )
        ) {
            throw new RuntimeException(
                'El archivo de la guía no es válido.'
            );
        }

        /*Comprobar MIME real.*/

        $finfo =
            new \finfo(
                FILEINFO_MIME_TYPE
            );

        $mimeType =
            $finfo->file(
                $temporaryPath
            );


        if (
            !$mimeType
            || !isset(
                self::ALLOWED_MIME_TYPES[
                    $mimeType
                ]
            )
        ) {
            throw new RuntimeException(
                'La guía debe ser una imagen JPG, PNG o WEBP.'
            );
        }

        if (
            @getimagesize(
                $temporaryPath
            ) === false
        ) {
            throw new RuntimeException(
                'El archivo seleccionado no es una imagen válida.'
            );
        }

        $extension =
            self::ALLOWED_MIME_TYPES[
                $mimeType
            ];

        /*
         * Carpeta.
         */

        $relativeDirectory = 'uploads/shipping_guides';
        $directory =
            BASE_PATH
            . '/public/'
            . $relativeDirectory;

        if (!is_dir($directory)) {

            if (
                !mkdir(
                    $directory,
                    0775,
                    true
                )
                && !is_dir($directory)
            ) {
                throw new RuntimeException(
                    'No fue posible crear la carpeta de guías.'
                );
            }
        }

        /*
         * Nombre único.
         */

        $filename =
            'guide_'
            . date('Ymd_His')
            . '_'
            . bin2hex(
                random_bytes(8)
            )
            . '.'
            . $extension;

        $destination =
            $directory
            . '/'
            . $filename;

        if (
            !move_uploaded_file(
                $temporaryPath,
                $destination
            )
        ) {
            throw new RuntimeException(
                'No fue posible guardar la imagen de la guía.'
            );
        }

        return $relativeDirectory
            . '/'
            . $filename;
    }

    public function delete(
        ?string $relativePath
    ): void {

        if (!$relativePath) {
            return;
        }

        if (
            !str_starts_with(
                $relativePath,
                'uploads/shipping_guides/'
            )
        ) {
            return;
        }

        $fullPath =
            BASE_PATH
            . '/public/'
            . $relativePath;

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}