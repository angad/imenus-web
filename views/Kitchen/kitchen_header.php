<?php 
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

#order,
#header,
#orders 
{
	font-size:30px;
	font-family:sans-serif;
	margin:20px 0px 10px 0px;
}

.item_name,
.time
{
	float:left;
	width:200px;
}

.quantity,
.table_number
{
	float:left;
	width:140px;
}

.remarks
{
	float:left;
	width:300px
}

</style>

<script>

window.onload = function(){
  interval = setInterval('fetch()', <?php echo $fetch_time ?> *1000);// 5 secs between requests
};

function fetch()
{
	document.getElementById("orders").innerHTML="<div></div>";
		
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
			document.getElementById("orders").innerHTML=xmlhttp.responseText;
		}
	}

	xmlhttp.open("POST","http://imenus.tk/index.php/Kitchen/orders/getorders/", true);
	xmlhttp.send();
}

</script>

</head>
<body>

<div id = "outer-wrapper">

<div id = "header">
<b>	<div class = "item_name">
		Name
	</div>
	<div class = "quantity">
		Quantity
	</div>
	<div class = "remarks">
		Remarks
	</div>
	<div class = "time">
		Time
	</div>
	<div class = "table_number">
		Table Number
	</div>
</b>	
</div>
<br style = "clear:both"/>
<div id = "orders">