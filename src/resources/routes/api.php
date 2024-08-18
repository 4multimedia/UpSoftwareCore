<?php

use Illuminate\Support\Facades\Route;

Route::prefix('media')->namespace('Media')->group(function () {
    Route::get('/', 'CategoryController@index')->name('index');
});
