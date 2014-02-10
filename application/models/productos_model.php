<?php

class Productos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function get_productos($limit, $offset)
	{
		$query = $this->db->get('productos', $limit, $offset);
		return $query->result();
	}

	function get_search_productos($search, $type, $limit, $offset){
		$search = str_replace(" ", "(.*)", strtoupper($search));

		$this->db->select('*');
		$this->db->from('productos');
		$this->db->where('nombre REGEXP', $search);

		if(!empty($type) or $type != ''){
			if($type == 'bici:'){ $this->db->where('tipo', 0); }
			if($type == 'moto:'){ $this->db->where('tipo', 1); }
		}

		$this->db->limit($limit, $offset);
		$q = $this->db->get();

		return $q->result();
	}

	function get_total_productos(){
		return $this->db->count_all('productos');
	}

	function get_total_search_productos($search, $type){
		$search = str_replace(" ", "(.*)", strtoupper($search));

		$this->db->select('*');
		$this->db->from('productos');
		$this->db->where('nombre REGEXP', $search);

		if(!empty($type)){ $this->db->where('tipo', $type); }

		return $this->db->count_all_results();
	}

	function insert_producto($data)
	{
		$this->db->insert('productos', $data);
	}

	function update_pedido($data, $id_producto)
	{
		$this->db->update('productos', $data, array('id' => $id_producto));
	}

}

?>