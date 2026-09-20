<?php
namespace App\Services;
use RuntimeException;

class StoreLogoUploadService{
    private const MAX_FILE_SIZE = 3 * 1024 * 1024;

    private const ALLOWED_MIME_TYPES = [

        'image/jpeg' =>'jpg',
        'image/png' =>'png',
        'image/webp' =>'webp',
    ];


    /*SUBIR LOGO*/

    public function upload(
        ?array $file
    ): ?string {

        /*No subir un logo es válido.*/

        if (
            !$file
            || !isset($file['error'])
            || (int) $file['error']
                === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }


        if (
            (int) $file['error']
            !== UPLOAD_ERR_OK
        ) {
            throw new RuntimeException(
                'No fue posible recibir el logo.'
            );
        }


        $size = (int) ($file['size'] ?? 0);

        if ($size <= 0 || $size > self::MAX_FILE_SIZE) {
            throw new RuntimeException(
                'El logo debe pesar como máximo 3 MB.'
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
                'El archivo del logo no es válido.'
            );
        }


        /*MIME real.*/

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
                'El logo debe ser JPG, PNG o WEBP.'
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

        /*Directorio*/
        $relativeDirectory ='uploads/store';
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
                    'No fue posible crear la carpeta del logo.'
                );
            }
        }


        /*
         * Nombre único.
         */

        $filename =
            'logo_'
            . date('Ymd_His')
            . '_'
            . bin2hex(
                random_bytes(6)
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
                'No fue posible guardar el logo.'
            );
        }


        return $relativeDirectory
            . '/'
            . $filename;
    }


    /*LIMINAR*/

    public function delete(
        ?string $relativePath
    ): void {

        if (!$relativePath) {
            return;
        }

        /*
         * Solo permitimos eliminar archivos
         * de la carpeta de configuración.
         */

        if (
            !str_starts_with(
                $relativePath,
                'uploads/store/'
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