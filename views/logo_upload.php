<html>
<head>
<title>Logo Upload</title>
</head>
<body>

<?php echo $error;?>

File Size should not exceed 1MB<br/>
Gif, PNG or JPEG only <br/>
<?php echo form_open_multipart('upload/logo_upload');?>
<input type="file" name="logo" size="20" />
<br /><br />
<input type="submit" value="Upload" />
</form>
</body>
</html>