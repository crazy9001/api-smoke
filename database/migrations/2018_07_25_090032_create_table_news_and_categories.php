<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNewsAndCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $tableNames = config('cms.database_table_name');

        Schema::create($tableNames['categories'], function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('parent_id')->unsigned();
            $table->string('title_seo', 180)->nullable();
            $table->text('description')->nullable();
            $table->string('description_seo', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('user_id')->unsigned();
            $table->string('icon', 60)->nullable();
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('order')->default(0);
            $table->string('position')->default('top');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';

        });

        Schema::create($tableNames['news'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash_id', 40);
            $table->string('title_primary', 255);
            $table->string('slug', 255);
            $table->string('title_secondary', 255)->nullable();
            $table->integer('category_primary')->references('id')->on('categories');
            $table->string('category_secondary', 255)->nullable();
            $table->string('description_secondary', 400)->nullable();
            $table->string('description_primary', 400);
            $table->text('content_news');
            $table->string('image', 255)->nullable();
            $table->string('avatar_note', 400)->nullable();
            $table->string('author', 255)->nullable();
            $table->tinyInteger('format_type')->default(1);
            $table->tinyInteger('status');
            $table->integer('user_id')->references('id')->on('users');
            $table->integer('editor_user')->unsigned()->nullable()->default(null);
            $table->integer('publish_user')->unsigned()->nullable()->default(null);
            $table->tinyInteger('featured')->unsigned()->default(0);
            $table->tinyInteger('is_return')->unsigned()->default(0);
            $table->integer('views')->unsigned()->default(0);
            $table->string('note', 400)->nullable();
            $table->string('tags', 400)->nullable();
            $table->timestamp('sended_editor_at')->nullable();
            $table->timestamp('received_editor_at')->nullable();
            $table->timestamp('sended_publish_at')->nullable();
            $table->timestamp('received_publish_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('publish_at')->nullable();
            $table->softDeletes();
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
        $tableNames = config('cms.database_table_name');
        Schema::drop($tableNames['categories']);
        Schema::drop($tableNames['news']);
    }
}
