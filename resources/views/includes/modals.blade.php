<!-- Progress Bar -->
<div id="progress" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md gray-gallery">
        <div class="modal-content ">
            <div class="modal-body">
                <div id="progressbar" class="progress progress-striped active">
                    <div class="progress-bar progress-bar-success myprogress" id="progressbar-color" role="progressbar" style="width:0%"></div>
                </div>
                <div id="progress-msg"></div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="progress-close" class="btn btn-danger" disabled>Close</button>
            </div>
        </div>
    </div>
</div>

<!--msg-->
<div id="msg_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm gray-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 id="msg_status" class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <p id="msg_content"></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn grey-gallery">Close</button>
            </div>
        </div>
    </div>
</div>

<!--delete-->
<div id="delete_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm gray-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete?</p>
                <input type="hidden" name="delete_id" id="delete_id">
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirm_delete" class="btn green">Yes</button>
                <button type="button" data-dismiss="modal" class="btn red">No</button>
            </div>
        </div>
    </div>
</div>

<!--confirm-->
<div id="confirm_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm gray-gallery">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body">
                <p id="confirm_question"></p>
                <input type="hidden" name="confirm_id" id="confirm_id">
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirm" class="btn green">Yes</button>
                <button type="button" data-dismiss="modal" class="btn red">No</button>
            </div>
        </div>
    </div>
</div>

<!-- AJAX LOADER -->
<div id="loading" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm gray-gallery">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/ajax-loader.gif') }}" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  