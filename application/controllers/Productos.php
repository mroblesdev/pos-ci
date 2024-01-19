<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controlador de Productos
 *
 * Esta clase controla las operaciones relacionadas con los productos.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Productos extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("productos_model");
		$this->load->model('eventos_model');
		$this->load->helper('form');
		$this->load->library('form_validation');

		if (!$this->session->userdata('login')) {
			redirect(base_url());
		}
	}

	// Cargar catalogo
	public function index()
	{
		$datos["productos"] = $this->productos_model->obtener(1);
		$this->load->view('header');
		$this->load->view('productos/index', $datos);
		$this->load->view('footer');
	}

	// Cargar catalogo eliminados
	public function eliminados()
	{
		$datos["productos"] = $this->productos_model->obtener(0);

		$this->load->view("header");
		$this->load->view("productos/eliminados", $datos);
		$this->load->view("footer");
	}

	// Mostrar formulario nuevo
	public function nuevo()
	{
		$this->load->view('header');
		$this->load->view('productos/nuevo');
		$this->load->view('footer');
	}

	// Valida e inserta nuevo registro
	public function insertar()
	{
		$this->form_validation->set_rules('codigo', 'código', 'required|is_unique[productos.codigo]');
		$this->form_validation->set_rules('nombre', 'nombre', 'required');
		$this->form_validation->set_rules('precio_venta', 'precio de venta', 'required|numeric');
		$this->form_validation->set_rules('precio_compra', 'precio de compra', 'numeric');
		$this->form_validation->set_rules('existencia', 'existencia', 'numeric');
		$this->form_validation->set_message('required', 'El campo {field} es obligatorio.');
		$this->form_validation->set_message('numeric', 'El campo {field} debe contener solo números.');
		$this->form_validation->set_message('is_unique', 'El campo {field} debe contener un valor único.');

		if ($this->form_validation->run()) {
			$resultado = $this->productos_model->insertar();
			if ($resultado) {
				redirect("productos/");
			}
		}
		$this->nuevo();
	}

	// Cargar vista editar
	public function editar($id)
	{
		$producto = $this->productos_model->porId($id);
		$datos['producto'] = $producto;

		$this->load->view('header');
		$this->load->view("productos/edita", $datos);
		$this->load->view('footer');
	}

	// Valida y actualiza formulario editar
	public function actualizar()
	{
		$id = $this->input->post("id");

		if ($id == null) {
			redirect("productos/");
		}

		$this->form_validation->set_rules('codigo', 'código', 'required|callback_check_codigo[' . $id . ']');
		$this->form_validation->set_rules('nombre', 'nombre', 'required');
		$this->form_validation->set_rules('precio_venta', 'precio de venta', 'required|numeric');
		$this->form_validation->set_rules('precio_compra', 'precio de compra', 'numeric');
		$this->form_validation->set_rules('existencia', 'existencia', 'numeric');
		$this->form_validation->set_message('required', 'El campo {field} es obligatorio.');
		$this->form_validation->set_message('numeric', 'El campo {field} debe contener solo números.');
		$this->form_validation->set_message('is_unique', 'El campo {field} debe contener un valor único.');

		if ($this->form_validation->run()) {
			$resultado = $this->productos_model->actualizar($id);

			if ($resultado) {
				redirect("productos/");
			}
		}
		$this->editar($id);
	}

	//Elimina producto
	public function eliminar($id = null)
	{
		if ($id != null) {
			$this->productos_model->eliminar($id);
		}
		redirect("productos/");
	}

	// Reingresa producto
	public function reingresar($id = null)
	{
		if ($id != null) {
			$this->productos_model->reingresar($id);
		}
		redirect("productos/eliminados");
	}

	// Consulta producto para datatables
	public function mostrarProductos()
	{
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$search = $this->input->post("search");
		$activo = $this->input->post("activo");
		$searchValue  = $search['value'];
		$col = 0;
		$dir = "";

		$aColumns = array('codigo', 'nombre', 'precio_venta', 'precio_compra', 'existencia', 'id');
		$sTable = "productos";
		$sWhere = "activo = $activo";

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
		$productos = $this->db->get($sTable)->result();

		$data = array();

		if ($activo == 1) {
			foreach ($productos as $rows) {
				$buttons = array(
					"<a class='btn btn-warning btn-sm' href='" . site_url('productos/editar/' . $rows->id) . "' rel='tooltip' data-bs-placement='top' title='Modificar registro'><span class='fas fa-edit'></span></a>",
					"<a class='btn btn-danger btn-sm' href='#' data-bs-href='" . site_url('productos/eliminar/' . $rows->id) . "' rel='tooltip' data-bs-toggle='modal' data-bs-target='#confirmaModal' data-bs-placement='top' title='Eliminar registro'><span class='fa-solid fa-trash'></span></a>"
				);

				$data[] = array_merge(
					[
						html_escape($rows->codigo),
						html_escape($rows->nombre),
						$rows->precio_venta,
						$rows->precio_compra,
						$rows->existencia
					],
					$buttons
				);
			}
		} else {
			foreach ($productos as $rows) {
				$buttons = array(
					"<a class='btn btn-success btn-sm' href='#' data-bs-href='" . site_url('productos/reingresar/' . $rows->id) . "' rel='tooltip' data-bs-toggle='modal' data-bs-target='#confirmaModal' data-bs-placement='top' title='Reingresar registro'><span class='fa-solid fa-circle-up'></span></a>"
				);

				$data[] = array_merge(
					[
						html_escape($rows->codigo),
						html_escape($rows->nombre),
						$rows->precio_venta,
						$rows->precio_compra,
						$rows->existencia
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

	// Función de callback para verificar el código
	public function check_codigo($codigo, $idProducto)
	{
		$resultado = $this->productos_model->existeCodigo($codigo, $idProducto);

		// Si se encuentra un producto con el mismo código
		if ($resultado) {
			$this->form_validation->set_message('check_codigo', 'El código ya está en uso por otro usuario.');
			return false;
		}

		// El código es único
		return true;
	}
}
