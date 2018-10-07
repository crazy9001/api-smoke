<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 1:45 PM
 */

namespace Vtv\Videos\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Vtv\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Vtv\Videos\Repositories\Interfaces\VideoElementInterface;
use Vtv\Videos\Repositories\Interfaces\VideoInterface;
use Validator;
use Vtv\Videos\Repositories\Interfaces\VideoSeoInterface;
use Vtv\Videos\Repositories\Interfaces\VideoTimelineInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IndexController extends BaseController
{

    protected $videoRepository;

    protected $videoElementRepository;

    protected $videoTimelineRepository;

    protected $videoSeoRepository;

    public function __construct(VideoInterface $videoRepository, VideoElementInterface $videoElementRepository, VideoTimelineInterface $videoTimelineRepository, VideoSeoInterface $videoSeoRepository)
    {
        $this->videoRepository = $videoRepository;
        $this->videoElementRepository = $videoElementRepository;
        $this->videoTimelineRepository = $videoTimelineRepository;
        $this->videoSeoRepository = $videoSeoRepository;
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'file_name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first(), 422);
        }

        // video
        $video = $this->videoRepository->getModel();
        $video->fill($request->all());
        $video->slug = $this->videoRepository->createSlug($request->title, null);
        $video->status = $request->status ? $request->status : 'DRAFT';
        $video->highlight = $request->highlight ? 1 : 0;
        // save video
        $video = $this->videoRepository->createOrUpdate($video);

        // video_element
        $videoElement = $this->videoElementRepository->getModel();
        $videoElement->id_video = $video->id;
        $videoElement->created_user = Auth::user()->id;
        // save video element
        $videoElement = $this->videoElementRepository->createOrUpdate($videoElement);

        // video time_line
        $videoTimeline = $this->videoTimelineRepository->getModel();
        $videoTimeline->id_video = $video->id;
        $videoTimeline->publish_at = $request->publish_at ? $request->publish_at : Carbon::now();
        $videoTimeline->time_created = Carbon::now();
        //save video time line
        $videoTimeline = $this->videoTimelineRepository->createOrUpdate($videoTimeline);

        //video seo

        $videoSeo = $this->videoSeoRepository->getModel();
        $videoSeo->id_video = $video->id;
        $videoSeo->meta_title = $request->meta_title;
        $videoSeo->meta_keyword = $request->meta_keyword;
        $videoSeo->meta_description = $request->meta_description;

        //save video seo
        $videoSeo = $this->videoSeoRepository->createOrUpdate($videoSeo);

        return $this->sendResponse($video->toArray(), 'Successfully');

    }

    public function getListVideosDraft()
    {
        $query = $this->videoRepository->getVideoDraft();
        $result = $query->paginate(50);
        return $this->sendResponse($result->toArray(), 'Successfully');
    }

    public function getListVideosPublish()
    {
        $query = $this->videoRepository->getVideoPublish();
        $result = $query->paginate(50);
        return $this->sendResponse($result->toArray(), 'Successfully');
    }



}