<?php

class Pedidos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_fecha_pedido_por_id($id_pedido)
	{
		$this->db->select('fecha');
		$query = $this->db->get_where('pedidos', array('id' => $id_pedido));
		return $query->result();
	}

	function get_pedidos_por_cliente($id_cliente)
	{
		$query = $this->db->get_where('pedidos', array('id_cliente' => $id_cliente));
		return $query->result();
	}

	function get_verpedido_por_id($id_pedido)
	{
		$this->db->select('pd_agregados.id, pd_agregados.cantidad, pd_agregados.id_producto, productos.cantidad as pcantidad, productos.nombre, productos.precio');
		$this->db->from('pd_agregados');
		$this->db->join('productos', 'pd_agregados.id_producto = productos.id');
		$this->db->where('pd_agregados.id_pedido', $id_pedido);
		$q = $this->db->get();
		return $q->result();
	}

	function get_descuento_por_id($id_pedido)
	{
		$this->db->select('descuento');
		$query = $this->db->get_where('pedidos', array('id' => $id_pedido));
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

	function delete_pedido($id_pedido)
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