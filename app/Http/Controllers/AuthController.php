<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Log;
use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * get view login
     *
     * @return \Illuminate\View\View
     */
    public function viewLogin()
    {
        return view('login.index');
    }

    /**
     * handle login
     *
     * @param App\Http\Requests\LoginRequest
     * @return \Illuminate\View\View
     */
    public function login(LoginRequest $request)
    {
        $data = $request->all();
        $loginData = [];
        $loginData['name'] = data_get($data, 'name', '');
        $loginData['password'] = data_get($data, 'password', '');

        $login = Auth::attempt($loginData);

        if($login) {
            return redirect()->route('todo_list.index');
        }

        return redirect()->back()->withInput()->with('message', "Login fail!");
    }

    /**
     * handle logout
     *
     * @return \Illuminate\View\View
     */
    public function logout()
    {
        Auth::logout();
        return view('login.index');
    }

    /**
     * get view register
     *
     * @return \Illuminate\View\View
     */
    public function viewRegister()
    {
        return view('register.index');
    }


    /**
     * register
     *
     * @param App\Http\Requests\RegisterRequest
     * @return \Illuminate\View\View
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $dataSave = [];
        $dataSave['email'] = data_get($data, 'email', '');
        $dataSave['name'] = data_get($data, 'name', '');
        $dataSave['password'] = bcrypt(data_get($data, 'password', ''));
        $user = $this->userRepository->create($dataSave);

        if($user) {
            Auth::login($user);
            return redirect()->route('todo_list.index');
        }

        return redirect()->back()->withInput()->with('message', "Register fail!");
    }
}