@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <form method="post" action="{{ url('/users/edit', array($user->id)) }}">
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
                        <h4 class="modal-title">Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>User Name</label>
                            <input disabled type="text" name="name" class="form-control" value="<?php echo $user->name; ?>">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role_id" class="form-control" style="width:250px" required>
                                @foreach ($roles as $value)
                                <option value="{{ $value->id }}" {{ $value->id == $selected_role ? 'selected' : '' }}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ url('/users') }}" type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">Back</a>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection