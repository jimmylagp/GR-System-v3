<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Productos extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('productos_model', 'productos');
		$this->load->library('pagination');
	}

	public function index(){
		if(!$this->session->userdata('id')){ redirect('/productos/lista'); }

		$data = array();
		$this->twig->display('productos.html.twig', $data);
	}

	public function lista()
	{

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		if($this->input->post('search')){

			if(substr($this->input->post('search'),0,5) == 'bici:' or substr($this->input->post('search'),0,5) == 'moto:'){
				$text = substr($this->input->post('search'),5,10);
				$this->session->set_userdata('search', $text);

				$type = substr($this->input->post('search'),0,5);
				$this->session->set_userdata('type', $type);
			}elseif(substr($this->input->post('search'),0,5) == 'todo:'){
				$this->session->unset_userdata('search');
				$this->session->unset_userdata('type');
			}else{
				$this->session->set_userdata('search', $this->input->post('search'));
				$this->session->set_userdata('type', '');
			}

			if($this->session->userdata('search') or $this->session->userdata('type')){
				$list = $this->productos->get_search_productos($this->session->userdata('search'), $this->session->userdata('type'), 20, $page);
				$total = $this->productos->get_total_search_productos($this->session->userdata('search'), $this->session->userdata('type'));
			}else{
				$list = $this->productos->get_productos(20, $page);
				$total = $this->productos->get_total_productos();
			}

		}elseif($this->session->userdata('search')){
			
			$list = $this->productos->get_search_productos($this->session->userdata('search'), $this->session->userdata('type'), 20, $page);
			$total = $this->productos->get_total_search_productos($this->session->userdata('search'), $this->session->userdata('type'));

		}else{

			$list = $this->productos->get_productos(20, $page);
			$total = $this->productos->get_total_productos();

		}

		$config = array(
			'base_url' => '/index.php/productos/lista',
			'total_rows' => $total,
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
			'productos' => $list,
			'pagination' => $this->pagination->create_links()
		);

		$this->twig->display('lista.html.twig', $data);
	}

	private function search_products($s, $t){

	}
}