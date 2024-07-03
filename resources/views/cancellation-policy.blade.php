<html>
    <head>
    	<title>Cancellation Policy</title>
    	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">
    	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <style>
        	body{
        		margin: 0;
        		padding: 0;
        		letter-spacing: 1px;
                font-size: 12px;
                font-family: 'Roboto', sans-serif;
        	}
            

            .header{
            	background-color: #DD5F46;
			    color: #fafafa;			    border: none;
			    -webkit-box-shadow: none;
			    box-shadow: none;
			    padding: 10px;
			    text-align: center;
            }

            section{
                padding: 15px;
            }

            .header-safearea{ 
                background: #DD5F46;
                height: 14px;
                display: none;
            }

            @supports (-webkit-touch-callout: none) {
              /* CSS specific to iOS devices */ 
              .header-safearea{
                display: block;
              }
            }
            


        </style>
        <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="header-safearea"></div>

    	<div class="header"><a href="javascript:history.go(-1);" style="display: inline; font-size: 16px; margin-left: 0px; color: #fafafa !important; float: left; text-decoration: none; letter-spacing: normal;">&larr; &nbsp;</a><span style="margin-left: -16px">Cancellation Policy</span></div>
        <section>
            {!!$data!!}
        </section>
        
    </body>
</html>