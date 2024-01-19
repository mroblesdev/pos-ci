<?php

/**
 * Modelo de Productos
 *
 * Esta modelo gestiona la interacción con la tabla de productos.
 * Incluye funciones para obtener, insertar, actualizar y eliminar
 * registros de productos.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Productos_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Obtener productos, recibe activo 1 o 0
	public function obtener($activo = 1)
	{
		$this->db->order_by('codigo', 'ASC');
		return $this->db->get_where("productos", array('activo' => $activo))->result();
	}

	// Consulta producto por ID
	public function porId($id)
	{
		return $this->db->get_where("productos", ["id" => $id])->row();
	}

	// Consulta producto por codigo
	public function porCodigo($codigo)
	{
		return $this->db->get_where("productos", ["codigo" => $codigo, "activo" => 1]);
	}

	// Consulta producto por codigo
	public function porCodigoRes($codigo)
	{
		return $this->db->get_where("productos", ["codigo" => $codigo, "activo" => 1])->row();
	}

	// Consulta si el código ya existe para otros registro
	public function existeCodigo($codigo, $idProducto)
	{
		return $this->db->where('codigo', $codigo)
			->where('id !=', $idProducto)
			->get('productos')
			->row();
	}

	// Consulta producto por existecia
	public function porExistencia($existencia)
	{
		return $this->db->get_where("productos", ["existencia" => $existencia, "activo" => 1])->result();
	}

	// Insertar producto
	public function insertar()
	{
		$codigo = $this->input->post("codigo");
		$nombre = $this->input->post("nombre");
		$tipoVenta = $this->input->post("tipo_venta");
		$precioVenta = preg_replace('([^0-9\.])', '', $this->input->post("precio_venta"));
		$precioCompra = preg_replace('([^0-9\.])', '', $this->input->post("precio_compra"));
		$inventariable = $this->input->post("inventariable");
		$existencia = preg_replace('([^0-9\.])', '', $this->input->post('existencia'));

		$datos = array(
			"codigo" => $codigo,
			"nombre" => $nombre,
			"tipo_venta" => $tipoVenta,
			"precio_venta" => $precioVenta,
			"precio_compra" => $precioCompra,
			"inventariable" => $inventariable,
			"existencia" => $existencia,
			"activo" => 1,
			"fecha_alta" => date('Y-m-d H:i:s')
		);

		return $this->db->insert("productos", $datos);
	}

	// Actualiza producto
	public function actualizar($id)
	{
		$codigo = $this->input->post("codigo");
		$nombre = $this->input->post("nombre");
		$tipoVenta = $this->input->post("tipo_venta");
		$precioVenta = preg_replace('([^0-9\.])', '', $this->input->post("precio_venta"));
		$precioCompra = preg_replace('([^0-9\.])', '', $this->input->post("precio_compra"));
		$inventariable = $this->input->post("inventariable");
		$existencia = preg_replace('([^0-9\.])', '', $this->input->post('existencia'));

		$datos = array(
			"codigo" => $codigo,
			"nombre" => $nombre,
			"tipo_venta" => $tipoVenta,
			"precio_venta" => $precioVenta,
			"precio_compra" => $precioCompra,
			"inventariable" => $inventariable,
			"existencia" => $existencia,
		);

		$this->db->where('id', $id);
		return $this->db->update("productos", $datos);
	}

	// Actualiza activo de producto a 0
	public function eliminar($id)
	{
		$datos = ["activo" => 0];
		return $this->db->update("productos", $datos, ["id" => $id]);
	}

	// Actualiza activo de producto a 1
	public function reingresar($id)
	{
		$datos = ["activo" => 1];
		return $this->db->update("productos", $datos, ["id" => $id]);
	}
}
