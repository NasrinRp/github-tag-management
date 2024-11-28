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

    /**
     * Adds one or more tags to a starred repository by a user who starred it before.
     *
     * @param Request $request
     * @param $username
     * @param $repositoryId (Consider that repositoryId comes from git-hub not your database)
     * @return JsonResponse
     */
    public function addTag(Request $request, $username, $repositoryId): JsonResponse
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255',
        ]);

        $repository = StarredRepository::query()
            ->where('repository_id', $repositoryId)
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
}

