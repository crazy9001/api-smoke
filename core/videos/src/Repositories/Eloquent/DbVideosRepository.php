<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 3:23 PM
 */

namespace Vtv\Videos\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\Videos\Repositories\Interfaces\VideoInterface;
use DB;

class DbVideosRepository  extends RepositoriesAbstract implements VideoInterface
{
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

    public function getVideoDraft()
    {
        $tableNames = config('cms.database_table_name');
        $query = $this->getModel()
                ->join($tableNames['video_element'] . ' as element_video', 'element_video.id_video', '=', 'db_videos.id')
                ->where(function ($que) {
                    $que->where('db_videos.status', '=', 'DRAFT');
                    $que->where('element_video.created_user', '=', Auth::user()->id);
                })
                ->with(['createdUser', 'editorUser', 'publishUser', 'seoInformation']);
        return $query;
    }

    public function getVideoPublish()
    {
        $tableNames = config('cms.database_table_name');
        $query = $this->getModel()
            ->join($tableNames['video_element'] . ' as element_video', 'element_video.id_video', '=', 'db_videos.id')
            ->where(function ($que) {
                $que->where('db_videos.status', '=', 'PUBLISH');
            })
            ->with(['createdUser', 'editorUser', 'publishUser', 'seoInformation']);
        return $query;
    }

}