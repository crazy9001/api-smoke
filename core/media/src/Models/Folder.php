<?php

namespace Vtv\Media\Models;

use Eloquent;

class Folder extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['media_folders'];
    }
    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    public $reservedNames = [
        'shared',
        'share',
        'shares',
        'type',
        'avatars'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Toi Nguyen
     */
    public function files()
    {
        return $this->hasMany(File::class, 'folder_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Toi Nguyen
     */
    public function parentFolder()
    {
        return $this->hasOne(Folder::class, 'id', 'parent');
    }



    /**
     * @author Toi Nguyen
     */
    public function __wakeup()
    {
        parent::boot();
    }
}
