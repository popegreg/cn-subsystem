@extends('layouts.master')

@section('title')
    Home | Pricon Microelectronics, Inc.
@endsection

@section('content')

    <div class="page-content">
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey-gallery">
                    <div class="portlet-title">
                        <div class="caption">
                            YPICS SUBSYSTEM
                        </div>
                    </div>
                    <div class="portlet-body blue">
                        <p style="color:#fff;">
                            <strong>DISCLAIMER :</strong> Information appearing on PMI YPICS Sub System intranet application are copyrighted by Pricon Microelectronics, Inc. (PMI). Permission to reprint or electronically reproduce any document or graphic in part or in its entirely for any reason other than personal use is expressly prohibited, unless prior written consent is obtained from PMI staff and the proper entities.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-comments"></i> NOTIFICATIONS
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_3" style="font-size:10px;">

                            <thead>
                                <tr>
                                    <td width="20%">
                                        ITEM CODE
                                    </td>
                                    <td>
                                        ITEM NAME
                                    </td>
                                    <td width="20%">
                                        FOR ORDERING
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->itemcode}}</td>
                                    <td>{{$data->itemname}}</td>
                                    <td>{{$data->forordering}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>

@endsection

@push('script')
    <script type="text/javascript">
        // $('#inventory').DataTable({
        //     'bDestroy': true,
        //     'processing': true,
        //     'serverSide': true,
        //     'ajax': '/home/list',
        //     'columns': [
        //         {data: 'itemcode'},
        //         {data: 'itemname'},
        //         {data: 'forordering'}
        //     ]
        // });
    </script>
@endpush