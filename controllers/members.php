<?php

class Members extends Controller 
{

	public function __construct()
	{
		parent::__construct();
	}	

	public function register()
	{
		$data['success'] = false;
		$data['username']='';
		$data['email']='';


		if(isset($_POST['submit']))
		{

			$username        = $_POST['username'];
			$email           = $_POST['email'];
			$password        = $_POST['password'];
			$passwordConfirm = $_POST['passwordConfirm'];

			$data['username']=$username;
			$data['email']=$email;

			if(strlen($username) < 5){
				$error[] = 'Username is too short';
			}
			else if(strlen($username) > 20){
				$error[] = 'Username is too long';
			}
			else if(!ctype_alnum($username))
			{
				$error[] = 'Username must be alphanumeric';

			} else {

				$check = $this->_model->get_username($username);

				if(strtolower($check[0]->username) == strtolower($username)){
					$error[] = 'Username already taken';
				}
			}


			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			     $error[] = 'Please enter a valid email address';
			} else {
				$check = $this->_model->get_email($email);
				if(strtolower($check[0]->email) == strtolower($email)){
					$error[] = 'Email already taken';
				}
			}

			if(strlen($password) < 8){
				$error[] = 'Password is too short';
			} elseif($password != $passwordConfirm){
				$error[] = 'Password do not match';
			}

			if(!isset($error)){

				$uuid = Utility::uuid();

				$emailtoken = Utility::uuid();

				$hash=password_hash($password, PASSWORD_ARGON2I);

				$active = 'unconfirmedemail';
				
				$postdata = array(
					'uuid' => $uuid,
					'username' => $username,
					'email' => $email,
					'password' => $hash,
					'active' => $active,
					'emailtoken' => $emailtoken
				);
				$myfile = fopen("emailovi/".$username, "w") or die("Unable to open file!");

				$txt = DIR.'members/activate/'.$uuid.'/'.$emailtoken;
				fwrite($myfile, $txt);
				fclose($myfile);


				$id = $this->_model->insert_member($postdata);

				$data['success'] = true;
			}
		}

		$data['title'] = 'Members';

		$this->_view->rendertemplate('header',$data);
		$this->_view->render('members/register',$data,$error);
		$this->_view->rendertemplate('footer',$data);	
	}

	public function activate($uuid,$emailtoken){

		if((strlen($uuid) == 36) && (strlen($emailtoken) == 36)){

			$user = $this->_model->get_memberID($uuid);
			#print_r($user);
			#var_dump($user) ;
			if(empty($user)){
				$error[] = 'No such account';
			} elseif($user[0]->active == 'confirmed'){
				$error[] = 'Account has already been activated';
			} elseif($user[0]->emailtoken != $emailtoken){
				$error[] = 'Wrong token';
			} else {

				$postdata = array('active' => 'confirmed');
				$where = array('uuid' => $uuid);
				$this->_model->update_data('users',$postdata,$where);

				$data['success'] = 'Your account is now active you may now <a href='.DIR.'>Login</a>';
			}

		} else {
			$error[] = 'Invalid link provided';
		}

		$data['title'] = 'Activate Account';

		$this->_view->rendertemplate('header',$data);
		$this->_view->render('members/activate',$data,$error);
		$this->_view->rendertemplate('footer',$data);	
	}

	public function index(){

		if(Session::get('loggedin') == true){
			Url::redirect('members/memberspage');
		}

		if(isset($_POST['submit'])){

			$data = $this->_model->get_member_hash($_POST['username']);

			if(password_verify($_POST['password'], $data[0]->password)){

				Session::set('uuid',$data[0]->uuid);
				Session::set('username',$data[0]->username);
				Session::set('loggedin',true);

				Url::redirect('members/memberspage');
			} else {
				$error[] = 'Wrong username or password or account has not been activated yet';
			}

		}

		$data['title'] = 'Login';

		$this->_view->rendertemplate('header',$data);
		$this->_view->render('members/home',$data,$error);
		$this->_view->rendertemplate('footer',$data);	
	}

	public function download($fileuuid)
	{
		function dl($fileuuid,$file,$name)
		{
			header('Content-Description: File Transfer');
			header('Content-Type: '.mime_content_type($file));
			header('Content-Disposition: attachment; filename="'.basename($name).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		}

		$filelocation="files/";
		$file = $filelocation.$fileuuid;

		$data=$this->_model->get_file($fileuuid);

		if (file_exists($file)) {
			if ($data[0]->{'public'}==1)
			{
				$this->_model->increment_download($fileuuid);
				dl($fileuuid,$file,$data[0]->{'name'});
			}
			else if($data[0]->{'uploader'}==Session::get('uuid'))
			{
				$this->_model->increment_download($fileuuid);

				dl($fileuuid,$file,$data[0]->{'name'});
			}
			else
			{
				$error[]="You don't have permission to download this file!";

			}
		}
		else
		{
			$error[]='File does not exist!';
		}

		$data['title'] = 'Download file';


		$this->_view->rendertemplate('header',$data);
		$this->_view->render('members/download',$data,$error);
		$this->_view->rendertemplate('footer',$data);	

	}


	public function memberspage(){

		if(Session::get('loggedin') == false){
			Url::redirect('members/login');
		}

		if(isset($_POST["submit"]))
		{
			if(isset($_FILES["uploadfile"]))
			{
				$fileuuid=Utility::uuid();
				$filelocation="files/";
				clearstatcache();
				$filename=basename($_FILES["uploadfile"]["name"]);
				$filesize=filesize($_FILES["uploadfile"]["tmp_name"]);

				
				if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $filelocation.$fileuuid)) 
				{

					
					$postdata = array(
						'fileuuid' => $fileuuid,
						'name' => $filename,
						'size' => $filesize,
						'uploader' => Session::get('uuid')
					);

					$this->_model->insert_file($postdata);
					$data['success'] = true;


				} 
				else 
				{
					die("Unable to move file!");
					
				}
			}


		}

		if(isset($_POST['fileaccess']))
		{
			$fileuuid=$_POST['fileaccess'];
			$data=$this->_model->get_file($fileuuid);
			if ($data[0]->{'public'}==1)
			{
				$public=0;
			}
			else if ($data[0]->{'public'}==0)
			{
				$public=1;
			}

			$postdata = array('public' => $public);
			$where = array('fileuuid' => $fileuuid);
			$this->_model->update_data('files',$postdata,$where);
		}

		if(isset($_POST['fileremove']))
		{
			$fileuuid=$_POST['fileremove'];
			$data=$this->_model->get_file($fileuuid);

			if (!empty($data[0]))
			{
				if ($data[0]->{'uploader'}==Session::get('uuid'))
				
				$filelocation="files/";
				$file = $filelocation.$fileuuid;
				try
				{
				unlink($file);
				}
				catch(Exception $e)
				{
					echo "Error";
				}
				$this->_model->delete_file('files','fileuuid',$fileuuid);

				
				
			}

			$postdata = array('public' => $public);
			$where = array('fileuuid' => $fileuuid);
			$this->_model->update_data('files',$postdata,$where);
		}


		$files = $this->_model->get_files(Session::get('uuid'));

		if (!empty($files))
		{
			#print_r($files);
			$data['files']='';
			$data['files'].="<table style='border: solid 1px black;'>";
			$data['files'].="<tr><th>ID</th><th>File name</th><th>Size</th><th>Downloads</th><th>Options</th></tr>";

			foreach($files as $k=>$v)
			{

				$data['files'].='<tr>';
				foreach($v as $k1=>$v1) 
				{
					if ($k1=='name')
					{
						$data['files'].="<td style='width: 300px; border: 1px solid black;'><a href='".DIR."members/download/" . $v->{'fileuuid'} . "'>".$v1."</a></td>";
					}
					else if ($k1=='size')
					{
						$data['files'].="<td style='width: 150px; border: 1px solid black;'>" . Utility::formatUnitSize($v1) . "</td>";
					}
					else if ($k1=='downloads')
					{
						$data['files'].="<td style='width: 50px; border: 1px solid black;'>" .$v1 . "</td>";
					}
					else if ($k1=='public')
					{
						if ($v1==1)
						{

							$data['files'].="<td style='width: 200px; border: 1px solid black;'>
							<form action='' method='POST'>" .
							"<button type='submit' name = 'fileaccess' value = '". $v->{'fileuuid'} ."'>make private</button>".
							"<button type='submit' name = 'fileremove' value = '". $v->{'fileuuid'} ."'>remove</button>".
							"</form></td>";
						}
						else
						{

							$data['files'].="<td style='width: 200px; border: 1px solid black;'>
							<form action='' method='POST'>" .
							"<button type='submit' name = 'fileaccess' value = '". $v->{'fileuuid'} ."'>make public</button>".
							"<button type='submit' name = 'fileremove' value = '". $v->{'fileuuid'} ."'>remove</button>".
							"</form></td>";

						}

					}
					else
					{
						$data['files'].="<td style='width: 300px; border: 1px solid black;'>" . $v1 . "</td>";
					}
				}
				$data['files'].='</tr>';
			}
		
				$data['files'].="</table>";


		}

		$data['title'] = 'Members Page';

		$this->_view->rendertemplate('header',$data);
		$this->_view->render('members/memberspage',$data);
		$this->_view->rendertemplate('footer',$data);	
	}



	public function error()
	{
		$this->_view->rendertemplate('header',$data);
		$this->_view->render('error/404',$data);
		$this->_view->rendertemplate('footer',$data);	
	}


	public function logout()
	{
		Session::destroy();
		Url::redirect('members/index');
	}

}