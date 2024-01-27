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

<h4 class="mt-3" id="titulo">Ventas eliminadas</h4>

<div class="centrado">
    <p>
        <a href="<?php echo site_url('ventas'); ?>" class="btn btn-info btn-sm">Ventas</a>
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
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
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
                    activo: "0"
                }
            },
        });
    });
</script>