<?php
/**
 * Created by PhpStorm.
 * User: PTG
 * Date: 8/24/2018
 * Time: 11:06 PM
 */

namespace Vtv\News\Http\Controllers;

use Carbon\Carbon;
use Vtv\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Input;

class ProgramController extends BaseController
{

    public function index()
    {
        $channel = Input::get('channel') ? Input::get('channel') : 1;
        $date = Input::get('date') ? Input::get('date') : date('d/m/Y', strtotime(Carbon::now()));
        $data = file_get_contents('http://vtvapi1.cnnd.vn/services/programschedules.ashx?channel=' . $channel . '&date='.  $date . '&seckey=111aaa7890zz');
        $data = json_decode($data);
        return $this->sendResponse($data, 'Success');
    }

    public function simple_curl($url)
    {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $content = curl_exec ($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close ($ch);
        return $contentType;
    }
}