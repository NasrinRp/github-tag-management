<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarredRepositoryController;
use App\Http\Controllers\TagController;

Route::prefix('{username}/starred-repositories')->group(function () {
    Route::post('/', [StarredRepositoryController::class, 'getStarredRepositories']); // Fetch starred repositories
    Route::get('/', [StarredRepositoryController::class, 'searchRepositoriesByTag']); // search in starred repositories by tag

    Route::post('{repositoryId}/tags', [TagController::class, 'addTag']); // Add tag to a stared repository
});

