<?php

namespace Vtv\Media\Repositories\Interfaces;

use Vtv\Base\Repositories\Interfaces\RepositoryInterface;

interface FolderInterface extends RepositoryInterface
{

    /**
     * @param $folderId

     */
    public function getFolderByParentId($folderId);

    /**
     * @param $name
     */
    public function createSlug($name);

    /**
     * @param $name
     * @param $parent
     */
    public function createName($name, $parent);
}
