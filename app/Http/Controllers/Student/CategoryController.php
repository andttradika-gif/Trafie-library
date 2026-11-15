<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();
        return view('student.categories.index', compact('categories'));
    }
}