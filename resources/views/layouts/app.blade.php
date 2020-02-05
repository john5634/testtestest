<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config( 'app.name', 'Laravel' ) }}</title>

	<!-- Styles -->
	<link rel="stylesheet" href="{{ asset( 'vendor/bootstrap/css/bootstrap.min.css' ) }}">
	<link rel="stylesheet" href="{{ asset( 'css/base.css' ) }}">
	<link rel="stylesheet" href="{{ asset( 'vendor/owl/assets/owl.carousel.min.css' ) }}">
	<link rel="stylesheet" href="{{ asset( 'vendor/owl/assets/owl.theme.default.min.css' ) }}">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css">
	<!--<link rel="stylesheet" href="https://mdbootstrap.com/wp-content/themes/mdbootstrap4/css/compiled-4.8.9.min.css">-->

	<!-- Custom GCodeMe Styles -->
	<link rel="stylesheet" type="text/css" href="{{ asset( '/css/gcodeme.css' ) }}">

	<!-- Scripts -->
	<script src="{{ asset( 'vendor/jquery/jquery.min.js' ) }}"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="{{ asset( 'vendor/bootstrap/js/bootstrap.min.js' ) }}" defer></script>
	<script src="{{ asset( 'vendor/owl/owl.carousel.min.js' ) }}" defer></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
	<script src="https://kit.fontawesome.com/70f86993e7.js"></script>

</head>

<body>
	<div id="app">
		<nav class="navbar navbar-expand-md navbar-light bg-gray_gcm shadow-sm">
			<div class="container">
				<a href="{{ url('/') }}">
					<img class="navbar-logo_gcm" src="{{ url('/img/GC-01_navbar-logo_opt.png') }}">
					<!--{{ config('app.name', 'Laravel') }}-->
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<!-- Left Side Of Navbar -->
					<ul class="navbar-nav mr-auto"></ul>

					<!-- Right Side Of Navbar -->
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a class="nav-link nav-bold_gcm" href="/landing">HOME</a></li>
						<li class="nav-item"><a class="nav-link nav-bold_gcm" href="/parts">EXPLORE PARTS</a></li>
	    				<li class="nav-item"><a class="nav-link nav-bold_gcm" href="/part/create">ADD A PART</a></li>
	    				<li class="nav-item"><a class="nav-link nav-bold_gcm" href="#">SLICER PROFILES</a></li>
	    				<li class="nav-item"><a class="nav-link nav-bold_gcm" href="#">SLICE ME</a></li>
						<!-- Authentication Links -->
						@guest
							<li class="nav-item">
								<a class="nav-link nav-bold_gcm nav-less-space_gcm" href="{{ route('login') }}">{{ __('Login') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link nav-bold_gcm nav-less-space_gcm">|</a>
							</li>
							@if (Route::has('register'))
								<li class="nav-item">
									<a class="nav-link nav-bold_gcm nav-less-space_gcm" href="{{ route('register') }}">{{ __('Register') }}</a>
								</li>
							@endif
						@else
							<li class="nav-item dropdown">
								<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
									{{ Auth::user()->name }} <span class="caret"></span>
								</a>

								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="{{ route('logout') }}"
									   onclick="event.preventDefault();
													 document.getElementById('logout-form').submit();">
										{{ __('Logout') }}
									</a>

									<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
										@csrf
									</form>
								</div>
							</li>
						@endguest
					</ul>
				</div>
			</div>
		</nav>

		<main class="py-4">
			@yield('content')
		</main>
	</div>

	<div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
	let modal = {
		info: ( title, body ) => {
			$( "#modal-info h5" ).html( title );
			$( "#modal-info .modal-body" ).html( body );
			$( "#modal-info" ).modal( "show" );
		}
	};
	</script>

</body>

<footer class="page-footer font-small bg-gray_gcm shadow-sm">
	
	<!--<div class="footer-color_gcm">
		<div class="container">
      		<div class="row py-4 d-flex align-items-center">
        		<div class="col-md-6 col-lg-5 text-center text-md-left mb-4 mb-md-0">
          			<h6 class="mb-0">Get connected with us on social networks!</h6>
	        	</div>
	        	<div class="col-md-6 col-lg-7 text-center text-md-right">
	          		<a class="fb-ic">
	           			<i class="fab fa-facebook-f white-text mr-4"> </i>
	          		</a>
	          		<a class="tw-ic">
	            		<i class="fab fa-twitter white-text mr-4"> </i>
	          		</a>
				    <a class="gplus-ic">
				        <i class="fab fa-google-plus-g white-text mr-4"> </i>
				    </a>
				    <a class="li-ic">
				        <i class="fab fa-linkedin-in white-text mr-4"> </i>
				    </a>
				    <a class="ins-ic">
				        <i class="fab fa-instagram white-text"> </i>
				    </a>
	       		</div>
	      	</div>
	    </div>
	</div>-->
	<div class="container text-center text-md-left footer-pt-5_gcm">
		<div class="row footer-mt-3_gcm">
			<div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
				<a href="{{ url('/') }}" target="_blank">
					<img class="navbar-logo_gcm" src="{{ url('/img/GC-01_navbar-logo_opt.png') }}">
				</a>
			</div>
			<div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4"></div>
			<div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
				<h6 class="text-uppercase font-weight-bold">Useful Links</h6>
				<hr class="deep-purple accent-2 mb-4 mt-0 d-incline-block mx-auto" style="width: 60px;">
				<p><a class="footer-links_gcm" href="#">Your Account</a></p>
				<p><a class="footer-links_gcm" href="#">About GcodeMe</a></p>
			</div>
			<div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
				<h6 class="text-uppercase font-weight-bold">Contact Us</h6>
				<hr class="deep-purple accent-2 mb-4 mt-0 d-incline-block mx-auto" style="width: 60px;">
				<p><a class="footer-links_gcm" href="mailto:info@GcodeMe.com?Subject=Contact%20Us%20-%20GcodeMe.com" target="_top">info@GcodeMe.com</a></p>
				<p><a class="footer-links_gcm" href="tel:916-844-2633">916-84G-CODE (4-2633)</a></p>
			</div>
		</div>
	</div>
	<div class="footer-copyright text-center py-3 footer-color_gcm">© 2019 
		<a class="footer-links_gcm" href="https://www.GcodeMe.com/" target="_blank">GcodeMe</a>
	</div>

</footer>

</html>
