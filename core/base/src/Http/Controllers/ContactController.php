<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 9/5/2018
 * Time: 11:45 AM
 */

namespace Vtv\Base\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Vtv\Base\Services\SendMailServices;

class ContactController extends BaseController
{
    protected $sendMailServices;

    public function __construct(SendMailServices $sendMailServices)
    {
        $this->sendMailServices = $sendMailServices;
    }

    public function sendMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'email' =>  'required|email',
            'phone' =>  'required',
            'title' =>  'required',
            'type'  =>  'required',
            'content'   =>  'required'
        ],[
            'name.required' =>  'Vui lòng nhập họ tên',
            'email.required'    =>  'Vui lòng nhập email',
            'email.email'   =>  'Định dạng email không chính xác',
            'phone.required'    =>  'Vui lòng nhập số điện thoại',
            'title.required' =>  'Vui lòng nhập tiêu đề email',
            'type.required' =>  'Vui lòng chọn thể loại liên hệ',
            'content.required'  =>  'Vui lòng nhập nội dung liên hệ'
        ]);

        if( $validator->fails() ){
            return $this->sendError('Error.', $validator->errors()->first());
        }
        $type = (int)$request->type;
        $to = config('cms.email_support.'. $type);
        return $sendMail = $this->sendMailServices->send($to, $request->title, $request->all(), 'email.contact');
    }
}