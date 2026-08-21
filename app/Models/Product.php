<?php
namespace App\Models;
use Core\Model;
use Core\Database;
use Core\Pagination\Paginator;

class Product extends Model{
    protected string $table = 'products';

    public static function findBySlug(
        string $slug
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `products`
             WHERE `slug` = ?
             LIMIT 1",
            [$slug]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public static function findBySku(
        string $sku
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "SELECT *
             FROM `products`
             WHERE `sku` = ?
             LIMIT 1",
            [trim($sku)]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase($row);
    }

    public static function allWithCategory(): array{
        $instance = new static();

        $rows = Database::select(
            "SELECT
                products.*,
                categories.name AS category_name,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            ORDER BY products.id DESC"
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    //catalogo publico
    public static function publicCatalog(
        ?string $categorySlug = null,
        ?string $search = null
    ): array {
        $instance = new static();

        $sql = "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.active = 1
            AND categories.active = 1
        ";

        $params = [];

        if (
            $categorySlug !== null
            && $categorySlug !== ''
        ) {
            $sql .= "
                AND categories.slug = ?
            ";

            $params[] = $categorySlug;
        }

        if (
            $search !== null
            && $search !== ''
        ) {
            $sql .= "
                AND (
                    products.name LIKE ?
                    OR products.sku LIKE ?
                    OR products.description LIKE ?
                )
            ";

            $term = '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= "
            ORDER BY products.name ASC
        ";

        $rows = Database::select(
            $sql,
            $params
        );

        return array_map(
            fn (array $row) =>
                $instance->newFromDatabase($row),
            $rows
        );
    }

    public static function findPublicBySlug(
        string $slug
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.slug = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            ",
            [$slug]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    //carrito de compras
    public static function findPublicById(
        int $id
    ): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT
                products.*,

                categories.name AS category_name,
                categories.slug AS category_slug,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM product_variants pv
                        WHERE pv.product_id = products.id
                    )
                    THEN COALESCE(
                        (
                            SELECT SUM(pv2.stock)
                            FROM product_variants pv2
                            WHERE pv2.product_id = products.id
                            AND pv2.active = 1
                        ),
                        0
                    )
                    ELSE products.stock
                END AS available_stock,

                (
                    SELECT product_images.path
                    FROM product_images
                    WHERE product_images.product_id = products.id
                    ORDER BY
                        product_images.is_primary DESC,
                        product_images.id ASC
                    LIMIT 1
                ) AS image_path

            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.id = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            ",
            [$id]
        );

        if (!$row) {
            return null;
        }

        return $instance->newFromDatabase(
            $row
        );
    }

    //checkout y pedidos
    public static function findPublicForUpdate(int $id): ?static {
        $instance = new static();

        $row = Database::first(
            "
            SELECT products.*
            FROM products

            INNER JOIN categories
                ON categories.id = products.category_id

            WHERE products.id = ?
            AND products.active = 1
            AND categories.active = 1

            LIMIT 1
            FOR UPDATE
            ",
            [$id]
        );

        if (!$row) {
            return null;
        }
        return $instance->newFromDatabase(
            $row
        );
    }

    //filtros de busqueda
    public static function paginateAdmin(
        array $filters = [],
        int $page = 1,
        int $perPage = 15
    ): Paginator {

        $page =
            max(1, $page);

        $perPage =
            max(
                5,
                min(
                    $perPage,
                    100
                )
            );

        /*
        * Creamos primero un catálogo interno
        * que calcula correctamente el stock,
        * incluyendo productos con variantes.
        */

        $sql = "
            FROM
            (
                SELECT

                    products.*,

                    categories.name
                        AS category_name,

                    CASE

                        WHEN EXISTS (
                            SELECT 1

                            FROM product_variants pv

                            WHERE pv.product_id =
                                products.id
                        )

                        THEN COALESCE(
                            (
                                SELECT
                                    SUM(pv2.stock)

                                FROM product_variants pv2

                                WHERE pv2.product_id =
                                    products.id

                                AND pv2.active = 1
                            ),
                            0
                        )

                        ELSE products.stock

                    END AS available_stock,


                    (
                        SELECT
                            product_images.path

                        FROM product_images

                        WHERE product_images.product_id =
                            products.id

                        ORDER BY
                            product_images.is_primary DESC,
                            product_images.id ASC

                        LIMIT 1

                    ) AS image_path

                FROM products

                INNER JOIN categories
                    ON categories.id =
                    products.category_id

            ) AS catalog

            WHERE 1 = 1
        ";

        $params = [];

        /* * BUSCADOR*/

        $search = trim(
            (string) (
                $filters['q']
                ?? ''
            )
        );

        if ($search !== '') {

            $sql .= "
                AND (
                    catalog.name LIKE ?
                    OR catalog.sku LIKE ?
                    OR catalog.description LIKE ?
                )
            ";

            $term =
                '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        /*
        * CATEGORÍA
        */

        $categoryId =
            (int) (
                $filters['category_id']
                ?? 0
            );


        if ($categoryId > 0) {

            $sql .= "
                AND catalog.category_id = ?
            ";

            $params[] =
                $categoryId;
        }

        /*
        * ESTADO
        */

        $active =
            $filters['active']
            ?? '';


        if (
            $active === '1'
            || $active === '0'
        ) {

            $sql .= "
                AND catalog.active = ?
            ";

            $params[] =
                (int) $active;
        }

        /*
        * STOCK
        */

        $stock =
            $filters['stock']
            ?? '';

        if ($stock === 'out') {

            $sql .= "
                AND catalog.available_stock = 0
            ";

        } elseif ($stock === 'low') {

            $sql .= "
                AND catalog.available_stock > 0
                AND catalog.available_stock <= 5
            ";

        } elseif ($stock === 'available') {

            $sql .= "
                AND catalog.available_stock > 5
            ";
        }

        /*
        * TOTAL
        */

        $countRow =
            Database::first(
                "
                SELECT COUNT(*) AS total
                {$sql}
                ",
                $params
            );

        $total =
            (int) (
                $countRow['total']
                ?? 0
            );

        $lastPage =
            max(
                1,
                (int) ceil(
                    $total
                    / $perPage
                )
            );

        $page =
            min(
                $page,
                $lastPage
            );

        $offset =
            ($page - 1)
            * $perPage;

        /*
        * RESULTADOS
        */

        $rows =
            Database::select(
                "
                SELECT *
                {$sql}

                ORDER BY catalog.id DESC

                LIMIT {$perPage}
                OFFSET {$offset}
                ",
                $params
            );


        $instance =
            new static();

        $items =
            array_map(
                fn (array $row) =>
                    $instance->newFromDatabase(
                        $row
                    ),
                $rows
            );

        return new Paginator(
            $items,
            $total,
            $perPage,
            $page
        );
    }

    //promos
    public function hasActiveSale(): bool{
        if (
            $this->sale_price === null
            || $this->sale_price === ''
        ) {
            return false;
        }

        $regularPrice = (float) $this->price;
        $salePrice = (float) $this->sale_price;

        if ($salePrice >= $regularPrice) {
            return false;
        }

        $now = time();

        if (
            $this->sale_starts_at
            && strtotime(
                (string) $this->sale_starts_at
            ) > $now
        ) {
            return false;
        }

        if (
            $this->sale_ends_at
            && strtotime(
                (string) $this->sale_ends_at
            ) < $now
        ) {
            return false;
        }
        return true;
    }

    public function finalPrice(): float{
        if ($this->hasActiveSale()) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }


    public function discountPercentage(): int{
        if (!$this->hasActiveSale()) {
            return 0;
        }

        $regular = (float) $this->price;
        $sale = (float) $this->sale_price;

        if ($regular <= 0) {
            return 0;
        }

        return (int) round(
            (
                ($regular - $sale)
                / $regular
            ) * 100
        );
    }

}