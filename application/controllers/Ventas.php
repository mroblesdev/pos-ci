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

	// Consulta producto para datatables
	public function mostrarVentas()
	{
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$search = $this->input->post("search", TRUE);
		$activo = $this->input->post("activo");
		$searchValue = $search['value'];
		$col = 0;
		$dir = "";

		$aColumns = array('folio', 'total', 'fecha', 'usuario', 'id');
		$sTable   = "v_ventas";
		$sWhere   = "activo = $activo";

		if (!empty($order)) {
			$col = $order[0]['column'];
			$dir = $order[0]['dir'];

			if (!isset($aColumns[$col])) {
				$order = null;
			} else {
				$order = $aColumns[$col];
			}
		}

		if ($dir != "asc" && $dir != "desc") {
			$dir = "desc";
		}

		if (!empty($searchValue)) {
			$searchConditions = array();
			foreach ($aColumns as $sterm) {
				$searchConditions[] = "$sterm LIKE '%" . $searchValue . "%' ";
			}
			$sWhere .= " AND (" . implode(" OR ", $searchConditions) . ")";
		}

		$this->db->where($sWhere);
		$this->db->order_by($order, $dir);
		$this->db->limit($length, $start);
		$ventas = $this->db->get($sTable)->result();

		$data = array();

		if ($activo == 1) {
			foreach ($ventas as $venta) {
				$buttons = array(
					"<a class='btn btn-primary btn-sm' href='" . site_url('ventas/ticket/' . $venta->id) . "' rel='tooltip' data-bs-placement='top' title='Ver ticket'><span class='fas fa-receipt'></span></a>",
					"<a class='btn btn-danger btn-sm' href='#' data-bs-href='" . site_url('ventas/eliminar/' . $venta->id) . "' rel='tooltip' data-bs-toggle='modal' data-bs-target='#confirmaModal' data-bs-placement='top' title='Eliminar registro'><span class='fa-solid fa-ban'></span></a>"
				);

				$data[] = array_merge(
					[
						$venta->folio,
						$venta->total,
						$venta->fecha,
						$venta->usuario,
					],
					$buttons
				);
			}
		} else {
			foreach ($ventas as $venta) {
				$buttons = array(
					"<a class='btn btn-primary btn-sm' href='" . site_url('ventas/ticket/' . $venta->id) . "' rel='tooltip' data-bs-placement='top' title='Ver ticket'><span class='fas fa-receipt'></span></a>"
				);

				$data[] = array_merge(
					[
						$venta->folio,
						$venta->total,
						$venta->fecha,
						$venta->usuario,
					],
					$buttons
				);
			}
		}

		$total_registros = $this->totalRegistro($sTable, "activo = $activo");
		$total_registros_filtrado = $this->totalRegistro($sTable, $sWhere);
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total_registros,
			"recordsFiltered" => $total_registros_filtrado,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	// Contar total de registro
	public function totalRegistro($sTable, $sWhere)
	{
		$query = $this->db->select("COUNT(*) as num")
			->where($sWhere)
			->get($sTable)
			->row();

		if (isset($query)) {
			return $query->num;
		}

		return 0;
	}

	//Cancela venta
	public function eliminar($id)
	{
		$this->ventas_model->eliminar($id);
		redirect("ventas/");
	}
}
