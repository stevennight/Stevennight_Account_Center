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
    str_replace(':username',$username,
        trans('view.resetPassword.email_template_content_new_pass_content')
    )
}}
<br />
{{ trans('view.resetPassword.email_template_content_new_pass_title') }}{{$newPass}}
<br />
{{ trans('view.resetPassword.email_template_content_warn') }}
</aside>
<footer>
    copyright {{ \App\Database\ConfigGlobalWebsite::all()->first()->name }}
</footer>