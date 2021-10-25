@extends('layouts.app')

@section('content')
<div class="container">
  <div class="text-center mb-3">{{ $num_label }} メモ編集</div>
  <form action="{{ route('update') }}" method="POST">
    @csrf
    <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
    <label for="todoInput" class="form-label">メモ内容</label>
    @error('content')
      <div class="text-danger">
        {{ $message }}
      </div>
    @enderror
    <input type="string" name="content" class="form-control mb-3" id="todoInput" value="{{ $edit_memo[0]['content'] }}" placeholder="メモの内容を入力してください">
    @if(count($categories))
      <label for="category-check" class="form-label d-block">関連カテゴリ</label>
      @foreach($categories as $cate)
        <div class="form-check form-check-inline mb-3" id="category-check">
          <input class="form-check-input" type="checkbox" name="categories[]" id="{{ $cate['id'] }}" value="{{ $cate['id'] }}" {{ in_array($cate['id'], $include_categories) ? 'checked' : '' }}>
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
    <input type="text" class="form-control w-50 mb-3" id="new-category-input" name="new_category" placeholder="新規カテゴリを関連付ける場合は入力してください"/>
    <button class="btn btn-success m-1" onclick="location.href='/home'">戻る</button>
    <button type="submit" class="btn btn-primary m-1">登録</button>
  </form>
</div>
@endsection
