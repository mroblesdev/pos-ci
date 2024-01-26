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

class Ventas extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');

		if (!$this->session->userdata('login')) {
			redirect(base_url());
		}
	}

	//Carga vista inicio de sesion
	public function index()
	{
		$this->load->view('header');
		$this->load->view('inicio');
		$this->load->view('footer');
	}

	//Carga caja
	public function caja()
	{
		$this->load->view("header");
		$this->load->view("ventas/caja", ["titulo" => "Caja"]);
		$this->load->view("footer");
	}
}
