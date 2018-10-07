<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::create($tableNames['sub_new_subcategories'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('new_id')->references('id')->on($tableNames['news'])->index();
            $table->integer('category_id')->references('id')->on($tableNames['categories'])->index();
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
        Schema::dropIfExists($tableNames['sub_new_subcategories']);
    }
}
