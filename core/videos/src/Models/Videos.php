<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 3:17 PM
 */

namespace Vtv\Videos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vtv\Users\Models\User;

class Videos extends Model
{
    use SoftDeletes;

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['videos'];
    }

    protected $hidden = ['id'];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['title', 'slug', 'description', 'category', 'tags', 'source', 'status', 'file_name', 'thumbnails'];

    public function element()
    {
        return $this->hasOne(Element::class, 'id_video', 'id');
    }

    public function seoInformation()
    {
        return $this->hasOne(VideoSeo::class, 'id_video', 'id');
    }

    public function createdUser()
    {
        $tableNames = config('cms.database_table_name');
        return $this->belongsToMany(User::class, $tableNames['video_element'], 'id_video', 'created_user')->withTimestamps();
    }

    public function editorUser()
    {
        $tableNames = config('cms.database_table_name');
        return $this->belongsToMany(User::class, $tableNames['video_element'], 'id_video', 'editor_user')->withTimestamps();
    }

    public function publishUser()
    {
        $tableNames = config('cms.database_table_name');
        return $this->belongsToMany(User::class, $tableNames['video_element'], 'id_video', 'publish_user')->withTimestamps();
    }

}