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
        <span class="caption-subject font-dark sbold uppercase"> New Push Notification </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/sendpush" id="PushForm">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="title" id="title" value="">
                            <label for="title"> Title :</label>

                            <span class="help-block " id="title_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="description" id="description" value="">
                            <label for="description"> Description :</label>

                            <span class="help-block " id="description_validate"></span>

                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="type"> Audience :</label>
                            <select name="type" style="min-width: 200px;margin-left: 10px;" onchange="showHideStripe(this.value)">
                                @foreach($types as $type)
                                    <option value="{{$type}}">{{ucwords($type)}}</option>
                                @endforeach
                            </select>

                            <span class="help-block " id="type_validate"></span>

                        </div>
                    </div>

                </div>

                <div id="showHideStripe" class="row" style="display: none;">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="stripe_id"> Customers With at least 1 Booking :</label>
                            <select name="stripe_id" style="min-width: 200px;margin-left: 10px;">
                                <option value="">N/A</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>

                            <span class="help-block " id="stripe_id_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="order_type"> Order Type :</label>
                            <select name="order_type" style="min-width: 200px;margin-left: 10px;">
                                <option value="">N/A</option>
                                <option value="oneoff">One Off</option>
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Bi Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>

                            <span class="help-block " id="order_type_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="order_status"> Order Status :</label>
                            <select name="order_status" style="min-width: 200px;margin-left: 10px;">
                                <option value="">N/A</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="accepted">Accepted</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled-user">Cancelled User</option>
                                <option value="cancelled-cleaner">Cancelled Cleaner</option>
                            </select>

                            <span class="help-block " id="order_status_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="SendPush" type="submit" class="btn red">Send</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </form>
    </div>
</div>

</div>
<!-- End: life time stats -->
</div>
</div>
</div>



<!-- END CONTENT BODY -->
</div>

@endsection

<script type="text/javascript">
    function showHideStripe(val){
        if(val == 'user'){
            $('#showHideStripe').show();
        }else{
            $('#showHideStripe').hide();
        }
    }
</script>
