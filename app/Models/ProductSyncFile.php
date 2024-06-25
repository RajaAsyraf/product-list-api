<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSyncFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
    ];
}
