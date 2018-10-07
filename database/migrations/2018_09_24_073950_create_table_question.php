<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');

        Schema::create($tableNames['topic'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        Schema::create($tableNames['question'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->references('id')->on($tableNames['topic'])->index();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('content')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        Schema::create($tableNames['answer'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('question_id')->unsigned()->references('id')->on($tableNames['question'])->index();
            $table->string('content')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists($tableNames['topic']);
        Schema::dropIfExists($tableNames['question']);
        Schema::dropIfExists($tableNames['answer']);
    }
}
