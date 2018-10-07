<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::create($tableNames['video_category'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('parent_id')->unsigned();
            $table->string('title_seo', 180)->nullable();
            $table->text('description')->nullable();
            $table->string('description_seo', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('order')->default(0);
            $table->string('position')->default('top');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create($tableNames['videos'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('category')->references('id')->on($tableNames['video_category']);
            $table->string('tags', 400)->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('DRAFT');
            $table->tinyInteger('highlight')->default(0);
            $table->string('file_name');
            $table->string('thumbnails')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create($tableNames['video_time_line'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('id_video')->references('id')->on($tableNames['videos']);
            $table->timestamp('time_created')->nullable();
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('time_editor')->nullable();
            $table->timestamp('time_publish')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create($tableNames['video_element'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('id_video')->references('id')->on($tableNames['videos']);
            $table->integer('created_user')->unsigned()->nullable()->default(null);
            $table->integer('editor_user')->unsigned()->nullable()->default(null);
            $table->integer('publish_user')->unsigned()->nullable()->default(null);
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create($tableNames['video_seo'], function (Blueprint $table) use ($tableNames) {
            $table->increments('id');
            $table->integer('id_video')->references('id')->on($tableNames['videos']);
            $table->text('meta_title')->nullable()->default(null);
            $table->text('meta_keyword')->nullable()->default(null);
            $table->text('meta_description')->nullable()->default(null);
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
        Schema::drop($tableNames['video_category']);
        Schema::drop($tableNames['videos']);
        Schema::drop($tableNames['video_time_line']);
        Schema::drop($tableNames['video_element']);
        Schema::drop($tableNames['video_seo']);
    }
}
