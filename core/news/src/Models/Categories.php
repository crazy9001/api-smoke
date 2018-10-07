<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:23 PM
 */

namespace Vtv\News\Models;

use Eloquent;
use Nestable\NestableTrait;

class Categories extends Eloquent
{
    use NestableTrait;

    protected $table;

    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['categories'];
    }

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'parent_id', 'title_seo', 'description_seo', 'slug', 'icon', 'featured', 'order', 'is_default', 'status', 'user_id', 'position'];

    public function parent() {
        return $this->belongsTo(static::class, 'parent_id');
    }

    // Each category may have multiple children
    public function children() {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function news()
    {
        $tableNames = config('cms.database_table_name');
        return $this->belongsToMany(News::class, $tableNames['new_categories']);
    }

}