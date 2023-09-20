<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controlador de Inicio
 *
 * Esta clase controla las operaciones relacionadas con el panel de inicio.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Inicio extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//Carga vista inicio de sesion
	public function index()
	{
		if ($this->session->userdata('login') != 1) {
			redirect('login');
		}

		$this->load->view('header');
		$this->load->view('inicio');
		$this->load->view('footer');
	}
}
