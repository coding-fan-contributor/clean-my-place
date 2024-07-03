@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Orders</h3></div>
              <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="btn-group pull-left">
                                    <a class="btn blue" id="export_orders" style="margin-right: 5px;"><i class="fa fa-download" style="font-size: 14px;"></i> Export CSV</a>
                                </div>
                                <div class="btn-group pull-right">
                                  <form method="post" action="/admin/orders">
                                  {{ csrf_field() }}
                                  <input id="start" type="hidden" value="{{$start}}">
                                  <input id="end" type="hidden" value="{{$end}}">
                                  <input type="date" name="start" value="{{$start}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid #c2cad8; border-radius: 3px; padding-left: 15px;" max="2099-12-31">
                                  <input type="date" name="end" value="{{$end}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid#c2cad8; border-radius: 3px; padding-left: 15px;" max="2099-12-31">
                                  <button class="btn blue btn-default float-right" type="submit">Filter</button>
                                  </form>
                                </div>

                            </div>
                        </div>
                    </div>

              <div class="table-responsive">
                  <table id="table_orders" class="table table-striped table-bordered table-hover table-checkable order-column">
                      <thead>
                      <tr style="font-weight: bold;" >
                            <td>ID</td>
                            <td>Customer</td>
                            <td>Main Cleaner</td>
                            <td>Frequency</td>
                            <td>Hours</td>
                            <td>Date</td>
                            <td>Time</td>
                            <td>Status</td>
                            <td>Actions</td>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($orders as $order)
                        <tr id="tr_{{$order->id}}">
                            <td>{{$order->id}}</td>
                            <td><a href="/admin/customer/{{$order->user->id}}">{{$order->user->name}}</a></td>
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
                            <td>{{$order->frequency}}</td>
                            <td>{{$order->hours}}</td>
                            <td>{{date('d-m-Y', strtotime($order->date))}}</td>
                            <td>{{$order->time}}</td>
                            <td>
                                @if ($order->status == 'pending')
                                    <a id="status{{$order->id}}" class="label label-default status{{$order->id}}">{{$order->status}}</a>
                                @elseif ($order->status == 'cancelled-user' || $order->status == 'cancelled-cleaner' || $order->status == 'cancelled-admin')
                                    <a id="status{{$order->id}}" class="label label-danger status{{$order->id}}">{{$order->status}}</a>
                                @else
                                    <a id="status{{$order->id}}" class="label label-success status{{$order->id}}">{{$order->status}}</a>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="/admin/order/{{$order->id}}"><i class="glyphicon glyphicon-zoom-out"></i> View</a></li></ul></div>
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
