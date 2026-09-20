<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\StoreSetting;
use App\Services\StoreLogoUploadService;

class StoreSettingController extends Controller{
    /*CONFIGURACIÓN*/

    public function index(): void{
        view(
            'settings/index',
            [
                'title' =>'Configuración de la tienda',
                'settings' =>StoreSetting::current(),
            ]
        );
    }


    /*GUARDAR*/

    public function update(): void{
        $request =new Request();
        $input = $request->all();
        $settings =StoreSetting::current();
        $newLogoPath = null;

        $oldLogoPath =
            $settings->logo_path
                ? (string)
                    $settings->logo_path
                : null;


        try {

            /*INFORMACIÓN GENERAL*/

            $businessName =
                trim(
                    (string) (
                        $input['business_name']
                        ?? ''
                    )
                );

            if ($businessName === '') {
                throw new RuntimeException(
                    'El nombre de la tienda es obligatorio.'
                );
            }

            if (
                mb_strlen(
                    $businessName
                ) > 150
            ) {
                throw new RuntimeException(
                    'El nombre de la tienda no puede superar los 150 caracteres.'
                );
            }

            $email =
                trim(
                    (string) (
                        $input['email']
                        ?? ''
                    )
                );

            if (
                $email !== ''
                && !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                throw new RuntimeException(
                    'El correo electrónico no es válido.'
                );
            }


            /*MONEDA*/

            $currencyCode =
                strtoupper(
                    trim(
                        (string) (
                            $input['currency_code']
                            ?? 'GTQ'
                        )
                    )
                );

            $currencySymbol =
                trim(
                    (string) (
                        $input['currency_symbol']
                        ?? 'Q'
                    )
                );

            if ($currencyCode === '') {
                $currencyCode =
                    'GTQ';
            }

            if ($currencySymbol === '') {
                $currencySymbol =
                    'Q';
            }


            /* URLS*/

            $facebook =
                $this->nullableUrl(
                    $input['facebook_url']
                    ?? null,
                    'Facebook'
                );

            $instagram =
                $this->nullableUrl(
                    $input['instagram_url']
                    ?? null,
                    'Instagram'
                );

            $tiktok =
                $this->nullableUrl(
                    $input['tiktok_url']
                    ?? null,
                    'TikTok'
                );

            /*LOGO*/

            $newLogoPath =
                (
                    new StoreLogoUploadService()
                )->upload(
                    $_FILES['logo']
                    ?? null
                );

            /*ASIGNAR*/

            $settings->business_name = $businessName;

            $settings->phone =
                $this->nullable(
                    $input['phone']
                    ?? null
                );

            $settings->whatsapp =
                $this->nullable(
                    $input['whatsapp']
                    ?? null
                );

            $settings->email =
                $email === ''
                    ? null
                    : mb_strtolower(
                        $email
                    );

            $settings->address =
                $this->nullable(
                    $input['address']
                    ?? null
                );

            $settings->business_hours =
                $this->nullable(
                    $input['business_hours']
                    ?? null
                );

            $settings->currency_code = $currencyCode;
            $settings->currency_symbol = $currencySymbol;
            $settings->facebook_url = $facebook;
            $settings->instagram_url = $instagram;
            $settings->tiktok_url = $tiktok;


            /*BANCO*/

            $settings->bank_name =
                $this->nullable(
                    $input['bank_name']
                    ?? null
                );

            $settings->bank_account_name =
                $this->nullable(
                    $input['bank_account_name']
                    ?? null
                );

            $settings->bank_account_number =
                $this->nullable(
                    $input['bank_account_number']
                    ?? null
                );

            $settings->bank_account_type =
                $this->nullable(
                    $input['bank_account_type']
                    ?? null
                );

            /*
             * Si subieron un logo nuevo,
             * sustituimos el anterior.
             */

            if ($newLogoPath) {
                $settings->logo_path =
                    $newLogoPath;
            }

            /*GUARDAR*/

            if (!$settings->save()) {
                throw new RuntimeException(
                    'No fue posible guardar la configuración.'
                );
            }

            /*
             * La base de datos ya guardó
             * correctamente el logo nuevo.
             *
             * Ahora sí eliminamos el anterior.
             */

            if (
                $newLogoPath
                && $oldLogoPath
                && $oldLogoPath
                    !== $newLogoPath
            ) {
                (
                    new StoreLogoUploadService()
                )->delete(
                    $oldLogoPath
                );
            }

            Session::flash(
                'success',
                'La configuración fue actualizada correctamente.'
            );

        } catch (RuntimeException $exception) {

            /*
             * Si subimos un logo nuevo pero
             * ocurrió un error antes de guardar,
             * eliminamos ese archivo.
             */

            if ($newLogoPath) {

                (
                    new StoreLogoUploadService()
                )->delete(
                    $newLogoPath
                );
            }

            Session::flash(
                'warning',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {

            if ($newLogoPath) {
                (
                    new StoreLogoUploadService()
                )->delete(
                    $newLogoPath
                );
            }

            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'No fue posible actualizar la configuración.'
            );
        }

        redirect(
            'configuracion'
        );
    }

    /*VALORES VACÍOS*/

    private function nullable(
        mixed $value
    ): ?string {

        $value =
            trim(
                (string) $value
            );

        return $value === ''
            ? null
            : $value;
    }

    /*URL OPCIONAL*/

    private function nullableUrl(
        mixed $value,
        string $label
    ): ?string {

        $value =
            trim(
                (string) $value
            );


        if ($value === '') {
            return null;
        }


        if (
            !filter_var(
                $value,
                FILTER_VALIDATE_URL
            )
        ) {
            throw new RuntimeException(
                "La dirección de {$label} no es válida."
            );
        }

        return $value;
    }
}