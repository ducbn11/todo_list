@extends('layouts.master')
@section('title', 'Todo List')
@section('content')
@include('layouts.header')
<div class="content-container">
    <div class="content-title mb-5">
        Todo List
    </div>
    <div class="content-fitler mb-3 d-flex align-items-center row">
        <div class="filter-title">Filter</div>
        <input class="form-control col-2 mr-2" type="number" placeholder="ID" id="filter_id" value="{{data_get($data, 'id', '')}}">
        <input class="form-control col-2 mr-2" type="text" placeholder="Title" id="filter_title" value="{{data_get($data, 'title', '')}}">
        <div class="col-3 d-flex align-items-center mr-2">
            <label class="mr-2">Due date</label>
            <input class="form-control" type="date" placeholder="Due date" style="width: 70%" id="filter_due_date" value="{{data_get($data, 'due_date', '')}}">
        </div>
        <div class="col-2 d-flex align-items-center mr-2">
            <label class="mr-2">Status</label>
            <select class="form-control" id="filter_status">
                <option value="" selected>Status</option>
                <option value="0" @if(data_get($data, 'status', '') != '' && data_get($data, 'status', '') == \App\Models\TodoList::STATUS_NOT_FINISH) selected @endif>Not finish</option>
                <option value="1" @if(data_get($data, 'status', '') != '' && data_get($data, 'status', '') == \App\Models\TodoList::STATUS_FINISHED) selected @endif>Finished</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary mr-2" id="button_filter">Filter</button>
        <a type="button" class="btn btn-primary mr-2" id="button_export_csv">Export to csv</a>
        <a type="button" class="btn btn-primary" href="{{route('todo_list.todo_item')}}">Create new todo</a>
    </div>
    <div class="content-table mb-3">
        @if(Session::has('message'))
        <div class="alert alert-primary" role="alert">
            {{Session::get('message')}}
        </div>
        @endif
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Due date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todoList as $todo)
                    <tr>
                        <th>{{$todo->id}}</th>
                        <th>{{$todo->title}}</th>
                        <th>{{$todo->due_date}}</th>
                        <th id="todo_status_{{$todo->id}}">
                            @if($todo->status == \App\Models\TodoList::STATUS_NOT_FINISH)
                                <span class="badge badge-primary">Not finish</span>
                            @elseif($todo->status == \App\Models\TodoList::STATUS_FINISHED)
                                <span class="badge badge-warning">Finished</span>
                            @endif
                        </th>
                        <th>{{$todo->created_at}}</th>
                        <th id="todo_actions_{{$todo->id}}">
                            <a type="button" class="btn btn-primary mr-2" id="button_edit_{{$todo->id}}" href="{{route('todo_list.todo_item', ['id' => $todo->id])}}">Edit</a>
                            <a type="button" class="btn btn-danger mr-2" id="button_edit_{{$todo->id}}" href="" onclick="confirmDeleteTodoItem({{$todo->id}}, this)">Delete</a>
                            @if($todo->status == \App\Models\TodoList::STATUS_NOT_FINISH)
                                <button type="button" class="btn btn-warning mr-2" id="button_change_status_{{$todo->id}}" onclick="updateStatus({{$todo->id}}, {{\App\Models\TodoList::STATUS_FINISHED}})">Finish</button>
                            @elseif($todo->status == \App\Models\TodoList::STATUS_FINISHED)
                                <button type="button" class="btn btn-warning mr-2" id="button_change_status_{{$todo->id}}" onclick="updateStatus({{$todo->id}}, {{\App\Models\TodoList::STATUS_NOT_FINISH}})">Not Finish</button>
                            @endif
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {!! $todoList->appends(Request::except('page'))->render('pagination.custom') !!}
    </div>
</div>
@stop

@section('body_script')
<script>
    $( document ).ready(function() {
        $('#button_filter').click(function() {
            let filterId = $('#filter_id').val();
            let filterTitle = $('#filter_title').val();
            let filterDueDate = $('#filter_due_date').val();
            let filterStatus = $('#filter_status').val();
            let todoListUrl = "{{route('todo_list.index')}}";
            let filterUrl = todoListUrl + '?id=' + filterId + '&title=' + filterTitle + '&due_date=' + filterDueDate + '&status=' + filterStatus;
            window.location.href = filterUrl;
        });

        $('#button_export_csv').click(function(event) {
            event.preventDefault();
            let filterId = $('#filter_id').val();
            let filterTitle = $('#filter_title').val();
            let filterDueDate = $('#filter_due_date').val();
            let filterStatus = $('#filter_status').val();
            let todoListExportUrl = "{{route('todo_list.export')}}";
            let filterUrl = todoListExportUrl + '?id=' + filterId + '&title=' + filterTitle + '&due_date=' + filterDueDate + '&status=' + filterStatus;
            window.location.href = filterUrl;
        });

        
    });
    function updateStatus(id, status) {
        if(confirm("Are you sure to change the status of this todo?")) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                method: "POST",
                url: "{{route('todo_list.update_status')}}",
                data: {
                    id: id,
                    status: status,
                },
                success: function(data) {
                    if(data.status == 1) {
                        let html = '';
                        let text = "";
                        if(status == 0) {
                            html = '<span class="badge badge-primary">Not finish</span>';
                            text = "Finish";
                        } else if (status == 1) {
                            html = '<span class="badge badge-warning">Finished</span>';
                            text = "Not Finish";
                        }
                        $('#todo_status_' + id).html(html);
                        $('#button_change_status_' + id).text(text);
                        $('#button_change_status_' + id).attr("onclick","updateStatus("+ id +", "+ (status == 0 ? 1 : 0) +")");
                        alert("Update status successfully!");
                    } else {
                        alert("Update status fail!");
                    }
                },
                error: function(error) {
                    console.log("updateStatus");
                    console.log(error);
                    alert("Update status fail!");
                }
            });
        }
    }

    function confirmDeleteTodoItem(id, el) {
        if(confirm("Are you sure to delete this todo?")) {
            $(el).attr('href', "{{route('todo_list.delete_todo_item')}}" + '/' + id);
            $(el).attr('onclick', "");
            $(el).click();
        }
    }
</script>
@stop