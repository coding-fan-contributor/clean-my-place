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
        <span class="caption-subject font-dark sbold uppercase"> View Document </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
        <form role="form" method="post" action="/admin/updatedocument/{{$document->id}}" id="UpdateDocumentForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" id="name" value="{{$document->cleaner->name}}" disabled="true">
                            <label for="name"> Cleaner Name :</label>

                            <span class="help-block " id="name_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row-2">
                    <div class="file-upload">
                        <label for="image" style="color: #999999 !important; font-size: 16px;">
                            Id Proof
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @if($document->id_proof)
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/cleanerdata/{{$document->id_proof}}" style="height:inherit; padding-bottom: 10px;" data-featherlight="/uploads/cleanerdata/{{$document->id_proof}}">
                                </div>
                            @endif
                        </div>
                        <span class="help-block " id="image_validate"></span>
                    </div>
                    <br/>
                </div>

                <div class="row-2">
                    <div class="file-upload">
                        <label for="image" style="color: #999999 !important; font-size: 16px;">
                            Address Proof
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @if($document->address_proof)
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/cleanerdata/{{$document->address_proof}}" style="height:inherit; padding-bottom: 10px;" data-featherlight="/uploads/cleanerdata/{{$document->address_proof}}">
                                </div>
                            @endif
                        </div>
                        <span class="help-block " id="image_validate"></span>
                    </div>
                    <br/>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="distance" id="distance" value="{{$document->cleaner->distance}}">
                            <label for="distance"> Distance to travel :</label>

                            <span class="help-block " id="distance_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="experience" id="experience" value="{{$document->experience}}">
                            <label for="experience"> Experience :</label>

                            <span class="help-block " id="experience_validate"></span>

                        </div>
                    </div>
                </div>

                @php
                    if($document->bank_details){
                        $bank = json_decode($document->bank_details);
                    }
                @endphp

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="bank[name]" id="bank_name" value="@if(isset($bank->name)){{$bank->name}}@endif">
                            <label for="bank_name"> Bank Name :</label>

                            <span class="help-block " id="bank_name_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="bank[beneficiary]" id="beneficiary_name" value="@if(isset($bank->beneficiary)){{$bank->beneficiary}}@endif">
                            <label for="beneficiary_name"> Beneficiary Name :</label>

                            <span class="help-block " id="beneficiary_name_validate"></span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="bank[number]" id="number" value="@if(isset($bank->number)){{$bank->number}}@endif">
                            <label for="number"> Account/IBAN Number :</label>

                            <span class="help-block " id="number_validate"></span>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="bank[code]" id="code" value="@if(isset($bank->code)){{$bank->code}}@endif">
                            <label for="code"> Branch Code/SWIFT Code/SORT Code :</label>

                            <span class="help-block " id="code_validate"></span>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="status"> Change Status :</label>
                            <select name="status" style="min-width: 400px;margin-left: 10px;">
                               <option value="pending" @if ($document->status == 'pending') selected @endif>Pending</option>
                               <option value="accepted" @if ($document->status == 'accepted') selected @endif>Accepted</option>
                               <option value="rejected" @if ($document->status == 'rejected') selected @endif>Rejected</option>
                            </select>

                            <span class="help-block " id="status_validate"></span>

                        </div>
                    </div>
                </div>
                <div class="row" id="stripe_acct_id_block">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="stripe_acct_id" id="stripe_acct_id" value="@if(isset($document->cleaner->stripe_acct_id)){{$document->cleaner->stripe_acct_id}}@endif">
                            <label for="stripe_acct_id"> Stripe Account ID :</label>

                            <span class="help-block " id="stripe_acct_id_validate"></span>

                        </div>
                    </div>
                </div>

                

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="reason" id="reason" value="@if(isset($document->reason)){{$document->reason}}@endif">
                            <label for="reason"> Reject Reason :</label>

                            <span class="help-block " id="reason_validate"></span>

                        </div>
                    </div>
                </div>


                


                <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="UpdateDocument" type="submit" class="btn red">Save / Submit</button>
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
