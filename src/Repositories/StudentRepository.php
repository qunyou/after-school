<?php

namespace Onepoint\AfterSchool\Repositories;

use Hash;
use Illuminate\Validation\Rule;
use Onepoint\Dashboard\Services\BaseService;
use Onepoint\Dashboard\Repositories\BaseRepository;
use Onepoint\AfterSchool\Entities\Student;

/**
 * 學生
 */
class StudentRepository extends BaseRepository
{
    /**
     * 建構子
     */
    function __construct()
    {
        parent::__construct(new Student);
    }

    /**
     * 權限
     */
    public function permissions()
    {
        return $this->model;
    }

    /**
     * 列表
     */
    public function getList($id = 0, $paginate = 0)
    {
        $query = $this->permissions();
        $student_name = request('student_name', '');
        $seat_number = request('seat_number', '');
        $school_student_id = request('school_student_id', '');
        if (!empty($student_name)) {
            $query = $query->where('student_name', 'like', '%' . $student_name . '%');
        }
        if (!empty($seat_number)) {
            $query = $query->where('seat_number', 'like', '%' . $seat_number . '%');
        }
        if (!empty($school_student_id)) {
            $query = $query->where('school_student_id', 'like', '%' . $school_student_id . '%');
        }
        return $this->fetchList($query, $id, $paginate);
    }

    /**
     * 單筆資料查詢
     */
    public function getOne($id)
    {
        $query = $this->permissions();
        return $this->fetchOne($query, $id);
    }

    /**
     * 更新
     */
    public function setUpdate($id = 0, $datas = [])
    {
        // 表單驗證
        $rule_array = [
            'school_student_id' => [
                'required',
                'max:255',
                Rule::unique('students')->ignore($id)->whereNull('deleted_at'),
            ]
        ];
        if ($id == 0) {
            $rule_array['password'] = [
                'required',
                'max:255',
                'confirmed',
                'min:6',
                'alpha_dash'
            ];
        } else {
            if (request('password', false)) {
                $rule_array['password'] = [
                    'max:255',
                    'confirmed',
                    'min:6',
                    'alpha_dash'
                ];
            }
        }
        $custom_name_array = [
            'school_student_id' => __('auth.帳號'),
            'password' => __('auth.密碼'),
        ];
        $datas['password'] = Hash::make(request('password'));
        if ($id) {
            $result = $this->rule($rule_array, $custom_name_array)->append($datas)->replicateUpdate($id);
        } else {
            $result = $this->rule($rule_array, $custom_name_array)->append($datas)->update();
        }
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 選單用資料查詢
     */
    public function getFrontendOptionItem()
    {
        $query = $this->model->where('status', '啟用')->orderBy('sort')->get();
        foreach ($query as $value) {
            $arr[$value->id] = $value->student_name;
        }
        return $arr;
    }

    /**
     * 以學號查詢
     */
    public function getOneBySchoolId($school_student_id)
    {
        $query = $this->model->where('status', '啟用')->where('school_student_id', $school_student_id)->first();
        if (!is_null($query)) {
            return $query;
        }
        return $arr;
    }

    /**
     * 個人資料維護
     */
    public function frontendProfileUpdate()
    {
        $rule_array = [];
        $custom_name_array = [];
        $datas = [];
        // 表單驗證
        if (!empty(request('password', ''))) {
            $rule_array['password'] = [
                'required',
                'max:255',
                'confirmed',
                'min:6',
                'alpha_dash'
            ];
            $datas['password'] = Hash::make(request('password'));
            $custom_name_array = [
                'password' => '密碼',
            ];
        }
        $result = $this->rule($rule_array, $custom_name_array)->append($datas)->replicateUpdate(auth()->guard('student')->id());
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}
