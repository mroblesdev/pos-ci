<?php

/**
 * Modelo de Detalle de Ventas
 *
 * Esta modelo gestiona la interacción con la tabla detalle_venta.
 * Incluye funciones para insertar y obtener
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Detalle_venta_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Inserta detalles de venta (conceptos)
    public function insertar($datos)
    {
        return $this->db->insert("detalle_venta", [
            "id_venta"    => $datos['id_venta'],
            "id_producto" => $datos['id_producto'],
            "nombre"      => $datos['nombre'],
            "cantidad"    => $datos['cantidad'],
            "precio"      => $datos['precio'],
        ]);
    }

    public function porIdVenta($idVenta)
    {
        return $this->db->get_where("detalle_venta", array("id_venta" => $idVenta))->result();
    }
}
