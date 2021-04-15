@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{session('success')}}
</div>
@elseif(session('warning'))
<div class="alert alert-warning">
    {{session('warning')}}
</div>
@elseif(session('danger'))
<div class="alert alert-danger">
    {{session('danger')}}
</div>
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <span style="font-size:large">{{ __('task.modal_title_home') }}</span>
                    

                    @if(auth()->user()->role->name == 'Teacher')
                    <a class="btn btn-small btn-primary" style="float:right;" href="{{ url('/tasks/add') }}">{{ __('task.button_add') }}</a>
                    @endif
                    @if(auth()->user()->role->name == 'Student' && !$thesis_reserved)
                    <a class="btn btn-small btn-primary" style="float:right;" href="{{ url('/tasks/sort') }}">{{ __('task.button_sort') }}</a>
                    @endif
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center">{{ __('task.label_title') }}</th>
                            <th style="text-align:center">{{ __('task.label_title_english') }}</th>
                            <th style="text-align:center">{{ __('task.label_task') }}</th>
                            <th style="text-align:center">{{ __('task.label_study_type') }}</th>
                            @if(auth()->user()->role->name == 'Teacher')
                            <th style="text-align:center">{{ __('task.label_student_name') }}</th>
                            @endif
                            <th style="text-align:center">{{ __('task.label_action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td style="text-align:center">{{$task->title}}</td>
                            <td style="text-align:center">{{$task->title_in_english}}</td>
                            <td style="text-align:center">{{$task->task}}</td>
                            <td style="text-align:center">{{$task->study_type}}</td>
                            @if(auth()->user()->role->name == 'Teacher')
                            <td style="text-align:center">{{$task->student_name}}</td>
                            @endif
                            <td style="text-align:center">
                                @if($task->is_reserved)
                                Reserved
                                @elseif(in_array(auth()->user()->role->name, ['Administrator', 'Teacher']))
                                <a class="btn btn-small btn-success" href="{{ url('tasks/show/' . $task->id) }}">{{ __('task.button_choose_student') }}</a>
                                <a class="btn btn-small btn-primary" href="{{ url('/tasks/update/' . $task->id) }}">{{ __('task.button_edit') }}</a>

                                <form method="POST" action="/tasks/delete/{{$task->id}}" style="display: inline-block">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}

                                    <input type="submit" class="btn btn-small btn-danger" value="{{ __('task.button_delete') }}" onClick="return confirm('Delete this task?')">
                                </form>
                                @elseif(auth()->user()->role->name == 'Student')
                                @if($task->is_selected)
                                <a class="btn btn-small btn-danger" href="{{ url('/tasks/select/' . $task->id) }}" onClick="return confirm('Remove your choice?')">{{ __('task.button_unselect') }}</a>
                                @else
                                <a class="btn btn-small btn-primary" href="{{ url('/tasks/select/' . $task->id) }}" onClick="return confirm('Confirm your choice?')">{{ __('task.button_select') }}</a>
                                @endif
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td style="text-align:center"><b>{{ __('task.label_no_tasks') }}</b></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection