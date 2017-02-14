<style>
    *{
        font-size:1.2em;
        font-weight: bolder;
        color: dimgray;
    }
    header,footer{
        background:pink;
        text-align: center;
        height:50px;
    }
</style>
<header>

</header>
<aside>
{{
    str_replace(':time', gmstrftime('%H'.trans('view.resetPassword.email_template_content_hours'),\App\Database\ConfigGlobalWebsite::all()->first()->email_token_expire) ,
        str_replace(':username',$username,
            trans('view.resetPassword.email_template_content')
        )
    )
}}
<br />
<a href="{{ route('resetPasswordFunction',[$userid,$token]) }}">{{ route('resetPasswordFunction',[$userid,$token]) }}</a>
<br />
{{ trans('view.resetPassword.email_template_content_warn') }}
</aside>
<footer>
    copyright {{ \App\Database\ConfigGlobalWebsite::all()->first()->name }}
</footer>