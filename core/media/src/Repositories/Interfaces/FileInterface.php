<?php

namespace Vtv\Media\Repositories\Interfaces;

use Vtv\Base\Repositories\Interfaces\RepositoryInterface;

interface FileInterface extends RepositoryInterface
{
    /**
     * @return mixed
       
     */
    public function getSpaceUsed();

    /**
     * @return mixed
     */
    public function getSpaceLeft();

    /**
     * @return mixed
       
     */
    public function getQuota();

    /**
     * @return mixed
       
     */
    public function getPercentageUsed();

    /**
     * @param $name
     * @param $folder
       
     */
    public function createName($name, $folder);

    /**
     * @param $name
     * @param $extension
     * @param $folder
       
     */
    public function createSlug($name, $extension, $folder);

    /**
     * @param $folder_id
     * @param array $type
     * @return mixed
       
     */
    public function getFilesByFolderId($folder_id, $type = []);
}
