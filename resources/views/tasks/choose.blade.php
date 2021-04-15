@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <form method="post" action="{{ url('/tasks/choose', array($task->id)) }}">
                    {{csrf_field()}}
                    @method('PUT')
                    @if(count($errors) > 0)
                    <div class="modal-header">
                        @foreach($errors->all() as $error)
                        <div class="alert alert-danger">{{$error}}</div>
                        @endforeach
                    </div>
                    @endif
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('task.modal_title_choose') }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('task.label_title') }}</label>
                            <input type="text" name="title" class="form-control" value="<?php echo $task->title; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>{{ __('task.label_title_english') }}</label>
                            <input type="text" name="title_in_english" class="form-control" value="<?php echo $task->title_in_english; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>{{ __('task.label_task') }}</label>
                            <textarea class="form-control" name="task" disabled><?php echo $task->task; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __('task.label_study_type') }}</label>
                            <select name="study_type_id" class="form-control" style="width:250px" disabled>
                                <option value="<?php echo $task->study_type_id; ?>" selected>{{ $task->study_type_name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('task.label_accept_student') }}</label>
                            <select name="student_id" class="form-control" style="width:250px" required>
                                @foreach ($students as $student_id => $student_name)
                                <option value="{{ $student_id }}">{{ $student_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ url('/tasks') }}" type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">{{ __('task.button_back') }}</a>

                        <input type="submit" class="btn btn-success" value="{{ __('task.button_submit') }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection