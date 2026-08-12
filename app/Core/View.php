<?php
namespace Core;
use RuntimeException;

class View{
    public static function render(
        string $view,
        array $data = [],
        ?string $layout = 'admin'
    ): void {
        $viewPath = BASE_PATH
            . '/resources/views/'
            . str_replace('.', '/', $view)
            . '.php';

        if (!file_exists($viewPath)) {
            throw new RuntimeException(
                "La vista '{$view}' no existe."
            );
        }

        /*
         * Convertimos las claves del array en variables.
         *
         * Ejemplo:
         * ['title' => 'Inicio']
         *
         * se convierte en:
         * $title = 'Inicio';
         */
        extract($data, EXTR_SKIP);

        /*
         * Capturamos el HTML generado por la vista.
         */
        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        /*
         * Si no queremos layout, imprimimos
         * directamente la vista.
         */
        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = BASE_PATH
            . '/resources/views/layouts/'
            . str_replace('.', '/', $layout)
            . '.php';

        if (!file_exists($layoutPath)) {
            throw new RuntimeException(
                "El layout '{$layout}' no existe."
            );
        }
        require $layoutPath;
    }
}