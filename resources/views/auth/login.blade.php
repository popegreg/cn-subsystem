@extends('layouts.loginlayout')

@section('title')
    Pricon Microelectronics, Inc.
@endsection

@section('content')
    <div class="content blue-madison">
        <form class="login-form" action="{{ url('/login') }}" method="post">
            <!--<div class="alert alert-danger display-hide">
                <button class="close" data-close="alert"></button>
                <span>
                Enter any username and password. </span>
            </div>-->
            <div class="titlehead">
                <img src="{{ asset(Config::get('constants.PUBLIC_PATH').'assets/images/PRICON-LOGO.png') }}" alt="" class="img-responsive">
            </div>
            <div class="tpicshead text-center">
                <h4>CN YPICS SUBSYSTEM</h4>
            </div>

            @if (\Session::has('productline_error'))
                <div class="alert alert-danger">
                    <!-- <button type="button" id="btn_close_error" class="close text-white" data-dismiss="alert" aria-hidden="true">&times;</button> -->
                    <strong>Failed!</strong> {{ \Session::get('productline_error') }} <br/>
                </div>
            @endif

            <div class="form-group {{$errors->has('user_id') ? 'has-error' : ''}}">
                <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                <label class="control-label visible-ie8 visible-ie9">User ID</label>
                <div class="input-icon">
                    <i class="fa fa-user"></i>
                    <input class="form-control input-sm placeholder-no-fix" type="text" placeholder="User ID" name="user_id" id="user_id" value="{{ old('user_id') }}" autocomplete="off" />
                    @if ($errors->has('user_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('user_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group {{$errors->has('password') ? 'has-error' : ''}}">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input class="form-control input-sm placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" id="password"/>
                    {{ csrf_field() }}
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="fa fa-send"></i> Login </button>
                <button type="button" onclick="javascript:reset()" class="btn blue pull-right"><i class="fa fa-refresh"></i> Reset </button>
            </div>
            
        </form>
    </div>
@endsection
@push('script')
    <script type="text/javascript">
        function reset() {
            $('#user_id').val("");
            $('#password').val("");
        }
    </script>
@endpush