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

	//Carga vista inicio de sesion
	public function index()
	{
		$this->load->view('header');
		$this->load->view('ventas/caja');
		$this->load->view('footer');
	}

	public function inserta()
	{
		$error = '';

		if ($this->input->is_ajax_request()) {

			$codigo   = $this->input->post('codigo', TRUE);
			$cantidad = $this->input->post('cantidad', TRUE);
			$id_venta = $this->input->post('id_venta', TRUE);

			$producto = $this->productos->where('codigo', $codigo)->first();

			if ($producto) {

				$datosExiste = $this->tmp_mov->porIdProductoMov($producto['id'], $id_venta);
				if ($datosExiste) {
					$cantidad = $datosExiste->cantidad + $cantidad;
					if ($producto['inventariable'] == 1) {
						if ($producto['existencias'] >= $cantidad) {

							$subtotal = $cantidad * $datosExiste->precio;

							$this->tmp_mov->actualizarProductoMov($producto['id'], $id_venta, $cantidad, $subtotal);
						} else {
							$error = 'NO hay suficientes existencia';
						}
					} else {
						$subtotal = $cantidad * $datosExiste->precio;
						$this->tmp_mov->actualizarProductoMov($producto['id'], $id_venta, $cantidad, $subtotal);
					}
				} else {

					if ($producto['inventariable'] == 1) {
						if ($producto['existencias'] >= $cantidad) {

							$subtotal = $cantidad * $producto['precio_venta'];

							$this->tmp_mov->save([
								'folio' => $id_venta,
								'id_producto' => $producto['id'],
								'codigo' => $producto['codigo'],
								'nombre' => $producto['nombre'],
								'precio' => $producto['precio_venta'],
								'cantidad' => $cantidad,
								'subtotal' => $subtotal,
							]);
						} else {
							$error = 'No hay existencias';
						}
					} else {
						$subtotal = $cantidad * $producto['precio_venta'];

						$this->tmp_mov->save([
							'folio' => $id_venta,
							'id_producto' => $producto['id'],
							'codigo' => $producto['codigo'],
							'nombre' => $producto['nombre'],
							'precio' => $producto['precio_venta'],
							'cantidad' => $cantidad,
							'subtotal' => $subtotal,
						]);
					}
				}
			} else {
				$error = 'No existe el producto';
			}

			$res['datos'] = $this->cargaProductos($id_venta);
			$res['total'] = number_format($this->totalProductos($id_venta), 2, '.', ',');
			$res['error'] = $error;
			echo json_encode($res);
		}
	}

	public function cargaProductos($id_venta)
	{
		$resultado = $this->tmp_mov->porMovimiento($id_venta);
		$fila = '';
		$numFila = 0;

		foreach ($resultado as $row) {
			$numFila++;
			$fila .= "<tr id='fila" . $numFila . "'>";
			$fila .= "<td>" . $numFila . "</td>";
			$fila .= "<td>" . $row['codigo'] . "</td>";
			$fila .= "<td>" . $row['nombre'] . "</td>";
			$fila .= "<td>" . $row['precio'] . "</td>";
			$fila .= "<td>" . $row['cantidad'] . "</td>";
			$fila .= "<td>" . $row['subtotal'] . "</td>";
			$fila .= "<td><a onclick=\"eliminaProducto(" . $row['id_producto'] . ")\" class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
			$fila .= "</tr>";
		}
		return $fila;
	}

	public function totalProductos($id_venta)
	{
		$resultado = $this->tmp_mov->porMovimiento($id_venta);
		$total = 0;

		foreach ($resultado as $row) {
			$total += $row['subtotal'];
		}
		return $total;
	}
}
