@extends('layouts.master')
@section('title', 'Todo Item')

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center container-title">
        {{data_get($todo, 'id') ? 'Edit Todo' : 'Create Todo'}}
    </div>
    <div class="row justify-content-md-center">
        <div class="col-12 form-container">
            <form action="{{route('todo_list.save_todo_item')}}" method="post" class="p-3" id="form_save_todo_item">
                @if(Session::has('message'))
                <div class="alert alert-primary" role="alert">
                    {{Session::get('message')}}
                </div>
                @endif
                @csrf
                <input type="hidden" value="{{data_get($todo, 'id', '')}}" name="id">
                <div class="form-group row" id="title_form_group">
                    <label for="input_title" class="col-3">Title</label>
                    <textarea class="col-9" type="text" id="input_title" name="title">{{data_get($todo, 'title')}}</textarea>
                    <div id="title_help" class="offset-3 col-9"></div>
                </div>
                <div class="form-group row" id="due_date_form_group">
                    <label for="input_due_date" class="col-3">Due date</label>
                    <input type="date" class="form-control col-9" id="input_due_date" aria-describedby="username_help" name="due_date"  value="{{data_get($todo, 'due_date')}}">
                    <div id="due_date_help" class="offset-3 col-9"></div>
                </div>
                <div class="form-group row" id="status_form_group">
                    <label for="input_password" class="col-3">Status</label>
                    <select class="form-control col-9" id="input_status" name="status">
                        <option value="{{\App\Models\TodoList::STATUS_NOT_FINISH}}" @if(data_get($todo, 'status', '') == \App\Models\TodoList::STATUS_NOT_FINISH) selected @endif>Not finish</option>
                        <option value="{{\App\Models\TodoList::STATUS_FINISHED}}" @if(data_get($todo, 'status', '') != '' && data_get($todo, 'status', '') == \App\Models\TodoList::STATUS_FINISHED) selected @endif>Finished</option>
                    </select>
                    <div id="status_help" class="offset-3 col-9"></div>
                </div>
                <div class="d-flex justify-content-center mt-5">
                    <button type="button" class="btn btn-primary" id="btn_save_todo_item">Save</button>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <a href="{{route('todo_list.index')}}">Back to todo list</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('body_script')
<script>
    $( document ).ready(function() {
        $('#btn_save_todo_item').click(function() {
            let title = $('#input_title').val();
            let dueDate = $('#input_due_date').val();
            let status = $('#input_status').val();

            $.ajax({
                method: "GET",
                url: "{{route('todo_list.validate_save_todo_item')}}",
                data: {
                    title: title,
                    due_date: dueDate,
                    status: status,
                },
                success: function(data) {
                    if(data.status == 1) {
                        $('#form_save_todo_item').submit();
                    } else {
                        let errors = data.errors;
                        if(errors['due_date']) {
                            let html = '';
                            for(let i = 0; i < errors['due_date'].length; i++) {
                                html += '<small id="due_date_help" class="form-text text-muted">' + errors['due_date'][i]  + '</small>';
                            }
                            $('#due_date_help').html(html);
                        }
                        if(errors['title']) {
                            let html = '';
                            for(let i = 0; i < errors['title'].length; i++) {
                                html += '<small id="title_help" class="form-text text-muted">' + errors['title'][i]  + '</small>';
                            }
                            $('#title_help').html(html);
                        }
                        if(errors['status']) {
                            let html = '';
                            for(let i = 0; i < errors['status'].length; i++) {
                                html += '<small id="status_help" class="form-text text-muted">' + errors['status'][i]  + '</small>';
                            }
                            $('#status_help').html(html);
                        }
                    }
                },
                error: function(error) {
                    console.log("updateStatus");
                    console.log(error);
                }
            });
        });
    });
</script>
@stop