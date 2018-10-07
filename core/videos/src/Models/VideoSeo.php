<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 4:12 PM
 */

namespace Vtv\Videos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoSeo extends Model
{
    use SoftDeletes;

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['video_seo'];
    }

    protected $hidden = ['id', 'id_video'];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['meta_title', 'meta_keyword', 'meta_description'];
}