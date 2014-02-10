<?php

class Rutas_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_rutas()
	{
		$query = $this->db->get('rutas');
		return $query->result();
	}

	function insert_ruta($data)
	{
		$this->db->insert('rutas', $data);
	}

	function update_ruta($data, $id_ruta)
	{
		$this->db->update('rutas', $data, array('id' => $id_ruta));
	}

}

?>