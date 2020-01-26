@extends('dashboard::layouts.dashboard')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page">
                <a href="{{ url(config('dashboard.uri') . '/dashboard/index') }}">
                    Home
                </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="{{ url(config('dashboard.uri') . '/student/index') }}">
                    @lang('after-school::student.學生')
                </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="#">
                    @if ($student_id)
                        @if (isset($duplicate) && $duplicate)
                            @lang('backend.複製')
                        @else
                            @lang('backend.編輯')
                        @endif
                    @else
                        @lang('backend.新增')
                    @endif
                </a>
            </li>

            @if ($version)
                <li class="breadcrumb-item" aria-current="page">
                    <a href="#">
                        @lang('backend.版本檢視')
                    </a>
                </li>
            @endif
        </ol>
    </nav>
@endsection

@section('js')
    @include('dashboard::laravel-filemanager')
@endsection

@section('main_block')
    @component('dashboard::components.backend-update-card', $component_datas)
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="normal-tab" data-toggle="tab" href="#normal" role="tab" aria-controls="normal" aria-selected="true">資料設定</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="advanced" aria-selected="false">進階設定</a>
            </li>
        </ul>
        <div class="tab-content pt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="normal" role="tabpanel" aria-labelledby="normal-tab">
                @include('dashboard::backend-update-input', ['form_array' => $form_array_normal, 'form_value' => $student ?? ''])
            </div>
            <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                @include('dashboard::backend-update-input', ['form_array' => $form_array_advanced, 'form_value' => $student ?? ''])
            </div>
        </div>
    @endcomponent
@endsection
