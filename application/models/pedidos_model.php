<?php

class Pedidos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_pedidos_por_cliente($id_cliente)
	{
		$query = $this->db->get_where('pedidos', array('id_cliente' => $id_cliente));
		return $query->result();
	}

	function insert_pedido($data)
	{
		$this->db->insert('pedidos', $data);
		return $this->db->insert_id();
	}

	function update_pedido($data, $id_pedido)
	{
		$this->db->update('pedidos', $data, array('id' => $id_pedido));
	}

	function delete_pedido()
	{
		$this->db->delete('pedidos', array('id' => $id_pedido));
	}

	function total_pedido($id_pedido){
		$this->db->select('sum(pd_agregados.cantidad*productos.precio) as total');
		$this->db->from('pd_agregados');
		$this->db->join('productos','pd_agregados.id_producto = productos.id');
		$this->db->where(array('pd_agregados.id_pedido' => $id_pedido));
		$q = $this->db->get();
		return $q->result();
	}
}

?>