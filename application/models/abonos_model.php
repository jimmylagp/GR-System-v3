<?php

class Abonos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_abonos_por_pedido($id)
	{
		$query = $this->db->get_where('abonos', array('id_pedido' => $id));
		return $query->result();
	}

	function insert_abono($data)
	{
		$this->db->insert('abonos', $data);
	}

	function update_abono($data, $id_abono)
	{
		$this->db->update('abonos', $data, array('id' => $id_abono));
	}

}

?>