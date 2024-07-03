@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Transactions</h3></div>
              <div class="portlet-body">

                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                          <button id="export_transactions" class="btn blue btn-default float-left" type="button"><i class="fa fa-download"></i>Export CSV</button>

                            <div class="btn-group pull-right">
                              <form method="post" action="/admin/transactions">
                              {{ csrf_field() }}
                              <input id="start" type="hidden" value="{{$start}}">
                              <input id="end" type="hidden" value="{{$end}}">
                              <input type="date" name="start" value="{{$start}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid #c2cad8; border-radius: 3px; padding-left: 15px;" max="2999-12-31">
                              <input type="date" name="end" value="{{$end}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid#c2cad8; border-radius: 3px; padding-left: 15px;" max="2999-12-31">
                              <button class="btn blue btn-default float-right" type="submit">Filter</button>
                              </form>
                            </div>

                        </div>
                    </div>
                </div>

              <div class="table-responsive">
                <table id="table_transactions" class="table table-striped table-bordered table-hover table-checkable order-column">
                    <thead>
                    <tr style="font-weight: bold;" >
                          <td>Id</td>
                          <td>User</td>
                          <td>Cleaner</td>
                          <td>Service Date</td>
                          <td>stripe_customer_id</td>
                          <td>transaction_id</td>
                          <td>Amount</td>
                          <td>Status</td>
                          <td style="width: 110px; display: inline-block;">Date</td>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($transactions as $transaction)
                      <tr id="tr_{{$transaction->id}}">
                          <td>{{$transaction->id}}</td>
                          <td><a href="/admin/user/{{$transaction->user_id}}">{{$transaction->user->name}}</a></td>

                          @php
                            if($transaction->cleaner):
                          @endphp
                          <td><a href="/admin/cleaner/{{$transaction->cleaner->id}}">{{$transaction->cleaner->name}}</a></td>
                          @php
                            else:
                          @endphp
                            <td></td>
                          @php  
                            endif;
                          @endphp

                          <td>{{date('d-m-Y', strtotime($transaction->service_date))}}</td>
                          <td>{{$transaction->stripe_customer_id}}</td>
                          <td>{{$transaction->transaction_id}}</td>
                          <td>{{$transaction->amount}}</td>
                          <td>
                              @if ($transaction->status == 'succeeded')
                                  <a class="label label-success">{{$transaction->status}}</a>
                              @elseif($transaction->status == 'partial-refunded')
                                  <a class="label label-warning">{{$transaction->status}}</a>
                              @else
                                  <a class="label label-danger">{{$transaction->status}}</a>
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

  <style type="text/css">
      .table-scrollable{
          overflow-y: auto;
      }
  </style>
  <!-- END CONTENT BODY -->
  </div>

@endsection
