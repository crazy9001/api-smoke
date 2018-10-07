<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/13/2018
 * Time: 3:15 PM
 */

namespace Vtv\Menu\Http\Controllers;

use Illuminate\Http\Request;
use Vtv\Base\Http\Controllers\BaseController;
use Vtv\Menu\Repositories\Interfaces\MenuInterface;
use Validator;
use Vtv\News\Repositories\Interfaces\CategoriesInterface;
use Illuminate\Support\Facades\Input;

class MenuController extends BaseController
{
    protected $menuRepository;

    protected $categoriesRepository;

    public function __construct(MenuInterface $menuRepository, CategoriesInterface $categoriesRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->categoriesRepository = $categoriesRepository;
    }

    public function index()
    {
        $menu = $this->menuRepository->allBy(['parent_id' => 0], ['children']);
        return $this->sendResponse( $menu, 'Success');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:' . config('cms.database_table_name')['menu_manager'] . ',name',
            'link_type' =>  'required',
            'link'  =>  'required_if:link_type,==,out',
            'category'  =>  'required_if:link_type,==,in|exists:'.config('cms.database_table_name')['categories'] . ',id',
            'blank_type'    =>  'required'
        ],
        [
            'name.required' => trans('menu::notices.validate.name_required'),
            'name.unique'   =>  trans('menu::notices.validate.name_unique'),
            'link_type.required' =>  trans('menu::notices.validate.link_type_required'),
            'link.required_if' =>  trans('menu::notices.validate.link_required'),
            'category.required_if' =>  trans('menu::notices.validate.category_required'),
            'category.exists'   =>  trans('menu::notices.validate.category_exists'),
            'blank_type.required' =>  trans('menu::notices.validate.blank_type_required'),
        ]);

        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $menu = $this->menuRepository->getModel();
        $menu->fill($request->all());
        $menu->status = isset($request->status) && !empty($request->status) ? $request->status : 1;
        $menu->parent_id = isset($request->parent_id) && !empty($request->parent_id) ? $request->parent_id : 0;
        $menu->position = isset($request->position) && !empty($request->position) ? $request->position : 'top';
        if( $request->link_type == 'in'){
            $categoryId = $request->category;
            $category = $this->categoriesRepository->findById($categoryId);
            $menu->link = $category->slug;
        }else{
            $menu->link =  $request->link ;
        }
        try{
            $menu = $this->menuRepository->createOrUpdate($menu);
            return $this->sendResponse($menu->toArray(), 'Successfully');
        }catch(\Exception $e){
            return $this->sendError('Error.', $e->getMessage(), 400);
        }
    }


    public function publishListMenu()
    {
        $position = Input::get('position');
        $filters = array(
            'position'  =>  $position
        );
        $result = $this->menuRepository->getPublishMenu($filters);
        $listMenu = $result->get();
        return $this->sendResponse($listMenu->toArray(), trans('menu::notices.result.get_menu_success'), 200);
    }

}