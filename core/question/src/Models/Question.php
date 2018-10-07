<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 2:53 PM
 */

namespace Vtv\Question\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table;

    protected $fillable = ['name', 'phone', 'email', 'content', 'topic_id'];

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['question'];
    }

    public function answer()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id')->where(['status' => 1]);
    }
}