<?php echo $error;?>
File Size should not exceed 1MB<br/>
Gif, PNG or JPEG only
<?php echo form_open_multipart('upload/logo_upload');?>
<input type="file" name="logo" size="20" />
<input type="submit" value="Upload" />
</form>