<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controlador de Ventas
 *
 * Esta clase controla las operaciones relacionadas con las ventas
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
		$this->load->model("ventas_model");

		if (!$this->session->userdata('login')) {
			redirect(base_url());
		}
	}

	//Carga vista inicio de sesion
	public function index()
	{
		$datos["ventas"] = $this->ventas_model->obtener(1);
		$this->load->view('header');
		$this->load->view('ventas/index', $datos);
		$this->load->view('footer');
	}

	// Cargar catalogo eliminados
	public function eliminados()
	{
		$datos["ventas"] = $this->ventas_model->obtener(0);

		$this->load->view("header");
		$this->load->view("ventas/eliminados", $datos);
		$this->load->view("footer");
	}

	// Valida e inserta venta
	public function insertar()
	{
		$this->load->model("temporal_caja_model");
		$idVentaTmp = $this->input->post("id_venta", TRUE);

		$datos = array(
			'folio' => str_pad($this->ventas_model->ultimoFolio(), 10, 0, STR_PAD_LEFT),
			'total' => preg_replace('/[\$,]/', '', $this->input->post("total", TRUE)),
			'fecha' => date('Y-m-d H:i:s'),
			'id_usuario' => $this->session->userdata('id_usuario')
		);

		$resultado = $this->ventas_model->insertar($datos);

		if ($resultado) {
			$this->ventas_model->siguienteFolio();

			$ventaTmp = $this->temporal_caja_model->porVenta($idVentaTmp);

			$this->load->model("detalle_venta_model");
			$this->load->model("productos_model");

			foreach ($ventaTmp as $productoTmp) {
				$producto = array(
					'id_venta'    => $resultado,
					'id_producto' => $productoTmp->id_producto,
					'nombre'      => $productoTmp->nombre,
					'cantidad'    => $productoTmp->cantidad,
					'precio'      => $productoTmp->precio,
				);

				$this->detalle_venta_model->insertar($producto);

				$datosProducto = $this->productos_model->porId($productoTmp->id_producto);

				if ($datosProducto->inventariable == 1) {
					$this->productos_model->actualizaExistencia($productoTmp->id_producto, $productoTmp->cantidad, '-');
				}
			}
		}

		$this->temporal_caja_model->eliminaVenta($idVentaTmp);

		//redirect("caja/muestraTicket/$resultado");
	}

	//Cancela venta
	public function eliminar($id)
	{
		$resultado = $this->ventasModel->eliminar($id);
		redirect("ventas/");
	}
}
