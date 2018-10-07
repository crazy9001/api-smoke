<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDbNewsAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');

        Schema::create($tableNames['news_attribute'], function (Blueprint $table) use ($tableNames) {

            $table->increments('id');
            $table->string('news')->references('hash_id')->on($tableNames['news']);
            $table->integer('display')->default(0);
            $table->timestamps();
            $table->engine = 'InnoDB';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('cms.database_table_name');
        Schema::drop($tableNames['news_attribute']);
    }
}
