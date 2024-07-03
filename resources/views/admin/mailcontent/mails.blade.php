
@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Manage Mail Content</h3></div>
              <div class="portlet-body">
                  <div class="table-toolbar">
                  <div class="row">
                      <div class="col-md-12 col-xs-12">
                            <div class="btn-group pull-right">
                                <a class="btn blue" href="/CreateMailContent" id="add_new"><i class="fa fa-plus" style="font-size: 18px;"></i> Add New</a>
                            </div>

                      </div>
                  </div>
              </div>

              <div class="table-responsive">
                  <table id="table_cleaners" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                        <td>id</td>
                        <td>Type</td>
                        <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($mailcontent as $mailcontent_data)
                        <tr id="tr_{{$mailcontent_data->id}}">
                        <td>{{$mailcontent_data->id}}</td>
                        
                        <td>{{$mailcontent_data->type}}</td>
                        <td>
                            <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/MailContentEdit/{{$mailcontent_data->id}}"><i class="glyphicon glyphicon-edit"></i>Edit</a></li><li><a href="javascript:;" onclick="delete_item({{$mailcontent_data->id}}, 'mailcontent');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
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
