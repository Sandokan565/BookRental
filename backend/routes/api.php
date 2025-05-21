<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::apiResource('author', AuthorController::class);
Route::apiResource('book', BookController::class);
