<?php
namespace Core\Pagination;

class Paginator{
    public function __construct(
        protected array $items,
        protected int $total,
        protected int $perPage,
        protected int $currentPage
    ) {
    }

    public function items(): array{
        return $this->items;
    }

    public function total(): int{
        return $this->total;
    }

    public function perPage(): int{
        return $this->perPage;
    }

    public function currentPage(): int{
        return $this->currentPage;
    }

    public function lastPage(): int{
        if ($this->total <= 0) {
            return 1;
        }

        return (int) ceil(
            $this->total
            / $this->perPage
        );
    }

    public function from(): int{
        if ($this->total === 0) {
            return 0;
        }

        return (
            ($this->currentPage - 1)
            * $this->perPage
        ) + 1;
    }


    public function to(): int{
        if ($this->total === 0) {
            return 0;
        }

        return min(
            $this->currentPage
            * $this->perPage,
            $this->total
        );
    }


    public function hasPages(): bool{
        return $this->lastPage() > 1;
    }

    public function hasPreviousPage(): bool{
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool{
        return $this->currentPage
            < $this->lastPage();
    }
}