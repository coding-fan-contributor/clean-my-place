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
        <span class="caption-subject font-dark sbold uppercase"> Extra Services </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/createextraservice" id="CreateExtraServiceForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" id="name" value="">
                            <label for="link"> Name :</label>

                            <span class="help-block " id="name_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="price" id="price" step="0.01">
                            <label for="price"> Price ({{$currency}}) :</label>

                            <span class="help-block " id="price_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="priority" id="priority" value="0">
                            <label for="priority"> Priority :</label>

                            <span class="help-block " id="priority_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="UpdateExtraService" type="submit" class="btn red">Save / Submit</button>
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
