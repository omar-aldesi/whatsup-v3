<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imports';

    protected $fillable = [
        'user_id',
        'admin_id',
        'name',
        'path',
        'mime',
        'group_id',
        'type',
    ];
}
