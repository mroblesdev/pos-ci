<?php

/**
 * Modelo de Ventas
 *
 * Esta modelo gestiona la interacción con la tabla de ventas.
 * Incluye funciones para realizar ventas
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Ventas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Inserta producto en tabla temporal
    public function insertar($datos, $id_transaccion)
    {
        return $this->db->insert("movimientos", [
            "id_transaccion" => $id_transaccion,
            "tipo" => 'V',
            "id_producto" => $datos['id'],
            "codigo" => $datos['codigo'],
            "nombre" => $datos['nombre'],
            "cantidad" => 1,
            "precio" => $datos['precio_venta'],
            "subtotal" => $datos['precio_venta'],
        ]);
    }

    //Elimina producto de tabla temporal por id_producto e id_venta
    public function eliminar($id_producto, $id_venta)
    {
        return $this->db->delete("caja", ["id_producto" => $id_producto, "id_venta" => $id_venta]);
    }

    //Elimina productos de tabla temporal por id_venta
    public function eliminarVenta($id_venta)
    {
        return $this->db->delete("caja", ["id_venta" => $id_venta]);
    }

    //Consulta ventas por id_venta
    public function porVenta($id_venta)
    {
        return $this->db->get_where("caja", ["id_venta" => $id_venta])->result();
    }

    //Busca producto en tabla temporal por codigo e id_venta
    public function porCodigoVenta($codigo, $id_transaccion)
    {
        return $this->db->get_where("movimientos", ["codigo" => $codigo, "id_transaccion" => $id_transaccion, "tipo" => 'V'])->row();
    }

    //Busca producto en tabla temporal por id_producto e id_venta
    public function porIdProductoVenta($id_producto, $id_venta)
    {
        return $this->db->get_where("caja", ["id_producto" => $id_producto, "id_venta" => $id_venta])->row();
    }

    //Actualiza cantidad y subtotal de producto si existe en tabla temporal por codigo e id_venta
    public function actualizaProductoVenta($codigo, $id_transaccion, $cantidad, $subtotal)
    {
        $datos = [
            "cantidad" => $cantidad,
            "subtotal" => $subtotal,
        ];
        return $this->db->update("movimientos", $datos, ["codigo" => $codigo, "id_transaccion" => $id_transaccion, "tipo" => "C"]);
    }
}
