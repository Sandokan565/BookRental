<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookService
{
    private const PER_PAGE = 20;

    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function list(array $data): LengthAwarePaginator|Collection
    {
        $filters = array_intersect_key($data, array_flip(['title', 'author_name', 'is_borrowed']));

        return $this->bookRepository->getFilteredBooks($filters, self::PER_PAGE);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(array $data, Book $book): Book
    {
        $book->update($data);

        return $book;
    }

    public function delete(Book $book): Book
    {
        $book->delete();

        return $book;
    }
}
