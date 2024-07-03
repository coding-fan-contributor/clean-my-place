@extends('layouts.admin')

@section('content')

<div class="page-content">
	<div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{$users}}">{{$users}}</span>
                            <small class="font-green-sharp"></small>
                        </h3>
                        <small>USERS</small>
                        <small>: {{$sec}}</small>
                    </div>
                    <div class="icon">
                        <i class="icon-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{$cleaners}}">{{$cleaners}}</span>
                        </h3>
                        <small>Cleaners</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{$orders}}">{{$orders}}</span>
                        </h3>
                        <small>Order</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-database"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{$transactions}}">{{$transactions}}</span>
                        </h3>
                        <small>Transactions</small>
                    </div>
                    <div class="icon">
                        <i class="icon-speedometer"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{count($enquiries)}}">{{count($enquiries)}}</span>
                        </h3>
                        <small>Enquiries</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-envelope-o"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span data-counter="counterup" data-value="{{$ratings}}">{{$ratings}}</span>
                        </h3>
                        <small>reviews</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-comment-o"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject font-dark sbold uppercase"> Actions Required </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-pane" id="tab-2">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="datatable table table-striped table-bordered table-hover table-checkable order-column">
                                        <thead>
                                        <tr style="font-weight: bold;" >
                                                <td>Order Id</td>
                                                <td>User</td>
                                                <td>Cleaner</td>
                                                <td style="width: 180px !important;">Cleaner Cancel Reason</td>
                                                <td>Status</td>
                                                <td>updated_at</td>
                                                <td>Action</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orderactions as $order)
                                            <tr id="tr_{{$order->id}}">
                                                <td><a href="/admin/order/{{$order->id}}">{{$order->id}}</a></td>
                                                <td><a href="/admin/user/{{$order->user_id}}">{{$order->user->name}}</a></td>

                                                @php
                                                  if($order->cleaner):
                                                @endphp
                                                <td><a href="/admin/cleaner/{{$order->cleaner->id}}">{{$order->cleaner->name}}</a></td>
                                                @php
                                                  else:

                                                @endphp
                                                  <td></td>
                                                @php
                                                  endif;
                                                @endphp

                                                <td style="width: 150px; max-width:150px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{$order->reason}}</td>

                                                @php
                                                    if($order->status == 'paid'){
                                                    $stat = 'warning';
                                                }else{
                                                    $stat = 'danger';
                                                }
                                                @endphp
                                                <td><a class="label label-{{$stat}}">{{$order->status}}</a></td>
                                                <td>@if($order->updated_at){{date('d-m-Y h:i:s', strtotime($order->updated_at))}}@endif</td>
                                                @php
                                                    $order->reason = trim(preg_replace('/\s+/', ' ', $order->reason));
                                                    $order->reason = str_ireplace("'", "", $order->reason);
                                                @endphp
                                                <td>
                                                    <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/order/{{$order->id}}"><i class="glyphicon glyphicon-edit"></i> Assign a Cleaner</a></li><li><a href="javascript:;" onclick="viewModal('{{$order->reason}}');"><i class="glyphicon glyphicon-zoom-out"></i> Cancellation Reason</a></li><li><a href="/admin/order/{{$order->id}}"><i class="glyphicon glyphicon-edit"></i> Edit</a></li></ul></div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-envelope font-dark"></i>
                        <span class="caption-subject font-dark sbold uppercase"> Enquiries </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-pane" id="tab-3">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="table_enquirys" class="datatable table table-striped table-bordered table-hover table-checkable order-column">
                                        <thead>
                                        <tr style="font-weight: bold;" >
                                                <td>Id</td>
                                                <td>Name</td>
                                                <td>User</td>
                                                <td>Cleaner</td>
                                                <td>Email</td>
                                                <td>Subject</td>
                                                <td>Message</td>
                                                <td>Action</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($enquiries as $enquiry)
                                            <tr id="tr_{{$enquiry->id}}">
                                                <td>{{$enquiry->id}}</td>
                                                <td>{{$enquiry->name}}</td>
                                                @php
                                                  if($enquiry->user_id):
                                                @endphp
                                                <td><a href="/admin/user/{{$enquiry->user_id}}">{{$enquiry->user->name}}</a></td>
                                                @php
                                                  else:

                                                @endphp
                                                  <td></td>
                                                @php
                                                  endif;
                                                @endphp

                                                @php
                                                  if($enquiry->cleaner_id):
                                                @endphp
                                                <td><a href="/admin/cleaner/{{$enquiry->cleaner_id}}">{{$enquiry->cleaner->name}}</a></td>
                                                @php
                                                  else:

                                                @endphp
                                                  <td></td>
                                                @php
                                                  endif;
                                                @endphp

                                                <td>{{$enquiry->email}}</td>

                                                <td>{{$enquiry->subject}}</td>

                                                <td style="width: 150px; max-width:150px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{$enquiry->text}}</td>

                                                @php
                                                    $enquiry->text = trim(preg_replace('/\s+/', ' ', $enquiry->text));
                                                    $enquiry->text = str_ireplace("'", "", $enquiry->text);
                                                @endphp
                                                <td>
                                                    <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="javascript:;" onclick="viewModal('{{$enquiry->text}}');"><i class="glyphicon glyphicon-zoom-out"></i> View Message</a></li><li><a href="javascript:;" onclick="delete_item({{$enquiry->id}}, 'enquiry');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject font-dark sbold uppercase"> Cancelled by users </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-pane" id="tab-2">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered table-hover table-checkable order-column">
                                        <thead>
                                        <tr style="font-weight: bold;" >
                                                <td>Order Id</td>
                                                <td>User</td>
                                                <td>Cleaner</td>
                                                <td>Status</td>
                                                <td>updated_at</td>
                                                <td>Action</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cancelled as $order)
                                            <tr id="tr_{{$order->id}}">
                                                <td><a href="/admin/order/{{$order->id}}">{{$order->id}}</a></td>
                                                <td><a href="/admin/user/{{$order->user_id}}">{{$order->user->name}}</a></td>

                                                @php
                                                  if($order->cleaner):
                                                @endphp
                                                <td><a href="/admin/cleaner/{{$order->cleaner->id}}">{{$order->cleaner->name}}</a></td>
                                                @php
                                                  else:

                                                @endphp
                                                  <td></td>
                                                @php
                                                  endif;
                                                @endphp

                                                @php
                                                    if($order->status == 'paid'){
                                                    $stat = 'warning';
                                                }else{
                                                    $stat = 'danger';
                                                }
                                                @endphp
                                                <td><a class="label label-{{$stat}}">{{$order->status}}</a></td>
                                                <td>@if($order->updated_at){{date('d-m-Y h:i:s', strtotime($order->updated_at))}}@endif</td>
                                                <td>
                                                    <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/order/{{$order->id}}"><i class="glyphicon glyphicon-edit"></i> View</a></li></ul></div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<!-- END CONTENT BODY -->
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Message</h4>
            </div>
            <div class="modal-body">
                <p class="message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

@endsection
