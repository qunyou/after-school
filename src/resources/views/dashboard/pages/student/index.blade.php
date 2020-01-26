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
                <a href="{{ url(config('dashboard.uri') . '/student/index') }}">
                    @lang('backend.列表')
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

@section('main_block')
    @component('dashboard::components.backend-list', $component_datas)
        @slot('button_block')
            <a class="btn btn-outline-deep-purple waves-effect" data-toggle="collapse" href="#collapseGuide" role="button" aria-expanded="false" aria-controls="collapseGuide">
                <i class="fa fa-fw fa-search"></i><span class="d-none d-md-inline">學生查詢</span>
            </a>
        @endslot
        @slot('search_block')
            <div class="collapse search show" id="collapseGuide">
                <div class="bg-light card-body p-3">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-auto">
                                學生姓名
                                <input type="text" name="student_name" class="form-control form-control-lg" value="{{ request('student_name', '') }}" placeholder="學生姓名">
                            </div>
                            <div class="col-auto">
                                座號
                                <input type="text" name="seat_number" class="form-control form-control-lg" value="{{ request('seat_number', '') }}" placeholder="座號">
                            </div>
                            <div class="col-auto">
                                學號
                                <input type="text" name="school_student_id" class="form-control form-control-lg" value="{{ request('school_student_id', '') }}" placeholder="學號">
                            </div>
                        </div>

                        <div class="text-center mt-2">
                            <div class="btn-group">
                                <a class="btn btn-outline-deep-purple waves-effect" data-toggle="collapse" href="#collapseGuide" role="button" aria-expanded="false" aria-controls="collapseGuide">
                                    關閉
                                </a>
                                <a class="btn btn-outline-deep-purple waves-effect" href="{{ url($uri . 'index') }}">
                                    重設查詢
                                </a>
                                <button type="submit" class="btn btn-outline-deep-purple waves-effect">
                                    查詢
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endslot
    @endcomponent
@endsection
