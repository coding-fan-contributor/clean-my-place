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
        <span class="caption-subject font-dark sbold uppercase"> Edit Mail Content </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/UpdateMailContent/{{$mailcontent->id}}" id="UpdateUserForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="type"> Type :</label>
                            <select name="type" style="min-width: 150px;margin-left: 10px;">
                                <option value="Registration" @if ($mailcontent->type == 'Registration') selected @endif>Registration</option>
                                <option value="Verification" @if ($mailcontent->type == 'Verification') selected @endif>Verification</option>
                                <option value="Contact" @if ($mailcontent->type == 'Contact') selected @endif>Contact</option>
                                <option value="Receat Password" @if ($mailcontent->type == 'Receat Password') selected @endif>Receat Password</option>
                            </select>

                            <span class="help-block " id="type_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                        <label for="content"> Content :</label>
                            <textarea class="form-control" name="content">@isset($mailcontent->content){{$mailcontent->content}}@endisset</textarea>
                            <span class="help-block " id="content_validate"></span>

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
