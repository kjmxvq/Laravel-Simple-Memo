@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
      <a href="/home">← ホームに戻る</a>
    </div>
    <form action="{{ route('category_store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="category-input" class="form-label">新規カテゴリ名</label>
            @error('new_category')
              <div class="text-danger">
                {{ $message }}
              </div>
            @enderror
            <input type="string" name="new_category" class="form-control w-50 mb-3" id="category-input" placeholder="新規カテゴリ名を入力してください">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
    </form>
    <hr class="my-5">
    <table class="table text-center">
        <thead>
          <tr>
            <th scope="col" style="width:10%">No</th>
            <th scope="col" style="width:35%">カテゴリ名</th>
            <th scope="col" style="width:20%">作成日時</th>
            <th scope="col" style="width:20%">更新日時</th>
            <th scope="col" style="width:15%"></th>
          </tr>
        </thead>
        <tbody>
        @foreach($categories as $cate)
            <tr>
                <th scope="row">{{ $loop->index + 5 * ($page_param - 1) + 1 }}</th>
                <td>{{ $cate['name'] }}</td>
                <td>{{ $cate['created_at']->format("Y.m.d") }}</td>
                <td>{{ $cate['updated_at']->format("Y.m.d") }}</td>
                <td>
                  <form action="{{ route('category_destroy') }}" method="POST" onsubmit="return destroy_confirm()">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $cate['id'] }}">
                    <button type="submit" class="btn btn-danger">削除</button>
                  </form>
                </td>
            </tr>
        @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
      </div>
</div>
@endsection
