<?php
/**
 * Created by PhpStorm.
 * User: PTG
 * Date: 8/12/2018
 * Time: 3:43 PM
 */

namespace Vtv\News\Models;

use Eloquent;

class NewsAttribute extends Eloquent
{
    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['news_attribute'];
    }

    protected $fillable = ['news', 'display'];
}