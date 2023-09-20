<?php

/**
 * Pie de página de la plantilla
 *
 * Esta vista proporciona el pie de página de la plantilla para las vistas.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */
?>

</div>
</main>
<footer class="mt-4 py-3 bg-light">
	<div class="container-fluid px-4">
		<div class="d-flex align-items-center justify-content-between small">
			<div class="text-muted">Copyright &copy; POS-CI MRoblesDev <?php echo date('Y'); ?></div>
		</div>
	</div>
</footer>
</div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="<?php echo base_url('js/bootstrap.bundle.min.js'); ?>" crossorigin="anonymous"></script>

<script src="<?php echo base_url('js/scripts.js'); ?>"></script>
<script src="<?php echo base_url('js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('js/dataTables.bootstrap5.min.js'); ?>"></script>

<!-- Page level plugin JavaScript-->
<script src="<?php echo base_url() ?>vendor/chart.js/Chart.min.js"></script>

<!-- Demo scripts for this page-->
<script src="<?php echo base_url() ?>js/demo/chart-area-demo.js"></script>

<script>

	if (document.getElementById('confirmaModal') !== null) {
		const confirmaModal = document.getElementById('confirmaModal')
		confirmaModal.addEventListener('show.bs.modal', event => {
			const button = event.relatedTarget
			const href = button.getAttribute('data-bs-href')

			const buttonElimina = confirmaModal.querySelector('.modal-footer .btn-ok')
			buttonElimina.href = href
		})
	}

	$(document).ready(function() {
		$("body").tooltip({
			selector: '[rel=tooltip]'
		});
	});
</script>
</body>

</html>