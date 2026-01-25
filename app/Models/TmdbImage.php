<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class TmdbImage extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'tmdb_image',
    ];
}
