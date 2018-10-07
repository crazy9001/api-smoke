<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 2:52 PM
 */

namespace Vtv\Question\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['topic'];
    }

}