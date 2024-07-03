@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Documents</h3></div>
              <div class="portlet-body">
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
              <div class="table-responsive">
                <table id="table_orders" class="table table-striped table-bordered table-hover table-checkable order-column">
                    <thead>
                    <tr style="font-weight: bold;" >
                          <td>Id</td>
                          <td>Cleaner</td>
                          <td>Id Proof</td>
                          <td>Address Proof</td>
                          <td>Status</td>
                          <td>Actions</td>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($documents as $document)
                      <tr id="tr_{{$document->id}}">
                          <td>{{$document->id}}</td>
                          <td><a href="/admin/cleaner/{{$document->cleaner->id}}">{{$document->cleaner->name}}</a></td>
                          <td>
                            @if ($document->id_proof != '')
                              <img src="/uploads/cleanerdata/{{$document->id_proof}}" height="40"/>
                            @else
                                <img src="/admin/images/noimg.jpeg" height="40"/>
                            @endif
                          </td>
                          <td>
                            @if ($document->address_proof != '')
                              <img src="/uploads/cleanerdata/{{$document->address_proof}}" height="40"/>
                            @else
                                <img src="/admin/images/noimg.jpeg" height="40"/>
                            @endif
                          </td>
                          <td>
                              @if ($document->status == 'pending')
                                  <a id="status{{$document->id}}" class="label label-default status{{$document->id}}">{{$document->status}}</a>
                              @elseif ($document->status == 'rejected')
                                  <a id="status{{$document->id}}" class="label label-danger status{{$document->id}}">{{$document->status}}</a>
                              @else
                                  <a id="status{{$document->id}}" class="label label-success status{{$document->id}}">{{$document->status}}</a>
                              @endif
                          </td>
                          <td>
                              <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/document/{{$document->id}}"><i class="glyphicon glyphicon-zoom-out"></i> View</a></li></ul></div>
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
