<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:22 PM
 */

namespace Vtv\News\Repositories\Eloquent;

use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\News\Repositories\Interfaces\CategoriesInterface;

class DbCategoriesRepository extends RepositoriesAbstract implements CategoriesInterface
{
    public function getPublicCategory(array $filters)
    {
        $tableNames = config('cms.database_table_name');

        $query = $this->getModel()->with('children')
                ->where(function ($que) use ($filters, $tableNames){
                    //$que->where('parent_id', '=', 0);
                    isset($filters['parent']) ?  $que->where('parent_id', '=', $filters['parent']) :  $que->where('parent_id', '=', 0);
                    if(isset($filters['active'])) {
                        $que->where($tableNames['categories'] . '.status', $filters['active']);
                    }
                    if(isset($filters['position'])) {
                        $que->where($tableNames['categories'] . '.position', $filters['position']);
                    }
                });
        return $query;
    }
}