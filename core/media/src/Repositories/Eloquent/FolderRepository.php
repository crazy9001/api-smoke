<?php
namespace Vtv\Media\Repositories\Eloquent;

use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\Media\Repositories\Interfaces\FolderInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class FileSystemRepository
 * @package Botble\Media
  * @author Toi Nguyen
 */
class FolderRepository extends RepositoriesAbstract implements FolderInterface
{

    /**
     * @param $folderId
     * @return mixed
     * @author Toi Nguyen
     */
    public function getFolderByParentId($folderId)
    {
        return $this->model->where('parent', '=', $folderId)
            ->where(function ($query) {
                $query->orWhere('user_id', '=', Auth::user()->id)
                    ->orWhere('user_id', '=', 0);
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param $name
     * @return mixed
     * @author Toi Nguyen
     */
    public function createSlug($name)
    {
        $slug = str_slug($name);
        $index = 1;
        $baseSlug = $slug;
        while ($this->model->whereSlug($slug)->count() > 0) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }

    /**
     * @param $name
     * @param $parent
     * @return mixed
     * @author Toi Nguyen
     */
    public function createName($name, $parent)
    {
        $newName = $name;
        $index = 1;
        $baseSlug = $newName;
        while ($this->model->whereUserId(Auth::user()->id)
                ->whereName($newName)
                ->whereParent($parent)
                ->count() > 0) {
            $newName = $baseSlug . '-' . $index++;
        }

        return $newName;
    }
}
