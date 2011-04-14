
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
		element.setAttribute("style", "margin:5px 0px 5px 0px;")
		
		options.appendChild(element);
		
		var br = document.createElement("br");
		options.appendChild(br);
		
		var count = document.getElementById("count");
		count.setAttribute("value", i);
	}
	
	function selectIcon(t)
	{
		var elem = document.getElementById("icon"+t);
		elem.style.borderColor = "#F05B16";
		elem.style.borderWidth = "thick";
		
		var iconelem = document.getElementById("icon");
		iconelem.value = t;
	}
	
</script>

<div id = "contentarea">

    <h2 class = "title">Item Feature</h2>
    
    <?php echo validation_errors(); ?>
	<?php echo $error ?>
    <?php echo form_open_multipart('features/newfeature'); ?>

        <p><h4>Name</h4> <input type = "text" name = "name" value = "<?php if(isset($feature)) echo $feature['Name']; else echo set_value('name'); ?>" size = "50" /></p>
		
        <p><h4>Type</h4>
			<p><input onclick = "numeric()" type="radio" name="rad" value="<?php if(isset($feature)) echo $feature['Type']; ?>" style ="width:20px;"/>Numeric<br/></p>
			<p><input onclick = "options()" type="radio" name="rad" value="<?php if(isset($feature)) echo $feature['Type']; ?>" style ="width:20px;"/>Options</p>
		</p>
		
		<div id = "numeric">
		<p>
			<h4>MaxValue</h4><input type = "text" name = "maxvalue" value = "<?php if(isset($feature)) echo $feature['MaxValue']; ?>"/>
		</p>
		</div>
		<div id = "options">
		<p>
			<a href = "javascript:addTextBox()">+</a><br/><br/>
			<input type = "text" name = "option1" size = "50" value = "<?php if(isset($feature)) echo $feature['Type']; ?>"/>
		</p>
		</div>
		
		<input type = "hidden" id = "count" name = "count" value = "0"/>
		
		<h4>Choose Icon</h4>
		<div id = "icons" style = "#image {border-color:#f05b16; border-width:0px}">
			<a href = "javascript:selectIcon(1)"><image id = "icon1" src = "http://imenus.tk/images/1.gif" width = "32px" height = "32px"/></a>
			<a href = "javascript:selectIcon(2)"><image id = "icon2" src = "http://imenus.tk/images/2.gif" width = "32px" height = "32px"/></a>
			<a href = "javascript:selectIcon(3)"><image id = "icon3" src = "http://imenus.tk/images/3.gif" width = "32px" height = "32px"/></a>
		</div>

		<input type = "hidden" id = "icon" name = "icon" value = "1"/>
		
		<p><input type = "submit" value = "Submit" /></p>
		</form>

</div>