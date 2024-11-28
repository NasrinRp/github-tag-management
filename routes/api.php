<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarredRepositoryController;
use App\Http\Controllers\TagController;

Route::prefix('{username}/starred-repositories')->group(function () {
    Route::post('/', [StarredRepositoryController::class, 'getStarredRepositories']);
    Route::post('{repositoryId}/tags', [TagController::class, 'addTag']);

    Route::get('{starredRepository}/tags', [TagController::class, 'getTags']);
    Route::get('search', [TagController::class, 'searchByTag']);
});

Route::get('tags', [TagController::class, 'index']); // List all tags

