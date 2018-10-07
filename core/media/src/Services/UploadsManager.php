<?php
namespace Vtv\Media\Services;

use Vtv\Media\Models\File;
use Storage;
use File as Filesystem;
use Carbon\Carbon;

class UploadsManager
{
    /**
     * @var mixed
     */
    protected $disk;
    /**
     * @var mixed
     */
    protected $mimeDetect;

    /**
     */
    public function __construct()
    {
        $this->disk = Storage::disk(config('filesystems.default'));

    }

    /**
     * Return files and directories within a folder
     *
     * @param string $folder
     * @return array of [
     *    'folder' => 'path to current folder',
     *    'folderName' => 'name of just current folder',
     *    'breadCrumbs' => breadcrumb array of [ $path => $foldername ]
     *    'folders' => array of [ $path => $foldername] of each subfolder
     *    'files' => array of file details on each file in folder
     * ]
     */
    public function folderInfo($folder)
    {
        $folder = $this->cleanFolder($folder);

        $breadcrumbs = $this->breadcrumbs($folder);
        $slice = array_slice($breadcrumbs, -1);
        $folderName = current($slice);
        $breadcrumbs = array_slice($breadcrumbs, 0, -1);

        $sub_folders = [];
        foreach (array_unique($this->disk->directories($folder)) as $sub_folder) {
            $sub_folders[DIRECTORY_SEPARATOR . $sub_folder] = basename($sub_folder);
        }

        $files = [];
        foreach ($this->disk->files($folder) as $path) {
            $files[] = $this->fileDetails($path);
        }

        return compact(
            'folder',
            'folderName',
            'breadcrumbs',
            'sub_folders',
            'files'
        );
    }

    /**
     * Sanitize the folder name
     *
     * @param $folder
     * @return string

     */
    protected function cleanFolder($folder)
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $folder), DIRECTORY_SEPARATOR);
    }

    /**
     * Return breadcrumbs to current folder
     *
     * @param $folder
     * @return array
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, DIRECTORY_SEPARATOR);
        $crumbs = [DIRECTORY_SEPARATOR => 'root'];

        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode(DIRECTORY_SEPARATOR, $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= DIRECTORY_SEPARATOR . $folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

    /**
     * Return an array of file details for a file
     *
     * @param $path
     * @return array
     */
    public function fileDetails($path)
    {
        return [
            'filename' => basename($path),
            'url' => $this->disk->url($path),
            'mime_type' => $this->fileMimeType($path),
            'size' => $this->fileSize($path),
            'modified' => $this->fileModified($path),
            'extension' =>  $this->fileExtension($path)
        ];
    }

    public function fileExtension($path)
    {
        return  Filesystem::extension($path);
    }

    /**
     * Return the full web path to a file
     *
     * @param $path
     * @return string
     */
    public function uploadPath($path)
    {
        return rtrim(config('filesystems.path'), '/') . '/' . ltrim($path, '/');
        //return $this->disk->url($path);
    }

    /**
     * Return the mime type
     *
     * @param $path
     * @return mixed|null|string
     */
    public function fileMimeType($path)
    {
        return array_get(File::$mimeTypes, Filesystem::extension($path));
    }

    /**
     * Return the file size
     *
     * @param $path
     * @return int
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * Return the last modified time
     *
     * @param $path
     * @return string
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp(
            $this->disk->lastModified($path)
        );
    }

    /**
     * Create a new directory
     *
     * @param $folder
     * @return bool|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        if ($this->disk->exists($folder)) {
            return trans('media::media.folder_exists', ['folder' => $folder]);
        }

        return $this->disk->makeDirectory($folder);
    }

    /**
     * Delete a directory
     *
     * @param $folder
     * @return bool|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        $filesFolders = array_merge(
            $this->disk->directories($folder),
            $this->disk->files($folder)
        );
        if (!empty($filesFolders)) {
            return trans('media::media.directory_must_empty');
        }

        return $this->disk->deleteDirectory($folder);
    }

    /**
     * Delete a file
     *
     * @param $path
     * @return bool|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);

        if (!$this->disk->exists($path)) {
            info(trans('media::media.file_not_exists'));
            return trans('media::media.file_not_exists');
        }

        if (is_image($this->fileMimeType($path))) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $thumb = str_replace($filename, $filename . '-' . config('media.thumb-size'), $path);
            $featured = str_replace($filename, $filename . '-' . config('media.featured-size'), $path);

            return $this->disk->delete([$path, $thumb, $featured]);
        } else {
            return $this->disk->delete([$path]);
        }
    }

    /**
     * Save a file
     *
     * @param $path
     * @param $content
     * @return bool|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);

        return $this->disk->put($path, $content);
    }
}
