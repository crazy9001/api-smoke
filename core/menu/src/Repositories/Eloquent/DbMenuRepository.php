<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/13/2018
 * Time: 3:01 PM
 */

namespace Vtv\Menu\Repositories\Eloquent;

use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\Menu\Repositories\Interfaces\MenuInterface;

class DbMenuRepository extends RepositoriesAbstract implements MenuInterface
{
    public function getPublishMenu(array $filters)
    {
        $query = $this->getModel()->with('children')
                ->where('status', '=', 1)
                ->where('parent_id', '=', 0)
                ->where(function ($que) use($filters){
                    if(isset($filters['position']) && !empty(($filters['position']))){
                        $que->where('position', '=', $filters['position']);
                    }
                })
                ->select('id as id', 'name as name', 'link as link', 'link_type as type', 'blank_type as blank_type', 'position as position', 'parent_id as parent_id')
                ->orderBy((isset($sortInfo['column']) && !empty($sortInfo['column'])) ? $sortInfo['column'] : 'order', (isset($sortInfo['order']) && !empty($sortInfo['order'])) ? $sortInfo['order'] : 'asc' );;
        return $query;

    }
}