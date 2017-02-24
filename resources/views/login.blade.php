@extends('layouts.global')

@section('page_title')
    {{ trans('view.login.page_title') }}
    @stop

{{-- 登录表单 login form--}}
@section('contain')
    <div class="row">
        <div class="col s6 offset-l3"> </div>
    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-5" method="post" action="">
            <div class="card {{ count($errors)?'pink':'blue' }} accent-2 z-depth-2 ">
                <div class="card-content white-text">
                    <span class="card-title">{{ trans('view.login.login_title') }}</span>
                    <p>
                        @if(count($errors))
                            <li class="" style="font-weight: bolder">{{ $errors->first() }}</li>
                        @else
                            {{ trans('view.login.login_tips') }}
                        @endif
                    </p>
                </div>
            </div>
            {{ csrf_field() }}
            <input type="text" hidden name="redirect" value="{{old('redirect')?old('redirect'):$redirect}}"/>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="icon_prefix" type="text" class="validate" name="username" value="{{ old('username') }}">
                    <label class="blue-text" for="icon_prefix" >{{ trans('view.login.username_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="password" type="password" class="validate" name="password">
                    <label class="blue-text" for="icon_telephone">{{ trans('view.login.password_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="right col">
                    <a href="{{ route('resetPasswordEmailSend') }}">{{ trans('view.login.password_forget') }}</a>
                    <button class="btn blue waves-effect waves-light" type="submit" name="submit"> {{ trans('view.login.login_button') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
                <div class="left col">
                    <a class="btn blue waves-effect waves-light" href="{{ route('register') }}">{{ trans('view.login.register_button') }}</a>
                </div>
            </div>
        </form>
    </div>
    @stop