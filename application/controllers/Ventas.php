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

		redirect("ventas/ver_ticket/$resultado");
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
					"<a class='btn btn-primary btn-sm' href='" . site_url('ventas/ver_ticket/' . $venta->id) . "' rel='tooltip' data-bs-placement='top' title='Ver ticket'><span class='fas fa-receipt'></span></a>",
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
					"<a class='btn btn-primary btn-sm' href='" . site_url('ventas/ver_ticket/' . $venta->id) . "' rel='tooltip' data-bs-placement='top' title='Ver ticket'><span class='fas fa-receipt'></span></a>"
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

	public function ver_ticket($idVenta){
		$this->load->view('header');
		$this->load->view('ventas/ver_ticket', array('idVenta' => $idVenta));
		$this->load->view('footer');
	}

	public function ticket($idVenta)
	{
		ini_set('display_errors', 1);
		require APPPATH . '/third_party/fpdf/fpdf.php';
		require APPPATH . '/third_party/numeros_letras.php';

		$this->load->model("detalle_venta_model");

		$datosVenta = $this->ventas_model->porId($idVenta);
		$detalleVenta = $this->detalle_venta_model->porIdVenta($idVenta);

		$pdf = new FPDF('P', 'mm', array(80, 250));
		$pdf->AddPage();
		$pdf->SetMargins(5, 5, 5);
		$pdf->SetTitle("Ticket");
		$pdf->SetFont('Arial', 'B', 9);

		$fecha = substr($datosVenta->fecha, 0, 10);
		$hora = substr($datosVenta->fecha, 11, 8);

		$pdf->image(base_url('/images/logo.png'), 55, 3, 20, 15, 'PNG');
		$pdf->SetXY(5, 7);
		$pdf->Multicell(50, 4, utf8_decode($this->db->get_where('configuracion', array('nombre' => 'tienda_nombre'))->row()->valor), 0, 'C', 0);

		$pdf->Ln(7);
		$pdf->SetFont('Arial', '', 7);
		$pdf->Multicell(70, 4, utf8_decode($this->db->get_where('configuracion', array('nombre' => 'tienda_direccion'))->row()->valor), 0, 'C', 0);
		$pdf->Multicell(70, 4, utf8_decode($this->db->get_where('configuracion', array('nombre' => 'tienda_telefono'))->row()->valor), 0, 'C', 0);

		$pdf->SetFont('Arial', '', 8);
		$pdf->Ln();
		$pdf->Cell(60, 4, utf8_decode('Nº ticket:  ') . $datosVenta->folio, 0, 1, 'L');

		$pdf->Cell(60, 4, '=========================================', 0, 1, 'L');

		$pdf->Cell(7, 3, 'Cant.', 0, 0, 'L');
		$pdf->Cell(36, 3, utf8_decode('Descripción'), 0, 0, 'L');
		$pdf->Cell(14, 3, 'Precio', 0, 0, 'L');
		$pdf->Cell(14, 3, 'Importe', 0, 1, 'L');
		$pdf->Cell(70, 3, '------------------------------------------------------------------------', 0, 1, 'L');

		$pdf->SetFont('Arial', '', 6.5);

		foreach ($detalleVenta as $row) {
			$importe  = number_format(($row->cantidad * $row->precio), 2, '.', ',');
			$pdf->Cell(7, 3, $row->cantidad, 0, 0, 'C');
			$y = $pdf->GetY();
			$pdf->MultiCell(36, 3, utf8_decode($row->nombre), 0, 'L');
			$y2 = $pdf->GetY();
			$pdf->SetXY(48, $y);
			$pdf->Cell(14, 3, '$ ' . number_format($row->precio, 2, '.', ','), 0, 1, 'C');
			$pdf->SetXY(62, $y);
			$pdf->Cell(14, 3, '$ ' . $importe, 0, 1, 'C');
			$pdf->SetY($y2);
		}

		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(50, 4, 'Total', 0, 0, 'R');
		$pdf->Cell(20, 4, '$ ' . number_format($datosVenta->total, 2, '.', ','), 0, 1, 'R');

		$pdf->Ln();
		$pdf->SetFont('Arial', '', 8);
		$pdf->MultiCell(70, 4, 'Son ' .ucfirst(strtolower(NumeroALetras::convertir($datosVenta->total, 'pesos','centavos'))), 0, 'L', 0);

		$pdf->Ln();
		$pdf->Cell(10);
		$pdf->Cell(30, 4, 'Fecha: ' . date("d/m/Y", strtotime($fecha)), 0, 0, 'L');
		$pdf->Cell(30, 4, 'Hora: ' . $hora, 0, 1, 'L');

		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Multicell(70, 4, utf8_decode($this->db->get_where('configuracion', array('nombre' => 'ticket_leyenda'))->row()->valor), 0, 'C', 0);

		if ($datosVenta->activo == 0) {
            $pdf->SetTextColor(255, 0, 0,);
            $pdf->SetFontSize(24);
            $pdf->SetY(33);
            $pdf->Cell(0, 5, 'Venta cancelada', 0, 0, 'C');
        }

		$pdf->Output("Ticket.pdf", 'I');
	}
}
