<?php

class Pd_agregados_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_pdAgregados_por_pedido($id)
	{
		$query = $this->db->get_where('pd_agregados', array('id_pedido' => $id));
		return $query->result();
	}

	function insert_pdAgregado($data)
	{
		$this->db->insert('pd_agregados', $data);
	}

	function update_pdAgregado($data, $id_pdAgregado)
	{
		$this->db->update('pd_agregados', $data, array('id' => $id_pdAgregado));
	}

}

?>