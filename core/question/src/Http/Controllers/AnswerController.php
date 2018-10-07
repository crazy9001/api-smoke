<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 3:37 PM
 */

namespace Vtv\Question\Http\Controllers;


use Vtv\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Vtv\Question\Repositories\Interfaces\AnswerInterface;
use Validator;

class AnswerController extends BaseController
{
    protected $answerRepository;

    public function __construct(AnswerInterface $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    public function store(Request $request)
    {
        $input = $request->only('question_id', 'content');
        $validator = Validator::make($input, [
            'question_id' => 'required',
            'content' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $answer = $this->answerRepository->getModel();
        $answer->fill($request->all());
        $answer->status = isset($request->status) && !empty($request->status) ? $request->status : 1;
        $answer = $this->answerRepository->createOrUpdate($answer);
        return $this->sendResponse($answer->toArray(), 'Successfully');
    }


}