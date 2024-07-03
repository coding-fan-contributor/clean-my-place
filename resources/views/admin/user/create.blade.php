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
        <span class="caption-subject font-dark sbold uppercase"> Add Customer </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/CreateCustomer" id="CreateUserForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="is_admin" value="true"/>

            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" id="name" value="">
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
                            <input type="email" class="form-control" name="email" id="email" value="">
                            <label for="email"> Email :</label>

                            <span class="help-block " id="email_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="phone" id="phone" value="">
                            <label for="phone"> Phone :</label>

                            <span class="help-block " id="phone_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="house_number" id="house_number" value="">
                            <label for="house_number"> Flat / House Number :</label>

                            <span class="help-block " id="house_number_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="address" id="address" value="">
                            <label for="address"> Address :</label>

                            <span class="help-block " id="address_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="postcode" id="postcode" value="">
                            <label for="postcode"> Postcode :</label>

                            <span class="help-block " id="postcode_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="password" class="form-control" name="password" id="password" value="" required>
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
<!-- End: life time stats -->
</div>
</div>
</div>




<!-- END CONTENT BODY -->
</div>

@endsection
