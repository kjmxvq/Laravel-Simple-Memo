<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Category;
use App\Models\MemoCategory;
use DB;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $categories = Category::where('user_id', '=', \Auth::id())->whereNull('deleted_at')->orderBy('id', 'ASC')
        ->paginate(5);

        $page_param = $request->page;
        if($page_param < 2) $page_param = 1;
        return view('category_index', compact('categories', 'page_param'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all();

        Category::where('id', $posts['category_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);
        return redirect(route('category_index'));
    }

    public function store(Request $request)
    {
        $posts = $request->all();

        $request -> validate(
            ['new_category' => 'required|max:50'],
            ['new_category.required' => '必須項目になります','new_category.max' => '50字以下で入力してください']
        );

        $category_exists = Category::where('user_id', '=', \Auth::id())
        ->where('name', '=', $posts['new_category'])
        ->whereNull('deleted_at')
        ->exists();

        if(!$category_exists){
            Category::insert(['user_id' => \Auth::id(), 'name' => $posts['new_category']]);
        }

        return redirect(route('category_index'));
    }
}
