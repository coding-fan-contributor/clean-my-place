@extends('layouts.admin')

@section('content')
<!-- BEGIN CONTENT BODY -->
<div class="page-content">
    <div class="row" >
      <div class="col-md-12" >
          <div class="portlet light bordered" >
              <div class="portlet-title" ><h3>Payouts</h3></div>
              <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                    <div class="btn-group pull-right">
                                      <form method="post" action="/admin/payouts">
                                      {{ csrf_field() }}
                                      <input id="month" type="hidden" value="{{$month}}">
                                      <input id="year" type="hidden" value="{{$year}}">
                                      <input id="start" type="hidden" value="{{$start}}">
                                      <input id="end" type="hidden" value="{{$end}}">
                                      <select id="monthSelect" class="form-control pull-left" name="month" style="width: 150px !important; margin-right: 10px;">
                                        @foreach($months as $key => $val)
                                          <option value="{{$key}}" @if($key == $month) selected @endif>{{$val}}</option>
                                        @endforeach
                                      </select>
                                      <input id="startInput" type="date" name="start" value="{{$start}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid #c2cad8; border-radius: 3px; padding-left: 15px;" onChange="validateDates()" max="2999-12-31">
                                      <input id="endInput" type="date" name="end" value="{{$end}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid#c2cad8; border-radius: 3px; padding-left: 15px;" onChange="validateDates()" max="2999-12-31">
                                      <button class="btn blue btn-default pull-right" type="submit">Filter</button>
                                      </form>
                                    </div>

                            </div>
                        </div>
                    </div>

              <div class="table-responsive">
                <table id="table_payout" class="table table-striped table-bordered table-hover table-checkable order-column">
                    <thead>
                    <tr style="font-weight: bold;" >
                          <td>Cleaner</td>
                          <td>Total</td>
                          <td>Full refunded</td>
                          <td>Partial Refunded</td>
                          <td>Payble</td>
                          <td>Action</td>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($payouts as $payout)
                      <tr id="tr_{{$payout->cleaner_id}}">
                          <td><a href="/admin/cleaner/{{$payout->cleaner_id}}">{{$payout->cleaner_name}}</a></td>
                          <td>{{$payout->total}}</td>
                          <td>- {{$payout->full}}</td>
                          <td>- {{$payout->partial}}</td>
                          <td>{{$payout->payble}}</td>
                          <td>
                            <a id="status{{$payout->cleaner_id}}" class="label label-success status{{$payout->cleaner_id}}" onclick="Pay({{$payout->cleaner_id}}, {{$payout->payble}});">Pay</a>
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

<script>
  function validateDates(){
    var startInput = $('#startInput').val();
    var endInput = $('#endInput').val();
    var monthSelect = $('#monthSelect').val();

    if(monthSelect >= 10){
      monthSelect --;
    }else{
      monthSelect = ('0' + (monthSelect - 1)).slice(-2);
    }

    //console.log(monthSelect);

    if(startInput != ''  && endInput != ''){
      // validate for month
      if(new Date(startInput).getMonth() != monthSelect || new Date(endInput).getMonth() != monthSelect){
        // empty range values
        $('#startInput').val('');
        $('#endInput').val('');
        swal('Error', 'Date range month must be same as the selected month for payout', 'error');
      }else if(new Date(startInput).getMonth() != new Date(endInput).getMonth()){
        // empty range values
        $('#startInput').val('');
        $('#endInput').val('');
        swal('Error', 'Date range must be selected from same month for payout', 'error');
      }else if(startInput > endInput){
        // empty range values
        $('#startInput').val('');
        $('#endInput').val('');
        swal('Error', 'Start date can not be greater than end date', 'error');
      }else{
        // done
      }
    }
  }
</script>
