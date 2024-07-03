<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact</title>
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
            <h4>{{$name}} sent you a message</h4>
            {{$text}}<br/><br/>

            Regards,<br/><br/>
            {{env('APP_NAME')}} Team
        </div>
        <div class="footer">
            <p>&copy; {{date('Y')}} {{env('APP_NAME')}} All Right Reserved</p>
        </div>
    </div>
</body>
</html>
