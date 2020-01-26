<?php

namespace Onepoint\AfterSchool\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 學生
 * php artisan make:migration create_students_table --create=students
 */
class Student extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        // 'created_at',
        // 'updated_at',
        // 'deleted_at',

        // 舊版本
        'old_version',

        // 版本原始id
        'origin_id',

        // 記錄更新人員
        'update_user_id',

        //狀態 enum('啟用', '停用')
        'status',

        // 排序
        'sort',

        // 備註
        'note',

        // 學生姓名
        'student_name',

        // 班級
        'student_class',

        // 座號
        'seat_number',

        // 學號(帳號)
        'school_student_id',

        // 密碼
        'password',

        // 電子郵件
        'email',

        // 學制(國小,國中,高中,高職,大學)(必填)
        'educational_system',

        // 畢業年度(西元)(必填)
        'graduation_year',

        // 科系(無科系請填普通科)(必填)
        'department',

        // 入學年度(西元)(必填)
        'school_year',

        // 性別(必填)
        'gender',

        // 身份證字號
        'id_number',

        // 出生年月日(YYYY-mm-dd)
        'birthday',

        // 電話
        'tel',

        // 縣市
        'city',

        // 地區
        'area',

        // 地址
        'address',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 人員關聯
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'update_user_id');
    }
}
