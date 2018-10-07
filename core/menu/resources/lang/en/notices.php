<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/13/2018
 * Time: 2:55 PM
 */
return [

    'validate'  =>  [
        'name_required' =>  'Please enter menu name',
        'name_unique'   =>  'Menu has been exists',
        'link_required' =>  'Please enter link menu',
        'link_type_required'    =>  'Please select link type',
        'blank_type_required'   =>  'Please enter blank type',
        'category_required' =>  'Please select category',
        'category_exists' =>  'Category don\'t exists',
    ],
    'result'    =>  [
        'get_menu_success'  =>  'Get menu success'
    ]

];