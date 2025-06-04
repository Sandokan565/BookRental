<?php

namespace App\Services;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AuthorService
{
    private const PER_PAGE = 20;

    private AuthorRepository $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function list(array $data): LengthAwarePaginator|Collection
    {
        $perPage = $validated['perPage'] ?? self::PER_PAGE;

        if ($perPage == -1) {
            $authors = ['data' => Author::orderBy('created_at', 'desc')->get()];

        } else {
            $filters = array_intersect_key($data, array_flip(['name', 'surname']));
            $authors = $this->authorRepository->getFilteredAuthors($filters, self::PER_PAGE);
        }

        return $authors;
    }

    public function create(array $data): Author
    {
        return Author::create($data);
    }

    public function update(array $data, Author $author): Author
    {
        $author->update($data);

        return $author;
    }

    public function delete(Author $author): Author
    {
        $author->delete();

        return $author;
    }
}
