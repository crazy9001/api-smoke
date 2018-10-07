<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 3:34 PM
 */

namespace Vtv\Videos\Models;

use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['video_time_line'];
    }

    protected $hidden = ['id', 'id_video'];
}