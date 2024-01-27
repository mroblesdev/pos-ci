<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controlador de Caja
 *
 * Esta clase controla las operaciones relacionadas con la caja.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Caja extends CI_Controller
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

	// Carga vista de caja
	public function index()
	{
		$this->load->view('header');
		$this->load->view('ventas/caja');
		$this->load->view('footer');
	}

	// Inserta registro a tabla temporal del caja
	public function inserta()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}

		$error = '';

		$codigo   = $this->input->post('codigo', TRUE);
		$cantidad = $this->input->post('cantidad', TRUE);
		$idVenta = $this->input->post('id_venta', TRUE);

		$this->load->model("productos_model");
		$this->load->model("temporal_caja_model");

		$producto = $this->productos_model->porCodigoRes($codigo);

		if (empty($producto)) {
			$error = 'No existe el producto';
		} else {

			$idProducto = $producto->id;
			$productoCodigo = $producto->codigo;
			$productoNombre = $producto->nombre;
			$productoInventariable = $producto->inventariable;
			$productoExistencia = $producto->existencia;
			$productoPrecioVenta = $producto->precio_venta;

			$productoVenta = $this->temporal_caja_model->porIdProductoVenta($idProducto, $idVenta);
			$cantidad += $productoVenta ? $productoVenta->cantidad : 0;

			if ($productoInventariable == 1 && $productoExistencia < $cantidad) {
				$error = 'No hay suficientes existencias';
			} else {
				$subtotal = $cantidad * $productoPrecioVenta;

				$data = [
					'id_venta' => $idVenta,
					'id_producto' => $idProducto,
					'codigo' => $productoCodigo,
					'nombre' => $productoNombre,
					'precio' => $productoPrecioVenta,
					'cantidad' => $cantidad,
					'importe' => $subtotal,
				];

				if ($productoVenta) {
					$this->temporal_caja_model->actualizaProductoVenta($idProducto, $idVenta, $cantidad, $subtotal);
				} else {
					$this->temporal_caja_model->insertar($data);
				}
			}
		}

		$res['datos'] = $this->cargaProductos($idVenta);
		$res['total'] = number_format($this->temporal_caja_model->totalPorVenta($idVenta), 2, '.', ',');
		$res['error'] = $error;
		echo json_encode($res);
	}

	// Elimina producto de tabla temporal por id_producto e id_venta
	public function eliminaProductoVenta()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}

		$this->load->model("temporal_caja_model");

		$idProducto = $this->input->post('id_producto', TRUE);
		$idVenta = $this->input->post('id_venta', TRUE);

		$datos = $this->temporal_caja_model->porIdProductoVenta($idProducto, $idVenta);

		if ($datos) {
			$cantidad = max($datos->cantidad - 1, 0);
			$subtotal = $cantidad * $datos->precio;

			if ($cantidad > 0) {
				$this->temporal_caja_model->actualizaProductoVenta($idProducto, $idVenta, $cantidad, $subtotal);
			} else {
				$this->temporal_caja_model->eliminar($idProducto, $idVenta);
			}
		}
		$res['datos'] = $this->cargaProductos($idVenta);
		$res['total'] = number_format($this->temporal_caja_model->totalPorVenta($idVenta), 2, '.', ',');
		$res['error'] = '';

		echo json_encode($res);
	}

	// Carga los productos de la ventan a una tabla
	public function cargaProductos($idVenta)
	{
		$this->load->helper('form');

		$resultado = $this->temporal_caja_model->porVenta($idVenta);
		$fila = '';
		$numFila = 0;

		foreach ($resultado as $row) {
			$numFila++;
			$fila .= "<tr id='fila" . $numFila . "'>";
			$fila .= "<td>" . $numFila . "</td>";
			$fila .= "<td>" . html_escape($row->codigo) . "</td>";
			$fila .= "<td>" . html_escape($row->nombre) . "</td>";
			$fila .= "<td>" . $row->precio . "</td>";
			$fila .= "<td>" . $row->cantidad . "</td>";
			$fila .= "<td>" . $row->importe . "</td>";
			$fila .= "<td><a onclick=\"eliminaProducto(" . $row->id_producto . ", '" . $row->id_venta . "')\" class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
			$fila .= "</tr>";
		}
		return $fila;
	}
}
