<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Category;
use App\Models\MemoCategory;
use DB;

class HomeController extends Controller
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
        $category_param = \Request::query('category');

        if($category_param){
            $memos = Memo::select('memos.*')
            ->leftJoin('memo_categories', 'memo_categories.memo_id', '=', 'memos.id')
            ->where('memo_categories.category_id', '=', $category_param)
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'ASC')
            ->paginate(5);               
        } else {
            $memos = Memo::select('memos.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'ASC')
            ->paginate(5);   
        }

        $categories = Category::where('user_id', '=', \Auth::id())
        ->whereNull('deleted_at')
        ->orderBy('id', 'ASC')
        ->get();
        
        $page_param = $request->page;
        if($page_param < 2) $page_param = 1;
        return view('home', compact('category_param', 'memos', 'categories', 'page_param'));
    }

    public function store(Request $request)
    {
        $posts = $request->all();

        $request -> validate(
            ['content' => 'required|max:100', 'new_category' => 'max:50'],
            ['content.required' => '必須項目になります','content.max' => '100字以下で入力してください','new_category.max' => '50字以下で入力してください']
        );

        DB::transaction(function() use($posts){
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            $category_exists = Category::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_category'])
            ->exists();

            if($posts['new_category'] && !$category_exists){
                $category_id = Category::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_category']]);
                MemoCategory::insert(['memo_id' => $memo_id, 'category_id' => $category_id]);
            }

            if(!empty($posts['categories'][0])){
                foreach($posts['categories'] as $cate){
                    MemoCategory::insert(['memo_id' => $memo_id, 'category_id' => $cate]);
                }
            }
        });

        return redirect(route('home'));
    }

    public function edit($id, $num='')
    {        
        $edit_memo = Memo::select('memos.*', 'categories.id AS category_id')
            ->leftJoin('memo_categories', 'memo_categories.memo_id', '=', 'memos.id')
            ->leftJoin('categories', 'memo_categories.category_id', '=', 'categories.id')
            ->where('memos.user_id', '=', \Auth::id())
            ->where('memos.id', '=', $id)
            ->whereNull('memos.deleted_at')
            ->get();

        $include_categories = [];
        foreach($edit_memo as $em){
            array_push($include_categories, $em['category_id']);
        }

        $categories = Category::where('user_id', '=', \Auth::id())->whereNull('deleted_at')->orderBy('id', 'ASC')
        ->get();

        $num? $num_label = 'No.'.$num : $num_label='';
        return view('edit', compact('edit_memo', 'num_label', 'include_categories', 'categories'));
    }

    public function update(Request $request)
    {
        $posts = $request->all();

        $request -> validate(
            ['content' => 'required|max:100', 'new_category' => 'max:50'],
            ['content.required' => '必須項目になります','content.max' => '100字以下で入力してください','new_category.max' => '50字以下で入力してください']
        );

        DB::transaction(function() use($posts){
            Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content']]);
            MemoCategory::where('memo_id', '=', $posts['memo_id'])->delete();
            
            if(!empty($posts['categories'][0])){
                foreach($posts['categories'] as $cate){
                    MemoCategory::insert(['memo_id' => $posts['memo_id'], 'category_id' => $cate]);
                }
            }

            $category_exists = Category::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_category'])
            ->exists();

            if($posts['new_category'] && !$category_exists){
                $category_id = Category::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_category']]);
                MemoCategory::insert(['memo_id' => $posts['memo_id'], 'category_id' => $category_id]);
            }
        });

        Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content']]);
        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all();

        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);
        return redirect(route('home'));
    }
}
