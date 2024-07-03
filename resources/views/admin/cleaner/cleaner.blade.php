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
        <span class="caption-subject font-dark sbold uppercase"> Edit Cleaner </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/updatecleaner/{{$cleaner->id}}" id="UpdateUserForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" id="name" value="{{$cleaner->name}}">
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
                            @if($cleaner->image)
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/profile/{{$cleaner->image}}" data-featherlight="/uploads/profile/{{$cleaner->image}}" style="height:inherit; padding-bottom: 10px;">
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
                            <input type="email" class="form-control" name="email" id="email" value="{{$cleaner->email}}">
                            <label for="email"> Email :</label>

                            <span class="help-block " id="email_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="phone" id="phone" value="{{$cleaner->phone}}">
                            <label for="phone"> Phone :</label>

                            <span class="help-block " id="phone_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="address" id="address" value="{{$cleaner->address}}">
                            <label for="address"> Address :</label>

                            <span class="help-block " id="address_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="postcode" id="postcode" value="{{$cleaner->postcode}}">
                            <label for="postcode"> Postcode :</label>

                            <span class="help-block " id="postcode_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="distance" id="distance" value="{{$cleaner->distance}}">
                            <label for="distance"> Distance in miles/kms :</label>

                            <span class="help-block " id="distance_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="dob"> Date Of Birth :</label><br />
                            <input id="dob" type="date" name="dob" value="{{$cleaner->dob}}" style="width: 150px !important; margin-right: 10px; height: 32px; border: 1px solid #c2cad8; border-radius: 3px; padding-left: 15px;" max="{{ date('Y-m-d')}}">                            

                            <span class="help-block " id="dob_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="about" id="about" placeholder="About">{{$cleaner->about}}</textarea>

                            <span class="help-block " id="about_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="qualification" id="qualification" placeholder="Qualification">{{$cleaner->qualification}}</textarea>

                            <span class="help-block " id="qualification_validate"></span>

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
            <span class="caption-subject font-dark sbold uppercase"> Schedule </span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="tab-pane" id="tab-2">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="panel panel-success">
                            <div class="panel-heading bold">Update Schedule</div>
                            <div class="panel-body">
                                <form method="post" action="/admin/updatecleanerchedule/{{$cleaner->id}}">
                                    {{ csrf_field() }}
                                    <select name="day" class="btn" style="margin-right: 15px;">
                                    <option value="mon" > Monday </option>
                                    <option value="tue" > Tuesday </option>
                                    <option value="wed" > Wednesday </option>
                                    <option value="thu" > Thursday </option>
                                    <option value="fri" > Friday </option>
                                    <option value="sat" > Saturday </option>
                                    <option value="sun" > Sunday </option>
                                    </select>
                                    <select name="start" class="btn" style="margin-right: 15px;">
                                        <option value="06:00:00" > 06:00:00 </option>
                                        <option value="07:00:00" > 07:00:00 </option>
                                        <option value="08:00:00" > 08:00:00 </option>
                                        <option value="09:00:00" > 09:00:00 </option>
                                        <option value="10:00:00" > 10:00:00 </option>
                                        <option value="11:00:00" > 11:00:00 </option>
                                        <option value="12:00:00" > 12:00:00 </option>
                                        <option value="13:00:00" > 13:00:00 </option>
                                        <option value="14:00:00" > 14:00:00 </option>
                                        <option value="15:00:00" > 15:00:00 </option>
                                        <option value="16:00:00" > 16:00:00 </option>
                                        <option value="17:00:00" > 17:00:00 </option>
                                        <option value="18:00:00" > 18:00:00 </option>
                                        <option value="19:00:00" > 19:00:00 </option>
                                        <option value="20:00:00" > 20:00:00 </option>
                                        <option value="21:00:00" > 21:00:00 </option>
                                        <option value="22:00:00" > 22:00:00 </option>
                                    </select>
                                    <select name="end" class="btn" style="margin-right: 15px;">
                                        <option value="06:00:00" > 06:00:00 </option>
                                        <option value="07:00:00" > 07:00:00 </option>
                                        <option value="08:00:00" > 08:00:00 </option>
                                        <option value="09:00:00" > 09:00:00 </option>
                                        <option value="10:00:00" > 10:00:00 </option>
                                        <option value="11:00:00" > 11:00:00 </option>
                                        <option value="12:00:00" > 12:00:00 </option>
                                        <option value="13:00:00" > 13:00:00 </option>
                                        <option value="14:00:00" > 14:00:00 </option>
                                        <option value="15:00:00" > 15:00:00 </option>
                                        <option value="16:00:00" > 16:00:00 </option>
                                        <option value="17:00:00" > 17:00:00 </option>
                                        <option value="18:00:00"  selected> 18:00:00 </option>
                                        <option value="19:00:00" > 19:00:00 </option>
                                        <option value="20:00:00" > 20:00:00 </option>
                                        <option value="21:00:00" > 21:00:00 </option>
                                        <option value="22:00:00" > 22:00:00 </option>
                                    </select>

                                <input type="submit" name="submit" class="btn btn-success" id="generateSchedule" value="Save" >
                                </form>
                            </div>
                        </div>

                        <div class="panel panel-success">
                            <div class="panel-heading bold">Current Weekly Schedule</div>
                           </div>
                            <div class="panel-body">
                              <div class="row">
                                      <div class="panel-group" id="accordion">
                                        <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Monday"><span class="glyphicon glyphicon-plus"></span> Monday</a>
                                                  </h4>
                                              </div>
                                              <div id="Monday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                      @if (isset($schedules['mon']))
                                                          @foreach ($schedules['mon'] as $item)
                                                            <div class="row" id="schedule_{{$item['id']}}">
                                                                <div class="col-md-4">
                                                                    <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                                </div>
                                                            </div>
                                                          @endforeach
                                                      @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Tuesday"><span class="glyphicon glyphicon-plus"></span> Tuesday</a>
                                                  </h4>
                                              </div>
                                              <div id="Tuesday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['tue']))
                                                        @foreach ($schedules['tue'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Wednesday"><span class="glyphicon glyphicon-plus"></span> Wednesday</a>
                                                  </h4>
                                              </div>
                                              <div id="Wednesday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['wed']))
                                                        @foreach ($schedules['wed'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Thursday"><span class="glyphicon glyphicon-plus"></span> Thursday</a>
                                                  </h4>
                                              </div>
                                              <div id="Thursday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['thu']))
                                                        @foreach ($schedules['thu'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Friday"><span class="glyphicon glyphicon-plus"></span> Friday</a>
                                                  </h4>
                                              </div>
                                              <div id="Friday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['fri']))
                                                        @foreach ($schedules['fri'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Saturday"><span class="glyphicon glyphicon-plus"></span> Saturday</a>
                                                  </h4>
                                              </div>
                                              <div id="Saturday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['sat']))
                                                        @foreach ($schedules['sat'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                     <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="panel panel-primary">
                                              <div class="panel-heading">
                                                  <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#Sunday"><span class="glyphicon glyphicon-plus"></span> Sunday</a>
                                                  </h4>
                                              </div>
                                              <div id="Sunday" class="panel-collapse collapse">
                                                  <div class="panel-body">
                                                    @if (isset($schedules['sun']))
                                                        @foreach ($schedules['sun'] as $item)
                                                          <div class="row" id="schedule_{{$item['id']}}">
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_start[]" value="{{$item['start']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <input type="text" name="mon_end[]" value="{{$item['end']}}" disabled>
                                                              </div>
                                                              <div class="col-md-4">
                                                                  <a onclick="remove_btn({{$item['id']}}, {{$item['cleaner_id']}});" ><i class="glyphicon glyphicon-trash" ></i> remove</a>
                                                              </div>
                                                          </div>
                                                        @endforeach
                                                    @endif
                                                      <!-- this day times -->
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <p>
                                          <input type="submit" class="btn btn-success hide" name="artist_submit_schedule" onclick="$('input').removeAttr('disabled','disabled');" value="Submit Schedule">
                                      </p>
                              </div>
                          </div>


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
            <span class="caption-subject font-dark sbold uppercase"> Order / Bookings </span>
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
                                    <td>User Id</td>
                                    <td>Hours</td>
                                    <td>Date</td>
                                    <td>Time</td>
                                    <td>Frequency</td>
                                    <td>Status</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleaner->orders as $order)
                                <tr id="tr_{{$order->id}}">
                                    <td>{{$order->id}}</td>
                                    <td><a href="/admin/user/{{$order->user_id}}">{{$order->user_id}}</a></td>
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
            <span class="caption-subject font-dark sbold uppercase"> Orders / Bookings </span>
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
                                    <td>User Id</td>
                                    <td>Order Id</td>
                                    <td>Frequency</td>
                                    <td>Status</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleaner->orders as $order)
                                <tr id="tr_{{$order->id}}">
                                    <td>{{$order->id}}</td>
                                    <td><a href="/admin/user/{{$order->user_id}}">{{$order->user_id}}</a></td>
                                    <td><a href="/admin/order/{{$order->id}}">{{$order->id}}</a></td>
                                    <td>{{$order->frequency}}</td>
                                    <td>
                                        @if ($order->status == 'active')
                                            <a class="label label-success">Active</a>
                                        @elseif($order->status == 'inactive')
                                            <a class="label label-danger">Inactive</a>
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
                                    <td>User Id</td>
                                    <td>stripe_customer_id</td>
                                    <td>transaction_id</td>
                                    <td>Amount</td>
                                    <td>Status</td>
                                    <td>Date</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleaner->transactions as $transaction)
                                <tr id="tr_{{$transaction->id}}">
                                    <td>{{$transaction->id}}</td>
                                    <td><a href="/admin/user/{{$transaction->user_id}}">{{$transaction->user_id}}</a></td>
                                    <td>{{$transaction->stripe_customer_id}}</td>
                                    <td>{{$transaction->transaction_id}}</td>
                                    <td>{{$transaction->amount}}</td>
                                    <td>
                                        @if ($transaction->status == 'succeeded')
                                            <a class="label label-success">{{$transaction->status}}</a>
                                        @elseif($transaction->status == 'refunded')
                                            <a class="label label-danger">{{$transaction->status}}</a>
                                        @elseif($transaction->status == 'partial-refunded')
                                            <a class="label label-warning">{{$transaction->status}}</a>
                                        @endif
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

<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject font-dark sbold uppercase"> Payouts </span>
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
                                    <td>Month</td>
                                    <td>Year</td>
                                    <td>Amount</td>
                                    <td>Status</td>
                                    <td>Date</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleaner->payouts as $payout)
                                <tr id="tr_{{$payout->id}}">
                                    <td>{{$payout->id}}</td>
                                    <td>{{$payout->month}}</td>
                                    <td>{{$payout->year}}</td>
                                    <td>{{$payout->amount}}</td>
                                    <td>
                                        @if ($payout->status == 'paid')
                                            <a class="label label-success">{{$payout->status}}</a>
                                        @else($payout->status == 'pending')
                                            <a class="label label-default">{{$payout->status}}</a>
                                        @endif
                                    </td>
                                    <td>{{date('d-m-Y', strtotime($payout->created_at))}}</td>
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
            <span class="caption-subject font-dark sbold uppercase"> Reviews </span>
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
                                    <td>User Id</td>
                                    <td>Rating</td>
                                    <td>Comment</td>
                                    <td>Action</td>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleaner->ratings as $review)
                                <tr id="tr_{{$review->id}}">
                                    <td>{{$review->id}}</td>
                                    <td><a href="/admin/user/{{$review->user_id}}">{{$review->user_id}}</a></td>
                                    <td>{{$review->rating}}</td>
                                    <td style="width: 350px; max-width:350px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{$review->comment}}</td>
                                    @php
                                        $review->comment = trim(preg_replace('/\s+/', ' ', $review->comment));
                                        $review->comment = str_ireplace("'", "", $review->comment);
                                    @endphp
                                    <td>
                                        <div class="btn-group" style="position:relative !important;"><button class="btn btn-xs red dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions<i class="fa fa-angle-down"></i></button><ul class="dropdown-menu pull-right " role="menu" style="width:5px;"><li><a href="javascript:;" onclick="viewModal('{{$review->comment}}');"><i class="glyphicon glyphicon-zoom-out"></i> View</a></li><li><a href="javascript:;" onclick="delete_item({{$review->id}}, 'review');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li></ul></div>
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


<!-- End: life time stats -->
</div>
</div>
</div>




<!-- END CONTENT BODY -->
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Comment</h4>
        </div>
        <div class="modal-body">
            <p class="message"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

@endsection
