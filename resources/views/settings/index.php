<?php
$success =session('success');
$warning =session('warning');

?>

<h1>Configuración de la tienda</h1>

<p>
    Administra la información general,
    redes sociales, moneda y datos
    bancarios de tu tienda.
</p>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<?php if ($warning): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars(
            (string) $warning,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </div>
<?php endif; ?>

<form
    method="POST"
    enctype="multipart/form-data"
    action="<?= htmlspecialchars(
        url('configuracion'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>


    <!--INFORMACIÓN GENERAL -->

    <section class="dashboard-section">
        <h2>Información general</h2>

        <div>

            <label for="business_name">Nombre comercial</label>

            <input
                id="business_name"
                type="text"
                name="business_name"
                maxlength="150"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->business_name
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"

                required
            >
        </div>

        <div>

            <label for="phone">Teléfono</label>

            <input
                id="phone"
                type="text"
                name="phone"
                maxlength="30"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->phone
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>

        <div>
            <label for="whatsapp">WhatsApp</label>

            <input
                id="whatsapp"
                type="text"
                name="whatsapp"
                maxlength="30"
                placeholder="50255551234"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->whatsapp
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
            <small>
                Recomendado: código de país + número,
                sin espacios. Ejemplo: 50255551234.
            </small>
        </div>

        <div>
            <label for="email">Correo electrónico</label>

            <input
                id="email"
                type="email"
                name="email"
                maxlength="150"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->email
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="address">Dirección</label>

            <textarea
                id="address"
                name="address"
                rows="4"
            ><?= htmlspecialchars(
                (string) (
                    $settings->address
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>
        </div>

        <div>
            <label for="business_hours">Horarios</label>

            <textarea
                id="business_hours"
                name="business_hours"
                rows="4"
                placeholder="Lunes a sábado de 9:00 a 18:00"
            ><?= htmlspecialchars(
                (string) (
                    $settings->business_hours
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>

        </div>

    </section>


    <!-- LOGO -->
    <section class="dashboard-section">
        <h2>Logo</h2>

        <?php if ($settings->logo_path): ?>
            <div style="margin-bottom:20px;">
                <p>Logo actual:</p>

                <img
                    src="<?= htmlspecialchars(
                        asset(
                            (string)
                            $settings->logo_path
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="Logo"

                    style="
                        display:block;
                        max-width:220px;
                        max-height:140px;
                        object-fit:contain;
                    "
                >
            </div>
        <?php endif; ?>

        <div>
            <label for="logo">Subir nuevo logo</label>

            <input
                id="logo"
                type="file"
                name="logo"

                accept="
                    .jpg,
                    .jpeg,
                    .png,
                    .webp,
                    image/jpeg,
                    image/png,
                    image/webp
                "
            >
            <small>
                JPG, PNG o WEBP.
                Máximo 3 MB.
            </small>

        </div>
    </section>


    <!-- MONEDA -->
    <section class="dashboard-section">
        <h2>Moneda</h2>

        <div>
            <label for="currency_code">Código de moneda</label>

            <input
                id="currency_code"
                type="text"
                name="currency_code"
                maxlength="10"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->currency_code
                        ?: 'GTQ'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="currency_symbol">Símbolo</label>

            <input
                id="currency_symbol"
                type="text"
                name="currency_symbol"
                maxlength="10"

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->currency_symbol
                        ?: 'Q'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>
    </section>

    <!-- REDES SOCIALES-->

    <section class="dashboard-section">
        <h2>Redes sociales</h2>

        <div>
            <label for="facebook_url">Facebook</label>

            <input
                id="facebook_url"
                type="url"
                name="facebook_url"
                placeholder="https://facebook.com/..."

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->facebook_url
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="instagram_url">Instagram</label>

            <input
                id="instagram_url"
                type="url"
                name="instagram_url"
                placeholder="https://instagram.com/..."

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->instagram_url
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="tiktok_url">TikTok</label>

            <input
                id="tiktok_url"
                type="url"
                name="tiktok_url"
                placeholder="https://tiktok.com/@..."

                value="<?= htmlspecialchars(
                    (string) (
                        $settings->tiktok_url
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>
    </section>


    <!-- DATOS BANCARIOS-->
    <section class="dashboard-section">
        <h2>Datos bancarios</h2>

        <p>
            Esta información podrá mostrarse
            posteriormente cuando el cliente
            seleccione transferencia bancaria.
        </p>

        <div>
            <label for="bank_name">Banco</label>

            <input
                id="bank_name"
                type="text"
                name="bank_name"
                maxlength="150"
                value="<?= htmlspecialchars(
                    (string) (
                        $settings->bank_name
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="bank_account_name">Nombre de la cuenta</label>

            <input
                id="bank_account_name"
                type="text"
                name="bank_account_name"
                maxlength="150"
                value="<?= htmlspecialchars(
                    (string) (
                        $settings->bank_account_name
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="bank_account_number">Número de cuenta</label>

            <input
                id="bank_account_number"
                type="text"
                name="bank_account_number"
                maxlength="100"
                value="<?= htmlspecialchars(
                    (string) (
                        $settings->bank_account_number
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="bank_account_type">Tipo de cuenta</label>

            <input
                id="bank_account_type"
                type="text"
                name="bank_account_type"
                maxlength="100"
                placeholder="Monetaria / Ahorros"
                value="<?= htmlspecialchars(
                    (string) (
                        $settings->bank_account_type
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>
    </section>


    <!-- GUARDAR-->
    <div style="margin-bottom:40px;">
        <button type="submit">Guardar configuración</button>
    </div>
</form>