<?php

namespace App\Console\Commands\Benchmarks\EagerLoading\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
