<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StarredRepository extends Model
{
    use HasFactory;

    protected $table = 'starred_repositories';

    protected $fillable = [
        'username',
        'repository_id',
        'name',
        'description',
        'url',
        'language',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'repository_tag',
            'starred_repository_id',
            'tag_id'
        );
    }
}

