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
        <span class="caption-subject font-dark sbold uppercase"> Edit Customer </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/updatecustomer/{{$user->id}}" id="UpdateUserForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" id="name" value="{{$user->name}}">
                            <label for="name"> Name :</label>

                            <span class="help-block " id="name_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row-2">
                    <div class="file-upload">
                        <label for="image" style="color: #999999 !important; font-size: 16px;">
                            Profile Image
                            <span> ( Upload Profile Image ) </span>
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @if($user->image)
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/profile/{{$user->image}}" data-featherlight="/uploads/profile/{{$user->image}}" style="height:inherit; padding-bottom: 10px;">
                                </div>
                            @endif
                            <span class="btn red btn-file"><span class="fileinput-new">Select image</span>
                                <input type="file" name="image" accept="image/*" />
                            </span>
                        </div>
                        <span class="help-block " id="image_validate"></span>
                    </div>
                    <br/>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="email" class="form-control" name="email" id="email" value="{{$user->email}}">
                            <label for="email"> Email :</label>

                            <span class="help-block " id="email_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="phone" id="phone" value="{{$user->phone}}">
                            <label for="phone"> Phone :</label>

                            <span class="help-block " id="phone_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="house_number" id="house_number" value="{{$user->house_number}}">
                            <label for="house_number"> Flat / House Number :</label>

                            <span class="help-block " id="house_number_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="address" id="address" value="{{$user->address}}">
                            <label for="address"> Address :</label>

                            <span class="help-block " id="address_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="postcode" id="postcode" value="{{$user->postcode}}">
                            <label for="postcode"> Postcode :</label>

                            <span class="help-block " id="postcode_validate"></span>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="password" class="form-control" name="password" id="password" value="" autocomplete="off">
                            <label for="password"> Password :</label>

                            <span class="help-block " id="password_validate"></span>

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
                    </div>
                </div>


            </div>
        </form>
    </div>
</div>

</div>


<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject font-dark sbold uppercase"> Orders </span>
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
                                    <td>ID</td>
                                    <td>Cleaner ID</td>
                                    <td>Hours</td>
                                    <td>Date</td>
                                    <td>Time</td>
                                    <td>Frequency</td>
                                    <td>Status</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->orders as $order)
                                <tr id="tr_{{$order->id}}">
                                    <td>{{$order->id}}</td>
                                    <td><a href="/admin/cleaner/{{$order->cleaner_id}}">{{$order->cleaner_id}}</a></td>
                                    <td>{{$order->hours}}</td>
                                    <td>{{date('d-m-Y', strtotime($order->date))}}</td>
                                    <td>{{$order->time}}</td>
                                    <td>{{$order->frequency}}</td>
                                    <td>
                                        @if ($order->status == 'paid' || $order->status == 'accepted' || $order->status == 'completed' )
                                            <a class="label label-success">{{$order->status}}</a>
                                        @elseif($order->status == 'pending')
                                            <a class="label label-default">{{$order->status}}</a>
                                        @else
                                            <a class="label label-danger">{{$order->status}}</a>
                                        @endif
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

<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject font-dark sbold uppercase"> Transactions </span>
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
                                    <td>Id</td>
                                    <td>Cleaner Id</td>
                                    <td>stripe_customer_id</td>
                                    <td>transaction_id</td>
                                    <td>Amount</td>
                                    <td>Status</td>
                                    <td>Date</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->transactions as $transaction)
                                <tr id="tr_{{$transaction->id}}">
                                    <td>{{$transaction->id}}</td>
                                    <td>@if($transaction->cleaner_id)<a href="/admin/cleaner/{{$transaction->cleaner_id}}">{{$transaction->cleaner_id}}</a>@endif</td>
                                    <td>{{$transaction->stripe_customer_id}}</td>
                                    <td>{{$transaction->transaction_id}}</td>
                                    <td>{{$transaction->amount}}</td>
                                    <td>
                                        @php
                                        if($transaction->status == 'succeeded'){
                                            $class = 'success';
                                        }else if($transaction->status == 'partial-refunded'){
                                            $class = 'warning';
                                        }else{
                                            $class = 'danger';
                                        }
                                        @endphp
                                        <a class="label label-{{$class}}">{{$transaction->status}}</a>
                                    </td>
                                    <td>{{date('d-m-Y H:i:s', strtotime($transaction->created_at))}}</td>
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

<!-- End: life time stats -->
</div>
</div>
</div>




<!-- END CONTENT BODY -->
</div>

@endsection
