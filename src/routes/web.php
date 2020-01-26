<?php
Route::namespace('Onepoint\AfterSchool\Controllers')->prefix(config('dashboard.uri'))->middleware(['web', 'auth'])->group(function () {

    // 學生
    Route::prefix('student')->group(function () {
        Route::get('index', 'StudentController@index');
        Route::put('index', 'StudentController@putIndex');
        Route::get('update', 'StudentController@update');
        Route::put('update', 'StudentController@putUpdate');
        Route::get('duplicate', 'StudentController@duplicate');
        Route::put('duplicate', 'StudentController@putDuplicate');
        Route::get('detail', 'StudentController@detail');
        Route::get('delete', 'StudentController@delete');

        // 匯入
        Route::get('import', 'StudentController@import');
        Route::put('import', 'StudentController@putImport');
    });
});