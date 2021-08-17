@extends('layouts.master')
@section('title', 'Login')
<?php
$error = $errors->messages();
?>
@section('content')
<div class="container">
    <div class="row d-flex justify-content-center container-title">
        Login
    </div>
    <div class="row justify-content-md-center">
        <div class="col-md-4 form-container">
            <form action="{{route('post_login')}}" method="post">
                @if(Session::has('message'))
                <div class="alert alert-primary" role="alert">
                    {{Session::get('message')}}
                </div>
                @endif
                @csrf
                <div class="form-group">
                    <label for="input_username">Username</label>
                    <input type="text" class="form-control" id="input_username" aria-describedby="username_help" name="name">
                    @if(isset($error) && isset($error['name']))
                        @foreach($error['name'] as $error_name)
                            <small id="email_help" class="form-text text-muted">{{$error_name}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="form-group">
                    <label for="input_password">Password</label>
                    <input type="password" class="form-control" id="input_password" name="password">
                    @if(isset($error) && isset($error['password']))
                        @foreach($error['password'] as $error_password)
                            <small id="email_help" class="form-text text-muted">{{$error_password}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <a href="{{route('view_register')}}">Register</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop