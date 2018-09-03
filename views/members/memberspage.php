
<h1>Welcome <?php echo Session::get('username');?></h1>
<?php
if(isset($error)){
	foreach($error as $error){
		echo "<p style='color:red;'>$error</p>";
	}
}
?>
<form action="" method="post" enctype="multipart/form-data" >
	Select file to upload:

	<input type="file" class="hide" name="uploadfile" id="uploadfile">
	<label for="uploadfile" class="btn btn-info">Browse</label>
	<input type="submit" class="btn btn-success" value="Upload file" name="submit">
</form>
<br>
<?php echo $data['files'];?>


<p><a href='<?php echo DIR;?>members/logout'>Logout</a></p>