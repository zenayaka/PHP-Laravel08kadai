<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            
            $table->string('title');
            
            $table->string('body');
            
            $table->string('image_path')->nullable();
            //->nullable();は画像のパスは空でも保存できるという意味
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
        //もしnewsというテーブルが存在すれば削除するという意味
    }
}
