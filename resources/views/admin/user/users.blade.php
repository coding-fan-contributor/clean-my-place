@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Customers</h3></div>
              <div class="portlet-body">
                  <div class="table-toolbar">
                  <div class="row">
                      <div class="col-md-12 col-xs-12">
                            <div class="btn-group pull-right">
                                <a class="btn blue" id="export_users" style="margin-right: 5px;"><i class="fa fa-download" style="font-size: 14px;"></i> Export CSV</a>
                                <!-- <a class="btn blue" href="/CreateCustomer" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add New</a> -->
                            </div>

                      </div>
                  </div>
              </div>

              <div class="table-responsive">
                  <table id="table_users" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>ID</td>
                            <td>Image</td>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Stripe ID</td>
                            <td>Device</td>
                            <td>Status</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $user)
                        <tr id="tr_{{$user->id}}">
                        <td>{{$user->id}}</td>
                        <td>
                            @if ($user->image != '')
                                <img src="/uploads/profile/{{$user->image}}" height="40"/>
                            @else
                                <img src="/admin/images/noimg.jpeg" height="40"/>
                            @endif
                        </td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->stripe_customer_id}}</td>
                        <td>{{ucwords($user->device)}}</td>
                        <td>
                            @if ($user->status == 0)
                                <a id="status{{$user->id}}" class="label label-danger status{{$user->id}}" onclick="change_status({{$user->id}}, 'user');">Inactive</a></td>
                            @else
                                <a id="status{{$user->id}}" class="label label-success status{{$user->id}}" onclick="change_status({{$user->id}}, 'user');">Active</a></td>
                            @endif

                        <td>
                            <div class="btn-group" style="position:relative !important;"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/customer/{{$user->id}}"><i class="glyphicon glyphicon-edit"></i>Edit</a></li><li><a href="javascript:;" onclick="delete_item({{$user->id}}, 'user');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
                        </td>
                        </tr>
                        @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

  <style type="text/css">
      .table-scrollable{
          overflow-y: auto;
      }
  </style>
  <!-- END CONTENT BODY -->
  </div>

@endsection
