<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    private AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * Display a listing of the resource with optional filtering and pagination.
     */
    public function index(AuthorRequest $request): JsonResponse
    {
        return response()->json($this->authorService->list($request->validated()), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorRequest $request): JsonResponse
    {
        return response()->json($this->authorService->create($request->validated()), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorRequest $request, Author $author): JsonResponse
    {
        return response()->json($this->authorService->update($request->validated(), $author), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): JsonResponse
    {
        return response()->json($this->authorService->delete($author), 200);
    }
}
