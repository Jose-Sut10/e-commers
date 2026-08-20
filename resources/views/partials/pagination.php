<?php

if (
    !isset($paginator)
    || !$paginator->hasPages()
) {
    return;
}

$currentPage = $paginator->currentPage();
$lastPage = $paginator->lastPage();

$startPage =
    max(
        1,
        $currentPage - 2
    );

$endPage =
    min(
        $lastPage,
        $currentPage + 2
    );

$pageUrl = function (
    int $page
) use (
    $paginationPath,
    $paginationQuery
): string {

    $query = $paginationQuery;
    $query['page'] = $page;

    $query = array_filter(
        $query,
        fn ($value) =>
            $value !== ''
            && $value !== null
    );

    return url(
        $paginationPath
        . '?'
        . http_build_query($query)
    );
};
?>

<nav class="pagination">

    <?php if (
        $paginator->hasPreviousPage()
    ): ?>

        <a
            href="<?= htmlspecialchars(
                $pageUrl(
                    $currentPage - 1
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="pagination-link"
        >
            ← Anterior
        </a>

    <?php endif; ?>

    <?php if ($startPage > 1): ?>
        <a
            href="<?= htmlspecialchars(
                $pageUrl(1),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="pagination-link"
        >
            1
        </a>

        <?php if ($startPage > 2): ?>
            <span class="pagination-dots">
                ...
            </span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for (
        $page = $startPage;
        $page <= $endPage;
        $page++
    ): ?>
        <?php if (
            $page === $currentPage
        ): ?>
            <span
                class="pagination-link is-active"
            >
                <?= $page ?>
            </span>

        <?php else: ?>

            <a
                href="<?= htmlspecialchars(
                    $pageUrl($page),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="pagination-link"
            >
                <?= $page ?>
            </a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if (
        $endPage < $lastPage
    ): ?>

        <?php if (
            $endPage
            < $lastPage - 1
        ): ?>

            <span class="pagination-dots">
                ...
            </span>

        <?php endif; ?>

        <a
            href="<?= htmlspecialchars(
                $pageUrl(
                    $lastPage
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="pagination-link"
        >
            <?= $lastPage ?>
        </a>

    <?php endif; ?>

    <?php if (
        $paginator->hasNextPage()
    ): ?>
        <a
            href="<?= htmlspecialchars(
                $pageUrl(
                    $currentPage + 1
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="pagination-link"
        >
            Siguiente →
        </a>
    <?php endif; ?>

</nav>