<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

?>

<?php echo $error;?>
File Size should not exceed 2MB<br/>
Gif, PNG or JPEG only
<?php echo form_open_multipart('upload/item_image_upload');?>
<input type="file" name="raw" size="20" />
<input type="submit" value="Upload" />
</form>