<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/23/2018
 * Time: 4:24 PM
 */

namespace Vtv\Media\Http\Controllers;

use Vtv\Base\Http\Controllers\BaseController;
use Vtv\Media\Http\Requests\FileRequest;
use Vtv\Media\Repositories\Interfaces\FileInterface;
use Vtv\Media\Repositories\Interfaces\FolderInterface;
use Vtv\Media\Services\UploadsManager;
use Exception;
use File;
use Illuminate\Http\Request;
use Image;
use Validator;
use Illuminate\Support\Facades\Auth;

class FileController extends BaseController
{
    /**
     * @var UploadsManager
     */
    protected $uploadManager;

    /**
     * @var FileInterface
     */
    protected $fileRepository;

    /**
     * @var FolderInterface
     */
    protected $folderRepository;

    /**
     * @param FileInterface $fileRepository
     * @param FolderInterface $folderRepository
     * @param UploadsManager $uploadManager
     */
    public function __construct(FileInterface $fileRepository, FolderInterface $folderRepository, UploadsManager $uploadManager)
    {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->uploadManager = $uploadManager;
    }

    /**
     * @param FileRequest $request
     * @return array
     * @throws \Throwable
     */
    public function postEdit(Request $request)
    {
        //return ($request->all());
        /*$validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first());
        }*/

        try {
            $folderId = $request->input('folder');
            $file = $this->fileRepository->getModel();

            $folder = $this->folderRepository->getFirstBy(['slug' => $folderId], ['id', 'slug']);
            if ($folder) {
                $folderId = $folder->id;
                $folderName = $folder->slug;
            } else {
                $folderId = 0;
                $folderName = null;
            }

            $fileUpload = $request->file('files')[0];

            $fileName = $this->fileRepository->createSlug(basename($fileUpload->getClientOriginalName(), $fileUpload->getClientOriginalExtension()), $fileUpload->getClientOriginalExtension(), $folderId);
            $path = str_finish($folderName, '/') . $fileName;

            $content = File::get($fileUpload->getRealPath());

            $this->uploadManager->saveFile($path, $content);

            if (is_image($this->uploadManager->fileMimeType($path))) {
                $thumb_size = explode('x', config('media.thumb-size'));
                $featured_size = explode('x', config('media.featured-size'));
                $hot_size = explode('x', config('media.hot-size'));
                $detail_size = explode('x', config('media.detail-size'));

                $dir_thumb_size = config('media.thumb-size');
                if(!File::exists($this->uploadManager->uploadPath(str_finish($dir_thumb_size, '/')))) {
                    File::makeDirectory($this->uploadManager->uploadPath(str_finish($dir_thumb_size, '/')), $mode = 0777, true, true);
                }
                $dir_featured_size = config('media.featured-size');
                if(!File::exists($this->uploadManager->uploadPath(str_finish($dir_featured_size, '/')))) {
                    File::makeDirectory($this->uploadManager->uploadPath(str_finish($dir_featured_size, '/')), $mode = 0777, true, true);
                }
                $dir_hot_size = config('media.hot-size');
                if(!File::exists($this->uploadManager->uploadPath(str_finish($dir_hot_size, '/')))) {
                    File::makeDirectory($this->uploadManager->uploadPath(str_finish($dir_hot_size, '/')), $mode = 0777, true, true);
                }
                $dir_detail_size = config('media.detail-size');
                if(!File::exists($this->uploadManager->uploadPath(str_finish($dir_detail_size, '/')))) {
                    File::makeDirectory($this->uploadManager->uploadPath(str_finish($dir_detail_size, '/')), $mode = 0777, true, true);
                }
                if(!File::exists($this->uploadManager->uploadPath(str_finish($folderName, '/')))) {
                    File::makeDirectory($this->uploadManager->uploadPath(str_finish($folderName, '/')), $mode = 0777, true, true);
                }
                Image::make($fileUpload)->fit($thumb_size[0], $thumb_size[1])->save($this->uploadManager->uploadPath(str_finish($dir_thumb_size, '/')) . File::name($fileName) . '.' . $fileUpload->getClientOriginalExtension());
                Image::make($fileUpload)->fit($featured_size[0], $featured_size[1])->save($this->uploadManager->uploadPath(str_finish($dir_featured_size, '/')) . File::name($fileName) . '.' . $fileUpload->getClientOriginalExtension());
                Image::make($fileUpload)->fit($hot_size[0], $hot_size[1])->save($this->uploadManager->uploadPath(str_finish($dir_hot_size, '/')) . File::name($fileName) . '.' . $fileUpload->getClientOriginalExtension());
                Image::make($fileUpload)->fit($detail_size[0], $detail_size[1])->save($this->uploadManager->uploadPath(str_finish($dir_detail_size, '/')) . File::name($fileName) . '.' . $fileUpload->getClientOriginalExtension());
            }

            $data = $this->uploadManager->fileDetails($path);

            if (empty($data['mime_type'])) {
                return $this->sendError('Error', 'can_not_detect_file_type', 400);
            }

            $file->name = $this->fileRepository->createName(File::name($fileUpload->getClientOriginalName()), $folderId);
            $file->public_url = $data['url'];
            $file->size = $data['size'];
            $file->mime_type = $data['mime_type'];
            $file->file_name = $fileName;
            $file->type = $data['mime_type'];
            $file->extension = $data['extension'];
            $file->folder_id = $folderId;
            $file->user_id = Auth::user()->id;
            $file = $this->fileRepository->createOrUpdate($file);
        } catch (Exception $ex) {
            return [
                'error' => true,
                'message' => $ex->getMessage()
            ];
        }
        return $this->sendResponse($file, 'Success');
    }

    public function fileDetail(Request $request)
    {
        $id = $request->input('file');
        $file = $this->fileRepository->getFirstBy(['id' => $id]);
        if($file){
            return $this->sendResponse($file->toArray(), 'Successfully', 200);
        }
        return $this->sendError('Error.', 'File không tồn tại', 400);
    }

}