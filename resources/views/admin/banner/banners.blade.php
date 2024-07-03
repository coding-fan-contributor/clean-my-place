@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Manage Banners</h3></div>
              <div class="portlet-body">
                  <div class="table-toolbar">
                  <div class="row">
                      <div class="col-md-12 col-xs-12">
                        <div class="btn-group pull-right">
                            <a class="btn blue" href="/admin/create/banner" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add New</a>
                        </div>
                      </div>
                  </div>
              </div>

              <div class="table-responsive">
                  <table id="table_banners" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>id</td>
                            <td>Image</td>
                            <td>Link</td>
                            <td>Target Audience</td>
                            <td>Priority</td>
                            <td>Status</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($banners as $banner)
                        <tr id="tr_{{$banner->id}}">
                        <td>{{$banner->id}}</td>
                        <td>
                            @if ($banner->image != '')
                                <img src="/uploads/banner/{{$banner->image}}" height="40"/>
                            @else
                                <img src="/admin/images/noimg.jpeg" height="40"/>
                            @endif
                        </td>
                        <td>{{$banner->link}}</td>
                        <td>{{$banner->type}}</td>
                        <td><input type="number" value="{{$banner->priority}}" onchange="updatePriority({{$banner->id}}, 'banner', this.value)"></td>
                        <td>
                            @if ($banner->status == 0)
                                <a id="status{{$banner->id}}" class="label label-danger status{{$banner->id}}" onclick="change_status({{$banner->id}}, 'banner');">Inactive</a>
                            @else
                                <a id="status{{$banner->id}}" class="label label-success status{{$banner->id}}" onclick="change_status({{$banner->id}}, 'banner');">Active</a>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="javascript:;" onclick="delete_item({{$banner->id}}, 'banner');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
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
