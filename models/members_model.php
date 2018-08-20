<?php

class Members_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}	

	public function get_member_hash($username)
	{
		return $this->_db->select("SELECT uuid,username,password FROM users WHERE active='confirmed' AND username = :username",array(':username' => $username));
	}

	public function get_memberID($uuid)
	{
		return $this->_db->select("SELECT uuid,emailtoken,active FROM users WHERE uuid = :uuid",array(':uuid' => $uuid));
	}

	public function get_username($username)
	{
		return $this->_db->select("SELECT username FROM users WHERE username = :username",array(':username' => $username));
	}

	public function get_email($email)
	{
		return $this->_db->select("SELECT email FROM users WHERE email = :email",array(':email' => $email));
	}

	public function increment_download($fileuuid)
	{
		$this->_db->increment("UPDATE files SET downloads = downloads + 1 WHERE fileuuid = :fileuuid",array(':fileuuid' => $fileuuid));
	}

	public function insert_member($data)
	{
		$this->_db->insert("users",$data);
		return $this->_db->lastInsertId('memberID');
	}

	public function update_data($table,$data,$where)
	{
		$this->_db->update($table,$data,$where);
	}

	public function delete_file($table, $where, $data)
	{
		$this->_db->delete($table, $where, $data);
	}

	public function get_files($uploader)
	{
		return $this->_db->select("SELECT fileuuid, name, size, downloads, public FROM files WHERE uploader = :uploader",array(':uploader' => $uploader));
	}


	public function get_file($fileuuid)
	{
		return $this->_db->select("SELECT public, name, uploader FROM files WHERE fileuuid = :fileuuid",array(':fileuuid' => $fileuuid));
	}

	public function insert_file($data)
	{
		$this->_db->insert("files",$data);
	}

}