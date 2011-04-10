
<script> 
	
	function load()
	{
		toggleLayer('numeric');
		toggleLayer('options');
	}
	
	function toggleLayer( whichLayer )
	{
	  var elem, vis;
	  if(document.getElementById) //standards
	    elem = document.getElementById( whichLayer );
	  else if(document.all) //old msie
	      elem = document.all[whichLayer];
	  else if(document.layers)
	    elem = document.layers[whichLayer];
	  vis = elem.style;
	
	  // if the style.display value is blank we try to figure it out here
	  if(vis.display == '' && elem.offsetWidth != undefined && elem.offsetHeight != undefined)
	    vis.display = (elem.offsetWidth != 0 && elem.offsetHeight != 0) ? 'block':'none';
	  vis.display = (vis.display == '' || vis.display == 'block') ? 'none':'block';
	}
	
	function numeric()
	{
		var elem, vis;
		elem = document.getElementById('numeric');
	    vis = elem.style;
		vis.display = 'block';
		 
		elem = document.getElementById('options');
		vis = elem.style;
		vis.display = 'none';
		
		var count = document.getElementById("numeric");
		count.setAttribute("value", 0);
	}
	
	function options()
	{
		var elem, vis;
		elem = document.getElementById('options');
	    vis = elem.style;
		vis.display = 'block';
		 
		elem = document.getElementById('numeric');
		vis = elem.style;
		vis.display = 'none';		
		
		var type = document.getElementById("t");
		count.setAttribute("value", 1);
	}
	
	var i = 1;
	function addTextBox()
	{
		i++;
		var options = document.getElementById("options");
		var element = document.createElement("input");

		element.setAttribute("type", "text");
	    element.setAttribute("class", "formelem");
	    element.setAttribute("name", "option" +i);
		
		options.appendChild(element);
		
		var count = document.getElementById("count");
		count.setAttribute("value", i);
	}
</script>

<div id = "content">

    <?php echo validation_errors(); ?>
	<?php echo $error;?>
    <?php echo form_open_multipart('features/newfeature'); ?>		

        <p><h4>Name</h4> <input type = "text" class = "formelem" name = "name" value = "<?php echo set_value('name'); ?>" size = "50" /></p>
		
        <p><h4>Type</h4>
			<input onclick = "numeric()" type="radio" name="rad" value="Numeric Value"/>Numeric<br/>
			<input onclick = "options()" type="radio" name="rad" value="Options"/>Options
		</p>
		
		<div id = "numeric">
		<p>
			<h4>MinValue</h4><input type = "text" class = "formelem" name = "minvalue"/><br/>
			<h4>MaxValue</h4><input type = "text" class = "formelem" name = "maxvalue"/>
		</p>
		</div>
		<div id = "options">
		<p>
			<a href = "javascript:addTextBox()">+</a>
			<input type = "text" class = "formelem" name = "option1" size = "50"/>
		</p>
		</div>
		
		<input type = "hidden" id = "count" name = "count" value = "0"/>
		<input type = "hidden" id = "t" name = "type" value = "0"/>
		
		<h4>Choose Icon</h4> Show Icons here
		
		<p><input type = "submit" value = "Submit" /></p>
		</form>

</div>