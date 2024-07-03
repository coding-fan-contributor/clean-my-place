@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Manage Cleaners</h3></div>
              <div class="portlet-body">
                  <div class="table-toolbar">
                  <div class="row">
                      <div class="col-md-12 col-xs-12">
                            <div class="btn-group pull-right">
                                <a class="btn blue" id="export_cleaners" style="margin-right: 5px;"><i class="fa fa-download" style="font-size: 14px;"></i> Export CSV</a>
                                <!-- <a class="btn blue" href="/admin/create/cleaner" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add New</a> -->
                            </div>

                      </div>
                  </div>
              </div>

              <div class="table-responsive">
                  <table id="table_cleaners" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>id</td>
                            <td>Image</td>
                            <td>Name</td>
                            <td>Postcode</td>
                            <td>Email</td>
                            <td>Rating</td>
                            <td>Available</td>
                            <td>Status</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($cleaners as $cleaner)
                        <tr id="tr_{{$cleaner->id}}">
                        <td>{{$cleaner->id}}</td>
                        <td>
                            @if ($cleaner->image != '')
                                <img src="/uploads/profile/{{$cleaner->image}}" height="40"/>
                            @else
                                <img src="/admin/images/noimg.jpeg" height="40"/>
                            @endif
                        </td>
                        <td>{{$cleaner->name}} @if($cleaner->details && $cleaner->details->status == 'accepted')<i style="color: #36c6d3; font-size: 2rem;" class="fa fa-check-circle-o" title="Document Verified"></i>@elseif($cleaner->details && $cleaner->details->status == 'rejected')<i style="color: red; font-size: 2rem;" class="fa fa-close" title="Document Rejected"></i>@endif</td>
                        <td>{{$cleaner->postcode}}</td>
                        <td>{{$cleaner->email}}</td>
                        <td>{{$cleaner->rating}}</td>
                        <td>
                            @if ($cleaner->available == 'no')
                                <a id="status{{$cleaner->id}}" class="label label-danger status_availability_{{$cleaner->id}}" onclick="change_availability({{$cleaner->id}});">No</a>
                            @else
                                <a id="status{{$cleaner->id}}" class="label label-success status_availability_{{$cleaner->id}}" onclick="change_availability({{$cleaner->id}});">Yes</a>
                            @endif

                        </td>
                        <td>
                            @if ($cleaner->status == 0)
                                <a id="status{{$cleaner->id}}" class="label label-danger status{{$cleaner->id}}" onclick="change_status({{$cleaner->id}}, 'cleaner');">Inactive</a>
                            @else
                                <a id="status{{$cleaner->id}}" class="label label-success status{{$cleaner->id}}" onclick="change_status({{$cleaner->id}}, 'cleaner');">Active</a>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/cleaner/{{$cleaner->id}}"><i class="glyphicon glyphicon-edit"></i>Edit</a></li><li><a href="javascript:;" onclick="delete_item({{$cleaner->id}}, 'cleaner');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
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
