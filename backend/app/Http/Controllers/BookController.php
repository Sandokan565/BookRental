<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    private BookService $bookService;

    public function __construct(BookService $bookService) {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BookRequest $request): JsonResponse
    {
        return response()->json($this->bookService->list($request->validated()), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request): JsonResponse
    {
        return response()->json($this->bookService->create($request->validated()), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book): JsonResponse
    {
        return response()->json($this->bookService->update($request->validated(), $book), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        return response()->json($this->bookService->delete($book), 200);
    }
}
