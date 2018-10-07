<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 3:36 PM
 */

namespace Vtv\Question\Http\Controllers;


use Vtv\Base\Http\Controllers\BaseController;
use Vtv\Base\Http\Requests\Request;
use Vtv\Question\Repositories\Interfaces\TopicInterface;

class TopicController extends BaseController
{
    protected $topicRepository;

    public function __construct(TopicInterface $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }
    public function store(Request $request)
    {
        dd($request->all());
    }
}