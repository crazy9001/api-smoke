<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 3:09 PM
 */

namespace Vtv\Question\Http\Controllers;


use Vtv\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Vtv\Question\Repositories\Interfaces\AnswerInterface;
use Vtv\Question\Repositories\Interfaces\QuestionInterface;
use Vtv\Question\Repositories\Interfaces\TopicInterface;
use Validator;
use Illuminate\Support\Facades\Input;

class QuestionController extends BaseController
{
    protected $topicRepository;
    protected $questionRepository;
    protected $answerRepository;

    public function __construct(TopicInterface $topicRepository, QuestionInterface $questionRepository, AnswerInterface $answerRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->questionRepository = $questionRepository;
        $this->answerRepository = $answerRepository;
    }
    public function store(Request $request)
    {
        $input = $request->only('name', 'phone', 'email', 'content', 'topic_id', 'status');
        $validator = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required',
            'email' =>  'required',
            'content'   =>  'required',
            'topic_id'  =>  'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $question = $this->questionRepository->getModel();
        $question->fill($request->all());
        $question->status = isset($request->status) && !empty($request->status) ? $request->status : 1;
        $question = $this->questionRepository->createOrUpdate($question);
        return $this->sendResponse($question->toArray(), 'Successfully');
    }

    public function publicListQuestion()
    {
        $limit = Input::get('limit') ? Input::get('limit') : 20;
        $pageId = Input::get('pageId') ? Input::get('pageId') : 1;
        $offset = ($pageId - 1) * $limit;
        $filters = array(
            'limit' => trim($limit),
            'offset' => trim($offset),
        );
        $result = $this->questionRepository->getPublicListQuestion($filters);
        $listQuestion = $result->get();
        return $this->sendResponse($listQuestion->toArray(), 'Success', 200);
    }

}