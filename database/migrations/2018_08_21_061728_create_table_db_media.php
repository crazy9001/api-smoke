<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDbMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::create($tableNames['media_folders'], function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('parent')->default(0);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create($tableNames['media_storage'], function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->string('name', 255);
            $table->integer('folder_id')->default(0);
            $table->string('mime_type', 120);
            $table->string('type', 120);
            $table->integer('size');
            $table->string('public_url', 255);

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
        Schema::dropIfExists($tableNames['media_folders']);
        Schema::dropIfExists($tableNames['media_storage']);
    }
}
