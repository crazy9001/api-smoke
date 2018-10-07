<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/13/2018
 * Time: 3:06 PM
 */

namespace Vtv\Menu\Models;

use Eloquent;

class Menu extends Eloquent
{
    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['menu_manager'];
    }

    /**
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $fillable = ['name', 'link', 'link_type', 'status', 'parent_id', 'order', 'blank_type', 'position'];

    /**
     * @return mixed
     */
    public function parent() {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @return mixed
     */
    // Each category may have multiple children
    public function children() {
        return $this->hasMany(static::class, 'parent_id');
    }

}