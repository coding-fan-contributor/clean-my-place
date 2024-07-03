<html>
<head>
    <title>Admin Panel - V1 Technologies</title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.28/sweetalert2.min.css">

    <style>
        body{
            margin: 0;
            padding: 0;
            background: url('');
            background: rgba(0,0,0,0.2);
            background-size: cover;
            background-position: center;
            font-family: sans-serif;
        }

        .loginbox{
            width: 320px;
            height: 420px;
            background: #000;
            color: #fff;
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%,-50%);
            box-sizing: border-box;
            padding: 70px 30px;

            box-shadow: -5px 12px 10px rgba(0,0,0, 0.5);
        }

        .avatar{
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
        }

        h1{
            margin: 0;
            padding: 0 0 20px;
            text-align: center;
            font-size: 22px;
        }

        .loginbox p{
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .loginbox input{
            width: 100%;
            margin-bottom: 20px;
        }

        .loginbox input[type="text"], input[type="password"], input[type="email"]
        {
            border: none;
            border-bottom: 1px solid #fff;
            background: transparent;
            outline: none;
            height: 40px;
            color: #fff;
            font-size: 16px;
            padding-left: 8px;
        }
        .loginbox input[type="submit"]
        {
            border: none;
            outline: none;
            height: 40px;
            background: #fb2525;
            color: #fff;
            font-size: 18px;
            border-radius: 20px;
        }
        .loginbox input[type="submit"]:hover
        {
            cursor: pointer;
            background: #ffc107;
            color: #000;
        }
        .loginbox a{
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: darkgrey;
        }

        .loginbox a:hover
        {
            color: #ffc107;
        }

        .hidden{
            display: none;
        }
    </style>
</head>
<body>
<div class="loginbox">
    <img src="data:image/png;base64,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" class="avatar">
    <h1>Login Here Supported By Dev</h1>
    <form method="POST" action="/login_admin">
        {{csrf_field()}}
        <p>Email</p>
        <input id="email" type="email" name="email" placeholder="admin@demo.com" required>
        <p>Password</p>
        <input id="password" type="password" name="password" placeholder="demo123" required>
        <input type="submit" name="" value="Login">
        <!--<a href="#">Lost your password?</a><br>-->
        <!--<a href="#">Don't have an account?</a>-->
    </form>

</div>

</body>
<script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.28/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        //
    });
</script>
</html>
