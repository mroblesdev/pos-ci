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

class Eventos_model extends CI_Model
{
	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('user_agent');
    }

    public function insertar($idUsuario, $evento)
    {
        $ip = $this->input->ip_address();
        $detalles = $this->agent->agent_string(); $_SERVER['HTTP_USER_AGENT'];
        $fecha = date('Y-m-d H:i:s');

        $datos = array(
            "id_usuario" => $idUsuario,
            "ip" => $ip,
            "evento" => $evento,
            "detalles" => $detalles,
            "fecha" => $fecha,
        );

        $this->db->insert("eventos", $datos);
    }
}
