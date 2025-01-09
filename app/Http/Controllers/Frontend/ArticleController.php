<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function LatestService()
    {

        $articles = Article::where('status', 1)->orderBy('created_at', 'DESC')->take(4)->get();
        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function AllService()
    {

        $articles = Article::where('status', 1)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }
}
