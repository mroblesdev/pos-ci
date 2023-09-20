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
	}

	//Cargar catalogo
	public function index()
	{
		if ($this->session->userdata('login') != 1) {
			redirect('login');
		}

		$datos["productos"] = $this->productos_model->obtener(1);
		$this->load->view('header');
		$this->load->view('productos/index', $datos);
		$this->load->view('footer');
	}

	//Cargar catalogo eliminados
	public function eliminados()
	{
		if ($this->session->userdata('login') != 1) {
			redirect('login');
		}

		$datos["productos"] = $this->productos_model->obtener(0);

		$this->load->view("header");
		$this->load->view("productos/eliminados", $datos);
		$this->load->view("footer");
	}

	public function nuevo()
	{
		if ($this->session->userdata('login') != 1) {
			redirect('login');
		}

		$this->load->view('header');
		$this->load->view('productos/nuevo');
		$this->load->view('footer');
	}

	//Valida e inserta formulario nuevo 
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
			} else {
				$this->nuevo();
			}
		} else {
			$this->nuevo();
		}
	}

	//Cargar vista editar
	public function editar($id)
	{
		if ($this->session->userdata('login') != 1) {
			redirect('login');
		}
		$producto = $this->productos_model->porId($id);
		$datos['producto'] = $producto;

		$this->load->view('header');
		$this->load->view("productos/edita", $datos);
		$this->load->view('footer');
	}

	//Valida y actualiza formulario editar 
	public function actualizar()
	{
		$id = $this->input->post("id");
		$this->form_validation->set_rules('codigo', 'código', 'required');
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
			} else {
				$this->editar($id);
			}
		} else {
			$this->editar($id);
		}
	}

	//Elimina producto
	public function eliminar($id)
	{
		$resultado = $this->productos_model->eliminar($id);
		redirect("productos/");
	}

	//Reingresa producto
	public function reingresar($id)
	{
		$resultado = $this->productos_model->reingresar($id);
		redirect("productos/eliminados");
	}

	function mostrarProductos()
	{
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$search = $this->input->post("search");
		$activo = $this->input->post("activo");
		$search = $search['value'];
		$col = 0;
		$dir = "";

		$aColumns = array('codigo', 'nombre', 'precio_venta', 'precio_compra', 'existencia', 'id');
		$sTable = "productos";
		$sWhere = "activo = $activo";
		$sWhereoRG = "activo = $activo";

		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$dir = $o['dir'];
			}
		}

		if ($dir != "asc" && $dir != "desc")
			$dir = "desc";

		if (!isset($aColumns[$col]))
			$order = null;
		else
			$order = $aColumns[$col];

		if ($order != null)
			$this->db->order_by($order, $dir);

		if (!empty($search)) {
			$x = 0;
			foreach ($aColumns as $sterm) {
				if ($x == 0) {
					$sWhere .= " AND (" . $sterm . " LIKE '%" . $search . "%' ";
				} else {
					$sWhere .= " OR " . $sterm . " LIKE '%" . $search . "%' ";
				}
				$x++;
			}
			$sWhere .= ")";
		}

		$this->db->where($sWhere);
		$this->db->limit($length, $start);
		$prodcutos = $this->db->get($sTable);

		$data = array();

		if ($activo == 1) {
			foreach ($prodcutos->result() as $rows) {
				$data[] = array(
					$rows->codigo, $rows->nombre, $rows->precio_venta, $rows->precio_compra, $rows->existencia,
					"<a class='btn btn-warning btn-sm' href='" . site_url('productos/editar/' . $rows->id) . "' rel='tooltip' data-bs-placement='top' title='Modificar registro'><span class='fas fa-edit'></span></a>",
					"<a class='btn btn-danger btn-sm' href='#' data-bs-href='" . site_url('productos/eliminar/' . $rows->id) . "' rel='tooltip' data-bs-toggle='modal' data-bs-target='#confirmaModal' data-bs-placement='top' title='Eliminar registro'><span class='fa-solid fa-trash'></span></a>"
				);
			}
		} else {
			foreach ($prodcutos->result() as $rows) {
				$data[] = array(
					$rows->codigo, $rows->nombre, $rows->precio_venta, $rows->precio_compra, $rows->existencia,
					"<a class='btn btn-success btn-sm' href='#' data-bs-href='" . site_url('productos/reingresar/' . $rows->id) . "' rel='tooltip' data-bs-toggle='modal' data-bs-target='#confirmaModal' data-bs-placement='top' title='Reingresar registro'><span class='fa-solid fa-circle-up'></span></a>"
				);
			}
		}

		$total_registros = $this->totalRegistro($sTable, $sWhereoRG);
		$total_registros_filtrado = $this->totalRegistroFiltrados($sTable, $sWhere);
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total_registros,
			"recordsFiltered" => $total_registros_filtrado,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function totalRegistro($sTable, $sWhereoRG)
	{
		$query = $this->db->select("COUNT(*) as num")->where($sWhereoRG)->get($sTable)->row();
		if (isset($query)) return $query->num;
		return 0;
	}

	public function totalRegistroFiltrados($sTable, $where)
	{
		$query = $this->db->select("COUNT(*) as num")->where($where)->get($sTable)->row();

		if (isset($query)) return $query->num;
		return 0;
	}
}
