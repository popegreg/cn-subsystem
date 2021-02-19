<div id="formMatrixModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">

		<div class="modal-content blue">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">IQC Matrix</h4>
			</div>
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/iqc-matrix-store') }}" id="frm_matrix">
				<div class="modal-body">
					{!! csrf_field() !!}
					<input type="hidden" id="id" name="id">

					<div class="form-group" id="item_div">
						<label for="inputcode" class="col-md-3 control-label">Item Code</label>
						<div class="col-md-8">
							<input type="text" class="form-control validate" id="item" name="item" autofocus>
							<span class="help-block">
                                <strong id="item_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="item_desc_div">
						<label for="inputcode" class="col-md-3 control-label">Description</label>
						<div class="col-md-8">
							<input type="text" class="form-control validate" id="item_desc" name="item_desc">
							<span class="help-block">
                                <strong id="item_desc_msg"></strong>
                            </span>
						</div>
					</div>

					<div class="form-group" id="classification_div">
						<label for="inputname" class="col-md-3 control-label">Classification</label>
						<div class="col-md-8">
							<select class="form-control validate" id="classification" name="classification">
								<option></option>
							</select>
							<span class="help-block">
                                <strong id="classification_msg"></strong>
                            </span>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				</div>
			</form>
		</div>

	</div>
</div>
