<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>@yield('page_title')--{{ \App\Database\ConfigGlobalWebsite::all()->first()->name }}</title>
		<!--Import Google Icon Font-->
		<link href="/css/icon.css" rel="stylesheet">
		<!--Import materialize.css-->
		<link rel="stylesheet" href="/css/materialize.min.css" />
		<!--Import Jquery-->
		<script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
		<!--Import materialize.js-->
		<script type="text/javascript" src="/js/materialize.min.js"></script>
		<!--这是固定footer的样式-->
		<style type="text/css">
			#body {
				display: flex;
				min-height: 100vh;
				flex-direction: column;
			}
			
			#main {
				flex: 1 0 auto;
			}
		</style>
	</head>

	<body class="blue-grey lighten-4" id="body">
		<nav class="blue lighten-2 z-depth-4">
			<div class="nav-wrapper">
				<a href="#" class="brand-logo"></a>
				<ul class="left hide-on-med-and-down">

					<li>
						<a href="{{ route('index') }}" class="waves-effect waves-light">{{ \App\Database\ConfigGlobalWebsite::all()->first()->name }}</a>
					</li>
				</ul>

				<ul class="right">
					@if(session()->has('member'))
						<li><a class="waves-effect waves-light" href="{{ route('profile') }}">{{ session('member.username') }}</a></li>
						<li><a class="waves-effect waves-light" href="{{ route('logout') }}">{{ trans('view.header.logout') }}</a></li>

					@else
						<li><a class="waves-effect waves-light" href="{{ route('login') }}">{{ trans('view.header.login') }}</a></li>
						@endif
				</ul>

			</div>
		</nav>
		<div class="container" id="main">
			@section('contain')

				@show
		</div>

		<footer class="blue lighten-2 z-depth-4 page-footer">
			<div class="container">
				<div class="row">
					<div class="col l6 s12">
						<h5 class="white-text">{{ trans('view.footer.thanks') }}</h5>
						<p class="grey-text text-lighten-4">给予我帮助以及建议的各位。</p>
						<p class="grey-text text-lighten-4">Everyone who give me some advices and helps.</p>
					</div>
					<div class="col l4 offset-l2 s12">
						<h5 class="white-text">{{ trans('view.footer.links') }}</h5>
						<ul>
							@forelse(session('links') as $link)
								<li>
									<a class="grey-text text-lighten-3" href="{{ $link->link }}">{{ $link->name }}</a>
								</li>
							@empty
								<li>
									<a class="grey-text text-lighten-3" >{{ trans('view.footer.without_link') }}</a>
								</li>
							@endforelse
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-copyright">
				<div class="container">
					&copy; 2016-2017 Stevennight. Design By Xat.
					{{--<a class="grey-text text-lighten-4 right" href="#!"></a>--}}
					<a class="grey-text text-lighten-4 right" href="https://github.com/stevennight">Github</a>
				</div>
			</div>
		</footer>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.tooltipped').tooltip({
					delay: 50
				});
			});
		</script>

	</body>

</html>