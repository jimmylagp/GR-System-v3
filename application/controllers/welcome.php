<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('usuarios_model', 'usuarios');
	}

	public function index()
	{
		$status = $this->loggin($this->input->post('user'), $this->input->post('pass'));

		$data = array(
			'sesion' => $this->session->all_userdata(),
			'status' => $status
		);

		$this->twig->display('welcome.html.twig', $data);
	}

	private function loggin($user, $pass){
		
		if( !empty($user) && !empty($pass)){
			$usuario = $this->usuarios->get_usuario($user, $pass);
			if(!empty($usuario)){
				$this->session->set_userdata(get_object_vars($usuario));
				return false;
			}else{
				return true;
			}
		}
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('/');
	}
}