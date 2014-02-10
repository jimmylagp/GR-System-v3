<?php

class Pedidos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_pedidos_por_cliente($id)
	{
		$query = $this->db->get_where('pedidos', array('id_clientes' => $id));
		return $query->result();
	}

	function insert_pedido($data)
	{
		$this->db->insert('pedidos', $data);
	}

	function update_pedido($data, $id_pedido)
	{
		$this->db->update('pedidos', $data, array('id' => $id_pedidos));
	}

}

?>