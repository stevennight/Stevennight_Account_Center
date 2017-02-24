@extends('layouts.global')

@section('page_title')
    {{ trans('view.resetPassword.page_title') }}
@stop

{{-- 重置密码表单 reset password form--}}
@section('contain')
    <div class="row">
        <div class="col s6 offset-l3"> </div>
    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-5" method="post" action="">
            <div class="card {{ count($errors)?'pink':'blue' }} accent-2 z-depth-2 ">
                <div class="card-content white-text">
                    <span class="card-title">{{ trans('view.resetPassword.tips_title') }}</span>
                    <p>
                    @if(count($errors))
                        <li class="" style="font-weight: bolder">{{ $errors->first() }}</li>
                        @else
                        {{ trans('view.resetPassword.tips_tips') }}
                        @endif
                        </p>
                </div>
            </div>
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="icon_prefix" type="text" class="validate" name="username" value="{{ old('username') }}">
                    <label class="blue-text" for="icon_prefix" >{{ trans('view.resetPassword.username_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="email" type="email" class="validate" name="email">
                    <label class="blue-text" for="icon_telephone">{{ trans('view.resetPassword.email_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="right col">
                    <button class="btn blue waves-effect waves-light" type="submit" name="submit">
                        {{ trans('view.resetPassword.reset_email_send') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop