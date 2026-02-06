<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;



class PostController extends Controller
{
    public function __construct() {}

    public function index()
    {
        // Use paginate to handle large datasets on the UI
        $posts = Post::latest()->paginate(15);
        return view('posts.index', compact('posts'));
    }
}
