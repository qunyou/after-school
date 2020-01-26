# 國小課後社團報名系統

## 自訂安裝

    將 packages 資料夾放在網站根目錄
    在 composer.json 加上
    "autoload": {
        "psr-4": {
            ...
            "Onepoint\\AfterSchool\\": "packages/onepoint/after-school/src",
        },
        ...
    },

    執行
    composer dump-autoload

在 config/app.php 的 providers 加上

    Onepoint\AfterSchool\AfterSchoolServiceProvider::class,

## 資料庫加入新資料表

    php artisan migrate

## 修改 custom/default/backendConfig.php

    'navigation_item' => [
        ['title' => '學生', 'translation' => 'after-school::student.', 'icon' => 'fas fa-user-graduate', 'action' => '\Onepoint\AfterSchool\Controllers\StudentController@index'],
        ...
    ],

    // 權限項目
    'permissions' => [
        '學生' => ['controller' => '\Onepoint\AfterSchool\Controllers\StudentController', 'permission' => ['read' => '檢視', 'update' => '修改', 'create' => '新增', 'delete' => '刪除']],
        ...
    ],