<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:02 PM
 */

namespace Vtv\News\Models;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vtv\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use SoftDeletes;

    protected $table;

    public function __construct()
    {
        $this->table = config('cms.database_table_name')['news'];
    }

    protected $hidden = ['id'];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title_primary', 'title_secondary', 'category_primary', 'description_secondary', 'description_primary', 'content_news', 'image',
        'avatar_note', 'author', 'format_type', 'note', 'tags', 'user_id', 'slug', 'sended_editor_at', 'received_editor_at', 'sended_publish_at', 'received_publish_at',
        'published_at', 'status', 'publish_at', 'hash_id', 'editor_user', 'publish_user', 'is_return', 'featured'];

    /**
     * function relationship user table get created user
     * @return $this
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select(["id", "name"]);
    }

    /**
     * function relationship user table get editor user
     * @return $this
     */
    public function editor_by()
    {
        return $this->belongsTo(User::class, 'editor_user', 'id')->select(['id', "name"]);
    }

    /**
     * function relationship user table get publish user
     * @return $this
     */
    public function published_by()
    {
        return $this->belongsTo(User::class, 'publish_user', 'id')->select(['id',"name"]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsTo(Categories::class, 'category_primary', 'id')->select(['id', 'name', 'slug']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function highlight()
    {
        return $this->belongsTo(NewsAttribute::class, 'primary', 'news')->select(['news', 'display']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sub_categories()
    {
        $tableNames = config('cms.database_table_name');
        return $this->belongsToMany(Categories::class, $tableNames['sub_new_subcategories'], 'new_id', 'category_id')->select(['name', 'slug'])->withTimestamps();
    }




}