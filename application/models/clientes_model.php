<?php

class Clientes_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_clientes_por_ruta($id)
	{
		$query = $this->db->get_where('clientes', array('id_ruta' => $id));
		return $query->result();
	}

	function insert_cliente($data)
	{
		$this->db->insert('clientes', $data);
	}

	function update_cliente($data, $id_cliente)
	{
		$this->db->update('clientes', $data, array('id' => $id_cliente));
	}

}

?>