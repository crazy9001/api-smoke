<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:00 PM
 */

namespace Vtv\News\Repositories\Eloquent;

use Carbon\Carbon;
use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\News\Repositories\Interfaces\NewInterface;

class DbNewsRepository extends RepositoriesAbstract implements NewInterface
{
    protected  $select = ['hash_id as id', 'title_primary as title', 'slug as slug', 'description_primary as description', 'image as image', 'author as author', 'tags as tags', 'category_primary', 'publish_at as publish_at'];
    protected $relationship = ['created_by', 'categories'];
    /**
     * @return $this
     */
    public function withTrashed() {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    /**
     * @return $this
     */
    public function onlyTrashed() {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    public function createSlug($name, $id)
    {
        $slug = str_slug($name);
        $index = 1;
        $baseSlug = $slug;
        while ($this->model->whereSlug($slug)->where('id', '!=', $id)->count() > 0) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }

    public function getNewsHighlightsPublish($filter = array())
    {
        $timeNow = Carbon::now('Asia/Ho_Chi_Minh');
        $tableNames = config('cms.database_table_name');
        $query = $this->getModel()
            ->join($tableNames['news_attribute'] . ' as new_attribute', 'new_attribute.news', '=', 'hash_id')
            ->with('categories')
            ->where(function ($que) use ($filter, $timeNow) {
                $que->where('new_attribute.display', '=', 1);
                $que->where('status', '=', config('news.status.publish'));
                $que->where('is_return', '=', config('news.status.is_default'));
                $que->where('deleted_at', '=', null);
                $que->where('publish_at', '<=', $timeNow);
            })
            ->select($this->select)
            ->limit($filter['limit'])
            ->offset($filter['offset'])
            ->orderBy((isset($sortInfo['column']) && !empty($sortInfo['column'])) ? $sortInfo['column'] : 'new_attribute.updated_at', (isset($sortInfo['order']) && !empty($sortInfo['order'])) ? $sortInfo['order'] : 'desc' );;
            return $query;
    }

    public function getNewsPublishByCategoryId($filter = array()){
        $timeNow = Carbon::now('Asia/Ho_Chi_Minh');
        $tableNames = config('cms.database_table_name');
        $query = $this->getModel()
            ->where('publish_at', '<=', $timeNow)
            ->where('status', '=', config('news.status.publish'))
            ->where('is_return', '=', config('news.status.is_default'))
            ->where('deleted_at', '=', null)
            ->where(function ($que) use ($filter) {
                $que->where('category_primary', '=', $filter['category']);
                if(isset($filter['featured']) && !empty($filter['featured'])){
                    $que->where('featured', '=', 1);
                }
            })
            ->orWhereHas('sub_categories', function ($query) use ($filter) {
                $query->where('category_id', '=', $filter['category']);
            })
            ->with('categories', 'sub_categories')
            ->select('id', 'hash_id as hash_id', 'title_primary as title', 'slug as slug', 'description_primary as description', 'image as image', 'author as author', 'tags as tags', 'category_primary', 'publish_at as publish_at')
            ->limit($filter['limit'])
            ->offset($filter['offset'])
            ->orderBy((isset($sortInfo['column']) && !empty($sortInfo['column'])) ? $sortInfo['column'] : $tableNames['news'].'.publish_at', (isset($sortInfo['order']) && !empty($sortInfo['order'])) ? $sortInfo['order'] : 'desc' );;
        return $query;
    }

    public function getNewsDetailPublish($filter = array())
    {
        $query = $this->getModel()
                ->where('slug', '=', $filter['id'])
                ->select('title_primary as title', 'slug as slug', 'title_secondary as sub_title', 'category_primary', 'category_secondary as sub_category', 'description_primary as description', 'description_secondary as sub_description', 'content_news as content', 'image as image', 'avatar_note as alt_image', 'author as author', 'format_type as type', 'views as views', 'tags as tags', 'publish_at as publishedAt', 'user_id as user_id')
                ->with($this->relationship);
        return $query;
    }

    public function getFilterPublish($filters = array())
    {
        $tableNames = config('cms.database_table_name');
        $query = $this->getModel()
            ->where(function ($que) use ($filters, $tableNames) {
                $que->where('status', '=', config('news.status.publish'));
                $que->where('is_return', '=', config('news.status.is_default'));
                $que->where('deleted_at', '=', null);
                if(isset($filters['tags']) && !empty($filters['tags'])){
                    $que->where('tags', '=', $filters['tags']);
                }
                if(isset($filters['keyword']) && !empty($filters['keyword'])){
                    $que->where($tableNames['news'] . '.title_primary', 'like', '%' . trim($filters['keyword']) . '%');
                    $que->orWhere($tableNames['news'] . '.description_primary', 'like', '%' . trim($filters['keyword']) . '%');
                    $que->orWhere($tableNames['news'] . '.content_news', 'like', '%' . trim($filters['keyword']) . '%');
                }
            })
            ->with('categories')
            ->select('hash_id as id', 'title_primary as title', 'slug as slug', 'description_primary as description', 'content_news as content', 'image as image', 'author as author', 'tags as tags', 'category_primary')
            ->limit($filters['limit'])
            ->offset($filters['offset'])
            ->orderBy((isset($sortInfo['column']) && !empty($sortInfo['column'])) ? $sortInfo['column'] : $tableNames['news'].'.published_at', (isset($sortInfo['order']) && !empty($sortInfo['order'])) ? $sortInfo['order'] : 'desc' );;
        return $query;
    }

}