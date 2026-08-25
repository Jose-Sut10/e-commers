<?php
namespace App\Services;
use Throwable;
use RuntimeException;
use Core\Database;
use App\Models\Order;

class OrderShipmentService{
    public function dispatch(
        int $orderId,
        array $data,
        ?array $guideFile
    ): Order {

        $carrier =
            trim(
                (string) (
                    $data['shipping_carrier']
                    ?? ''
                )
            );

        $trackingNumber =
            trim(
                (string) (
                    $data['tracking_number']
                    ?? ''
                )
            );

        if ($carrier === '') {
            throw new RuntimeException(
                'Debes indicar la empresa transportista.'
            );
        }

        if (mb_strlen($carrier) > 100) {
            throw new RuntimeException(
                'El nombre del transportista no puede superar los 100 caracteres.'
            );
        }

        if ($trackingNumber === '') {
            throw new RuntimeException(
                'Debes indicar el número de guía.'
            );
        }

        if (mb_strlen($trackingNumber) > 150) {
            throw new RuntimeException(
                'El número de guía no puede superar los 150 caracteres.'
            );
        }

        Database::beginTransaction();

        $guidePath = null;

        try {
            /*Bloquear pedido.*/
            $order =
                Order::findForUpdate(
                    $orderId
                );

            if (!$order) {
                throw new RuntimeException(
                    'El pedido no fue encontrado.'
                );
            }

            /*
             * Solamente pedidos confirmados
             * pueden ser despachados.
             */

            if ($order->status !== 'confirmed') {
                throw new RuntimeException(
                    'Solamente puedes despachar pedidos confirmados.'
                );
            }

            /* Subir guía.*/

            $guidePath =
                (
                    new ShippingGuideUploadService()
                )->upload(
                    $guideFile
                );

            /*Guardar despacho.*/

            $order->shipping_carrier = $carrier;
            $order->tracking_number = $trackingNumber;
            $order->shipping_guide_path = $guidePath;

            $order->shipped_at =
                date(
                    'Y-m-d H:i:s'
                );

            $order->status = 'shipped';

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible registrar el despacho.'
                );
            }

            Database::commit();

            return $order;

        } catch (Throwable $exception) {

            if (Database::inTransaction()) {
                Database::rollBack();
            }

            /*
             * Si el archivo se subió pero
             * falló la base de datos,
             * eliminamos la imagen.
             */

            if ($guidePath) {

                (
                    new ShippingGuideUploadService()
                )->delete(
                    $guidePath
                );
            }
            throw $exception;
        }
    }
}