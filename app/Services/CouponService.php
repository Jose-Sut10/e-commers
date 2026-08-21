<?php
namespace App\Services;
use DateTime;
use RuntimeException;
use App\Models\Coupon;

class CouponService{
   /*
     * =====================================================
     * GUARDAR / ACTUALIZAR CUPÓN
     * =====================================================
     */

    public function save(
        Coupon $coupon,
        array $data
    ): Coupon {
        $code =
            strtoupper(
                trim(
                    (string) (
                        $data['code']
                        ?? ''
                    )
                )
            );

        if ($code === '') {
            throw new RuntimeException(
                'El código del cupón es obligatorio.'
            );
        }

        if (
            strlen($code) > 50
        ) {
            throw new RuntimeException(
                'El código del cupón no puede tener más de 50 caracteres.'
            );
        }

        if (
            !preg_match(
                '/^[A-Z0-9_-]+$/',
                $code
            )
        ) {
            throw new RuntimeException(
                'El código solo puede contener letras, números, guion y guion bajo.'
            );
        }

        /*
         * Verificar código duplicado.
         */

        if ($coupon->id) {

            $existing =
                Coupon::findByCodeExceptId(
                    $code,
                    (int) $coupon->id
                );

        } else {

            $existing =
                Coupon::findByCode(
                    $code
                );
        }

        if ($existing) {
            throw new RuntimeException(
                'Ya existe un cupón con este código.'
            );
        }

        /* Tipo */

        $type =
            trim(
                (string) (
                    $data['type']
                    ?? ''
                )
            );

        if (
            !in_array(
                $type,
                [
                    'percentage',
                    'fixed',
                ],
                true
            )
        ) {
            throw new RuntimeException(
                'El tipo de descuento no es válido.'
            );
        }

        /*Valor*/

        $valueRaw =
            trim(
                (string) (
                    $data['value']
                    ?? ''
                )
            );

        if (
            $valueRaw === ''
            || !is_numeric($valueRaw)
        ) {
            throw new RuntimeException(
                'Debes indicar un valor de descuento válido.'
            );
        }

        $value = (float) $valueRaw;

        if ($value <= 0) {
            throw new RuntimeException(
                'El descuento debe ser mayor que cero.'
            );
        }

        if (
            $type === 'percentage'
            && $value > 100
        ) {
            throw new RuntimeException(
                'El descuento porcentual no puede ser mayor al 100%.'
            );
        }

        /*Compra mínima */

        $minOrderRaw =
            trim(
                (string) (
                    $data['min_order']
                    ?? ''
                )
            );

        $minOrder =
            $minOrderRaw === ''
                ? 0
                : (float) $minOrderRaw;

        if (
            $minOrder < 0
            || (
                $minOrderRaw !== ''
                && !is_numeric(
                    $minOrderRaw
                )
            )
        ) {
            throw new RuntimeException(
                'La compra mínima no es válida.'
            );
        }

        /*Límite de usos*/

        $usageLimitRaw =
            trim(
                (string) (
                    $data['usage_limit']
                    ?? ''
                )
            );

        $usageLimit = null;

        if ($usageLimitRaw !== '') {

            $valueLimit =
                filter_var(
                    $usageLimitRaw,
                    FILTER_VALIDATE_INT,
                    [
                        'options' => [
                            'min_range' => 1,
                        ],
                    ]
                );

            if (!$valueLimit) {
                throw new RuntimeException(
                    'El límite de usos debe ser un número entero mayor que cero.'
                );
            }

            $usageLimit = (int) $valueLimit;
        }

        /*Fechas*/

        $startsAt =
            $this->parseDate(
                $data['starts_at']
                ?? null
            );

        $endsAt =
            $this->parseDate(
                $data['ends_at']
                ?? null
            );

        if (
            $startsAt
            && $endsAt
            && strtotime($endsAt)
                <= strtotime($startsAt)
        ) {
            throw new RuntimeException(
                'La fecha de finalización debe ser posterior a la fecha de inicio.'
            );
        }

        $coupon->code = $code;
        $coupon->type = $type;
        $coupon->value = $value;
        $coupon->min_order = $minOrder;
        $coupon->usage_limit = $usageLimit;

        /*
         * No reiniciamos used_count
         * cuando se edita.
         */
        if (!$coupon->id) {
            $coupon->used_count = 0;
        }

        $coupon->starts_at = $startsAt;
        $coupon->ends_at = $endsAt;
        $coupon->active =
            isset(
                $data['active']
            )
                ? 1
                : 0;

        if (!$coupon->save()) {
            throw new RuntimeException(
                'No fue posible guardar el cupón.'
            );
        }
        return $coupon;
    }

    /*
     * =====================================================
     * PREVISUALIZAR CUPÓN
     * =====================================================
     */

    public function preview(
        string $code,
        float $subtotal
    ): array {
        $code =
            strtoupper(
                trim($code)
            );


        if ($code === '') {
            return $this->emptyResult(
                $subtotal
            );
        }

        $coupon =
            Coupon::findByCode(
                $code
            );

        if (!$coupon) {
            throw new RuntimeException(
                'El cupón indicado no existe.'
            );
        }

        return $this->calculate(
            $coupon,
            $subtotal
        );
    }

    /*
     * =====================================================
     * CONSUMIR CUPÓN AL CREAR PEDIDO
     * =====================================================
     *
     * Este método debe ejecutarse dentro
     * de la transacción del pedido.
     * =====================================================
     */

    public function consume(
        ?string $code,
        float $subtotal
    ): array {
        $code =
            strtoupper(
                trim(
                    (string) $code
                )
            );

        if ($code === '') {
            return $this->emptyResult(
                $subtotal
            );
        }

        $coupon =
            Coupon::findForUpdateByCode(
                $code
            );

        if (!$coupon) {
            throw new RuntimeException(
                'El cupón indicado ya no está disponible.'
            );
        }

        $result =
            $this->calculate(
                $coupon,
                $subtotal
            );

        $coupon->used_count =
            (int) $coupon->used_count
            + 1;

        if (!$coupon->save()) {
            throw new RuntimeException(
                'No fue posible registrar el uso del cupón.'
            );
        }

        return $result;
    }

    /*
     * =====================================================
     * CALCULAR
     * =====================================================
     */

    private function calculate(
        Coupon $coupon,
        float $subtotal
    ): array {
        $subtotal =
            round(
                max(0, $subtotal),
                2
            );


        if (!(bool) $coupon->active) {
            throw new RuntimeException(
                'Este cupón está desactivado.'
            );
        }

        $now =
            time();

        if (
            $coupon->starts_at
            && strtotime(
                (string)
                $coupon->starts_at
            ) > $now
        ) {
            throw new RuntimeException(
                'Este cupón todavía no está disponible.'
            );
        }

        if (
            $coupon->ends_at
            && strtotime(
                (string)
                $coupon->ends_at
            ) < $now
        ) {
            throw new RuntimeException(
                'Este cupón ya venció.'
            );
        }

        if (
            $coupon->usage_limit !== null
            && $coupon->usage_limit !== ''
            && (int) $coupon->used_count
                >= (int) $coupon->usage_limit
        ) {
            throw new RuntimeException(
                'Este cupón alcanzó su límite de usos.'
            );
        }

        if (
            $subtotal
            < (float) $coupon->min_order
        ) {
            throw new RuntimeException(
                'Este cupón requiere una compra mínima de Q '
                . number_format(
                    (float)
                    $coupon->min_order,
                    2
                )
                . '.'
            );
        }

        if (
            $coupon->type
            === 'percentage'
        ) {

            $discount =
                $subtotal
                * (
                    (float) $coupon->value
                    / 100
                );

        } elseif (
            $coupon->type
            === 'fixed'
        ) {

            $discount =
                (float) $coupon->value;

        } else {

            throw new RuntimeException(
                'El tipo de cupón no es válido.'
            );
        }

        $discount =
            round(
                min(
                    $discount,
                    $subtotal
                ),
                2
            );

        $total =
            round(
                $subtotal
                - $discount,
                2
            );

        return [
            'coupon' => $coupon,
            'code' => (string) $coupon->code,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }

    private function emptyResult(
        float $subtotal
    ): array {
        $subtotal =
            round(
                max(0, $subtotal),
                2
            );

        return [
            'coupon' => null,
            'code' => null,
            'discount' => 0.0,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }

    private function parseDate(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) $value
            );

        if ($value === '') {
            return null;
        }

        $date =
            DateTime::createFromFormat(
                'Y-m-d\TH:i',
                $value
            );

        if (!$date) {
            throw new RuntimeException(
                'Una de las fechas indicadas no es válida.'
            );
        }

        return $date->format(
            'Y-m-d H:i:s'
        );
    }
}