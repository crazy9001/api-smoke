<?php

namespace Vtv\Media\Models;

use Eloquent;

class File extends Eloquent
{

    protected $attributes = [];

    /**
     * The model attribute's original state.
     *
     * @var array
     */
    protected $original = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = true;

    /**
     * The cache of the mutated attributes for each class.
     *
     * @var array
     */
    protected static $mutatorCache = [];

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['media_storage'];
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
       
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'id', 'folder_id');
    }

    /**
     * @return string
       
     */
    public function getDocumentTypeAttribute()
    {
        switch ($this->attributes['mime_type']) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
                $type = 'image';
                break;
            case 'video/mp4':
                $type = 'video';
                break;

            case 'application/pdf':
                $type = 'pdf';
                break;

            case 'application/excel':
            case 'application/x-excel':
            case 'application/x-msexcel':
                $type = 'excel';
                break;

            case 'youtube':
                $type = 'youtube';
                break;

            default:
                $type = 'document';
                break;
        }
        return $type;
    }

    /**
     * @return string
       
     */
    public function getHumanSizeAttribute()
    {
        return human_file_size($this->attributes['size']);
    }

    /**
     * @return string
       
     */
    public function getIconAttribute()
    {
        switch ($this->attributes['mime_type']) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
                $icon = 'file-image-o';
                break;
            case 'video/mp4':
                $icon = 'file-video-o';
                break;

            case 'application/pdf':
                $icon = 'file-pdf-o';
                break;

            case 'application/excel':
            case 'application/x-excel':
            case 'application/x-msexcel':
                $icon = 'file-excel-o';
                break;

            case 'youtube':
                $icon = 'youtube';
                break;

            default:
                $icon = 'file-text-o';
                break;
        }
        return $icon;
    }

    /**
     * @var array
       
     */
    public static $mimeTypes = [
        'otf' => 'application/x-font-otf',
        'ttf' => 'application/x-font-ttf',
        'woff' => 'application/x-font-woff',
        'swf' => 'application/x-shockwave-flash',
        'sql' => 'application/x-sql',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
        'mp3' => 'audio/mpeg',
        'm3u' => 'audio/x-mpegurl',
        'wav' => 'audio/x-wav',
        'bmp' => 'image/bmp',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'psd' => 'image/vnd.adobe.photoshop',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'htm' => 'text/html',
        'txt' => 'text/plain',
        '3gp' => 'video/3gpp',
        '3g2' => 'video/3gpp2',
        'h261' => 'video/h261',
        'h263' => 'video/h263',
        'h264' => 'video/h264',
        'mp4' => 'video/mp4',
        'mp4v' => 'video/mp4',
        'mpg4' => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'm4u' => 'video/vnd.mpegurl',
        'webm' => 'video/webm',
        'f4v' => 'video/x-f4v',
        'flv' => 'video/x-flv',
        'm4v' => 'video/x-m4v',
        'mkv' => 'video/x-matroska',
        'wmv' => 'video/x-ms-wmv',
        'avi' => 'video/x-msvideo',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];


    /**
       
     */
    public function __wakeup()
    {
        parent::boot();
    }
}
