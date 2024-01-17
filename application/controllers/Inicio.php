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
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
	}

	//Carga vista inicio de sesion
	public function index()
	{
		if ($this->session->userdata('login') === false) {
			redirect(base_url());
		}

		$this->load->view('header');
		$this->load->view('inicio');
		$this->load->view('footer');
	}
}
