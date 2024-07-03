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
        <span class="caption-subject font-dark sbold uppercase"> New Review </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/createreview" id="CreateReviewForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="comment" id="comment" value="">
                            <label for="comment"> Comment :</label>

                            <span class="help-block " id="comment_validate"></span>

                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="rating"> Rating :</label>
                            <select name="rating" style="min-width: 200px;margin-left: 10px;">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>

                            <span class="help-block " id="rating_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="user"> User :</label>
                            <select name="user_id" style="min-width: 200px;margin-left: 10px;">
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->id}}-{{$user->name}}</option>
                                @endforeach

                            </select>

                            <span class="help-block " id="user_validate"></span>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="cleaner"> Cleaner :</label>
                            <select name="cleaner_id" style="min-width: 200px;margin-left: 10px;">
                                @foreach ($cleaners as $cleaner)
                                    <option value="{{$cleaner->id}}">{{$cleaner->id}}-{{$cleaner->name}}</option>
                                @endforeach

                            </select>

                            <span class="help-block " id="cleaner_validate"></span>

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="CreateRating" type="submit" class="btn red">Save / Submit</button>
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
