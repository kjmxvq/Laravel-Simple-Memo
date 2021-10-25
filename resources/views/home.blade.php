@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="todo-input" class="form-label">新規メモ内容</label>
            @error('content')
              <div class="text-danger">
                {{ $message }}
              </div>
            @enderror
            <input type="string" name="content" class="form-control w-50 mb-3" id="todo-input" placeholder="新規メモの内容を入力してください">
            @if(count($categories))
              <label for="category-check" class="form-label d-block">関連カテゴリ</label>
              @foreach($categories as $cate)
                <div class="form-check form-check-inline mb-3" id="category-check">
                  <input class="form-check-input" type="checkbox" name="categories[]" id="{{ $cate['id'] }}" value="{{ $cate['id'] }}">
                  <label class="form-check-label" for="{{ $cate['id'] }}">{{ $cate['name'] }}</label>
                </div>
              @endforeach
            @endif
            <label for="new-category-input" class="form-label d-block">新規関連カテゴリ</label>
            @error('new_category')
              <div class="text-danger">
                {{ $message }}
              </div>
            @enderror
            <input type="text" class="form-control w-50 mb-4" id="new-category-input" name="new_category" placeholder="新規カテゴリを関連付ける場合は入力してください"/>
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
    </form>
    <hr class="my-5">
    <div class="btn-group mr-1 mb-4">
      <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        カテゴリで検索
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="/home">すべて表示</a>
        <div class="dropdown-divider"></div>
        @foreach($categories as $cate)
          <a class="dropdown-item" href="/home?category={{ $cate['id'] }}">{{ $cate['name'] }}</a>
        @endforeach
      </div>
    </div>
    <button type="button" class="btn btn-success mx-1 mb-4" onclick="location.href='/category_index'">カテゴリの管理</button>
    <table class="table text-center">
        <thead>
          <tr>
            <th scope="col" style="width:10%">No</th>
            <th scope="col" style="width:35%">メモ内容</th>
            <th scope="col" style="width:20%">作成日時</th>
            <th scope="col" style="width:20%">更新日時</th>
            <th scope="col" style="width:15%"></th>
          </tr>
        </thead>
        <tbody>
        @foreach($memos as $memo) 
            <tr>
                <th scope="row">{{ $loop->index + 5 * ($page_param - 1) + 1 }}</th>
                <td>{{ $memo['content'] }}</td>
                <td>{{ $memo['created_at']->format("Y.m.d") }}</td>
                <td>{{ $memo['updated_at']->format("Y.m.d") }}</td>
                <td>
                  <button type="button" class="btn btn-success m-1" onclick="location.href='/edit/{{ $memo['id'] }}/{{ $loop->index + 5 * ($page_param - 1) + 1 }}'">編集</button>
                  <form action="{{ route('destroy') }}" method="POST" class="d-inline" onsubmit="return destroy_confirm()">
                    @csrf
                    <input type="hidden" name="memo_id" value="{{ $memo['id'] }}">
                    <button type="submit" class="btn btn-danger m-1">削除</button>
                  </form>
                </td>
            </tr>
        @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-4">
        {{ $memos->appends(['category'=>$category_param])->links() }}
      </div>
</div>
@endsection
