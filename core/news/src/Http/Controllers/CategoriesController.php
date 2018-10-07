<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:26 PM
 */

namespace Vtv\News\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Support\Facades\Auth;
use Vtv\Base\Http\Controllers\BaseController;
use Vtv\News\Models\Categories;
use Vtv\News\Repositories\Interfaces\CategoriesInterface;

class CategoriesController extends BaseController
{
    protected $categoriesRepository;

    public function __construct(CategoriesInterface $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    public function listCategories()
    {
        $categories = $this->categoriesRepository->getModel()->nested()->get();
        return $this->sendResponse($categories, 'Successfully');
    }

    /**
     * This function create new category
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->hasRole('Secretary')){
            return $this->sendError('Error.', 'You are don\'t permission', 405);
        }

        $input = $request->only('name', 'parent_id', 'description', 'order', 'title_seo', 'description_seo', 'status', 'position');
        $validator = Validator::make($input, [
            'name' => 'required|unique:'.config('cms.database_table_name')['categories'],
            'parent_id' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $category = $this->categoriesRepository->getModel();
        $category->fill($request->all());
        $category->slug = str_slug($request->name);
        $category->user_id = Auth::user()->id;
        $category->featured = $request->input('featured', 0);
        $category->featured = $request->input('status', 1);
        $category->position = isset($request->position) && !empty($request->position) ? $request->position : 'top';
        $category->featured = $request->input('order', 0);
        try{
            $category = $this->categoriesRepository->createOrUpdate($category);
            return $this->sendResponse($category->toArray(), 'Successfully');
        }
        catch(\Exception $e){
            return $this->sendError('Error.', trans('news::notices.categories.unable_to_create_category'), 400);
        }
    }

    /**
     * This function get information category
     * @return \Illuminate\Http\Response
     */
    public function detail()
    {
        $categoryId = Input::get('category');
        $category = $this->categoriesRepository->getFirstBy(['id' => $categoryId]);
        if($category){
            return $this->sendResponse($category->toArray(), 'Successfully', 200);
        }
        return $this->sendError('Error.', trans('news::notices.categories.category_not_found'), 400);
    }

    /**
     * This function update information category by id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(!Auth::user()->hasRole('Secretary')){
            return $this->sendError('Error.', 'You are don\'t permission', 405);
        }
        $input = $request->only('id', 'name', 'parent_id', 'description', 'order', 'status', 'title_seo', 'description_seo', 'status', 'position');
        $validator = Validator::make($input, [
            'id'    =>  'required|integer',
            'name' => 'required|unique:'.config('cms.database_table_name')['categories'],
            'parent_id' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $category = $this->categoriesRepository->getFirstBy(['id' => $input['id']]);
        if($category){
            $category->fill($request->all());
            $category->slug = str_slug($request->name);
            $category->featured = $request->input('featured', false);
            $category->featured = $request->input('status', 1);
            $category->featured = $request->input('order', 0);
            $category->featured = $request->input('position', 'top');
            $category = $this->categoriesRepository->createOrUpdate($category);
            return $this->sendResponse($category->toArray(), trans('news::notices.categories.update_success_fully'), 200);

        }
        return $this->sendError('Error.', trans('news::notices.categories.category_not_found'), 400);
    }

    public function getCategories()
    {
        $categories = $this->categoriesRepository->pluck('name' ,'id');
        return $this->sendResponse($categories, 'Successfully', 200);
    }


    public function getPublicCategories()
    {
        $filter['position'] = Input::get('position');
        $filter['active'] = Input::get('active');
        $filter['parent'] = Input::get('parent');
        $category = $this->categoriesRepository->getPublicCategory($filter)->get();
        return $this->sendResponse($category->toArray(), trans('news::notices.categories.get_category_success'), 200);
    }

}