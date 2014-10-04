<?php

class Usuarios_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_usuario($user, $pass)
	{
		$pass = md5($pass);
		$query = $this->db->get_where('usuarios', array('usuario' => $user, 'pass' => $pass));
		return $query->row();
	}

	function insert_usuario($data)
	{
		$this->db->insert('usuarios', $data);
	}

	function update_usuario($data, $id_usuario)
	{
		$this->db->update('usuarios', $data, array('id' => $id_cliente));
	}

}

?>