<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 3:34 PM
 */

namespace Vtv\Videos\Models;

use Illuminate\Database\Eloquent\Model;
use Vtv\Users\Models\User;

class Element extends Model
{
    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['video_element'];
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_user', 'id');
    }

    protected $hidden = ['id', 'id_video'];
}