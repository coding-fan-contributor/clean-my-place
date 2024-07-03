@extends('layouts.admin')

@section('content')

<!-- BEGIN CONTENT BODY -->
<div class="page-content">
<!-- END THEME PANEL -->

<!-- END PAGE HEADER-->
<div class="row">
<div class="col-md-12">
<!-- Begin: life time stats -->
<div class="portlet light portlet-fit portlet-datatable bordered">
<div class="portlet-title">
        @if(session('success'))
            <div id="alert" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div id="alert" class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    <div class="caption">
        <i class="icon-settings font-dark"></i>
        <span class="caption-subject font-dark sbold uppercase"> View / update Order Details </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        
        <div class="form-body">
            <form role="form" method="post" action="/admin/updateorder/{{$order->id}}" id="UpdateOrderForm" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="user" id="user" value="{{$order->user->name}} - ({{$order->user->email}}) - (id: {{$order->user->id}})" disabled>
                            <label for="user"> User :</label>

                            <span class="help-block " id="user_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="address" id="address" value="{{$order->address}}">
                            <label for="address"> Address :</label>

                            <span class="help-block " id="address_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="postcode" id="postcode" value="{{$order->postcode}}">
                            <label for="postcode"> Postcode :</label>

                            <span class="help-block " id="postcode_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="date" id="date" value="{{date('d-m-Y', strtotime($order->date))}}" maxlength="10">
                            <label for="date"> Date :</label>

                            <span class="help-block " id="date_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="time" id="time" value="{{$order->time}}">
                            <label for="time"> Time :</label>

                            <span class="help-block " id="time_validate"></span>

                        </div>
                    </div>

                    <div class="col-lg-2 col-md-2">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="hours" id="hours" value="{{$order->hours}}">
                            <label for="hours"> Hours :</label>

                            <span class="help-block " id="hours_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="frequency"> Frequency :</label>
                            <select name="frequency" style="min-width: 150px;margin-left: 10px;">
                                <option value="oneoff" @if ($order->frequency == 'oneoff') selected @endif>Oneoff</option>
                                <option value="oneoff" @if ($order->frequency == 'daily') selected @endif>Daily</option>
                                <option value="weekly" @if ($order->frequency == 'weekly') selected @endif>Weekly</option>
                                <option value="biweekly" @if ($order->frequency == 'biweekly') selected @endif>Biweekly</option>
                                <option value="monthly" @if ($order->frequency == 'monthly') selected @endif>Monthly</option>

                            </select>

                            <span class="help-block " id="frequency_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="status"> Status :</label>
                            <select name="status" style="min-width: 150px;margin-left: 10px;">
                                <option value="pending" @if ($order->status == 'pending') selected @endif>Pending</option>
                                <option value="paid" @if ($order->status == 'paid') selected @endif>Paid</option>
                                <option value="accepted" @if ($order->status == 'accepted') selected @endif>Accepted</option>
                                <option value="completed" @if ($order->status == 'completed') selected @endif>Completed</option>
                                <option value="cancelled-admin" @if ($order->status == 'cancelled-admin') selected @endif>Cancelled-admin</option>
                                <option value="cancelled-user" @if ($order->status == 'cancelled-user') selected @endif>Cancelled-user</option>
                                <option value="cancelled-cleaner" @if ($order->status == 'cancelled-cleaner') selected @endif disabled>Cancelled-cleaner</option>

                            </select>

                            <span class="help-block " id="status_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="payment_status"> Payment Status :</label>
                            <select name="payment_status" style="min-width: 150px;margin-left: 10px;">
                                <option value="pending" @if ($order->payment_status == 'pending') selected @endif>Pending</option>
                                <option value="paid" @if ($order->payment_status == 'paid') selected @endif>Paid</option>
                                <option value="failed" @if ($order->payment_status == 'failed') selected @endif>failed</option>
                                <option value="refunded" @if ($order->payment_status == 'refunded') selected @endif>Refunded</option>

                            </select>

                            <span class="help-block " id="payment_status_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="cleaner_id"> Assign as cleaner :</label>
                            <select name="cleaner_id" style="min-width: 400px;margin-left: 10px;">
                                <option value="">Select a cleaner</option>
                                @foreach ($cleaners as $cleaner)
                                    <option value="{{$cleaner->id}}" @if ($order->cleaner_id == $cleaner->id) selected @endif>{{$cleaner->name}} - ({{$cleaner->email}}) - (id: {{$cleaner->id}})</option>
                                @endforeach

                            </select>

                            <span class="help-block " id="cleaner_id_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="reason" id="reason" value="{{$order->reason}}">
                            <label for="reason"> Cancel Reason :</label>

                            <span class="help-block " id="reason_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="UpdateUser" type="submit" class="btn red">Save / Submit</button>
                            </div>
                        </div>
                        <div class="form-actions pull-right" style="margin-right: 10px">
                            <div class="btn-set">
                                <button id="ResendNotifications" type="button" class="btn" onclick="resend_notifications({{$order->id}})">Resend Notifications</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

                @if (count($notifications) > 0)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-settings font-dark"></i>
                                <span class="caption-subject font-dark sbold uppercase"> Order Request</span>
                            </div>

                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <th>Id</th>
                                        <th>Cleaner</th>
                                        <th>Request Alternate Day / Times</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                        <tr>
                                            <td>{{$notification->id}}</td>
                                            <td><a href="/admin/cleaner/{{$notification->cleaner->id}}">{{$notification->cleaner->name}}</a></td>
                                            <td>{{$notification->available_alternate}}</td>
                                            <td>
                                                <form role="form" method="post" action="/admin/accept_order/{{$order->id}}/{{$notification->cleaner->id}}" id="UpdateOrderForm" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                    <button id="status{{$notification->id}}" class="label label-success status{{$notification->id}}">Order Requested</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if ($order->cleans)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-settings font-dark"></i>
                                <span class="caption-subject font-dark sbold uppercase"> History </span>
                            </div>

                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover table-checkable order-column datatableClass">
                                    <thead>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->cleans as $clean)
                                        <tr>
                                            <td>{{$clean->id}}</td>

                                            <td><a id="date_{{$clean->id}}" onclick="changeDate({{$clean->id}}, '{{date('d-m-Y', strtotime($clean->date))}}')">{{date('d-m-Y', strtotime($clean->date))}} &nbsp;<i class="fa fa-edit"></i></a></td>
                                            <td>
                                                @php
                                                    if($clean->status == 'paid'){
                                                        $class = 'success';
                                                    }else if($clean->status == 'pending'){
                                                        $class = 'default';
                                                    }else if($clean->status == 'completed'){
                                                        $class = 'success';
                                                    }

                                                @endphp
                                                <a id="history_status{{$clean->id}}" class="label label-{{$class}} status{{$clean->id}}" onclick="change_cleans_status({{$clean->id}})" title="Click to change status">{{$clean->status}}</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
                @endif


                @if ($order->transactions && count($order->transactions) > 0)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-settings font-dark"></i>
                                <span class="caption-subject font-dark sbold uppercase"> Transactions </span>
                            </div>

                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <th>Id</th>
                                        <th>Service Date</th>
                                        <th>stripe_customer_id</th>
                                        <th>transaction_id</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->transactions as $transaction)
                                        <tr>
                                            <td>{{$transaction->id}}</td>
                                            <td>{{date('d-m-Y', strtotime($transaction->service_date))}}</td>
                                            <td>{{$transaction->stripe_customer_id}}</td>
                                            <td>{{$transaction->transaction_id}}</td>
                                            <td>{{$transaction->amount}}</td>
                                            <td>
                                                @if ($transaction->status == 'failed' || $transaction->status == 'refunded')
                                                    <a id="status{{$transaction->id}}" class="label label-danger status{{$transaction->id}}">{{$transaction->status}}</a>
                                                @elseif ($transaction->status == 'partial-refunded')
                                                    <a id="status{{$transaction->id}}" class="label label-warning status{{$transaction->id}}">{{$transaction->status}}</a>
                                                @elseif ($transaction->status == 'succeeded')
                                                    <a id="status{{$transaction->id}}" class="label label-success status{{$transaction->id}}">{{$transaction->status}}</a>
                                                @else
                                                    <a id="status{{$transaction->id}}" class="label label-default status{{$transaction->id}}">{{$transaction->status}}</a>
                                                @endif
                                            </td>
                                            <td>{{date('d-m-Y h:i:s', strtotime($transaction->created_at))}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
                @endif

            </div>
        
    </div>
</div>

</div>

<!-- End: life time stats -->
</div>
</div>
</div>




<!-- END CONTENT BODY -->
</div>

@endsection
