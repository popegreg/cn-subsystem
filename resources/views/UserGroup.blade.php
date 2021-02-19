@extends('layouts.master')
@section('title')
	User Group Master | Pricon Microelectronics, Inc.
@endsection

@section('content')

	<?php $state = ""; $readonly = ""; ?>
	@foreach ($userProgramAccess as $access)
		@if ($access->program_code == Config::get('constants.MODULE_CODE_UGRP'))  <!-- Please update "2001" depending on the corresponding program_code -->
			@if ($access->read_write == "2")
			<?php $state = "disabled"; $readonly = "readonly"; ?>
			@endif
		@endif
	@endforeach

	<div class="page-content">

		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				@include('includes.message-block')
				<div class="portlet box blue" >
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>  User Group Master
						</div>
					</div>
					<div class="portlet-body">

						<div class="row">
					        <div class="col-md-6 col-md-offset-3">
					            <form method="POST" action="" class="form-horizontal" id="findfrm">
					                {{ csrf_field() }}

					                <div class="form-group">
					                    <label class="control-label col-md-2">Find: </label>
					                    <div class="col-md-9">
					                        <input type="text" class="form-control" id="find" name="find">
					                    </div>

					                </div>

					            </form>
					        </div>
					    </div>

					    <div class="row">
					        <div class="col-md-6 col-md-offset-3">
					            <div class="scroller" style="height: 200px">
					                <table class="table table-striped table-bordered table-hover">
					                    <thead>
					                        <tr>
					                            <th class="table-checkbox" style="width: 10px">
					                                <input type="checkbox" class="group-checkable"/>
					                            </th>
					                            <th style="width: 10px"></th>
					                            <th>Description</th>
					                        </tr>
					                    </thead>
					                    <tbody>
					                        <tr>
					                            <td>
					                                <input type="checkbox" class="checkboxes" id="check_id" name="check_id[]"/>
					                            </td>
					                            <td>
					                                <a href="#" id="edit" class="getName" data-name=""><i class="fa fa-edit"></i></a>
					                            </td>
					                            <td></td>
					                        </tr>
					                    </tbody>
					                </table>
					            </div>
					        </div>
					    </div>

					    <div class="row">
					        <div class="col-md-4 col-md-offset-5">
					            <a href="#" id="add" class="btn btn-success">
					                <i class="fa fa-plus"></i> Add
					            </a>
					            <button type="button" class="btn btn-danger">
					                <i class="fa fa-trash"></i> Delete
					            </button>
					        </div>
					    </div>


					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>

    <div id="grpnameModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog gray-gallery">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" action="" class="form-horizontal" id="statusmdl">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <div class="col-sm-7">
                                        <p>
                                            User Group name is required.
                                        </p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3">Group Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control input-sm" id="grpname" name="grpname">
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-success">Save</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

<script src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$( document ).ready(function(e) {
		$('#add').on('click', function(e) {
			$('#grpnameModal').modal('show');
			$('.modal-title').html('Add Group Name');
		});

        $('#edit').on('click', function(e) {
			var id = $('.getName').attr('data-name');
			$('#grpnameModal').modal('show');
			$('.modal-title').html('Edit Group Name');
			$('#grpname').val(id);
		});

	});
</script>
