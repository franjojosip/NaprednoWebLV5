@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('task.modal_title_sort') }}</h4>
                </div>
                <table id="table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <td style="text-align:center">#</td>
                            <td style="text-align:center">{{ __('task.label_title') }}</td>
                            <td style="text-align:center">{{ __('task.label_title_english') }}</td>
                            <td style="text-align:center">{{ __('task.label_task') }}</td>
                            <td style="text-align:center">{{ __('task.label_study_type') }}</td>
                            <td style="text-align:center">{{ __('task.label_order') }}</td>
                        </tr>
                    </thead>
                    <tbody id="tablecontents">
                        @forelse($tasks as $task)
                        <tr class="row1" data-id="{{ $task->id }}">
                            <td style="text-align:center" class="pl-3"><i class="fa fa-sort"></i></td>
                            <td style="text-align:center">{{$task->title}}</td>
                            <td style="text-align:center">{{$task->title_in_english}}</td>
                            <td style="text-align:center">{{$task->task}}</td>
                            <td style="text-align:center">{{$task->study_type}}</td>
                            <td style="text-align:center">{{$task->sort_id}}</td>
                        </tr>
                        @empty
                        <tr>
                            <td style="text-align:center"><b>{{ __('task.label_no_tasks') }}</b></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                            <td style="text-align:center"></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="modal-footer">
                    <a href="{{ url('/tasks') }}" type="button" class="btn btn-primary" data-dismiss="modal" value="Cancel">{{ __('task.button_back') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
<script type="text/javascript">
    $(function() {
        $("#tablecontents").sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.6,
            update: function() {
                sendOrderToServer();
            }
        });

        function sendOrderToServer() {
            var task = [];
            var token = $('meta[name="csrf-token"]').attr('content');
            $('tr.row1').each(function(index, element) {
                task.push({
                    task_id: $(this).attr('data-id'),
                    sort_id: index + 1
                });
            });
            $.ajax({
                type: "POST",
                url: "{{ url('/tasks/update-sort') }}",
                datatype: 'JSON',
                data: {
                    task: task,
                    _token: token
                },
                success: function(response) {
                    window.location.href = "sort";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }

    });
</script>
@endsection