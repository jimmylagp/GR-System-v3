<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Productos extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('productos_model', 'productos');
		$this->load->library('pagination');
	}

	public function index()
	{
		if(!$this->session->userdata('id')){ redirect('/productos/lista'); }


		if($this->input->post('amountp') && $this->input->post('namep') && $this->input->post('pricep') && $this->input->post('typep') == 0 or $this->input->post('typep') == 1){
			$d = array(
				'cantidad' => $this->input->post('amountp'),
				'nombre' => $this->input->post('namep'),
				'precio' => $this->input->post('pricep'),
				'tipo' => $this->input->post('typep')
			);

			$this->productos->insert_producto($d);

			$status = 0;
		}else{
			$status = 1;
		}


		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		$result = $this->search_products($this->input->post('search'), $page);

		$config = array(
			'base_url' => '/index.php/productos/index',
			'total_rows' => $result['total'],
			'per_page' => 20,
			'uri_segment' => 3,
			'next_link' => '&raquo;',
			'next_tag_open' => '<li class="arrow">',
			'next_tag_close' => '</li>',
			'prev_link' => '&laquo;',
			'prev_tag_open' => '<li class="arrow">',
			'prev_tag_close' => '</li>',
			'cur_tag_open' => '<li class="current"><a>',
			'cur_tag_close' => '</a></li>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>',
			'first_link' => '',
			'last_link' => ''
		);
		$this->pagination->initialize($config);

		$data = array(
			'productos' => $result['list'],
			'pagination' => $this->pagination->create_links(),
			'status' => $status
		);
		$this->twig->display('productos.html.twig', $data);
	}

	public function update()
	{
		if($this->input->post('amount') && $this->input->post('name') && $this->input->post('price') && $this->input->post('id')){
			$data = array(
				'cantidad' => $this->input->post('amount'),
				'nombre' => $this->input->post('name'),
				'precio' => $this->input->post('price')
			);
			$this->productos->update_producto($data, $this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function delete()
	{
		if($this->input->post('id')){
			$this->productos->delete_producto($this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function lista()
	{

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		$result = $this->search_products($this->input->post('search'), $page);

		$config = array(
			'base_url' => '/index.php/productos/lista',
			'total_rows' => $result['total'],
			'per_page' => 20,
			'uri_segment' => 3,
			'next_link' => '&raquo;',
			'next_tag_open' => '<li class="arrow">',
			'next_tag_close' => '</li>',
			'prev_link' => '&laquo;',
			'prev_tag_open' => '<li class="arrow">',
			'prev_tag_close' => '</li>',
			'cur_tag_open' => '<li class="current"><a>',
			'cur_tag_close' => '</a></li>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>',
			'first_link' => '',
			'last_link' => ''
		);
		$this->pagination->initialize($config);

		$data = array(
			'productos' => $result['list'],
			'pagination' => $this->pagination->create_links()
		);

		$this->twig->display('lista.html.twig', $data);
	}

	private function search_products($search, $page){

		$result = array();

		if($search){

			if(substr($search,0,5) == 'bici:' or substr($search,0,5) == 'moto:'){
				$text = substr($search,5,10);
				$this->session->set_userdata('search', $text);

				$type = substr($search,0,5);
				$this->session->set_userdata('type', $type);
			}elseif(substr($search,0,5) == 'todo:'){
				$this->session->unset_userdata('search');
				$this->session->unset_userdata('type');
			}else{
				$this->session->set_userdata('search', $search);
				$this->session->set_userdata('type', '');
			}

			if($this->session->userdata('search') or $this->session->userdata('type')){
				$result['list'] = $this->productos->get_search_productos($this->session->userdata('search'), $this->session->userdata('type'), 20, $page);
				$result['total'] = $this->productos->get_total_search_productos($this->session->userdata('search'), $this->session->userdata('type'));
			}else{
				$result['list'] = $this->productos->get_productos(20, $page);
				$result['total'] = $this->productos->get_total_productos();
			}

		}elseif($this->session->userdata('search')){
			
			$result['list'] = $this->productos->get_search_productos($this->session->userdata('search'), $this->session->userdata('type'), 20, $page);
			$result['total'] = $this->productos->get_total_search_productos($this->session->userdata('search'), $this->session->userdata('type'));

		}else{

			$result['list'] = $this->productos->get_productos(20, $page);
			$result['total'] = $this->productos->get_total_productos();

		}

		return $result;
	}
}