<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 2:54 PM
 */

namespace Vtv\Question\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table;

    protected $fillable = ['question_id', 'content'];

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['answer'];
    }
}