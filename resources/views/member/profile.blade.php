@extends('layouts.global')

@section('page_title')
    {{ trans('view.profile.page_title') }}
@stop

{{-- 注册表单  register form --}}
@section('contain')
    <div class="row">
    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-1" method="post" enctype="multipart/form-data" >
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
            <div class="row center">
                <a><img class="circle col s4 offset-s4" src="{{ session('member.avatar') }}"></a>
            </div>
            <div class="row">
                <div class="file-field input-field col s10 offset-s1">
                    <div class="blue btn">
                        <span>{{ trans('view.profile.avatar_choose') }}</span>
                        <input type="file" name="avatar" value="{{ old('avatar') }}">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input id="icon_prefix" type="text" name="username" class="validate" value="{{ session('member.username')  }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.profile.username_title') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="text" class="validate" name="QQ" value="{{ session('member.QQ') }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.profile.QQ_title') }}{{ (session('member.QQ_active')==1)?'('.trans('view.profile.QQ_active_title').')':'' }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s10 offset-s1">
                    <input type="text" class="left validate" name="email" value="{{ session('member.email') }}">
                    <label class='blue-text' for="icon_prefix">{{ trans('view.profile.email_title') }}{{ (session('member.email_active')==1)?'('.trans('view.profile.email_active_title').')':'' }}</label>
                    <a class="right btn blue waves-effect waves-light" href="{{ route('send_email_verify') }}" >{{ trans('view.profile.send_email_verify') }}</a>
                </div>
            </div>
            <div class="row">
                <div class="right col">
                    <button class="btn blue waves-effect waves-light">{{ trans('view.profile.renewProfile') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
                <div class="left col">
                    <a class="btn blue waves-effect waves-light" href="{{ route('repassword') }}" >{{ trans('view.profile.change_password_button') }}</a>
                </div>
            </div>
        </form>
    </div>
@stop