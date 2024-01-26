<?php

/**
 * Vista de Nuevo Producto
 *
 * Esta vista proporciona un formulario para registar un nuevo producto.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

?>

<h4 class="mt-3">Agregar producto</h4>

<!-- Mensajes de validación -->
<?php if (validation_errors()) : ?>
	<div class="alert alert-danger alert-dismissible fade show col-md-6" role="alert">
		<ul>
			<?php echo validation_errors('<li>', '</li>');  ?>
		</ul>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<form method="post" class="row g-3 mt-2" action="<?php echo site_url('productos/insertar'); ?>" autocomplete="off">

	<div class="col-md-4">
		<label for="codigo" class="form-label"><span class="text-danger">*</span> Código</label>
		<input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo set_value('codigo') ?>" autofocus required>
	</div>

	<div class="col-md-8">
		<label for="nombre" class="form-label"><span class="text-danger">*</span> Nombre</label>
		<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo set_value('nombre') ?>" required>
	</div>

	<div class="col-md-4">
		<label for="precio_venta" class="form-label"><span class="text-danger">*</span> Precio de venta</label>
		<input type="text" class="form-control" id="precio_venta" name="precio_venta" value="<?php echo set_value('precio_venta', '0.00') ?>" onkeypress="return validateDecimal(this.value);" required>
	</div>

	<div class="col-md-4">
		<label for="precio_compra" class="form-label">Precio de compra</label>
		<input type="text" class="form-control" id="precio_compra" name="precio_compra" value="<?php echo set_value('precio_compra', '0.00') ?>" onkeypress="return validateDecimal(this.value);">
	</div>

	<div class="col-md-4">
		<label for="inventariable" class="form-label"><span class="text-danger">*</span> Es inventariable</label>
		<select class="form-select" name="inventariable" id="inventariable" required>
			<option value="1">Si</option>
			<option value="0">No</option>
		</select>
	</div>

	<div class="col-md-4">
		<label for="existencia" class="form-label">Existencia actual</label>
		<input type="text" class="form-control" id="existencia" name="existencia" value="<?php echo set_value('existencia', '0') ?>">
	</div>

	<div class="col-12">
		<label class="text-danger">( * ) Campo obligatorio</label>
	</div>

	<div class="col-12">
		<a href="<?php echo site_url('productos'); ?>" class="btn btn-secondary">Regresar</a>
		<button class="btn btn-success" type="submit">Guardar</button>
	</div>

</form>

<script type="text/javascript">
	$(document).on("keypress", 'form', function(e) {
		var code = e.keyCode || e.which;
		if (code == 13) {
			e.preventDefault();
			return false;
		}
	});

	function validateDecimal(valor) {
		let re = /^\d*\.?\d*$/;
		return re.test(valor);
	}

	$(document).ready(function() {
		$("#inventariable").change(function() {
			let option = $(this).children("option:selected").val();

			if (option == 1) {
				$("#existencia").prop('readonly', false);
			} else {
				$("#existencia").prop('readonly', true);
			}
			$("#existencia").val(0);
		});
	});
</script>