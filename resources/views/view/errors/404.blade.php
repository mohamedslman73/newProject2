
<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
<title>EGPAY - {{$exception->getStatusCode()}} ERROR</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href='//fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
<style type="text/css">
body{
	font-family: 'Courgette', cursive;
}
body{
	background:#25a8e2;
}	
.wrap{
	margin:0 auto;
	width:1000px;
}
.logo{
	margin-top:50px;
}	
.logo h1{
	font-size:200px;
	color:#FFF;
	text-align:center;
	margin-bottom:1px;
	text-shadow:1px 1px 6px #fff;
}	
.logo p{
	color: #FFF;
	font-size:20px;
	margin-top:1px;
	text-align:center;
}	
.logo p span{
	color:lightgreen;
}	
.sub a{
	color:#25a8e2;
	background:#FFF;
	text-decoration:none;
	padding:7px 120px;
	font-size:13px;
	font-family: arial, serif;
	font-weight:bold;
	-webkit-border-radius:3em;
	-moz-border-radius:.1em;
	-border-radius:.1em;
}	
.footer{
	color:#8F8E8C;
	position:absolute;
	right:10px;
	bottom:10px;
}	
.footer a{
	color:rgb(228, 146, 162);
}	
</style>
<script type="text/javascript" src="http://gc.kis.v2.scr.kaspersky-labs.com/86EF2CB3-2839-D042-9AD7-A90EA8A91955/main.js" charset="UTF-8"></script></head>


<body>
<!---728x90--->
	<div class="wrap">
	   <div class="logo">
	   <h1>{{$exception->getStatusCode()}}</h1>
		   @if($exception->getMessage() == '')
	    <p>{{__('Opps... !')}}</p>
		   @else
			   <p>{{$exception->getMessage()}}</p>
		   @endif
		<!---728x90--->
  	      <div class="sub">
	        <p><a href="javascript:;" onclick="window.history.back();">Back</a></p>
	      </div>
        </div>
	</div>
	<!---728x90--->
	<div class="footer">
	</div>
<script>
    setTimeout('history.go(-1)', 3000);
</script>
	
</body>