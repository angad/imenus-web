<?php 
/**
 * @author angad
 */
?>

<script> 

<?php 
$count = 0;
if(isset($feature))
{
	$options = explode(";", $feature['StringValues']);
	$count = sizeof($options);
	$type = $feature['Type'];
	$id = $feature['Id'];
}
?>
	
	function populateoptions()
	{
		<?php if(isset($feature)) { if($type==0) echo "showNumeric()"; else echo "showOptions();"; } ?>
			
		options = document.getElementById('options');
		option1 = document.getElementById('option1');
		options.removeChild(option1);
		
		optionsCount = document.getElementById('count');
		count.setAttribute("value", "<?php echo $count-1; ?>");
		
		var i=1;
		<?php for($i=0; $i<$count; $i++) {?>
			var element = document.createElement("input");

			element.setAttribute("type", "text");
		    element.setAttribute("class", "formelem");
		    element.setAttribute("name", "option" +i);
			element.setAttribute("style", "margin:5px 0px 5px 0px;");
			element.setAttribute("value", "<?php echo $options[$i]; ?>");
			options.appendChild(element);
			
			var br = document.createElement("br");
			options.appendChild(br);
			i++;
		<?php } ?>
	
	}

	function firstload()
	{
		toggleLayer('options');
		toggleLayer('numeric');
		toggleLayer('icons');
		<?php if(isset($feature)){ ?> populateoptions(); <?php } ?>
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
	
	function showNumeric()
	{
		var elem, vis;
		
		elem = document.getElementById('numeric');
	    vis = elem.style;
		vis.display = 'block';
		
		elem = document.getElementById('icons');
	    vis = elem.style;
		vis.display = 'block';
		
		elem = document.getElementById('options');
		vis = elem.style;
		vis.display = 'none';
	}
	
	function showOptions()
	{
		var elem, vis;
		
		elem = document.getElementById('options');
	    vis = elem.style;
		vis.display = 'block';
		
		elem = document.getElementById('icons');
	    vis = elem.style;
		vis.display = 'none';
		 
		elem = document.getElementById('numeric');
		vis = elem.style;
		vis.display = 'none';
	}
	
	function fixedcheck()
	{
		var elem;
		elem = document.getElementById('fixed');
		if(elem.checked) elem.value = "1";
		else elem.value = "0";
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
			<p><input onclick = "showNumeric()" type="radio" name="rad" value="<?php if(isset($feature)) echo $feature['Type'];  else echo "0" ?>" style ="width:20px;" <?php if(isset($feature)) if($feature['Type']==0) echo "checked"; else echo ""?>/>Numeric<br/></p>
			<p><input onclick = "showOptions()" type="radio" name="rad" value="<?php if(isset($feature)) echo $feature['Type']; else echo "1" ?>" style ="width:20px;" <?php if(isset($feature)) if($feature['Type']==1) echo "checked"; else echo ""?>/>Options</p>
		</p>
		
		<div id = "numeric">
		<p>
			<h4>MaxValue</h4><input type = "text" name = "maxvalue" value = "<?php if(isset($feature) && $feature['Type'] == 0) echo $feature['MaxValue']; ?>"/>
		</p>
		</div>
		<div id = "options">
			<a href = "javascript:addTextBox()">+</a><br/><br/>
			<input id = "option1" type = "text" name = "option1" size = "50" value = "<?php if(isset($feature)) echo ''; ?>"/>
		</div>
		
		<input type = "hidden" id = "count" name = "count" value = "0"/>
		
	
		<div id = "icons" style = "#image {border-color:#f05b16; border-width:0px}">
			<h4>Choose Icon</h4>
			<a href = "javascript:selectIcon(1)"><image id = "icon1" src = "http://imenus.tk/images/1.gif" width = "32px" height = "32px"/></a>
			<a href = "javascript:selectIcon(2)"><image id = "icon2" src = "http://imenus.tk/images/2.gif" width = "32px" height = "32px"/></a>
			<a href = "javascript:selectIcon(3)"><image id = "icon3" src = "http://imenus.tk/images/3.gif" width = "32px" height = "32px"/></a>
		</div>
		
		<input onclick = "fixedcheck()" id = "fixed"  type = "checkbox" name = "fixed" value = "<?php if(isset($feature)) echo $feature['Fixed'];  else echo "0"?>"/>Fixed</br>

		<input type = "hidden" id = "icon" name = "icon" value = "1"/>
		<input type = "hidden" id = "itemid" name = "itemid" value = "<?php if(isset($feature)) echo $id; ?>"/>
		
				
		<p><input type = "submit" value = "Submit" /></p>
		</form>

</div>