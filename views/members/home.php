<h1>Please sign in</h1>
<p>Need an account? <a href='<?php echo DIR;?>members/register'>Register</a>

<?php
if(isset($error)){
	foreach($error as $error){
		echo "<p style='color:red;'>$error</p>";
	}
}
?>

<form action='' method='post'>
<p>Username<br /><input type='text' name='username' value=''></p>
<p>Password<br /><input type='password' name='password' value=''></p>
<p><input type='submit' name='submit' class="btn btn-info" value='Login'></p>
</form>