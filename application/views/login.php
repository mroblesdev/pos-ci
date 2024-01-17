<?php

/**
 * Vista de Inicio de Sesión
 *
 * Esta vista proporciona un formulario de inicio de sesión para los usuarios.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */
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

	<!-- Estilos de plantilla -->
	<link href="<?php echo base_url('css/styles.css'); ?>" rel="stylesheet" />

</head>

<body class="bg-primary">
	<div id="layoutAuthentication">
		<div id="layoutAuthentication_content">
			<main>
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-5">
							<div class="card shadow-lg border-0 rounded-lg mt-5">
								<div class="card-header">
									<h3 class="text-center font-weight-light my-4">Iniciar sesión</h3>
								</div>
								<div class="card-body">
									<!-- Formulario de inicio de seisón -->
									<form action="<?php echo site_url('auth/verifica') ?>" method="POST" autocomplete="off">
										<div class="form-floating mb-3">
											<input class="form-control" id="usuario" name="usuario" type="text" placeholder="Usuario" value="<?php echo set_value('usuario') ?>" required autofocus>
											<label for="usuario">Usuario</label>
										</div>
										<div class="form-floating mb-3">
											<input class="form-control" id="password" name="password" type="password" placeholder="Contraseña" required>
											<label for="password">Contraseña</label>
										</div>

										<!-- Mensajes de validación -->
										<?php echo validation_errors('<div class="alert alert-danger" role="alert">', '</div>'); ?>

										<!-- Mensaje de datos incorrectos -->
										<?php if (isset($error)) { ?>
											<div class="alert alert-danger" role="alert">
												<?php echo $error;  ?>
											</div>
										<?php } ?>

										<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
											<button type="submit" class="btn btn-primary">Ingresar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>

		<!-- Pie de página -->
		<div id="layoutAuthentication_footer">
			<footer class="py-4 bg-light mt-auto">
				<div class="container-fluid px-4">
					<div class="d-flex align-items-center justify-content-between small">
						<div class="text-muted">Copyright &copy; POS-CI MRoblesDev <?php echo date('Y'); ?></div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<!-- Archivos de javascript -->
	<script src="<?php echo base_url('js/bootstrap.bundle.min.js'); ?>" crossorigin="anonymous"></script>
	<script src="<?php echo base_url('js/scripts.js'); ?>"></script>

</body>

</html>