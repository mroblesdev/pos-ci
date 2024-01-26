<?php

/**
 * Vista de caja
 *
 * Esta vista proporciona la caja para realizar ventas
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

$idVentaTmp = uniqid();

?>

<div class="row mt-4">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <form id="form_venta" name="form_venta" method="post" action="<?php echo site_url('ventas/insertar'); ?>" autocomplete="off">

            <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $idVentaTmp; ?>">
            <input id="id_cliente" name="id_cliente" value="1" type="hidden" />

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-barcode"></i></span>
                            <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Escribe el código y presiona enter" onkeyup="agregarProducto(event, this.value, total.value, '<?php echo $idVentaTmp; ?>')" aria-label="codigo" aria-describedby="basic-addon1" autofocus>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <label for="codigo" id="resultado_error" style="color:red"></label>
                    </div>

                    <div class="col-12 col-sm-12 col-md-4">
                        <label style='font-weight:bold; font-size:30px; text-align:center;'> Total $</label><input type="text" name="total" id="total" size="7" readonly="true" value="0.00" style='font-weight:bold; font-size:30px; text-align:center; border:#E2EBED; background:#ffffff' />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-2">
                        <button id="completa_venta" type="button" class="btn btn-success">Completar venta</button>
                    </div>
                </div>
            </div>

            <br>

            <table id="tablaProductos" class="table table-hover table-striped table-sm table-responsive tablaProductos">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>C&oacute;digo</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </form>
    </div>
</div>

<br>

<div id="modalito" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Caja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Debe agregar un producto para completar la venta.</h6>
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" data-dismiss="modal">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const idVenta = <?php echo $idVentaTmp; ?>
    const siteUrl = '<?php echo site_url(); ?>'

    $(document).ready(function() {
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#modalito').on('hidden.bs.modal', function(e) {
            $('#codigo').focus();
        })

        $("#codigo").autocomplete({
            source: siteUrl + 'productos/autocompleteData',
            minLength: 3,
            focus: function() {
                return false;
            },
            select: function(event, ui) {
                event.preventDefault();
                $("#codigo").val(ui.item.value);
                setTimeout(
                    function() {
                        e = jQuery.Event("keypress");
                        e.which = 13;
                        enviaProducto(e, ui.item.value, 1);
                    }, 500);
            }
        });
    });

    function enviaProducto(e, codigo, cantidad) {
        let enterKey = 13;
        if (e.which == enterKey) {
            if (codigo != '' && codigo != null && codigo != 0 && cantidad > 0) {
                agregarProducto(codigo, cantidad);
            }
        }
    }

    function agregarProducto(codigo, cantidad) {
        $.ajax({
            method: "POST",
            url: siteUrl + 'caja/inserta',
            data: {
                codigo: codigo,
                cantidad: cantidad,
                id_venta: idVenta
            },
            success: function(resultado) {
                if (resultado != 0) {
                    $("#codigo").removeClass('has-error');
                    $('#codigo').autocomplete('close');
                    $("#codigo").val('');
                    let resultado = JSON.parse(response);

                    if (resultado.error != '') {
                        $("#resultado_error").html(resultado.error);
                        $("#codigo").focus();
                    } else {
                        $('#tablaProductos tbody').empty();
                        $("#tablaProductos tbody").append(resultado.datos);
                        $("#total").val(resultado.total);
                    }
                }
            }
        });
    }

    function eliminarProducto(id, idVentaTmp) {
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/caja/eliminarProductoVenta/' + id + '/' + idVentaTmp,
            success: function(response) {
                if (response == 0) {
                    $(tagCodigo).val('');
                } else {
                    var resultado = JSON.parse(response);
                    $("#resultado_error").html('');
                    $('#tablaProductos tbody').empty();
                    $("#tablaProductos tbody").append(resultado.datos);
                    $("#total").val(resultado.total);
                    $("#codigo").val('');
                }
            }
        });
    }

    $(function() {
        $("#completa_venta").click(function() {
            var nFilas = $("#tablaProductos tr").length;

            if (nFilas < 2) {
                $('#modalito').modal('show');
            } else {
                $("#form_venta").submit();
            }
        });
    });
</script>