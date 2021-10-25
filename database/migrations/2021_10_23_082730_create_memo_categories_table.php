<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemoCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memo_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('memo_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('memo_id')->references('id')->on('memos');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memo_categories');
    }
}
