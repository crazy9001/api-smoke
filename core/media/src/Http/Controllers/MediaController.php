<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/21/2018
 * Time: 11:56 AM
 */

namespace Vtv\Media\Http\Controllers;
use Vtv\Base\Http\Controllers\BaseController;
use Vtv\Media\Repositories\Interfaces\FileInterface;
use Vtv\Media\Repositories\Interfaces\FolderInterface;
use Illuminate\Http\Request;
use Vtv\Media\Exceptions\MediaInvalidParent as MediaInvalidParent;

class MediaController extends BaseController
{
    /**
     * @var FileInterface
     */
    protected $fileRepository;

    /**
     * @var FolderInterface
     */
    protected $folderRepository;

    public function __construct(FileInterface $fileRepository, FolderInterface $folderRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
    }

    /**
     * @return array

     */
    public function getQuota()
    {
        return [
            'quota' => human_file_size($this->fileRepository->getQuota()),
            'used' => human_file_size($this->fileRepository->getSpaceUsed()),
            'percent' => $this->fileRepository->getPercentageUsed(),
        ];
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View

     */
    public function getGallery(Request $request)
    {
        $action = $request->input('action');
        $folderSlug = $request->input('folder');

        session()->forget('media_action');
        session()->put('media_action', $action);

        try {
            $contents = $this->getDirectory($folderSlug);
        } catch (MediaInvalidParent $e) {
            return redirect()->route('files.gallery.show')
                ->with('error_msg', trans('auth::feature.folder_not_exist'));
        }
        $response = [
            'contents'  =>  $contents,
            'filesystem'    =>  $this->fileRepository,
            'currentFolder' => $folderSlug
        ];
        return $this->sendResponse($response, 'Success');
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function getAjaxMediaFolder(Request $request)
    {
        $folderSlug = $request->input('folder');
        try {
            $contents = $this->getDirectory($folderSlug);
        } catch (MediaInvalidParent $e) {
            return ['error' => true, 'message' => trans('auth::feature.cannot_read_folder')];
        }

        $files = null;
        foreach ($contents['files'] as $file) {
            $files .= view('media::partials.file-row')
                ->with('file', $file)
                ->render();
        }
        $uplevel = view('media::partials.uplevel')
            ->with('folder', $contents['parentFolder'])
            ->render();
        $folders = null;
        if (count($contents['folders']) > 0) {
            foreach ($contents['folders'] as $new_folder) {
                $folders .= view('media::partials.folder-row')
                    ->with('folder', $new_folder)
                    ->render();
            }
        }
        return [
            'error' => false,
            'files' => $files,
            'uplevel' => $uplevel,
            'folders' => $folders,
            'currentFolder' => $contents['currentFolder']
        ];
    }

    /**
     * @param $folderSlug
     * @return array|\Illuminate\Http\RedirectResponse

     */
    private function getDirectory($folderSlug)
    {
        try {
            $contents = [];
            $folder = null;
            if (is_string($folderSlug) && $folderSlug !== null) {
                $folder = $this->folderRepository->getFirstBy(['slug' => $folderSlug]);
                if (!$folder) {
                    throw new MediaInvalidParent;
                }
                $folderId = $folder->id;
            } else {
                $folderId = 0;
            }
            // Get the folders
            $contents['folders'] = $this->folderRepository->getFolderByParentId($folderId);
            if (session('media_action') == 'attach_image' || session('media_action') == 'featured_image') {
                // Get all the files
                $contents['files'] = $this->fileRepository->getFilesByFolderId($folderId, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
            }elseif(session('media_action') == 'attach_video' || session('media_action') == 'featured_video'){
                // Get all the files
                $contents['files'] = $this->fileRepository->getFilesByFolderId($folderId, ['video/3gpp', 'video/mp4', 'video/webm', 'video/mpeg']);
            }else {
                // Get all the files
                $contents['files'] = $this->fileRepository->getFilesByFolderId($folderId);
            }
            // Get parent folder details
            if ($folderId == 0) {
                $contents['parentFolder'] = -1;
            } elseif ($folder->parent == 0) {
                $contents['parentFolder'] = null;
            } else {
                $contents['parentFolder'] = $folder->parentFolder()
                    ->first()->slug;
            }

            $contents['currentFolder'] = $folderSlug != null ? $folderSlug : 0;
            return $contents;
        } catch (MediaInvalidParent $e) {
            return redirect()->route('media.index')
                ->with('error_msg', trans('auth::feature.folder_not_exist'));
        }
    }
}