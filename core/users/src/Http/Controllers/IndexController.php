<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 12:51 PM
 */

namespace Vtv\Users\Http\Controllers;

use Vtv\Users\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Hash;
use Vtv\Base\Http\Controllers\BaseController;
use Vtv\Users\Repositories\Interfaces\UserInterface;
use Auth;

class IndexController extends BaseController
{
    protected $userRepository;

    protected $limitRecord = 50;

    protected $selectAPIListUsers = ['id', 'email', 'name', 'username'];

    public function __construct(UserInterface $usersRepository)
    {
        $this->userRepository = $usersRepository;
    }

    public function index()
    {
        $keyword = Input::get('keyword');
        $filters = array(
            'keyword' => trim($keyword),
        );
        $users = $this->userRepository
                    ->select($this->selectAPIListUsers)
                    ->where(function ($que) use ($filters) {
                        if(isset($filters['keyword']) && !empty($filters['keyword'])){
                            $que->where('email', 'like', '%' . trim($filters['keyword']) . '%');
                            $que->orWhere('username', '=', trim($filters['keyword']));
                        };
                    })
                    ->paginate($this->limitRecord);
        return $this->sendResponse($users, 'Success');
    }

    /**
     * This function create user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'username' => 'required|string|unique:'.config('cms.database_table_name')['users'],
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'role'  =>  'required|string|exists:'.config('permission.table_names')['roles'] . ',name'
        ]);

        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first(), 422);
        }

        $request->merge(['password' => Hash::make($request->password)]);

        try{
            $user = $this->userRepository->getModel();
            $user->fill($request->all());
            $this->userRepository->createOrUpdate($user);
            $user = $this->userRepository->createOrUpdate($user);
            if($user){
                $role = Role::where('name', '=', $request->role)->first();
                $user->assignRole($role->name);
                return $this->sendResponse($user->toArray(), trans('users::notices.register_success'), 200);
            }
            return $this->sendError('Error.', trans('users::notices.unable_to_register_user'), 400);
        }
        catch(\Exception $e){
            return $this->sendError('Error.', trans('users::notices.unable_to_register_user'), 400);
        }
    }

    /**
     * This function get detail user by user id
     * @return \Illuminate\Http\Response
     */
    public function detailUser()
    {
        $userId = Input::get('user');
        $user = $this->userRepository->getFirstBy(['id' => $userId]);
        if($user){
            return $this->sendResponse($user->toArray(), 'Successfully', 200);
        }
        return $this->sendError('Error.', trans('users::notices.user_not_found'), 400);
    }

    public function checkToken(Request $request)
    {
        return $this->sendResponse([ 'user' => Auth::user(), 'roles' => Auth::user()->roles], 'Token check success');
    }

    public function update(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id'    =>  'required',
            'name' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return $this->sendError('Error.', $validator->errors()->first(), 422);
        }

        $user = $this->userRepository->getFirstBy(['id' => $request->id]);
        if(!is_null($user)){
            $data = $request->only('name');
            $this->userRepository->update(['id' => $request->id], $data);
            return $this->sendResponse('Success',  trans('users::notices.user_update_success'), 200);
        }
        return $this->sendError('Error.', trans('users::notices.user_not_found'), 400);
    }

}