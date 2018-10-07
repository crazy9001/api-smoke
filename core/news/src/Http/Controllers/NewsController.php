<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 2:57 PM
 */

namespace Vtv\News\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Input;
use Vtv\Base\Http\Controllers\BaseController;
use Vtv\News\Repositories\Interfaces\NewAttributeInterface;
use Vtv\News\Repositories\Interfaces\NewInterface;
use Vtv\News\Repositories\Interfaces\CategoriesInterface;
use Carbon\Carbon;
use DateTime;

class NewsController extends BaseController
{
    protected $newsRepository;

    protected $newsAttributeRepository;

    protected $categoryRepository;

    protected $relation_ship = ['created_by', 'editor_by', 'published_by', 'highlight', 'categories'];

    protected $selectAPIListNews = ['hash_id as primary', 'title_primary as title', 'user_id', 'editor_user', 'publish_user', 'image as image', 'featured as featured', 'created_at as created_at', 'category_primary as category_primary', 'publish_at as publish_at'];
    protected $selectAPIDetailNew = ['hash_id as primary', 'title_primary as title', 'title_secondary as sub_title', 'user_id', 'editor_user', 'publish_user', 'image as image',
        'content_news as content', 'created_at as created_at', 'description_primary as description', 'description_secondary as sub_description', 'is_return as is_return', 'status as status',
        'tags as tags', 'category_primary as category', 'category_secondary as sub_category', 'format_type as format_type', 'author as author', 'publish_at as publish_at', 'note as note', 'deleted_at as deleted_at'];

    protected $limitRecord = 50;

    public function __construct(NewInterface $newsRepository, NewAttributeInterface $newsAttributeRepository, CategoriesInterface $categoryRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->newsAttributeRepository = $newsAttributeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function listNews()
    {
        $action = Input::get('action');
        $keyword = Input::get('keyword');
        $categories = Input::get('categories');
        $filters = array(
            'action' => trim($action),
            'keyword' => trim($keyword),
            'categories' => trim($categories)
        );

        // get list news temporary
        if( $action == 'Temporary' ) {
            $condition = [
                'status'    =>  config('news.status.temporary'),
                'user_id'   =>  Auth::user()->id,
                'is_return' =>  config('news.status.is_default'),
            ];
        }
        // get list news waiting editor
        elseif ( $action == 'PendingEditor' ){
            if(Auth::user()->hasRole('Reporter')) {
                $condition = [
                    'status'    =>  config('news.status.pending_editor'),
                    'user_id' => Auth::user()->id,
                    'editor_user' => null,
                    'is_return' => config('news.status.is_default'),
                ];
            }else{
                $condition = [
                    'status'    =>  config('news.status.pending_editor'),
                    'editor_user'   =>  null,
                    'is_return' =>  config('news.status.is_default'),
                ];
            }
        }
        // get list news receiver editor
        elseif ( $action == 'ReceiverEditor' ){
            if(!Auth::user()->hasRole('Reporter')) {
                $condition = [
                    'status' => config('news.status.pending_editor'),
                    'editor_user' => Auth::user()->id,
                    'is_return' => config('news.status.is_default'),
                ];
            }else{
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
        }
        // get list new waiting publication
        elseif ( $action == 'PendingPublication' ){
            if(!Auth::user()->hasRole('Reporter')) {
                if(Auth::user()->hasRole('Secretary')) {
                    $condition = [
                        'status' => config('news.status.pending_publish'),
                        'publish_user' => null,
                        'is_return' => config('news.status.is_default'),
                    ];
                }else{
                    $condition = [
                        'status' => config('news.status.pending_publish'),
                        'publish_user'  =>  null, 'editor_user' => Auth::user()->id,
                        'is_return' => config('news.status.is_default'),
                    ];
                }
            }else{
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            };
        }
        // get list news receiver publication
        elseif( $action == 'ReceivePublication' ) {
            if(Auth::user()->hasRole('Secretary')) {
                $condition = [
                    'status' => config('news.status.pending_publish'),
                    'publish_user' => Auth::user()->id,
                    'is_return' => config('news.status.is_default'),
                ];
            }else{
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
        }
        // get list new returned
        elseif ( $action == 'Returned' ){
            if(Auth::user()->hasRole('Editor')) {
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            $condition = [
                'status' => config('news.status.temporary'),
                'user_id' => Auth::user()->id,
                'is_return' => config('news.status.is_return'),
            ];
        }
        // get list news returned editor
        elseif( $action == 'ReturnedEditor' ){
            if(Auth::user()->hasRole('Reporter')) {
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            $condition = [
                'status' => config('news.status.pending_editor'),
                'editor_user' => Auth::user()->id,
                'is_return' => config('news.status.is_return_editor'),
            ];
        }
        // get list news published
        elseif( $action == 'Published' ) {
            if( Auth::user()->hasRole('Secretary') ) {
                $condition = [
                    'status' => config('news.status.publish'),
                    'is_return' => config('news.status.is_default'),
                ];
            }elseif( Auth::user()->hasRole('Editor') ) {
                $condition = [
                    'status'  => config('news.status.publish'),
                    'publish_user' => Auth::user()->id,
                    'is_return' => config('news.status.is_default'),
                ];
            }else{
                $condition = [
                    'status'  => config('news.status.publish'),
                    'user_id' => Auth::user()->id,
                    'is_return' => config('news.status.is_default'),
                ];
            }
        }
        // get news trashed
        elseif ( $action == 'Trashed' ) {
            if( !Auth::user()->hasRole('Secretary') ){
                $condition = [
                    'status'  => config('news.status.publish'),
                    'user_id' => Auth::user()->id,
                    'is_return' => config('news.status.is_default'),
                ];
            }else{
                $condition = [
                    'status'  => config('news.status.publish'),
                    'is_return' => config('news.status.is_default'),
                ];
            }
            $model = $news = $this->newsRepository->getModel();
            $news =   $model->with($this->relation_ship)
                ->select($this->selectAPIListNews)
                ->where($condition)
                ->whereNotNull('deleted_at')
                ->paginate($this->limitRecord);
            return $this->sendResponse($news->toArray(), 'Successfully');
        }
        try{
            $news = $this->newsRepository
                ->with($this->relation_ship)
                ->select($this->selectAPIListNews)
                ->where($condition)
                ->where(function ($que) use ($filters) {
                    if(isset($filters['keyword']) && !empty($filters['keyword'])){
                        $que->where('title_primary', 'like', '%' . trim($filters['keyword']) . '%');
                    };
                    if(isset($filters['categories']) && !empty($filters['categories'])) {
                        $que->where('category_primary', '=', trim($filters['categories']));
                    };
                })
                ->whereNull('deleted_at')
                ->orderBy('published_at', 'desc')
                ->paginate($this->limitRecord)->appends($filters);
            return $this->sendResponse($news->toArray(), 'Successfully');

        }catch (\Exception $e){
            return $this->sendError('Error.', $e->getMessage(), 400);
        }

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_primary'  =>  'required|integer',
            'title_primary' => 'required',
            'description_primary'   =>  'required',
            'content_news' => 'required',
        ],
            [
                'title_primary.required'    =>  trans('news::notices.news.validate.title_required'),
                'category_primary.required'    =>  trans('news::notices.news.validate.category_primary_required'),
                'category_primary.integer'    =>  trans('news::notices.news.validate.category_primary_required'),
                'description_primary.required'    =>  trans('news::notices.news.validate.description_primary_required'),
                'content_news.required'    =>  trans('news::notices.news.validate.content_news_required'),
            ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $new = $this->newsRepository->getModel();
        $new->fill($request->all());
        $new->description_secondary = $new->description_primary;
        $new->hash_id = md5(uniqid(rand(), true));
        $new->slug = $this->newsRepository->createSlug($request->title_primary, null);
        $new->user_id = Auth::user()->id;
        $new->featured = $request->input('featured', 0);
        //$new->category_secondary = json_encode($request->category_secondary);
        $new->publish_at = is_null($request->publish_at) ? Carbon::now() : $request->publish_at;
        if( $request->input('submit') == 'save' ) {
            $new->status = config('news.status.temporary');
        }
        elseif( $request->input('submit') == 'send' )
        {
            $new->sended_editor_at = Carbon::now();
            if(Auth::user()->hasRole('Reporter')) {
                $new->status = config('news.status.pending_editor');
            }else{
                $new->status = config('news.status.pending_publish');
                $new->received_editor_at = Carbon::now();
                $new->editor_user = Auth::user()->id;
                $new->sended_publish_at = Carbon::now();
            }
        }
        elseif( $request->input('submit') == 'send_publish' )
        {
            if( Auth::user()->hasRole('Reporter') ) {
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            $new->status = config('news.status.pending_publish');
            $new->editor_user = Auth::user()->id;
            $new->sended_editor_at = Carbon::now();
            $new->received_editor_at = Carbon::now();
            $new->sended_publish_at = Carbon::now();
        }
        elseif( $request->input('submit') == 'publish' )
        {
            if( Auth::user()->hasRole('Secretary') ) {
                $new->status = config('news.status.publish');
                $new->editor_user = $new->publish_user = Auth::user()->id;
                $new->sended_editor_at = Carbon::now();
                $new->received_editor_at = Carbon::now();
                $new->sended_publish_at = Carbon::now();
                $new->received_publish_at = Carbon::now();
                $new->published_at = Carbon::now();
            }else{
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
        }
        try{
            $new = $this->newsRepository->createOrUpdate($new);
            // sub_categories
            $sub_categories = $request->category_secondary;
            if (!empty($sub_categories)) {
                foreach ($sub_categories as $sub_category) {
                    $new->sub_categories()->attach($sub_category);
                }
            }
            //new attr
            $attribute = $this->newsAttributeRepository->getModel();
            $attribute->news = $new->hash_id;
            $this->newsAttributeRepository->createOrUpdate($attribute);
            return $this->sendResponse($new->toArray(), 'Successfully');

        }catch(\Exception $e){
            return $this->sendError('Error.', $e->getMessage(), 400);
        }
    }

    public function detailArticle()
    {
        $id = Input::get('news');
        $news = $this->newsRepository->getFirstBy(['hash_id' => $id], $this->selectAPIDetailNew, $this->relation_ship);
        if($news){
            return $this->sendResponse($news->toArray(), 'Successfully', 200);
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new' => 'required',
            'submit'  =>  'required',
        ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }

        $new = $this->newsRepository->getFirstBy(['hash_id' => $request->new]);
        if($new && !is_null($new)){
            if($request->submit == 'Send'){
                if($new->status == config('news.status.temporary') && $new->user_id == Auth::user()->id){
                    if(Auth::user()->hasRole('Reporter')){
                        if($new->is_return == 2){
                            $newData['is_return'] = 0;
                        }
                        $newData['status'] = config('news.status.pending_editor');
                        $newData['sended_editor_at'] = Carbon::now();
                        $this->newsRepository->update(['id' => $new->id], $newData);
                        return $this->sendResponse('Success', trans('news::notices.news.new_has_been_send_editor'), 200);
                    }
                    $newData['editor_user'] = Auth::user()->id;
                    $newData['sended_editor_at'] = Carbon::now();
                    $newData['received_editor_at'] = Carbon::now();
                    $newData['status'] = config('news.status.pending_publish');
                    $newData['sended_publish_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success', trans('news::notices.news.new_has_been_send_publish'), 200);
                }
                if($new->status == config('news.status.pending_editor')){
                    if(Auth::user()->hasRole('Reporter')){
                        return $this->sendError('Error.', 'You are don\'t permission', 405);
                    }
                    if($new->is_return == 1){
                        $newData['is_return'] = 0;
                    }
                    $newData['status'] = config('news.status.pending_publish');
                    $newData['editor_user']= Auth::user()->id;
                    $newData['received_editor_at'] = Carbon::now();
                    $newData['sended_publish_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_send_publish'), 200);
                }
                if($new->status == config('news.status.pending_publish') && $new->publish_user == Auth::user()->id){
                    if(!Auth::user()->hasRole('Secretary')){
                        return $this->sendError('Error.', 'You are don\'t permission', 405);
                    }
                    $newData['status'] = config('news.status.publish');
                    $newData['published_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_publish'), 200);
                }
                if(!is_null($new->deleted_at)){
                    if(!Auth::user()->hasRole('Secretary')){
                        return $this->sendError('Error.', 'You are don\'t permission', 405);
                    }
                    $newData['deleted_at'] = null;
                    $newData['published_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_publish'), 200);
                }
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            if($request->submit == 'Receive'){
                if(Auth::user()->hasRole('Reporter')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if($new->status == config('news.status.pending_editor')){
                    if(is_null($new->editor_user)){
                        $newData['editor_user'] = Auth::user()->id;
                        $newData['received_editor_at'] = Carbon::now();
                        $this->newsRepository->update(['id' => $new->id], $newData);
                        return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_receiver_editor'), 200);
                    }
                    return $this->sendError('Error.', trans('news::notices.news.new_has_been_receiver_other'), 400);
                }
                if($new->status == config('news.status.pending_publish')){
                    if(!Auth::user()->hasRole('Secretary')){
                        return $this->sendError('Error.', 'You are don\'t permission', 405);
                    }
                    if(is_null($new->publish_user)){
                        $newData['publish_user'] = Auth::user()->id;
                        $newData['received_publish_at'] = Carbon::now();
                        $this->newsRepository->update(['id' => $new->id], $newData);
                        return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_receiver_publish'), 200);
                    }
                    return $this->sendError('Error.', trans('news::notices.news.new_has_been_receiver_other'), 400);
                }
            }
            if($request->submit == 'Publish'){

                if(!Auth::user()->hasRole('Secretary')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if($new->status == config('news.status.temporary')){
                    $newData['status'] = config('news.status.publish');
                    $newData['editor_user'] = Auth::user()->id;
                    $newData['received_editor_at'] = Carbon::now();
                    $newData['sended_publish_at'] = Carbon::now();
                    $newData['received_publish_at'] = Carbon::now();
                    $newData['publish_user'] = Auth::user()->id;
                    $newData['published_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_publish'), 200);
                }
                if($new->status ==  config('news.status.pending_editor') ){
                    if(!is_null($new->editor_user)){
                        return $this->sendError('Error.', trans('news::notices.news.new_has_been_receiver_other'), 400);
                    }
                    $newData['status'] = config('news.status.publish');
                    $newData['editor_user'] = Auth::user()->id;
                    $newData['received_editor_at'] = Carbon::now();
                    $newData['sended_publish_at'] = Carbon::now();
                    $newData['received_publish_at'] = Carbon::now();
                    $newData['publish_user'] = Auth::user()->id;
                    $newData['published_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_publish'), 200);
                }
                if($new->status == config('news.status.pending_publish')){
                    $newData['status'] = config('news.status.publish');
                    $newData['sended_publish_at'] = Carbon::now();
                    $newData['received_publish_at'] = Carbon::now();
                    $newData['publish_user'] = Auth::user()->id;
                    $newData['published_at'] = Carbon::now();
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_publish'), 200);
                }
            }
            if($request->submit == 'ReturnEditor'){
                if(!Auth::user()->hasRole('Secretary')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if($new->is_return == config('news.status.is_default')){
                    $newData['status'] = config('news.status.pending_editor');
                    $newData['is_return'] = config('news.status.is_return_editor');
                    $newData['publish_user'] = null;
                    $newData['received_publish_at'] = null;
                    $newData['sended_publish_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_return_editor'), 200);
                }
                return $this->sendError('Error.',  trans('news::notices.news.new_return_editor_error'), 404);
            }

            if($request->submit == 'Return'){
                if(!is_null($new->deleted_at)){
                    $newData['deleted_at'] = null;
                }
                if($new->is_return == config('news.status.is_default')){
                    if(Auth::user()->hasRole('Reporter')){
                        return $this->sendError('Error.', 'You are don\'t permission', 405);
                    }
                    $newData['status'] = config('news.status.temporary');
                    $newData['is_return'] = config('news.status.is_return');
                    $newData['publish_user'] = null;
                    $newData['received_publish_at'] = null;
                    $newData['sended_publish_at'] = null;
                    $newData['editor_user'] = null;
                    $newData['received_editor_at'] = null;
                    $newData['sended_editor_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_return'), 200);
                }
                return $this->sendError('Error.',  trans('news::notices.news.new_return_error'), 404);
            }

            if($request->submit == 'Release'){
                if(Auth::user()->hasRole('Reporter')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if($new->status == config('news.status.pending_editor') && $new->editor_user == Auth::user()->id ){
                    $newData['editor_user'] = null;
                    $newData['received_editor_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_release'), 200);
                }
                if($new->status == config('news.status.pending_publish') && $new->publish_user == Auth::user()->id){
                    $newData['publish_user'] = null;
                    $newData['received_publish_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_release'), 200);
                }
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }

            if($request->submit == 'UnPublish'){
                if(!Auth::user()->hasRole('Secretary')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                $new->delete();
                return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_trash'), 200);
            }

            if($request->submit == 'btnReturnBack')
            {
                if($new->user_id != Auth::user()->id){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if($new->status == 1){
                    $newData['status'] = 0;
                    $newData['sended_editor_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_return_back_success'), 200);
                }
                if($new->status == 2){
                    $newData['status'] = 0;
                    $newData['editor_user'] = null;
                    $newData['sended_editor_at'] = null;
                    $newData['received_editor_at'] = null;
                    $newData['sended_publish_at'] = null;
                    $this->newsRepository->update(['id' => $new->id], $newData);
                    return $this->sendResponse('Success',  trans('news::notices.news.new_return_back_success'), 200);
                }
            }

            return $this->sendError('Error.', trans('news::notices.news.submit_not_found'), 400);

        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);

    }

    public function receiverNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash_id' => 'required',
        ],
            [
                'hash_id.required'   =>  'Please select new'
            ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $new = $this->newsRepository->getFirstBy(['hash_id' =>  $request->hash_id], $this->selectAPIDetailNew, $this->relation_ship);
        if(!is_null($new)){
            if(Auth::user()->hasRole('Reporter')){
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            if($new->status == config('news.status.pending_editor')){
                if(is_null($new->editor_user)){
                    $newData['editor_user'] = Auth::user()->id;
                    $newData['received_editor_at'] = Carbon::now();
                    $this->newsRepository->update(['hash_id' => $new->primary], $newData);
                    $news = $this->newsRepository->getFirstBy(['hash_id' => $new->primary], $this->selectAPIDetailNew, $this->relation_ship);
                    return $this->sendResponse($news,  trans('news::notices.news.new_has_been_receiver_editor'), 200);
                }
                return $this->sendError('Error.', trans('news::notices.news.new_has_been_receiver_other'), 400);
            }
            if($new->status == config('news.status.pending_publish')){
                if(!Auth::user()->hasRole('Secretary')){
                    return $this->sendError('Error.', 'You are don\'t permission', 405);
                }
                if(is_null($new->publish_user)){
                    $newData['publish_user'] = Auth::user()->id;
                    $newData['received_publish_at'] = Carbon::now();
                    $this->newsRepository->update(['hash_id' => $new->primary], $newData);
                    $news = $this->newsRepository->getFirstBy(['hash_id' => $new->primary], $this->selectAPIDetailNew, $this->relation_ship);
                    return $this->sendResponse($news,  trans('news::notices.news.new_has_been_receiver_publish'), 200);
                }
                return $this->sendError('Error.', trans('news::notices.news.new_has_been_receiver_other'), 400);
            }
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);

    }

    public function update(Request $request)
    {
        $news = $this->newsRepository->getFirstBy(['hash_id' => $request->hash_id]);
        if(!is_null($news)){
            if( $news->status == 1 && $news->editor_user != Auth::user()->id || $news->status == 2 && $news->publish_user != Auth::user()->id){
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            if( $news->status == 3 && !Auth::user()->hasRole('Secretary')){
                return $this->sendError('Error.', 'You are don\'t permission', 405);
            }
            $data = $request->only('content_news', 'category_primary', 'title_secondary', 'title_primary', 'description_secondary', 'description_primary', 'image', 'avatar_note',
                'format_type', 'author', 'publish_at', 'note', 'tags', 'category_secondary');
            if($news->is_return == config('news.status.is_return_editor') || $news->is_return == config('news.status.is_return')){
                $data['is_return'] = config('news.status.is_default');
            }
            $data['slug'] = $this->newsRepository->createSlug($request->title_primary, $news->id);
            $data['category_primary'] = (int)$request->category_primary;
            $data['featured'] = $request->input('featured', 0);
            $data['category_secondary'] = json_encode($request->category_secondary);
            $data['publish_at'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->publish_at)));
            $this->newsRepository->update(['id' => $news->id], $data);
            return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_update'), 200);
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);

    }

    public function updateHighLight(Request $request)
    {
        $new = $this->newsRepository->getFirstBy(['hash_id' => $request->hash_id]);
        if(!is_null($new)){
            $highlight = $this->newsAttributeRepository->getFirstBy(['news' => $new->hash_id], ['id', 'display']);
            $dataHighLight['display'] = $highlight->display == 1  ? 0 : 1;
            $this->newsAttributeRepository->update(['id' => $highlight->id], $dataHighLight);
            return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_update'), 200);
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);
    }

    public function updateFeatured(Request $request){
        $new = $this->newsRepository->getFirstBy(['hash_id' => $request->hash_id], ['id', 'featured']);
        if(!is_null($new)){
            $dataFeatured['featured'] = $new->featured == 1  ? 0 : 1;
            $this->newsRepository->update(['id' => $new->id], $dataFeatured);
            return $this->sendResponse('Success',  trans('news::notices.news.new_has_been_update'), 200);
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);
    }

    public function getNewsHighlightsPublish()
    {
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $home = Input::get('home');
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'limit' => trim($limit),
            'offset' => trim($offset),
            'home'  => $home
        );
        $result = $this->newsRepository->getNewsHighlightsPublish($filters);
        $newsList = $result->get();
        return $this->sendResponse($newsList->toArray(),  trans('news::notices.news.get_high_light_success'), 200);
    }

    public function getNewsPublishByCategory()
    {
        $slug = Input::get('category');
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $featured = Input::get('featured') ? Input::get('featured') : null;
        $offset = ($pageId - 1) * $limit;
        $category = $this->categoryRepository->getFirstBy(['slug' => $slug], ['id']);
        if(is_null($category)){
            return $this->sendError('Error', trans('news::notices.categories.category_not_found'));
        }
        $filters = array(
            'category'  =>  ($category->id),
            'limit' => trim($limit),
            'offset' => trim($offset),
            'featured'  => $featured
        );
        $validator = Validator::make($filters, [
            'category' => 'required',
        ],
            [
                'category.required' =>  'Missing parameter values'
            ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $result = $this->newsRepository->getNewsPublishByCategoryId($filters);
        $newsList = $result->get();
        return $this->sendResponse($newsList->toArray(),  trans('news::notices.news.get_news_success'), 200);
    }

    public function getNewsDetailPublish()
    {
        $slug = Input::get('id');
        $filters = array(
            'id'    =>  $slug
        );
        $validator = Validator::make($filters, [
            'id' => 'required',
        ],
            [
                'id.required' =>  'Missing parameter values'
            ]);
        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $result = $this->newsRepository->with(['categories'])->getNewsDetailPublish($filters);
        $new = $result->first();
        if(isset($new) && !is_null($new)){
            return $this->sendResponse($new->toArray(),  'Success', 200);
        }
        return $this->sendError('Error.', trans('news::notices.news.news_not_found'), 400);
    }

    public function getNewsFilterPublish()
    {
        $tags = Input::get('tags');
        $keyword = Input::get('keyword');
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'tags'    =>  $tags,
            'keyword' => $keyword,
            'limit' => trim($limit),
            'offset' => trim($offset),
        );
        $result = $this->newsRepository->getFilterPublish($filters);
        $new = $result->get();
        return $this->sendResponse($new->toArray(),  'Success', 200);
    }

}