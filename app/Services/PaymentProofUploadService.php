<?php

namespace App\Services;

use RuntimeException;

class PaymentProofUploadService{
    private const MAX_FILE_SIZE =
        2 * 1024 * 1024;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /*SUBIR COMPROBANTE*/

    public function upload(
        ?array $file
    ): string {
        if (
            !$file
            || !isset($file['error'])
        ) {
            throw new RuntimeException(
                'Debes adjuntar el comprobante de transferencia.'
            );
        }

        if (
            (int) $file['error']
            === UPLOAD_ERR_NO_FILE
        ) {
            throw new RuntimeException(
                'Debes adjuntar el comprobante de transferencia.'
            );
        }

        if (
            (int) $file['error']
            !== UPLOAD_ERR_OK
        ) {
            throw new RuntimeException(
                'No fue posible recibir el comprobante.'
            );
        }

        /*Máximo 2 MB.*/

        $size =
            (int) (
                $file['size']
                ?? 0
            );

        if ($size <= 0 || $size > self::MAX_FILE_SIZE
        ) {
            throw new RuntimeException(
                'El comprobante debe pesar como máximo 2 MB.'
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
                'El archivo recibido no es válido.'
            );
        }

        /*MIME REAL*/

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
            || !array_key_exists(
                $mimeType,
                self::ALLOWED_MIME_TYPES
            )
        ) {
            throw new RuntimeException(
                'El comprobante debe ser una imagen JPG, PNG o WEBP.'
            );
        }

        /*
         * También comprobamos que sea
         * realmente una imagen.
         */

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

        /*DIRECTORIO*/

        $relativeDirectory = 'uploads/payment_proofs';
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
                    'No fue posible crear la carpeta de comprobantes.'
                );
            }
        }

        /*Nombre aleatorio.*/

        $filename =
            'payment_'
            . date('Ymd_His')
            . '_'
            . bin2hex(
                random_bytes(8)
            )
            . '.'
            . $extension;

        $destination = $directory
            . '/'
            . $filename;


        if (
            !move_uploaded_file(
                $temporaryPath,
                $destination
            )
        ) {
            throw new RuntimeException(
                'No fue posible guardar el comprobante.'
            );
        }

        return $relativeDirectory
            . '/'
            . $filename;
    }

    /*ELIMINAR ARCHIVO*/

    public function delete(
        ?string $relativePath
    ): void {

        if (!$relativePath) {
            return;
        }

        /*
         * Solo permitimos eliminar archivos
         * de esta carpeta.
         */

        if (
            !str_starts_with(
                $relativePath,
                'uploads/payment_proofs/'
            )
        ) {
            return;
        }

        $fullPath =
            BASE_PATH
            . '/public/'
            . $relativePath;

        if (
            is_file($fullPath)
        ) {
            @unlink($fullPath);
        }
    }
}