<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController as Account;
use App\Http\Controllers\TaskController as Task;

Route::get('/', function () {
    return "All right reserved to Mohammad Abir Mahmud\n";
});

Route::group(['prefix' => '/v1'], function () {
    Route::post('/signup',[Account::class,'postSignupAction']);
    Route::post('/login',[Account::class,'postLoginAction']);

    Route::group(['middleware'=>[ 'appToken' ]],function () {
        Route::get('/get-user-info',[Account::class,'getUserInfoAction']);
        Route::post('/update-user',[Account::class,'postUpdateUserAction']);
        Route::post('/create-task',[Task::class,'postCreateTaskAction']);
        Route::post('/update-task',[Task::class,'postUpdateTaskAction']);
        Route::post('/update-task-status',[Task::class,'postUpdateTaskStatusAction']);
        Route::get('/get-task-status-report',[Task::class,'getTaskStatusReportAction']);
        Route::post('/delete-task',[Task::class,'postDeleteTaskAction']);
        Route::get('/get-task-list',[Task::class,'getTaskList']);
        Route::get('/get-task-info/{taskId}',[Task::class,'getTaskInfoById']);
        Route::get('/search-task/{keyword}',[Task::class,'getSearchTask']);
        Route::post('/delete-task',[Task::class,'postDeleteTaskAction']);
    });
});
