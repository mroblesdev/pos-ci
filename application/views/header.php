<?php

/**
 * Encabezado de la plantilla
 *
 * Esta vista proporciona el encabezado de la plantilla para las vistas.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

$nombre_sistema = $this->db->get_where("configuracion", array('nombre' => 'tienda_nombre'))->row()->valor;

?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="Sistema Punto de Venta POS-CI" />
	<meta name="author" content="MRoblesDev" />
	<title>POS-CI v1.0</title>
	<link rel="icon" href="<?php echo base_url('images/favicon.png'); ?>" sizes="32x32" />

	<link href="<?php echo base_url('css/styles.css'); ?>" rel="stylesheet" />
	<link href="<?php echo base_url('css/all.min.css'); ?>" rel="stylesheet" />

	<!-- Page level plugin CSS-->
	<link href="<?php echo base_url('css/dataTables.bootstrap5.min.css'); ?>" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="<?php echo base_url() ?>vendor/jquery-ui/jquery-ui.css" rel="stylesheet">

	<script src="<?php echo base_url('js/jquery.min.js'); ?>"></script>
	<script src="<?php echo base_url() ?>vendor/jquery-ui/jquery-ui.js"></script>

</head>

<body class="sb-nav-fixed">
	<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
		<!-- Navbar Brand-->
		<a class="navbar-brand ps-3" href="<?php echo site_url('inicio'); ?>"><?php echo $nombre_sistema; ?></a>
		<!-- Sidebar Toggle-->
		<button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
			<i class="fas fa-bars"></i>
		</button>
		<!-- Navbar-->
		<ul class="navbar-nav ms-auto me-0 me-md-3 me-lg-4">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="fa-solid fa-user"></i>
					<?php echo $this->session->userdata('nombre'); ?>
				</a>
				<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
					<li>
						<a class="dropdown-item" href="<?php echo site_url('usuarios/editar_password/' . $this->session->userdata('id_usuario')); ?>">
							<i class="fa-solid fa-key"></i> Cambiar contraseña
						</a>
					</li>
					<li>
						<hr class="dropdown-divider" />
					</li>
					<li>
						<a class="dropdown-item" href="<?php echo site_url('auth/logout'); ?>">
							<i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</nav>
	<div id="layoutSidenav">
		<div id="layoutSidenav_nav">
			<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
				<div class="sb-sidenav-menu">
					<div class="nav">
						<a class="nav-link" href="<?php echo site_url('productos'); ?>">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-basket-shopping"></i></div>
							Productos
						</a>

						<a class="nav-link" href="<?php echo site_url('clientes'); ?>">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-user-friends"></i></div>
							Clientes
						</a>

						<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCompras" aria-expanded="false" aria-controls="collapseCompras">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-truck"></i></div>
							Compras
							<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse" id="collapseCompras" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
							<nav class="sb-sidenav-menu-nested nav">
								<a class="nav-link" href="<?php echo site_url('compras/nueva'); ?>">Nueva compra</a>
								<a class="nav-link" href="<?php echo site_url('compras'); ?>">Compras</a>
							</nav>
						</div>

						<a class="nav-link" href="<?php echo site_url('caja'); ?>">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-cash-register"></i></div>
							Caja
						</a>

						<a class="nav-link" href="<?php echo site_url('caja'); ?>">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
							Ventas
						</a>

						<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseReportes" aria-expanded="false" aria-controls="collapseReportes">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-list-alt"></i></div>
							Reportes
							<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse" id="collapseReportes" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
							<nav class="sb-sidenav-menu-nested nav">
								<a class="nav-link" href="<?php echo site_url('reportes/detalle_reporte_venta'); ?>">Reporte de ventas</a>
								<a class="nav-link" href="<?php echo site_url('reportes/muestra_reporte_productos'); ?>">Reporte de productos</a>
							</nav>
						</div>

						<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseConfig" aria-expanded="false" aria-controls="collapseConfig">
							<div class="sb-nav-link-icon"><i class="fa-solid fa-gear"></i></div>
							Administración
							<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
						</a>
						<div class="collapse" id="collapseConfig" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
							<nav class="sb-sidenav-menu-nested nav">
								<a class="nav-link" href="<?php echo site_url('configuracion'); ?>">Configuración</a>
								<a class="nav-link" href="<?php echo site_url('usuarios'); ?>">Usuarios</a>
								<a class="nav-link" href="<?php echo site_url('roles'); ?>">Roles</a>
								<a class="nav-link" href="<?php echo site_url('cajas'); ?>">Cajas</a>
								<a class="nav-link" href="<?php echo site_url('eventos'); ?>">Eventos de acceos</a>
							</nav>
						</div>
					</div>
				</div>
			</nav>
		</div>
		<div id="layoutSidenav_content">
			<main>
				<div class="container-fluid px-4">