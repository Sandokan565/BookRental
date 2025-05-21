<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    private const PER_PAGE = 20;

    private AuthorRepository $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * Display a listing of the resource with optional filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'perPage' => 'nullable|integer'
        ]);

        $perPage = $validated['perPage'] ?? self::PER_PAGE;

        if ($perPage == -1) {
            $authors = ['data' => Author::orderBy('created_at', 'desc')->get()];

        } else {
            $filters = array_intersect_key($validated, array_flip(['name', 'surname']));
            $authors = $this->authorRepository->getFilteredAuthors($filters, self::PER_PAGE);
        }

        return response()->json($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
        ]);

        $author = Author::create($data);

        return response()->json($author, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
        ]);

        $author->update($data);

        return response()->json($author);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): JsonResponse
    {
        $author->delete();

        return response()->json($author);
    }
}
