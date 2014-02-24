<?php

class Clientes_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_clientes_por_ruta($id_ruta)
	{
		$query = $this->db->get_where('clientes', array('id_ruta' => $id_ruta));
		return $query->result();
	}

	function get_clientes_ruta_total($id_ruta){
		$this->db->where(array('id_ruta' => $id_ruta));
		return $this->db->count_all_results('clientes');
	}

	function insert_cliente($data)
	{
		$this->db->insert('clientes', $data);
	}

	function update_cliente($data, $id_cliente)
	{
		$this->db->update('clientes', $data, array('id' => $id_cliente));
	}

	function delete_cliente($id_cliente){
		$this->db->delete('clientes', array('id' => $id_cliente));
	}

}

?>