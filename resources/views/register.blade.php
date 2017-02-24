@extends('layouts.global')

@section('page_title')
    {{ trans('view.register.page_title') }}
@stop

{{-- 注册表单  register form --}}
@section('contain')
    <div class="row">


    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-1" method="post">
            {{ csrf_field() }}
            <div class="card {{ count($errors)?'pink':'blue' }} accent-2 z-depth-2 ">
                <div class="card-content white-text">
                    <span class="card-title">{{ trans('view.register.register_title') }}</span>
                    <p>
                        @if(count($errors))
                            <li class="" style="font-weight: bolder">{{ $errors->first() }}</li>
                        @else
                            {{ trans('view.register.register_tips') }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="icon_prefix" type="text" name="username" class="validate" value="{{ old('username') }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.register.username_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="password" type="password" name="password" class="validate">
                    <label class='blue-text' for="icon_telephone">{{ trans('view.register.password_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="validate">
                    <label class='blue-text' for="icon_telephone">{{ trans('view.register.password_confirmation_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="text" class="validate" name="QQ" value="{{ old('QQ') }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.register.qq_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="text" class="validate" name="email" value="{{ old('email') }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.register.email_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="col s5 offset-s5">
                    <input type="checkbox" class="blue-text blue" id="filled-in-box" name="accept_term" />
                    <label class='blue-text' for="filled-in-box">{{ trans('view.register.accept_term') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="right col">
                    <button class="btn blue waves-effect waves-light" type="submit" name="action">{{ trans('view.register.register_button') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
                <div class="left col">
                    <a class="btn blue waves-effect waves-light" href="{{ route('login') }}">{{ trans('view.register.login_button') }}</a>
                </div>
            </div>
        </form>
    </div>
    @stop