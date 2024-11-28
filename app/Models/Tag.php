<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(
            StarredRepository::class,
            'repository_tag',
            'tag_id',
            'starred_repository_id'
        );
    }
}
