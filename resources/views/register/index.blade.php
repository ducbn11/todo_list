@extends('layouts.master')
@section('title', 'Register')
<?php
$error = $errors->messages();
?>
@section('content')
<div class="container">
    <div class="row d-flex justify-content-center container-title">
        Register
    </div>
    <div class="row justify-content-md-center">
        <div class="col-md-4 form-container">
            <form action="{{route('post_register')}}" method="post">
                @if(Session::has('message'))
                <div class="alert alert-primary" role="alert">
                    {{Session::get('message')}}
                </div>
                @endif
                @csrf
                <div class="form-group">
                    <label for="input_email">Email</label>
                    <input type="email" class="form-control" id="input_email" aria-describedby="email_help" name="email" value="{{old('email')}}">
                    @if(isset($error) && isset($error['email']))
                        @foreach($error['email'] as $error_email)
                            <small id="email_help" class="form-text text-muted">{{$error_email}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="form-group">
                    <label for="input_username">Username</label>
                    <input type="text" class="form-control" id="input_username" aria-describedby="username_help" name="name"  value="{{old('name')}}">
                    @if(isset($error) && isset($error['name']))
                        @foreach($error['name'] as $error_username)
                            <small id="email_help" class="form-text text-muted">{{$error_username}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="form-group">
                    <label for="input_password">Password</label>
                    <input type="password" class="form-control" id="input_password" name="password"  value="{{old('password')}}">
                    @if(isset($error) && isset($error['password']))
                        @foreach($error['password'] as $error_password)
                            <small id="email_help" class="form-text text-muted">{{$error_password}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="form-group">
                    <label for="input_password_confirmation">Password Confirmation</label>
                    <input type="password" class="form-control" id="input_password_confirmation" name="password_confirmation"  value="{{old('password_confirmation')}}">
                    @if(isset($error) && isset($error['password_confirmation']))
                        @foreach($error['password_confirmation'] as $error_password_confirmation)
                            <small id="email_help" class="form-text text-muted">{{$error_password_confirmation}}</small>
                        @endforeach
                    @endif
                </div>
                <div class="d-flex justify-content-center mt-5">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <a href="{{route('view_login')}}">Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop