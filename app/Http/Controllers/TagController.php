<?php

namespace App\Http\Controllers;

use App\Models\StarredRepository;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(tagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function addTag(Request $request, $username, $repositoryId): JsonResponse
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255',
        ]);

        $repository = StarredRepository::where('id', $repositoryId)
            ->where('username', $username)
            ->first();

        if (!$repository) {
            return response()->json([
                'error' => 'Repository not found or the repository has not been starred by the user.',
                'details' => [
                    'username' => $username,
                    'repository_id' => $repositoryId,
                ]
            ], 404);
        }

        $this->tagService->addTagsToRepository($request->input('tags'), $repository);

        return response()->json(['message' => 'Tags added successfully']);
    }

    public function searchByTag(Request $request, $username): JsonResponse
    {
        $request->validate([
            'tag' => 'required|string|max:255',
        ]);

        $tagQuery = $request->query('tag');

        $repositories = $this->tagService->searchRepositoriesByTag($username, $tagQuery);

        return response()->json($repositories);
    }
}

