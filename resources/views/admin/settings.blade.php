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
        <span class="caption-subject font-dark sbold uppercase"> Settings </span>
    </div>

</div>
<div class="portlet-body">

    <div class="tab-pane active" id="tab_1">
            <form role="form" method="post" action="/admin/createorupdatesetting" id="UpdateSettingsForm" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="form-body">
                <div class="row-2">
                    <div class="file-upload">
                        <label for="image" style="color: #999999 !important; font-size: 16px;">
                            App Logo
                            <span> ( Upload App Logo ) </span>
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @isset($settings['image'])
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px; background: #ebebeb;">
                                    <img src="/uploads/images/{{$settings['image']}}" data-featherlight="/uploads/images/{{$settings['image']}}" style="height:inherit; padding-bottom: 10px;">
                                </div>
                            @endisset
                            <span class="btn red btn-file"><span class="fileinput-new">Select image</span>
                                <input type="file" name="image" accept="image/*" />
                            </span>
                        </div>
                        <span class="help-block " id="image_validate"></span>
                    </div>
                    <br/>
                </div>
                <div class="row-2">
                    <div class="file-upload">
                        <label for="icon" style="color: #999999 !important; font-size: 16px;">
                            App Icon for Push Notification
                            <span> ( Upload push notification icon ) </span>
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @isset($settings['icon'])
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/images/{{$settings['icon']}}" data-featherlight="/uploads/images/{{$settings['icon']}}" style="height:inherit; padding-bottom: 10px;">
                                </div>
                            @endisset
                            <span class="btn red btn-file"><span class="fileinput-new">Select image</span>
                                <input type="file" name="icon" accept="image/*" />
                            </span>
                        </div>
                        <span class="help-block " id="icon_validate"></span>
                    </div>
                    <br/>
                </div>

                <div class="row-2">
                    <div class="file-upload">
                        <label for="cleaner_login_image" style="color: #999999 !important; font-size: 16px;">
                            App Image for Cleaner Login Page
                            <span> ( Login image ) </span>
                        </label>
                        <div class="fileinput fileinput-new full-width" data-provides="fileinput">
                            @isset($settings['cleaner_login_image'])
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 150px;">
                                    <img src="/uploads/images/{{$settings['cleaner_login_image']}}" data-featherlight="/uploads/images/{{$settings['cleaner_login_image']}}" style="height:inherit; padding-bottom: 10px;">
                                </div>
                            @endisset
                            <span class="btn red btn-file"><span class="fileinput-new">Select image</span>
                                <input type="file" name="cleaner_login_image" accept="image/*" />
                            </span>
                        </div>
                        <span class="help-block " id="cleaner_login_image_validate"></span>
                    </div>
                    <br/>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="name" value="@isset($settings['name']){{$settings['name']}}@endisset">
                            <label for="name"> App Name :</label>
                            <span class="help-block " id="name_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="email" class="form-control" name="email" value="@isset($settings['email']){{$settings['email']}}@endisset">
                            <label for="email"> Email :</label>
                            <span class="help-block " id="email_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="email" class="form-control" name="verification_email" value="@isset($settings['verification_email']){{$settings['verification_email']}}@endisset">
                            <label for="verification_email"> Verification Email :</label>
                            <span class="help-block " id="verification_email_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="email" class="form-control" name="legal_email" value="@isset($settings['legal_email']){{$settings['legal_email']}}@endisset">
                            <label for="legal_email"> Legal Email :</label>
                            <span class="help-block " id="legal_email_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="address" value="@isset($settings['address']){{$settings['address']}}@endisset">
                            <label for="address"> Address :</label>
                            <span class="help-block " id="address_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="phone" value="@isset($settings['phone']){{$settings['phone']}}@endisset">
                            <label for="phone"> Phone :</label>
                            <span class="help-block " id="phone_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <label for="distance_type"> Distance Type :</label>
                            <select name="distance_type" style="min-width: 400px;margin-left: 10px;">
                               <option value="km" @isset($settings['distance_type']) @if($settings['distance_type'] == 'km') selected @endif  @endisset>Km</option>
                               <option value="mile" @isset($settings['distance_type']) @if($settings['distance_type'] == 'mile') selected @endif  @endisset>Mile</option>
                            </select>

                            <span class="help-block " id="distance_type_validate"></span>

                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="description">@isset($settings['description']){{$settings['description']}}@endisset</textarea>
                            <label for="description"> About Us :</label>
                            <span class="help-block " id="description_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="terms">@isset($settings['terms']){{$settings['terms']}}@endisset</textarea>
                            <label for="terms"> Terms & Conditions :</label>
                            <span class="help-block " id="terms_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="privacy">@isset($settings['privacy']){{$settings['privacy']}}@endisset</textarea>
                            <label for="privacy"> Privacy & Policy :</label>
                            <span class="help-block " id="privacy_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="cancellation">@isset($settings['cancellation']){{$settings['cancellation']}}@endisset</textarea>
                            <label for="cancellation"> Cancellation Policy :</label>
                            <span class="help-block " id="cancellation_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="insurance">@isset($settings['insurance']){{$settings['insurance']}}@endisset</textarea>
                            <label for="insurance"> Insurance :</label>
                            <span class="help-block " id="insurance_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <textarea class="form-control" name="community">@isset($settings['community']){{$settings['community']}}@endisset</textarea>
                            <label for="community"> Community Guidelines :</label>
                            <span class="help-block " id="community_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="caption">
                            <i class="icon-settings font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase"> Order & pricing settings </span>
                        </div>
                        <br/>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="booking_min_hours" value="@isset($settings['booking_min_hours']){{$settings['booking_min_hours']}}@endisset">
                            <label for="booking_min_hours"> Minimum hours required before order starts :</label>
                            <span class="help-block " id="booking_min_hours_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="cleaners_count" value="@isset($settings['cleaners_count']){{$settings['cleaners_count']}}@endisset">
                            <label for="cleaners_count"> No. of Cleaners to notify once new order posted:</label>
                            <span class="help-block " id="cleaners_count_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="free_cancellation_days" value="@isset($settings['free_cancellation_days']){{$settings['free_cancellation_days']}}@endisset">
                            <label for="free_cancellation_days"> Free Cancellation Before (days):</label>
                            <span class="help-block " id="free_cancellation_days_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="paid_cancellation_hours" value="@isset($settings['paid_cancellation_hours']){{$settings['paid_cancellation_hours']}}@endisset">
                            <label for="paid_cancellation_hours"> Paid Cancellation Before (hours):</label>
                            <span class="help-block " id="paid_cancellation_hours_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="paid_cancellation_charge" value="@isset($settings['paid_cancellation_charge']){{$settings['paid_cancellation_charge']}}@endisset">
                            <label for="paid_cancellation_charge"> Paid Cancellation Charge (%):</label>
                            <span class="help-block " id="paid_cancellation_charge_validate"></span>
                        </div>
                    </div>

                    <hr/>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="short_notice_skip_charge" value="@isset($settings['short_notice_skip_charge']){{$settings['short_notice_skip_charge']}}@endisset">
                            <label for="short_notice_skip_charge"> Cleaning Skip Charge for notice less than 3 hours ({{$currency}}):</label>
                            <span class="help-block " id="short_notice_skip_charge_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="same_day_skip_charge" value="@isset($settings['same_day_skip_charge']){{$settings['same_day_skip_charge']}}@endisset">
                            <label for="same_day_skip_charge"> Same Day Skip Charge ({{$currency}}):</label>
                            <span class="help-block " id="same_day_skip_charge_validate"></span>
                        </div>
                    </div>

                    <hr/>

                    <div class="col-lg-12 col-md-12">
                        <div class="caption">
                            <i class="icon-settings font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase"> Cronjob time settings </span>
                        </div>
                        <br/>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="cron_cleaner_hours" value="@isset($settings['cron_cleaner_hours']){{$settings['cron_cleaner_hours']}}@endisset">
                            <label for="cron_cleaner_hours"> Number of hours before Cleaner gets notified / reminded about upcoming booking:</label>
                            <span class="help-block " id="cron_cleaner_hours_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="number" class="form-control" name="cron_user_hours" value="@isset($settings['cron_user_hours']){{$settings['cron_user_hours']}}@endisset">
                            <label for="cron_user_hours"> Number of hours before User gets notified / reminded about upcoming booking:</label>
                            <span class="help-block " id="cron_user_hours_validate"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="caption">
                            <i class="icon-settings font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase"> Version settings </span>
                        </div>
                        <br/>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="android_version" value="@isset($settings['android_version']){{$settings['android_version']}}@endisset" maxlength="10">
                            <label for="android_version"> Latest Android Version:</label>
                            <span class="help-block " id="android_version_validate"></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group form-md-line-input form-md-floating-label">
                            <input type="text" class="form-control" name="ios_version" value="@isset($settings['ios_version']){{$settings['ios_version']}}@endisset" maxlength="10">
                            <label for="ios_version"> Latest iOS Version:</label>
                            <span class="help-block " id="ios_version_validate"></span>
                        </div>
                    </div>


            </div>

            <div class="row">
                    <div class="col-lg-12 clearfix">
                        <div class="form-actions pull-right">
                            <div class="btn-set">
                                <button id="UpdateSetting" type="submit" class="btn red">Save / Submit</button>
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
