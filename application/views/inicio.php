<?php

/**
 * Vista de Inicio
 * 
 * Esta vista proporciona el inicio del sistema.
 * 
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

?>

<!-- Icon Cards-->
<div class="row mt-3">
	<div class="col-xl-3 col-sm-6 mb-3">
		<div class="card text-white bg-primary o-hidden h-100">
			<div class="card-body">
				<div class="card-body-icon">
					<i class="fas fa-fw fa-list"></i>
				</div>
				<div class="mr-5"><?php //echo $this->db->get_where('productos', array('activo' => '1'))->num_rows(); ?> Total de productos</div>
			</div>
			<a class="card-footer text-white clearfix small z-1" href="<?php echo site_url('productos'); ?>">
				<span class="float-left">Ver detalles</span>
				<span class="float-right">
					<i class="fas fa-angle-right"></i>
				</span>
			</a>
		</div>
	</div>

	<div class="col-xl-3 col-sm-6 mb-3">
		<div class="card text-white bg-success o-hidden h-100">
			<div class="card-body">
				<div class="card-body-icon">
					<i class="fas fa-fw fa-shopping-cart"></i>
				</div>
				<div class="mr-5"><?php //echo $this->db->get_where('ventas', array('activo' => '1', 'DATE(fecha)' => date('Y-m-d'), 'id_caja' => $this->session->userdata('id_caja')))->num_rows(); ?> Ventas del día</div>
			</div>
			<a class="card-footer text-white clearfix small z-1" href="<?php echo base_url() ?>index.php/ventas/ventas_caja">
				<span class="float-left">Ver detalles</span>
				<span class="float-right">
					<i class="fas fa-angle-right"></i>
				</span>
			</a>
		</div>
	</div>

	<div class="col-xl-3 col-sm-6 mb-3">
		<div class="card text-white bg-danger o-hidden h-100">
			<div class="card-body">
				<div class="card-body-icon">
					<i class="fas fa-fw fa-shopping-basket"></i>
				</div>
				<div class="mr-5"><?php //echo $this->db->get_where('productos', 'stock_minimo>=existencia AND activo=1 AND inventariable=1')->num_rows(); ?> Productos con stock m&iacute;nimo</div>
			</div>
			<a class="card-footer text-white clearfix small z-1" href="<?php echo base_url() ?>index.php/productos/stockMinimo">
				<span class="float-left">Ver detalles</span>
				<span class="float-right">
					<i class="fas fa-angle-right"></i>
				</span>
			</a>
		</div>
	</div>
</div>