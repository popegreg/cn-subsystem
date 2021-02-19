<div id="qc-permission-modal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog gray-gallery modal-xl">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">Users</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-8">
						<table class="table table-striped table-condensed table-bordered" id="tbl_users">
							<thead>
								<tr>
									<td>Last Name</td>
									<td>First Name</td>
									<td>Middles Name</td>
									<td>User ID</td>
									<td></td>
								</tr>
							</thead>
							<tbody id="tbl_users_body"></tbody>
						</table>
					</div>

					<div class="col-md-4">
						<form class="form-horizontal" action="" method>
							{!! csrf_field() !!}

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											Program
										</span>
										<select name="txtprogram" id="txtprogram" class="form-control input-sm" <?php echo($state); ?>></select>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											<input type="checkbox" id="chkCreate">
										</span>
										<input type="text" class="form-control" value="Create / Add New" disabled="disabled">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											<input type="checkbox" id="chkUpdate">
										</span>
										<input type="text" class="form-control" value="Update / Edit" disabled="disabled">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											<input type="checkbox" id="chkDelete">
										</span>
										<input type="text" class="form-control" value="Delete" disabled="disabled">
									</div>
								</div>
							</div>

							<hr>

							<input type="hidden" id="txtid" name="txtid">

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											Last Name
										</span>
										<input type="text" id="txtlastname" name="txtlastname" class="form-control" readonly="true">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											First Name
										</span>
										<input type="text" id="txtfirstname" name="txtfirstname" class="form-control" readonly="true">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
												Middle Name
										</span>
										<input type="text" id="txtmiddlename" name="txtmiddlename" class="form-control" readonly="true">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6">
									<button class="btn btn-sm blue btn-block" id="btnSave">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>

								<div class="col-md-6">
									<button class="btn btn-sm grey-gallery btn-block" id="btnCancel">
										<i class="fa fa-times"></i> Cancel
									</button>
								</div>
							</div>
							
						</form>
					</div>
				</div>
						

			</div>

			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="btn_search-close">Close</button>
			</div>

		</div>
	</div>
</div>