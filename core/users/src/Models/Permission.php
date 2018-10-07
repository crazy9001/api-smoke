<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 1:40 PM
 */

namespace Vtv\Users\Models;


class Permission extends \Spatie\Permission\Models\Permission
{
    public static function defaultPermissions()
    {
        return [
            'List User',
            'Add User',
            'View User',
            'Delete User',
            'List News',
            'Add News',
            'Edit News',
            'Delete News',
            'List Categories',
            'Add Categories',
            'Edit Categories',
            'Delete Categories',
            'Media Manager',
            'List Menu',
            'Add Menu',
            'Add Question',
            'Add Answer'
        ];
    }
}