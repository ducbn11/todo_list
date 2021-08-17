<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Auth;
use Log;
use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;

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
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function login(Request $request)
    {
        try {
            $data = $request->all();
            $this->validate($request, [
                'name' => 'required|min:6',
                'password' => 'required|min:6',
            ]);
            $loginData = [];
            $loginData['name'] = data_get($data, 'name', '');
            $loginData['password'] = data_get($data, 'password', '');

            $login = Auth::attempt($loginData);

            if($login) {
                return redirect()->route('todo_list.index');
            }

            return redirect()->back()->withInput()->with('message', "Login fail!");
        } catch (ValidationException $e) {
            Log::error("login ValidationException " . $e);
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (Exception $e) {
            Log::error("login Exception " . $e);
            return redirect()->back()->withInput()->with('message', "Login fail!");
        }
        
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
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function register(Request $request)
    {
        try {
            $data = $request->all();
            $this->validate($request, [
                'email' => 'required|email|unique:users',
                'name' => 'required|unique:users|min:6',
                'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'required|min:6',
            ]);
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
        } catch (ValidationException $e) {
            Log::error("register ValidationException " . $e);
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (Exception $e) {
            Log::error("register Exception " . $e);
            return redirect()->back()->withInput()->with('message', "Register fail!");
        }
    }
}