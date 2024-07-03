<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Title</title>
    <style>
        body{margin:0px; padding: 0px;}
        .welcome1{margin:0 auto;width:100%; max-width: 600px; height: auto; box-shadow:0 2px 3px rgba(0,0,0,0.10), 0 2px 3px rgba(0,0,0,0.10);}
        .welcome1 h2{padding:0 10px; font-weight:bold; font-size:17px;}
        .welcome1 ul li{line-height:25px;}
        .welcome{width:100%; box-shadow:0 2px 3px rgba(0,0,0,0.10), 0 2px 3px rgba(0,0,0,0.10); height:56px;}
        .welcome img{width:43%; float: right; padding:15px;}
        .footer{background: #000;}
        .footer p{
            letter-spacing: 1px;
            margin: 0px;
            padding: 15px 0;
            text-align: center;
            color: #fff;
            font-size: 12px;}
            .mail-body{
                padding: 15px;
                line-height: 22px;
                letter-spacing: 0.9px;
            }
    </style>
</head>
<body>

    <div class="welcome1">
        <div class="mail-body">
            Hello {{$name}},<br/><br/>
            There is a new order on the CleanMyPlace app, and it is in your area.<br/><br/>
            Location: {{$order_details->address}}, {{$order_details->postcode}}<br/><br/>
            Date & Time: {{$order_details->date}} {{$order_details->time}}<br/><br/>
            Hours: {{$order_details->hours}} Hours<br/><br/>
            Frequency: Every {{$order_details->frequency_days}} Days<br/><br/>

            Please go to the cleanmyplace app on your phone to accept or reject the order. if you can't access the app for whatever reason, please contact Daniel on 07527 680695 to let us know you would like the order.
            <br/>
            Regards,<br/><br/>
            {{env('APP_NAME')}} Team
        </div>
        <div class="footer">
            <p>&copy; {{date('Y')}} {{env('APP_NAME')}} All Right Reserved</p>
        </div>
    </div>
</body>
</html>
