<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:11 AM
 */
return [

    'database_table_name' => [
        'users' =>  'db_users',
        'categories'    =>  'db_categories',
        'news'  =>  'db_news',
        'news_attribute'    =>  'db_news_attribute',
        'menu_manager'  =>  'db_menu_manager',
        'media_folders' =>  'db_media_folders',
        'media_storage' =>  'db_media_storage',
        'sub_new_subcategories'  =>  'db_new_sub_category',
        'topic'    =>  'db_topic',
        'question'  =>  'db_question',
        'answer'    =>  'db_answer',
        'video_category'    =>  'db_video_category',
        'videos'    =>  'db_videos',
        'video_time_line'   =>  'db_video_time_line',
        'video_element' =>  'db_video_element',
        'video_seo' =>  'db_video_seo',
    ],
    'roles_default_system'  =>  [
        'secretary' =>  'Secretary',
        'editor'    =>  'Editor',
        'reporter'  =>  'Reporter'
    ],
    'permission_system' =>  [
        'list_user' => 'List User',
        'add_new_user' => 'Add User',
        'view_detail_user' => 'View User',
        'delete_user' => 'Delete User',
        'list_new' => 'List News',
        'add_new_news' => 'Add News',
        'edit_news' => 'Edit News',
        'delete_news' => 'Delete News',
        'list_categories' => 'List Categories',
        'add_new_category' => 'Add Categories',
        'edit_category' => 'Edit Categories',
        'delete_category' => 'Delete Categories',
        'list_menu' => 'List Menu',
        'add_new_menu' => 'Add Menu',
        'media_manager' =>  'Media Manager',
        'add_new_question'  =>  'Add Question',
        'add_new_answer'  =>  'Add Answer',
    ],
    'email_support' =>  [
        1   =>  'ngoctoi.it@gmail.com',
        2   =>  'phongvtvnews@gmail.com',
        3   =>  'phongvtvnews@gmail.com'
    ]

];