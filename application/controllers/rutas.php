<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rutas extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('rutas_model', 'rutas');
		$this->load->model('clientes_model', 'clientes');
	}

	public function index()
	{
		$logged = $this->session->userdata('id') ? true : false;

		if($this->input->post('namer')){
			$d = array(
				'nombre' => $this->input->post('namer')
			);
			$this->rutas->insert_ruta($d);

			$status = 0;
		}else{
			$status = 1;
		}

		$data = array(
			'logged' => $logged,
			'rutas' => $this->clientes_por_ruta(),
			'status' => $status
		);

		$this->twig->display('rutas.html.twig', $data);
	}

	public function update(){
		if($this->input->post('id') && $this->input->post('name')){
			$data = array(
				'nombre' => $this->input->post('name')
			);
			$this->rutas->update_ruta($data, $this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function delete()
	{
		if($this->input->post('id')){
			$this->rutas->delete_ruta($this->input->post('id'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	private function clientes_por_ruta(){
		$list = $this->rutas->get_rutas();
		$rutas = array();
		foreach ($list as $key => $value) {
			$rutas[] = array(
				'id' => $value->id,
				'nombre' => $value->nombre,
				'clientes' => $this->clientes->get_clientes_ruta_total($value->id)
			);
		}

		return $rutas;
	}
}