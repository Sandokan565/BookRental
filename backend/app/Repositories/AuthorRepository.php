<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Author;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorRepository
{
    /**
     * @param array $filters
     * @param int|null $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function getFilteredAuthors(array $filters, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $query = Author::withCount('books');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['surname'])) {
            $query->where('surname', 'like', '%' . $filters['surname'] . '%');
        }

        $query->orderBy('created_at', 'desc');

        if ($perPage !== null) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
