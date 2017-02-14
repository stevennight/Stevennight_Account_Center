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
    str_replace(':time', gmstrftime('%H'.trans('view.email.verify.email_verify_token_template_content_hours'),\App\Database\ConfigGlobalWebsite::all()->first()->email_token_expire) ,
        str_replace(':email',$email,
            str_replace(':username',$username,
                trans('view.email.verify.email_verify_token_template_content')
            )
        )
    )
}}
<br />
<a href="{{ route('email_verify',[$userid,$token]) }}">{{ route('email_verify',[$userid,$token]) }}</a>
<br />
{{ trans('view.email.verify.email_verify_token_template_content_warn') }}
</aside>
<footer>
    copyright {{ \App\Database\ConfigGlobalWebsite::all()->first()->name }}
</footer>