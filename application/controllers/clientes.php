<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clientes extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('rutas_model', 'rutas');
		$this->load->model('clientes_model', 'clientes');
	}

	public function index()
	{
		$logged = $this->session->userdata('id') ? true : false;
		
		$rutas = $this->rutas->get_rutas();

		if($this->input->post('namec') && $this->input->post('placec') && $this->input->post('rutac')){
			$d = array(
				'nombre' => $this->input->post('namec'),
				'lugar' => $this->input->post('placec'),
				'id_ruta' => $this->input->post('rutac')
			);
			$this->clientes->insert_cliente($d);

			$status = 0;
		}else{
			$status = 1;
		}

		$data = array(
			'logged' => $logged,
			'rutas' => $rutas,
			'status' => $status
		);

		$this->twig->display('clientes.html.twig', $data);
	}

	public function load()
	{
		if($this->input->post('cruta')){
			
			$clientes = $this->clientes->get_clientes_por_ruta($this->input->post('cruta'));
			if(!empty($clientes)){
				foreach ($clientes as $key => $value) {
					$result['data'][] = get_object_vars($value);
				}
			}
			$result['error'] = 0;

			print_r(json_encode($result));
		}else{
			print_r(json_encode(array("data" => array(null), "error" => 1)));
		}
	}

	public function update()
	{
		if($this->input->post('name') && $this->input->post('place') && $this->input->post('id')){
			$data = array(
				'nombre' => $this->input->post('name'),
				'lugar' => $this->input->post('place')
			);
			$this->clientes->update_cliente($data, $this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function delete()
	{
		if($this->input->post('id')){
			$this->clientes->delete_cliente($this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}
}