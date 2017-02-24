@extends('layouts.global')

@section('page_title')
    {{ trans('view.repassword.page_title') }}
@stop

{{-- 注册表单  register form --}}
@section('contain')
    <div class="row">


    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-1" method="post">

            @if(count($errors))
                <div class="card pink accent-2 z-depth-2">
                    <div class="card-content white-text">
                        <li>{{ $errors->first() }}</li>
                    </div>
                </div>
            @endif

            {{ csrf_field() }}
            <div class="row">

            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="icon_prefix" type="password" name="oldpass" class="validate">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.repassword.old_password') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="password" class="validate" name="newpass">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.repassword.new_password') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="password" class="validate" name="newpass_confirmation">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.repassword.new_password_confirmation') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="right col">
                    <button class="btn blue waves-effect waves-light" href="{{ route('repassword') }}" >{{ trans('view.profile.change_password_button') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
                <div class="left col">
                    <a class="btn blue waves-effect waves-light">{{ trans('view.profile.delete_account_button') }}</a>
                </div>
            </div>
        </form>
    </div>
@stop