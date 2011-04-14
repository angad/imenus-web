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

table{
	font-size:24px;
}

#order,
#header,
#orders 
{
	font-size:30px;
	font-family:sans-serif;
	margin:20px 0px 10px 0px;
}

.odd{
	background-color:white;
}

.even{
	background-color:#94B8FF;
}

.started{
	background-color:#F05B16;
}

</style>

<script>

window.onload = function(){
	addClicks();
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
			dh=document.body.scrollHeight;
			ch=document.body.clientHeight;
			if(dh>ch)
			{
				moveme=dh-ch;
				window.scrollTo(0,moveme);
			}
			addClicks();
		}
	}
	
	xmlhttp.open("POST","http://imenus.tk/index.php/Kitchen/orders/getorders/", true);
	xmlhttp.send();
}

function addClicks()
{
	var table = document.getElementsByTagName("table")[0];
	var rows = table.getElementsByTagName("tr");
	
	for (i = 0; i < rows.length; i++) 
	{
		var currentRow = table.rows[i];
		
		if(i % 2 == 0)
		{
			rows[i].className = "even";
		}
		else
		{
			rows[i].className = "odd";
		}
		
		var createClickHandler = 
			function(row) 
			{
				return function() 
				{ 
					var cell = row.getElementsByTagName("td")[0];
					var id = cell.innerHTML;
					orderStarted(id);
					row.className = "started";
				};
			}
		currentRow.onclick = createClickHandler(currentRow);
	}
}

function orderStarted(id)
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
			
		}
	}
	
	xmlhttp.open("POST","http://imenus.tk/index.php/Kitchen/orders/orderStarted/"+id, true);
	xmlhttp.send();
}

</script>

</head>
<body>

<div id = "outer-wrapper">
<div id = "header">
	<h2><a href = "http://imenus.tk/"><img src = "http://imenus.tk/images/logo.png"/></a></h2><br/>
</div>


<div id = "orders">