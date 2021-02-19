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

            <div class="form-group">
                <p>CN Subsystem is current unavailable. Please come back later.</p>
            </div>
        </form>
    </div>
@endsection