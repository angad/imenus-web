<?php 
/**
 * @author angad
 */

$fetch_time = 5;
?>

<html>
<head>
	<title>Orders</title>

<style>
body{
	width:1024px;
	margin:0 auto;
}

#waiter
{
	font-size:30px;
	font-family:sans-serif;
	margin:20px 0px 10px 0px;
}

.status,
.time,
.table_number
{
	float:left;
	width:300px;
}


</style>

<script>

	window.onload = function(){
	  interval = setInterval('fetch()', <?php echo $fetch_time ?> *1000);
	};

	function fetch()
	{
		document.getElementById("requests").innerHTML="<div></div>";

		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				document.getElementById("requests").innerHTML=xmlhttp.responseText;
			}
		}

		xmlhttp.open("POST","http://imenus.tk/index.php/Kitchen/waiter/getRequests/", true);
		xmlhttp.send();
	}
	
	function removeCall(id)
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				document.getElementById("button"+id).innerHTML = "";
			}
		}
		xmlhttp.open("POST","http://imenus.tk/index.php/Kitchen/waiter/removeCall/", true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("id="+id);
	}
	

</script>

</head>
<body>

<div id = "outer-wrapper">

<div id = "waiter">
<b>	<div class = "time">
		Time
	</div>
	<div class = "table_number">
		Table Number
	</div>
	<div class = "status">
		Status
	</div>
</b>	
</div>
<br style = "clear:both"/>

<div id = "requests">