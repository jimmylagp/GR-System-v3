<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	function __construct()
	{
		parent::__construct();


		$this->load->model('productos_model', 'productos');
		$this->load->model('rutas_model', 'rutas');
		$this->load->model('clientes_model', 'clientes');
		$this->load->model('pedidos_model', 'pedidos');
		$this->load->model('pd_agregados_model', 'pd_agregados');
		$this->load->library('pagination');
	}

	public function index()
	{
		$logged = $this->session->userdata('id') ? true : false;

		$data = array();

		if($this->input->post('clientep'))
		{
			$pedidos = $this->pedidos->get_pedidos_por_cliente($this->input->post('clientep'));

			$lpedidos = array();
			foreach ($pedidos as $key => $value) {
				$total = $this->pedidos->total_pedido($value->id);
				$lpedidos[] = array(
					'id' 	=> $value->id,
					'folio' => $this->folio($value->id),
					'fecha' => $value->fecha,
					'total' => $total[0]->total - (($value->descuento/100) * $total[0]->total)
					);
			}

			$data['pedidos'] = $lpedidos;

			$path = FCPATH."/assets/pdfs/".$this->input->post('clientep');
			$data['remisiones'] = scandir($path);
			unset($data['remisiones'][0]);
			unset($data['remisiones'][1]);

			$cliente = $this->clientes->get_cliente_por_id($this->input->post('clientep'));
			$data['cliente'] = $cliente[0]->nombre;
			$data['id_cliente'] = $this->input->post('clientep');
		}

		$data['logged'] = $logged;
		$data['rutas'] = $this->rutas->get_rutas();

		$this->twig->display('pedidos.html.twig', $data);
	}

	public function crear()
	{
		$data = array();

		$logged = $this->session->userdata('id') ? true : false;

		if($this->session->userdata('pedido') or $this->input->post('cliente'))
		{
			if($this->input->post('cliente'))
			{
				$pedido = array(
					'fecha' => date('Y-m-d H:i:s'),
					'descuento' => 0,
					'estado' => 0,
					'id_cliente' => $this->input->post('cliente')
					);
				$sesion_pedido['id_pedido'] = $this->pedidos->insert_pedido($pedido);
				$sesion_pedido['id_cliente'] = $this->input->post('cliente');

				$this->session->set_userdata('pedido', $sesion_pedido);
			}

			$sesion_pedido = $this->session->userdata('pedido');

			$cliente = $this->clientes->get_cliente_por_id($sesion_pedido['id_cliente']);
			$data['cliente'] = $cliente[0]->nombre;

			$data['pedido'] = true;
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

			$result = $this->search_products($this->input->post('search'), $page);
			$descuento = $this->pedidos->get_descuento_por_id($sesion_pedido['id_pedido']);

			$data['productos'] = $result['list'];
			$data['descuento'] = $descuento[0]->descuento;
			$data['pd_agregados'] = $this->pd_agregados->get_pdAgregados_por_pedido($sesion_pedido['id_pedido']);

			$config = array(
				'base_url' => '/index.php/pedidos/crear',
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
			$data['pagination'] = $this->pagination->create_links();
		}
		else
		{
			$data['pedido'] = false;
			$data['rutas'] = $this->rutas->get_rutas();
		}

		$data['logged'] = $logged;

		$this->twig->display('crear_pedidos.html.twig', $data);
	}

	public function ver()
	{

		if(!$this->session->userdata('pedido')) redirect('/pedidos/crear/', 'refresh');

		$logged = $this->session->userdata('id') ? true : false;
		$sesion_pedido = $this->session->userdata('pedido');

		$data = array();

		$data['logged'] = $logged;
		$data['list'] = $this->pedidos->get_verpedido_por_id($sesion_pedido['id_pedido']);

		$cliente = $this->clientes->get_cliente_por_id($sesion_pedido['id_cliente']);
		$data['cliente'] = $cliente[0]->nombre;

		$descuento = $this->pedidos->get_descuento_por_id($sesion_pedido['id_pedido']);
		$data['descuento'] = $descuento[0]->descuento;

		$this->twig->display('ver_pedido.html.twig', $data);
	}

	public function editar($id_pedido, $id_cliente)
	{
		if($id_pedido && $id_cliente)
		{
			$sesion_pedido['id_pedido'] = $id_pedido;
			$sesion_pedido['id_cliente'] = $id_cliente;

			$this->session->set_userdata('pedido', $sesion_pedido);

			redirect('/pedidos/crear');
		}
	}

	public function guardar()
	{
		$this->session->unset_userdata('pedido');
		redirect('/');
	}

	public function imprimir()
	{
		if(!$this->session->userdata('pedido')) redirect('/pedidos/crear/', 'refresh');

		$logged = $this->session->userdata('id') ? true : false;
		$sesion_pedido = $this->session->userdata('pedido');

		$data = array();

		$data['logged'] = $logged;

		$data['folio'] = $this->folio($sesion_pedido['id_pedido']);
		$data['list'] = $this->pedidos->get_verpedido_por_id($sesion_pedido['id_pedido']);

		$cliente = $this->clientes->get_cliente_por_id($sesion_pedido['id_cliente']);
		$fecha = $this->pedidos->get_fecha_pedido_por_id($sesion_pedido['id_cliente']);

		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$data['fecha'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
		$data['nombre'] = $cliente[0]->nombre;
		$data['lugar'] = $cliente[0]->lugar;

		$descuento = $this->pedidos->get_descuento_por_id($sesion_pedido['id_pedido']);
		$data['descuento'] = $descuento[0]->descuento;

		$html = $this->load->view('imprimir_pedido', $data, true);

		$this->create_pdf($html, $sesion_pedido['id_pedido'], $data['fecha'], $sesion_pedido['id_cliente']);
	}

	/*Funciones para transacción de datos con Ajax*/
	public function add()
	{
		if($this->input->post('id_prod') && $this->input->post('cant')){
			$session = $this->session->userdata('pedido');

			$data = array(
				'cantidad' => $this->input->post('cant'),
				'id_producto' => $this->input->post('id_prod'),
				'id_pedido' => $session['id_pedido']
				);
			$this->pd_agregados->insert_pdAgregado($data);

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function delete()
	{
		if($this->input->post('id_pedido')){

			/*$pedido = $this->pedidos->get_verpedido_por_id($this->input->post('id_pedido'));
			foreach ($pedido as $key => $value) {
				$data = array(
					'cantidad' => ($value->pcantidad + $value->cantidad)
					);
				$this->productos->update_producto($data, $value->id_producto);
			}*/

			$this->pedidos->delete_pedido($this->input->post('id_pedido'));
			$this->session->unset_userdata('pedido');

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function deletepa()
	{
		if($this->input->post('id_pa')){

			$this->pd_agregados->delete_pdAgregado($this->input->post('id_pa'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function gettotal()
	{
		$session = $this->session->userdata('pedido');
		$total = $this->pedidos->total_pedido($session['id_pedido']);
		if(!empty($total)){
			$result['total'] = $total[0]->total;
			$result['error'] = 0;
			print_r(json_encode($result));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function updatedescuento()
	{
		if($this->input->post('descuento') >= 0){
			$session = $this->session->userdata('pedido');

			$data = array(
				'descuento' => $this->input->post('descuento')
				);
			$this->pedidos->update_pedido($data, $session['id_pedido']);

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function updateproducto()
	{
		if($this->input->post('id_producto') && $this->input->post('cantidad') >= 0){
			$session = $this->session->userdata('pedido');

			$data = array(
				'cantidad' => $this->input->post('cantidad')
				);
			$this->productos->update_producto($data, $this->input->post('id_producto'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	public function updatepa()
	{
		if($this->input->post('id_pa') && $this->input->post('cant')){
			$session = $this->session->userdata('pedido');

			$data = array(
				'cantidad' => $this->input->post('cant')
				);
			$this->pd_agregados->update_pdAgregado($data, $this->input->post('id_pa'));

			print_r(json_encode(array("error" => 0)));
		}else{
			print_r(json_encode(array("error" => 1)));
		}
	}

	/*Funciones de ayuda para los controladores*/
	private function search_products($search, $page)
	{
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

	private function folio($id_pedido)
	{
		$l = strlen($id_pedido);
		$t = 8 - $l;
		$f = '';

		for ($i=0; $i < $t; $i++) {
			$f = $f.'0';
		}
		$f = $f.$id_pedido;

		return $f;
	}

	private function create_pdf($html, $id_pedido, $fecha, $id_cliente)
	{
		/*$filename = strtolower(str_replace(' ', '_', $fecha));
		$filename = $this->normaliza($filename);
		$tmp = "c:\\wamp\\tmp\\";
		file_put_contents($tmp."$filename.html", $html);

		if (!file_exists(FCPATH.'assets\pdfs\\'.$id_cliente.'\\')) {
		    mkdir(FCPATH.'assets\pdfs\\'.$id_cliente.'\\', 0777);
		}

		$cmd = '"C:\Program Files (x86)\wkhtmltopdf\wkhtmltopdf.exe" -T 0 -B 0 -L 1 -R 0 -q -O landscape --zoom 1.3 '.$tmp.$filename.'.html '.FCPATH.'assets\pdfs\\'.$id_cliente.'\\'.$filename.'.pdf 2>&1';
		//print_r($cmd);
		exec($cmd);

		$data = array(
			'pdf' => $filename.".pdf",
			'cliente' => $id_cliente,
			'pedido' => $id_pedido

		);
		$this->twig->display('imprimir.html.twig', $data);*/

		$fecha = $this->normaliza($fecha);

		$filename = strtolower(str_replace(' ', '_', $fecha));
		$tmp = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir')."/" : sys_get_temp_dir()."/";
		file_put_contents($tmp."{$filename}.html", $html);
		$cmd = "/usr/bin/wkhtmltopdf/wkhtmltopdf -T 0 -B 0 -L 1 -R 0 -q -O landscape ".$tmp."{$filename}.html ".FCPATH."assets/pdfs/{$id_cliente}/{$filename}.pdf 2>&1";

		exec($cmd);

		$data = array(
			'pdf' => $filename.".pdf",
			'cliente' => $id_cliente,
			'pedido' => $id_pedido

		);
		$this->twig->display('imprimir.html.twig', $data);
	}

	private function normaliza ($cadena){
		$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
		ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
		$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
		bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
		$cadena = utf8_decode($cadena);
		$cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = strtolower($cadena);
		return utf8_encode($cadena);
}
}
