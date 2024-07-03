@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Manage Extra Services</h3></div>
              <div class="portlet-body">
                  <div class="table-toolbar">
                  <div class="row">
                      <div class="col-md-12 col-xs-12">
                        <div class="btn-group pull-right">
                            <a class="btn blue" href="/admin/create/extraservice" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add New</a>
                        </div>
                      </div>
                  </div>
              </div>

              <div class="table-responsive">
                  <table id="table_extraservices" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>Id</td>
                            <td>Name</td>
                            <td>Price ({{$currency}})</td>
                            <td>Priority</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($extraservices as $service)
                        <tr id="tr_{{$service->id}}">
                        <td>{{$service->id}}</td>
                        <td>{{$service->name}}</td>
                        <td>{{$service->price}}</td>
                        <td><input type="number" value="{{$service->priority}}" onchange="updatePriority({{$service->id}}, 'extraservice', this.value)"></td>
                        <td>
                            <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="javascript:;" onclick="delete_item({{$service->id}}, 'extraservice');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
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
