@extends('layouts/global')

@section('page_title')
    {{ trans('view.oauth.page_title') }}
@stop

{{-- 注册表单  register form --}}
@section('contain')
    <div class="row">


    </div>
    <div class="row">
        <form class="blue lighten-4 col s10 offset-s1 z-depth-1" method="post">
            {{ csrf_field() }}
            <div class="card blue darken-1 z-depth-2 ">
                <div class="card-content white-text">
                    <span class="card-title">{{ trans('view.oauth.oauth_title') }}</span>
                    <p>
                        {{
                            str_replace(':clientname',$client->name,trans('view.oauth.details'))
                         }}
                        <br /><br />
                        {{ trans('view.oauth.details2') }}<a class="white-text" href="{{ $client->userurl }}">{{ $client->name }}</a>
                    </p>
                    @if(count($errors))
                        <p>
                            <li>{{ $errors->first() }}</li>
                        </p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="right col">
                    <button class="btn blue waves-effect waves-light" type="submit" name="action">{{ trans('view.oauth.authencation') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
                <div class="left col">
                    <a class="btn blue waves-effect waves-light" href="{{ $client->userurl }}">{{ trans('view.oauth.cancel_authencation') }}</a>
                </div>
            </div>
        </form>
    </div>
@stop