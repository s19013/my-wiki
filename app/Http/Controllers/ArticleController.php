<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Tag;

class ArticleController extends Controller
{
    //
    public function serveUserAllTag(Request $request)
    {
        return Tag::getUserAllTag($request->id);
    }
}
