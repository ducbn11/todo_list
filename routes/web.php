<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', 'AuthController@viewLogin')->name('view_login');
    Route::post('/login', 'AuthController@login')->name('post_login');

    Route::get('/register', 'AuthController@viewRegister')->name('view_register');
    Route::post('/register', 'AuthController@register')->name('post_register');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', 'AuthController@logout')->name('logout');
    Route::get('/todo_list', 'TodoListController@index')->name('todo_list.index');
    Route::get('/export_todo_list', 'TodoListController@exportTodoList')->name('todo_list.export');
    Route::post('/update_todo_status', 'TodoListController@updateTodoStatus')->name('todo_list.update_status');
    Route::get('/todo_item/{id?}', 'TodoListController@todoItem')->name('todo_list.todo_item');
    Route::post('/save_todo_item', 'TodoListController@saveTodoItem')->name('todo_list.save_todo_item');
    Route::get('/delete_todo_item/{id?}', 'TodoListController@deleteTodoItem')->name('todo_list.delete_todo_item');
    Route::get('/validate_save_todo_item', 'TodoListController@validateSaveTodoItem')->name('todo_list.validate_save_todo_item');
});
