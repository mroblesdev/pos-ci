<?php

/**
 * Vista de catálogo de ventas
 *
 * Esta vista proporciona una tabla para mostrar las ventas con opciones
 * para ver y cancelar registro.
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

?>

<h4 class="mt-3" id="titulo">Ventas</h4>

<div class="centrado">
    <p>
        <a href="<?php echo site_url('ventas/eliminados'); ?>" class="btn btn-warning btn-sm">Eliminadas</a>
    </p>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm" id="dataTable" aria-describedby="titulo" style="width: 100%">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th width="3%"></th>
                <th width="3%"></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmaModal" tabindex="-1" aria-labelledby="confirmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="confirmaModalLabel">Eliminar registro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar este registro?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e) {
        var site_url = "<?php echo site_url(); ?>";

        $('#dataTable').DataTable({
            "language": {
                "url": "<?php echo base_url('js/DatatablesSpanish.json'); ?>"
            },
            "pageLength": 10,
            "serverSide": true,
            "order": [
                [0, "desc"]
            ],
            "ajax": {
                url: site_url + '/ventas/mostrarVentas',
                type: 'POST',
                data: {
                    activo: "1"
                }
            },
        });
    });

    const confirmaModal = document.getElementById('confirmaModal')
    confirmaModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const href = button.getAttribute('data-bs-href')

        const buttonElimina = confirmaModal.querySelector('.modal-footer .btn-ok')
        buttonElimina.href = href
    })
</script>