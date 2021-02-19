<div id="formMarkUpModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm">

        <div class="modal-content blue">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ADD\EDIT Mark Ups</h4>
            </div>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/invoicing-markup-store') }}"
                id="frm_markup">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" id="id" name="id">

                    <div class="form-group" id="prod_line_div">
                        <label for="inputcode" class="col-md-4 control-label">Product Line</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control validate" id="prod_line" name="prod_line" autofocus>
                            <span class="help-block">
                                <strong id="prod_line_msg"></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group" id="mark_up_div">
                        <label for="inputname" class="col-md-4 control-label">Mark Up %</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="text" class="form-control validate" id="mark_up" name="mark_up">
                                <span class="input-group-addon">%</span>
                            </div>
                            <span class="help-block">
                                <strong id="mark_up_msg"></strong>
                            </span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                        Close</button>
                </div>
            </form>
        </div>

    </div>
</div>