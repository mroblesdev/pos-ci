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

	// Obtener productos filtrando por código por LIKE
	public function porCodigoLike($codigo = '')
	{
		return $this->db->like('codigo', $codigo)
			->where('activo', 1)
			->order_by('codigo', 'ASC')
			->get('productos')
			->result();
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
	public function insertar($datos)
	{
		return $this->db->insert("productos", $datos);
	}

	// Actualiza producto
	public function actualizar($id, $datos)
	{
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
