<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Topic;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function show(Request $request, Category $category)
    {
        // 读取分类 ID 关联的话题，并按每 20 条分页
        $topics = Topic::query()
            ->withOrder($request->order)
            ->where('category_id', $category->id)
            ->with('user', 'category')
            ->paginate(20);

        $activeUsers = (new User())->getActiveUsers();

        // 传参变量话题和分类到模版中
        return view('topics.index', [
            'topics' => $topics,
            'category' => $category,
            'activeUsers' => $activeUsers,
        ]);
    }
}
