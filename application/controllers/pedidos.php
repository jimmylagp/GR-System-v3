<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	function __construct()
	{
		parent::__construct();


		$this->load->model('productos_model', 'productos');
		$this->load->model('rutas_model', 'rutas');
		$this->load->library('pagination');
	}

	public function index()
	{
		$logged = $this->session->userdata('id') ? true : false;

		$data = array(
			'logged' => $logged
		);

		$this->twig->display('pedidos.html.twig', $data);
	}

	public function crear()
	{
		$logged = $this->session->userdata('id') ? true : false;

		$rutas = $this->rutas->get_rutas();

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
			'logged' => $logged,
			'rutas' => $rutas,
			'productos' => $result['list'],
			'pagination' => $this->pagination->create_links()
		);

		$this->twig->display('crear_pedidos.html.twig', $data);
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