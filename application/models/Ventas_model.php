<?php

/**
 * Modelo de Ventas
 *
 * Esta modelo gestiona la interacción con la tabla de ventas.
 * Incluye funciones para insertar, obtener y actualizar registros
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

    // Obtener ventas, recibe activo 1 o 0
    public function obtener($activo = 1)
    {
        $this->db->select('v.*, u.usuario');
        $this->db->from('ventas v');
        $this->db->join('usuarios u', 'v.id_usuario = u.id');
        $this->db->where('v.activo', $activo);
        $this->db->order_by('v.fecha', 'DESC');
        return $this->db->get()->result();
    }

    // Inserta producto en tabla ventas
    public function insertar($datos)
    {
        $this->db->insert("ventas", [
            "folio" => $datos['folio'],
            "total" => $datos['total'],
            "fecha" => $datos['fecha'],
            "id_usuario" => $datos['id_usuario'],
            "activo" => 1,
        ]);
        return $this->db->insert_id();
    }

    public function porId($id)
    {
        return $this->db->get_where("ventas", array("id" => $id))->row();
    }

    //Obtener venta por inner join, recibe id_venta
    public function obtenerVenta($id_venta)
    {
        $this->db->select('*');
        $this->db->from('ventas');
        $this->db->where('id_venta', $id_venta);
        $this->db->order_by('fecha', 'DESC');
        return $this->db->get()->result();
    }

    //Cambia activo de venta a 0
    public function eliminar($id)
    {
        $datos = ["activo" => 0];
        return $this->db->update("ventas", $datos, array("id" => $id));
    }

    //Consulta ultimo folio
    public function ultimoFolio()
    {
        return $this->db->get_where("configuracion", array('nombre' => 'ventas_folio'))->row()->valor;
    }

    //Actualiza siguiente folio
    public function siguienteFolio()
    {
        $this->db->set('valor', "LPAD(valor+1,10,'0')", FALSE);
        $this->db->where('nombre', 'ventas_folio');
        $this->db->update('configuracion');
    }
}
