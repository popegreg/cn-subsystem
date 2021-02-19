<!-- MRP User Modal -->
<div id="ypicsUserModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg blue">
		<div class="modal-content ">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">YPICS Users</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered table-hover table-fixedheader" id="tbl_ypicsuser">
					<thead>
						<tr>
							<td style="width: 25%">Input User</td>
							<td style="width: 25%">Input Date</td>
							<td style="width: 25%">Ckey</td>
							<td style="width: 25%">Intval</td>
						</tr>
					</thead>

					<tbody id="tbl_ypicsuser_body"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<div id="success" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm gray-gallery">
		<div class="modal-content ">
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/ypicsr3/print-orderdatareport') }}" id="frm_print">
				{!! csrf_field() !!}
				<div class="modal-body">
					<p>You successfully generated all the needed data.</p>
				</div>
				<input type="hidden" id="selected_dbconnect" name="selected_dbconnect" value="@if (Session::has('dbconnection')) {{Session::get('dbconnection')}}@endif">
				<input type="hidden" id="selected_supplier"  name="selected_supplier" value="@if (Session::has('selected_supplier')) {{Session::get('selected_supplier')}}@endif">
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" <?php echo($state); ?> ><i class="fa fa-print" ></i> Print Data</button>
					<a href="javascript:;" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</a>
				</div>
			</form>
		</div>
	</div>
</div>