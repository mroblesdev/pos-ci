<?php

/**
 * Vista para mostrar ticket d eventa
 *
 * @version 1.0
 * @link https://github.com/mroblesdev/pos-ci
 * @author mroblesdev
 */

?>

<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="ratio ratio-16x9" style="margin-bottom: -20px;">
        <iframe src="<?php echo site_url('/ventas/ticket/' . $idVenta); ?>" title="Ticket"></iframe>
    </div>
</div>