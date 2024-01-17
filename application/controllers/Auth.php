<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controlador de Autenticación
 *
 * Esta clase controla las operaciones relacionadas con el inicio y cierre de sesión.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */
class Auth extends CI_controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("usuarios_model");
		$this->load->model("eventos_model");
		$this->load->helper('form');
		$this->load->library('form_validation');
	}

	//Carga vista inicio de sesion
	public function index()
	{
		$this->load->view('login');
	}

	//Verifica si usuario y password es correcto
	public function verifica()
	{
		$this->form_validation->set_rules('usuario', 'usuario', 'required');
		$this->form_validation->set_rules('password', 'contraseña', 'required');
		$this->form_validation->set_message('required', 'El campo {field} es obligatorio.');

		if ($this->form_validation->run()) {
			$usuario = $this->input->post('usuario');
			$password = $this->input->post('password');

			if ($this->usuarios_model->login($usuario, $password)) {
				$this->eventos_model->insertar($this->session->userdata('id_usuario'), 'INICIO DE SESIÓN');
				redirect('inicio');
			} else {
				$datos = array(
					'error' => 'El usuario y/o contraseña son incorrectos.'
				);
				$this->load->view('login', $datos);
			}
		} else {
			$this->load->view('login');
		}
	}

	//Cierra sesion
	public function logout()
	{
		if ($this->session->userdata('login') === true) {
			$this->eventos_model->insertar($this->session->userdata('id_usuario'), 'CIERRE DE SESIÓN');
		}
		$this->session->sess_destroy();
		redirect(base_url());
	}
}
