<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookRepository
{
    /**
     * @param array $filters
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public function getFilteredBooks(array $filters, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $query = Book::query()->join('authors', 'authors.id', '=', 'books.author_id');

        if (!empty($filters['title'])) {
            $query->where('books.title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['author_name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('authors.name', 'like', '%' . $filters['author_name'] . '%')
                  ->orWhere('authors.surname', 'like', '%' . $filters['author_name'] . '%');
            });
        }

        if (array_key_exists('is_borrowed', $filters)) {
            $borrowed = $filters['is_borrowed'];

            if ($borrowed === '0' || $borrowed === '1' || is_bool($borrowed)) {
                $query->where('books.is_borrowed', (bool)$borrowed);
            }
        }

        $query->select('books.*', DB::raw("CONCAT(authors.name, ' ', authors.surname) as author_name"));

        $query->orderBy('books.created_at', 'desc');

        return $perPage !== null ? $query->paginate($perPage) : $query->get();
    }
}
