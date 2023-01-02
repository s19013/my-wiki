<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
use Carbon\Carbon;
use App\Tools\searchToolKit;

class BookMark extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
}
