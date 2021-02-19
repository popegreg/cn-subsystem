<?php header("Content-type: text/html; charset=utf-8");?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Language" content="ja" />
	<meta charset="utf-8">
	<title>@yield('title')</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta http-equiv="cache-control" content="private, max-age=0, no-cache">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta content="" name="description"/>
	<meta content="" name="author"/>
	<!-- CSRF PREVENTION -->
	<meta name="csrf-token" content="{{ Session::token() }}">
	<!-- BEGIN PACE PLUGIN FILES -->
	<script src="assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
	<link href="assets/global/plugins/pace/themes/pace-theme-flash.css" rel="stylesheet" type="text/css"/>
	<!-- END PACE PLUGIN FILES -->

	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-select/bootstrap-select.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-fixed-table/table-fixed-header.css"/>
	<link href="assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
	<link href="assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
	<link href="assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/clockface/css/clockface.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME STYLES -->
	<link href="assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>
	<link href="assets/global/css/plugins-md.css" rel="stylesheet" type="text/css"/>
	<link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
	<link id="style_color" href="assets/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css"/>
	<link href="assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->
	<link rel="shortcut icon" href="favicon.ico"/>
</head>

<body class="page-md page-header-fixed page-quick-sidebar-over-content page-full-width">
	@yield('content')

	<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-alert/alert.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

	<script type="text/javascript" src="assets/global/plugins/fuelux/js/spinner.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
	<script type="text/javascript" src="assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
	<script src="assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-filestyle/bootstrap-filestyle.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-fixed-table/table-fixed-header.js" type="text/javascript"></script>
	<script src="assets/global/plugins/bootstrap-select/bootstrap-select.js" type="text/javascript"></script>
	<script src="assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
	<script src="assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/clockface/js/clockface.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
	<script src="assets/global/plugins/icheck/icheck.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="assets/global/scripts/metronic.js" type="text/javascript"></script>
	<script src="assets/global/scripts/user.js" type="text/javascript"></script>
	<script src="assets/global/scripts/productline.js" type="text/javascript"></script>
	<script src="assets/global/scripts/orderdatacheck.js" type="text/javascript"></script>
	<script src="assets/global/scripts/mra.js" type="text/javascript"></script>
	<script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
	<!--<script src="assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>-->
	<script src="assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
	<script src="assets/admin/pages/scripts/index.js" type="text/javascript"></script>
	<!--<script src="assets/admin/pages/scripts/tasks.js" type="text/javascript"></script>-->
	<script src="assets/admin/pages/scripts/table-managed.js"></script>
	<!--<script src="assets/admin/pages/scripts/form-icheck.js"></script>-->
	<script src="assets/global/scripts/datetime.js" type="text/javascript"></script>
	<script src="assets/admin/pages/scripts/components-form-tools.js"></script>
	<script src="assets/admin/pages/scripts/components-pickers.js"></script>
	<!-- END PAGE LEVEL SCRIPTS -->

	<!-- BEGIN:File Upload Plugin JS files-->
	<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
	<script src="assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
	<!-- The Templates plugin is included to render the upload/download listings -->
	<script src="assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
	<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
	<script src="assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
	<!-- The Canvas to Blob plugin is included for image resizing functionality -->
	<script src="assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
	<!-- blueimp Gallery script -->
	<script src="assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
	<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
	<script src="assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
	<!-- The basic File Upload plugin -->
	<script src="assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
	<script src="assets/admin/pages/scripts/table-editable.js"></script>
	<script src="assets/global/plugins/progressbar/dist/js/jquery.progresstimer.js"></script>
	
	@stack('script')
	<!--DITO YUNG STACK SCRIPT-->

	<!-- END:File Upload Plugin JS files-->
	<script type="text/javascript">
		jQuery(document).ready(function() {
			$(".alert").fadeTo(2000, 300).slideUp(300, function(){
				$(".alert").alert('close');
			});
			Metronic.init(); // init metronic core componets
			Layout.init(); // init layout
			//QuickSidebar.init(); // init quick sidebar
			Demo.init(); // init demo features
			TableManaged.init();
			ComponentsFormTools.init();
			ComponentsPickers.init();
			TableEditable.init();
			//FormFileUpload.init();
			//FormiCheck.init();
			// $('select').selectpicker();
		});
		window.onload = date_time('date_time');
		window.onload = load;
	</script>
	@if (Session::has('partStartPO'))
		<script type="text/javascript">
			$(document).ready(function() {
				$('#confirm').modal('show');
			});
		</script>
	@endif
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
