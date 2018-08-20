<h1>Please sign in</h1>
<p>Already a member? <a href='<?php echo DIR;?>'>Login</a>

<?php
if($data['success'] == true){
	echo "<h1 style='color:green;'>Registration Success</h1>";
	echo "<p style='color:green;>Please check your email to complete registration.</p>";
}


if(isset($error)){
	foreach($error as $error){
		echo "<p style='color:red;'>$error</p>";
	}
}
?>

<form action='' method='post'>
<p>Username<br /><input type='text' name='username' value='<?php if(isset($error)){ echo $data['username']; } ?>'></p>
<p>Email<br /><input type='text' name='email' value='<?php if(isset($error)){ echo $data['email']; } ?>'></p>
<p>Password<br /><input type='password' name='password' value=''></p>
<p>Password Confirm<br /><input type='password' name='passwordConfirm' value=''></p>
<p><input type='submit' name='submit' value='Register'></p>
</form>