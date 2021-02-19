<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8"/>
	<title>@yield('title')</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta content="" name="description"/>
	<meta content="" name="author"/>
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
	<!-- <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/> -->
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
	<!-- <link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/> -->
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	{{-- <link href="assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/> --}}
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/admin/pages/css/login.css') }}" rel="stylesheet" type="text/css"/>
	<!-- END PAGE LEVEL SCRIPTS -->
	<!-- BEGIN THEME STYLES -->
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/css/components-md.css') }}" id="style_components" rel="stylesheet" type="text/css"/>
	<!-- <link href="assets/global/css/plugins-md.css" rel="stylesheet" type="text/css"/> -->
	<!-- <link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/> -->
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/admin/layout/css/themes/darkblue.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/admin/layout/css/custom.css') }}" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->
	<link rel="shortcut icon" href="{{ asset(Config::get('constants.PUBLIC_PATH').'favicon.ico') }}"/>
</head>

<body class="page-md login">

	<div class="logo">
		<!--<a href="#">
			<h3 class="primary">PRICON MICROELECTRONICS, INC.</h3>
			<img src="assets/admin/layout/img/logo-big.png" alt=""/>
		</a>-->
	</div>

	<div class="menu-toggler sidebar-toggler"></div>

	@yield('content')

	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<!-- <script src="assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script> -->
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<!-- <script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script> -->
	<!-- <script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script> -->
	<!-- <script src="assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script> -->
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	{{-- <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script> --}}
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
	<!-- <script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script> -->
	<!-- <script src="assets/admin/layout/scripts/demo.js" type="text/javascript"></script> -->
	<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/admin/pages/scripts/login.js') }}" type="text/javascript"></script>
	<!-- END PAGE LEVEL SCRIPTS -->
	<script>
		jQuery(document).ready(function() {     
			Metronic.init(); // init metronic core components
			//Layout.init(); // init current layout
			//Login.init();
			//Demo.init();
			// $('myOjbect').css('background-image', 'url("assets/images/gray-waves-6571.jpg")');
		});
	</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>