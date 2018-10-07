<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsernameTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('cms.database_table_name');
        Schema::table($tableNames['users'], function($table) {
            $table->string('username')->after('name');
            $table->string('email')->nullable()->change();
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
        Schema::table($tableNames['users'], function($table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
}
