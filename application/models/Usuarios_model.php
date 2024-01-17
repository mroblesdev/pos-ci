<?php

/**
 * Modelo de Usuarios
 *
 * Esta modelo gestiona la interacción con la tabla de usuarios.
 * Incluye funciones para obtener, crear, actualizar y eliminar registros de
 * usuarios.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

class Usuarios_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function login($usuario, $password)
	{
		$query = $this->db->get_where('usuarios', array('usuario' => $usuario, 'activo' => 1));

		if ($query->num_rows() > 0) {
			$row = $query->row();

			if (password_verify($password, $row->password)) {
				$this->configurar_sesion($row);

				return true;
			}
		}

		return false;
	}

	private function configurar_sesion($row)
	{
		$userdata = array(
			'login' => true,
			'id_usuario' => $row->id,
			'usuario' => $row->usuario,
			'nombre' => $row->nombre,
			'id_rol' => $row->id_rol,
			'id_caja' => $row->id_caja
		);

		$this->session->set_userdata($userdata);
	}
}
