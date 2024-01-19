<?php

/**
 * Vista de Modifica Producto
 *
 * Esta vista proporciona un formulario para modificar un producto.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

?>

<h4 class="mt-3">Modificar producto</h4>

<?php if (validation_errors()) : ?>
	<div class="alert alert-danger alert-dismissible fade show col-md-6" role="alert">
		<ul>
			<?php echo validation_errors();  ?>
		</ul>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<form method="post" class="row g-3 mt-2" action="<?php echo site_url('productos/actualizar'); ?>" autocomplete="off">

	<input type="hidden" name="id" value="<?php echo $producto->id; ?>">

	<div class="col-md-4">
		<label for="codigo" class="form-label"><span class="text-danger">*</span> Código</label>
		<input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo set_value('codigo', $producto->codigo, true); ?>" autofocus required>
	</div>

	<div class="col-md-8">
		<label for="nombre" class="form-label"><span class="text-danger">*</span> Nombre</label>
		<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo set_value('nombre', $producto->nombre, true); ?>" required>
	</div>

	<div class="col-md-4">
		<label for="tipo_venta" class="form-label"><span class="text-danger">*</span> Se vende por</label>
		<select class="form-select" name="tipo_venta" id="tipo_venta">
			<option value="P" <?php echo ($producto->tipo_venta == 'P') ? 'selected' : ''; ?>>Unidad / Pieza</option>
			<option value="G" <?php echo ($producto->tipo_venta == 'G') ? 'selected' : ''; ?>>Granel (con decimales)</option>
		</select>
	</div>

	<div class="col-md-4">
		<label for="precio_venta" class="form-label"><span class="text-danger">*</span> Precio de venta</label>
		<input type="text" class="form-control" id="precio_venta" name="precio_venta" value="<?php echo set_value('precio_venta', $producto->precio_venta); ?>" onkeypress="return validateDecimal(this.value);" required>
	</div>

	<div class="col-md-4">
		<label for="precio_compra" class="form-label">Precio de compra</label>
		<input type="text" class="form-control" id="precio_compra" name="precio_compra" value="<?php echo set_value('precio_compra', $producto->precio_compra); ?>" onkeypress="return validateDecimal(this.value);" required>
	</div>

	<div class="col-md-4">
		<label for="inventariable" class="form-label"><span class="text-danger">*</span> Es inventariable</label>
		<select class="form-select" name="inventariable" id="inventariable">
			<option value="1" <?php echo ($producto->inventariable == 1) ? 'selected' : ''; ?>>Si</option>
			<option value="0" <?php echo ($producto->inventariable == 0) ? 'selected' : ''; ?>>No</option>
		</select>
	</div>

	<div class="col-md-4">
		<label for="existencia" class="form-label">Existencia actual</label>
		<input type="text" class="form-control" id="existencia" name="existencia" value="<?php echo set_value('existencia', $producto->existencia); ?>">
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