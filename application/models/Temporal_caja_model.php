<?php

/**
 * Modelo de Eventos
 *
 * Esta modelo gestiona la interacción con la tabla de eventos.
 * Incluye funciones para obtener e insertar registros de eventos.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Temporal_caja_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('user_agent');
    }

    public function porIdProductoVenta($idProducto, $idVenta)
    {
        return $this->db->where('id_producto', $idProducto)
            ->where('id_venta', $idVenta)
            ->get('temporal_caja')
            ->row();
    }

    public function actualizaProductoVenta($idProducto, $idVenta, $cantidad, $importe)
    {
        $this->db->where('id_venta', $idVenta);
        $this->db->where('id_producto', $idProducto);
        return $this->db->update("temporal_caja", array('cantidad' => $cantidad, 'importe' => $importe));
    }

    public function insertar($datos)
    {
        $this->db->insert("temporal_caja", $datos);
    }

    public function porVenta($idVenta)
    {
        return $this->db->get_where("temporal_caja", array('id_venta' => $idVenta))->result();
    }

    public function totalPorVenta($idVenta)
    {
        $resultado = $this->db->select_sum('importe')
            ->where('id_venta', $idVenta)
            ->get('temporal_caja');

        if ($resultado->num_rows() > 0) {
            $importe = $resultado->row()->importe;
            return ($importe !== null) ? $importe : 0;
        } else {
            return 0;
        }
    }

    //Elimina producto de tabla temporal por id_producto e id_venta
    public function eliminar($idProducto, $idVenta)
    {
        return $this->db->delete("temporal_caja", array("id_producto" => $idProducto, "id_venta" => $idVenta));
    }
}
