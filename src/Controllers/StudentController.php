<?php

namespace Onepoint\AfterSchool\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\StudentImport;
use Cache;
use Excel;
use Onepoint\Dashboard\Services\BaseService;
use Onepoint\Dashboard\Services\FileService;
use Onepoint\AfterSchool\Repositories\StudentRepository;

/**
 * 學生
 */
class StudentController extends Controller
{
    /**
     * 建構子
     */
    public function __construct(BaseService $base_services, StudentRepository $student_repository)
    {
        $this->base_services = $base_services;
        $this->tpl_data = $base_services->tpl_data;
        $this->tpl_data['base_services'] = $this->base_services;
        $this->permission_controller_string = get_class($this);
        $this->tpl_data['component_datas']['permission_controller_string'] = $this->permission_controller_string;
        $this->tpl_data['navigation_item'] = config('backend.navigation_item');

        $this->student_repository = $student_repository;

        // 預設網址
        $this->uri = config('dashboard.uri') . '/student/';
        $this->tpl_data['uri'] = $this->uri;
        $this->tpl_data['component_datas']['uri'] = $this->uri;

        // view 路徑
        $this->view_path = 'after-school::' . config('dashboard.view_path') . '.pages.student.';
        $this->student_id = request('student_id', false);
        $this->tpl_data['student_id'] = $this->student_id;
    }

    /**
     * 列表
     */
    public function index()
    {
        // 列表標題
        if (!$this->tpl_data['trashed']) {
            $this->tpl_data['component_datas']['page_title'] = __('backend.列表');
        } else {
            $this->tpl_data['component_datas']['page_title'] = __('backend.資源回收');
        }

        // 主資料 id query string 字串
        $this->tpl_data['component_datas']['id_string'] = 'student_id';

        // 回列表網址
        $this->tpl_data['component_datas']['back_url'] = url($this->uri . 'index');

        // 表格欄位設定
        $this->tpl_data['component_datas']['th'] = [
            ['title' => __('after-school::student.學生姓名'), 'class' => ''],
            ['title' => __('after-school::student.學號(帳號)'), 'class' => 'd-none d-xl-table-cell'],
            ['title' => __('after-school::student.班級'), 'class' => 'd-none d-xl-table-cell'],
        ];
        // class="d-none d-md-table-cell"
        $this->tpl_data['component_datas']['column'] = [
            ['type' => 'column', 'class' => 'd-none d-xl-table-cell', 'column_name' => 'student_name'],
            ['type' => 'column', 'class' => 'd-none d-xl-table-cell', 'column_name' => 'school_student_id'],
            ['type' => 'column', 'class' => 'd-none d-xl-table-cell', 'column_name' => 'student_class'],
            // ['type' => 'belongsTo', 'class' => 'd-none d-md-table-cell', 'with' => 'student_class', 'column_name' => 'class_name'],
        ];

        // 是否使用複製功能
        $this->tpl_data['component_datas']['use_duplicate'] = false;

        // 是否使用版本功能
        $this->tpl_data['component_datas']['use_version'] = false;

        // 是否使用排序功能
        // $this->tpl_data['component_datas']['use_sort'] = false;

        // 權限設定
        $this->tpl_data['component_datas']['footer_delete_hide'] = false;
        if (auth()->user()->hasAccess(['create-' . $this->permission_controller_string])) {
            $this->tpl_data['component_datas']['add_url'] = url($this->uri . 'update');
        }
        if (auth()->user()->hasAccess(['update-' . $this->permission_controller_string])) {
            $this->tpl_data['component_datas']['footer_dropdown_hide'] = false;
            $this->tpl_data['component_datas']['footer_sort_hide'] = false;
            if (!auth()->user()->hasAccess(['delete-' . $this->permission_controller_string])) {
                $this->tpl_data['component_datas']['footer_delete_hide'] = true;
            }
        } else {
            $this->tpl_data['component_datas']['footer_dropdown_hide'] = true;
            $this->tpl_data['component_datas']['footer_sort_hide'] = true;
        }
        if (auth()->user()->hasAccess(['create-' . $this->permission_controller_string])) {
            $this->tpl_data['component_datas']['dropdown_items']['items']['匯入'] = ['url' => url($this->uri . 'import')];
        }
        if (auth()->user()->hasAccess(['update-' . $this->permission_controller_string])) {
            $this->tpl_data['component_datas']['dropdown_items']['items']['舊版本'] = ['url' => url($this->uri . 'index?version=true')];
        }
        if (auth()->user()->hasAccess(['delete-' . $this->permission_controller_string])) {
            $this->tpl_data['component_datas']['dropdown_items']['items']['資源回收'] = ['url' => url($this->uri . 'index?trashed=true')];
        }

        // 列表資料查詢
        $this->tpl_data['component_datas']['list'] = $this->student_repository->getList($this->student_id, config('backend.paginate'));

        // 預覽按鈕網址
        // $this->tpl_data['component_datas']['preview_url'] = ['url' => url(config('backend.book.preview_url')) . '/', 'column' => 'book_name_slug'];
        return view($this->view_path . 'index', $this->tpl_data);
    }

    /**
     * 批次處理
     */
    public function putIndex()
    {
        return $this->batch();
    }

    /**
     * 批次處理
     */
    public function batch()
    {
        // $settings['file_field'] = 'file_name';
        // $settings['folder'] = 'student';
        // $settings['image_scale'] = true;
        $settings['use_version'] = true;
        $result = $this->student_repository->batch($settings);
        switch ($result['batch_method']) {
            case 'restore':
            case 'force_delete':
                $back_url_str = 'index?trashed=true';
                break;
            default:
                $back_url_str = 'index';
                break;
        }
        return redirect($this->uri . $back_url_str);
    }

    /**
     * 編輯
     */
    public function update()
    {
        $this->tpl_data['student'] = false;
        if ($this->student_id) {
            $page_title = __('backend.編輯');
            $query = $this->student_repository->getOne($this->student_id);

            // 複製
            if (isset($this->tpl_data['duplicate']) && $this->tpl_data['duplicate']) {
                $page_title = __('backend.複製');
                $this->tpl_data['duplicate'] = true;
                $password_help = '';
            } else {
                $password_help = __('auth.若不修改密碼請保持密碼欄位空白');
            }
            $this->tpl_data['student'] = $query;
        } else {
            $page_title = __('backend.新增');
            $password_help = '';
        }

        // 班級選單資料
        // $student_class_select_item = $this->student_class_repository->getOptionItem();

        // 一般表單資料
        $this->tpl_data['form_array_normal'] = [
            'student_name' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.學生姓名'),
            ],
            // 'student_class_id' => [
            //     'input_type' => 'select',
            //     'display_name' => __('after-school::student-class.班級'),
            //     'option' => $student_class_select_item,
            // ],
            'student_class' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student-class.班級'),
            ],
            'seat_number' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.座號'),
            ],
            'school_student_id' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.學號(帳號)'),
            ],
            'password' => [
                'input_type' => 'password',
                'input_value' => '',
                'display_name' => __('auth.密碼'),
                'help' => $password_help,
                'attribute' => ['autocomplete' => 'off'],
            ],
            'password_confirmation' => [
                'input_type' => 'password',
                'input_value' => '',
                'display_name' => __('auth.密碼確認'),
                'help' => __('auth.請再輸入一次密碼'),
                'attribute' => ['autocomplete' => 'off'],
            ],
            'email' => [
                'input_type' => 'text',
                'display_name' => __('auth.Email'),
            ],
            'educational_system' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.學制'),
            ],
            'graduation_year' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.畢業年度'),
            ],
            'department' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.科系'),
            ],
            'school_year' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.入學年度'),
            ],
            'gender' => [
                'input_type' => 'select',
                'display_name' => __('after-school::student.性別'),
                'option' => ['男' => '男', '女' => '女'],
            ],
            'id_number' => [
                'input_type' => 'text',
                'display_name' => __('after-school::student.身份證字號'),
            ],
            'birthday' => [
                'input_type' => 'date',
                'display_name' => __('after-school::student.出生年月日'),
            ],
            'tel' => [
                'input_type' => 'date',
                'display_name' => __('after-school::student.電話'),
            ],
            'city' => [
                'input_type' => 'date',
                'display_name' => __('after-school::student.縣市'),
            ],
            'area' => [
                'input_type' => 'date',
                'display_name' => __('after-school::student.地區'),
            ],
            'address' => [
                'input_type' => 'date',
                'display_name' => __('after-school::student.地址'),
            ],
        ];

        // 進階表單資料
        $this->tpl_data['form_array_advanced'] = [
            'sort' => [
                'input_type' => 'number',
                'display_name' => __('backend.排序'),
            ],
            'status' => [
                'input_type' => 'select',
                'display_name' => __('backend.狀態'),
                'option' => config('backend.status_item'),
            ],
            'note' => [
                'input_type' => 'textarea',
                'display_name' => __('backend.備註'),
                'rows' => 5,
            ],
        ];

        // 樣版資料
        $component_datas['page_title'] = $page_title;
        $component_datas['back_url'] = url($this->uri . 'index');
        $this->tpl_data['component_datas'] = $component_datas;
        return view($this->view_path . 'update', $this->tpl_data);
    }

    /**
     * 送出編輯資料
     */
    public function putUpdate()
    {
        $res = $this->student_repository->setUpdate($this->student_id);
        if ($res) {
            session()->flash('notify.message', __('backend.資料編輯完成'));
            session()->flash('notify.type', 'success');
            return redirect($this->uri . 'detail?student_id=' . $res);
        } else {
            session()->flash('notify.message', __('backend.資料編輯失敗'));
            session()->flash('notify.type', 'danger');
            return redirect($this->uri . 'update?student_id=' . $this->student_id)->withInput();
        }
    }

    /**
     * 複製
     */
    public function duplicate()
    {
        $this->tpl_data['duplicate'] = true;
        return $this->update();
    }

    /**
     * 複製
     */
    public function putDuplicate()
    {
        $this->student_id = 0;
        return $this->putUpdate();
    }

    /**
     * 細節
     */
    public function detail()
    {
        if ($this->student_id) {
            $student = $this->student_repository->getOne($this->student_id);
            $this->tpl_data['student'] = $student;

            // 表單資料
            $this->tpl_data['form_array'] = [
                'student_name' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.學生姓名'),
                ],
                // 'student_class_id' => [
                //     'input_type' => 'value',
                //     'display_name' => __('after-school::student-class.班級'),
                //     'input_value' => $student->student_class->class_name,
                // ],
                'student_class' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.班級'),
                ],
                'seat_number' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.座號'),
                ],
                'school_student_id' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.學號(帳號)'),
                ],
                'email' => [
                    'input_type' => 'value',
                    'display_name' => __('auth.Email'),
                ],
                'educational_system' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.學制'),
                ],
                'graduation_year' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.畢業年度'),
                ],
                'department' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.科系'),
                ],
                'school_year' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.入學年度'),
                ],
                'gender' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.性別'),
                ],
                'id_number' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.身份證字號'),
                ],
                'birthday' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.出生年月日'),
                ],
                'tel' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.電話'),
                ],
                'city' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.縣市'),
                ],
                'area' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.地區'),
                ],
                'address' => [
                    'input_type' => 'value',
                    'display_name' => __('after-school::student.地址'),
                ],
                'sort' => [
                    'input_type' => 'value',
                    'display_name' => __('backend.排序'),
                ],
                'status' => [
                    'input_type' => 'value',
                    'display_name' => __('backend.狀態'),
                ],
                'note' => [
                    'input_type' => 'value',
                    'display_name' => __('backend.備註'),
                ],
            ];

            // 樣版資料
            $component_datas['page_title'] = __('backend.檢視');
            $component_datas['back_url'] = url($this->uri . 'index');
            $component_datas['dropdown_items']['btn_align'] = 'float-left';
            if (auth()->user()->hasAccess(['update-' . $this->permission_controller_string])) {
                $component_datas['dropdown_items']['items']['編輯'] = ['url' => url($this->uri . 'update?student_id=' . $student->id)];
                if (auth()->user()->hasAccess(['delete-' . $this->permission_controller_string])) {
                    $component_datas['dropdown_items']['items']['刪除'] = ['url' => url($this->uri . 'delete?student_id=' . $student->id)];
                }
            }
            if (auth()->user()->hasAccess(['create-' . $this->permission_controller_string])) {
                $component_datas['dropdown_items']['items']['複製'] = ['url' => url($this->uri . 'duplicate?student_id=' . $student->id)];
            }
            // $component_datas['dropdown_items']['items']['預覽'] = ['url' => url(config('backend.student.preview_url', 'detail/') . $student->id)];
            $this->tpl_data['component_datas'] = $component_datas;
            return view($this->view_path . 'detail', $this->tpl_data);
        } else {
            return redirect($this->uri . 'index');
        }
    }

    /**
     * 版本還原
     */
    public function applyVersion()
    {
        if ($this->student_id) {
            $version_id = request('version_id', 0);
            if ($version_id) {
                $this->student_repository->applyVersion($this->student_id, $version_id);
            }
        }
        return redirect($this->uri . 'detail?student_id=' . $this->role_id);
    }

    /**
     * 刪除
     */
    public function delete()
    {
        if ($this->student_id) {
            $this->student_repository->delete($this->student_id);
        }
        return redirect($this->uri . 'index');
    }

    /**
     * 匯入
     */
    public function import()
    {
        $this->tpl_data['page_title'] = '匯入';

        // 匯入結果訊息
        $this->tpl_data['import_message'] = false;
        if (Cache::has('import_message')) {
            $this->tpl_data['import_message'] = Cache::get('import_message');
            Cache::forget('import_message');
        }

        // 上傳結果訊息
        $this->tpl_data['upload_message'] = false;
        if (Cache::has('upload_message')) {
            $this->tpl_data['upload_message'] = Cache::get('upload_message');
            Cache::forget('upload_message');
        }
        return view($this->view_path . 'import', $this->tpl_data);
    }

    /**
     * 匯入
     */
    public function putImport()
    {
        // 匯入時間
        $created_at = date('Y-m-d H:i:s', time());

        // 匯入訊息
        $upload_message = [];

        // 上傳檔案
        $prefix = 'student-import-' . $created_at;
        $res = FileService::upload('file_name', $prefix, 0, false, 'student-import');

        // 檢查是否多檔上傳
        if (isset($res[0]['file_name'])) {
            set_time_limit(120);
            foreach ($res as $value) {
                Excel::import(new StudentImport, config('frontend.upload_path') . '/student-import/' . $value['file_name'], 'public');
            }
        } else {
            $upload_message[] = '檔案 ' . $value['origin_name'] . '上傳失敗';
        }

        // 將上傳訊息存入快取
        if (count($upload_message)) {
            Cache::put('upload_message', $upload_message, 30);
        }
        return redirect($this->uri . 'import?student_id=' . $this->student_id);
    }
}
