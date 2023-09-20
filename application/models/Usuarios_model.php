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

	public function login()
	{
		$usuario = $this->input->post('usuario');
		$password = $this->input->post('password');

		$query = $this->db->get_where('usuarios', array('usuario' => $usuario, 'activo' => 1));

		if ($query->num_rows() > 0) {
			$row = $query->row();

			if (password_verify($password, $row->password)) {
				$this->session->set_userdata('login', $row->activo);
				$this->session->set_userdata('id_usuario', $row->id);
				$this->session->set_userdata('usuario', $row->usuario);
				$this->session->set_userdata('nombre', $row->nombre);
				$this->session->set_userdata('id_rol', $row->id_rol);
				$this->session->set_userdata('id_caja', $row->id_caja);

				return true;
			}
		}
		$this->session->unset_userdata('user_data');
		return false;
	}
}
