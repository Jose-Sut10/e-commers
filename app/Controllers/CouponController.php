<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Coupon;
use App\Services\CouponService;

class CouponController extends Controller{
    public function index(): void {
        view(
            'coupons/index',
            [
                'title' => 'Cupones',
                'coupons' => Coupon::allLatest(),
            ]
        );
    }

    public function create(): void {
        view(
            'coupons/create',
            [
                'title' => 'Nuevo cupón',
            ]
        );
    }

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        try {

            (
                new CouponService()
            )->save(
                new Coupon(),
                $input
            );

            Session::flash(
                'success',
                'El cupón fue creado correctamente.'
            );

            redirect('cupones');

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

            Session::flash(
                'old',
                $input
            );

            redirect(
                'cupones/crear'
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible crear el cupón.'
            );

            redirect(
                'cupones/crear'
            );
        }
    }

    public function edit(): void{
        $id =
            $this->validId(
                $_GET['id']
                ?? null
            );

        if (!$id) {
            Session::flash(
                'warning',
                'El cupón indicado no es válido.'
            );

            redirect('cupones');
        }

        $coupon = Coupon::find($id);

        if (!$coupon) {
            Session::flash(
                'warning',
                'El cupón no fue encontrado.'
            );
            redirect('cupones');
        }

        view(
            'coupons/edit',
            [
                'title' => 'Editar cupón',
                'coupon' => $coupon,
            ]
        );
    }

    public function update(): void{
        $request = new Request();
        $input = $request->all();

        $id =
            $this->validId(
                $input['id']
                ?? null
            );

        if (!$id) {
            Session::flash(
                'warning',
                'El cupón indicado no es válido.'
            );

            redirect('cupones');
        }

        $coupon = Coupon::find($id);


        if (!$coupon) {
            Session::flash(
                'warning',
                'El cupón no fue encontrado.'
            );

            redirect('cupones');
        }

        try {
            (
                new CouponService()
            )->save(
                $coupon,
                $input
            );

            Session::flash(
                'success',
                'El cupón fue actualizado correctamente.'
            );

            redirect('cupones');

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

            Session::flash(
                'old',
                $input
            );

            redirect(
                'cupones/editar?id='
                . $id
            );

        } catch (Throwable $exception) {

            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'No fue posible actualizar el cupón.'
            );

            redirect(
                'cupones/editar?id='
                . $id
            );
        }
    }

    private function validId(
        mixed $value
    ): ?int {
        $id =
            filter_var(
                $value,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

        return $id
            ? (int) $id
            : null;
    }
}