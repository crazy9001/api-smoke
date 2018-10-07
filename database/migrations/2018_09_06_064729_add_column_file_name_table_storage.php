<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFileNameTableStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::table($tableNames['media_storage'], function($table) {
            $table->string('file_name')->after('mime_type');
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
        Schema::table($tableNames['media_storage'], function($table) {
            $table->dropColumn('file_name');
        });
    }
}
