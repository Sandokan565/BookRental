<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private const PER_PAGE = 20;

    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository) {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'is_borrowed' => 'nullable|in:0,1',
            'perPage' => 'nullable|integer|min:1|max:100',
        ]);

        $filters = array_intersect_key($validated, array_flip(['title', 'author_name', 'is_borrowed']));

        $books = $this->bookRepository->getFilteredBooks($filters, self::PER_PAGE);

        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'author_id' => 'required|integer|exists:authors,id',
            'title' => 'required|string|max:255',
            'is_borrowed' => 'required|boolean',
        ]);

        $book = Book::create($data);

        return response()->json($book, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'author_id' => 'sometimes|required|int|exists:authors,id',
            'title' => 'sometimes|required|string',
            'is_borrowed' => 'sometimes|required|boolean',
        ]);

        $book->update($data);

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json($book);
    }
}
