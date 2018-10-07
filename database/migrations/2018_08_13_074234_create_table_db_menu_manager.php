<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDbMenuManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::create($tableNames['menu_manager'], function (Blueprint $table) use ($tableNames) {

            $table->increments('id');
            $table->string('name');
            $table->string('link');
            $table->string('link_type');
            $table->tinyInteger('status')->default(1);
            $table->integer('parent_id')->unsigned();
            $table->tinyInteger('order')->default(0);
            $table->tinyInteger('blank_type')->default(0);
            $table->string('position')->default('top');
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
        Schema::drop($tableNames['menu_manager']);
    }
}
