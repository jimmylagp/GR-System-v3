<?php

class Rutas_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_rutas()
	{
		$query = $this->db->get_where('rutas');
		return $query->result();
	}

	function get_rutas_por_id($id_ruta)
	{
		$query = $this->db->get_where('rutas', array('id' => $id_ruta));
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

	function delete_ruta($id_ruta){
		$this->db->delete('rutas', array('id' => $id_ruta));
	}

}

?>