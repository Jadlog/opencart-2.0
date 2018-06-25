<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-invoice" onclick="$('#form').attr('action', '<?php echo $dpdfranceexport; ?>'); $('#form').attr('target', '_self'); $('#form').submit();" data-toggle="tooltip" title="Exportar para Jadlog" class="btn btn-info"><i class="fa fa-print"></i></button>
      </div>
      <div id="dpdfrlogo"><img src="../image/data/dpdfrance/admin/admin.png"/></div><h1>Gerenciamento de pedidos</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
      <input id="tableFilter" value="<?php echo $text_search_filter; ?>"/><img id="filtericon" src="../image/data/dpdfrance/admin/search.png"/><br/><br/>
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="right"><?php if ($sort == 'o.order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.date_added') { ?>
                <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'customer') { ?>
                <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'shipping_code') { ?>
                <a href="<?php echo $sort_shipping_code; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_shipping_code; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_shipping_code; ?>"><?php echo $column_shipping_code; ?></a>
                <?php } ?></td>
             <td class="left"><?php if ($sort == 'address') { ?>
                <a href="<?php echo $sort_address; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_address; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_address; ?>"><?php echo $column_address; ?></a>
                <?php } ?></td>
              <td class="right"><?php echo $column_weight; ?></td>
              <td class="right">Campos DFE</td>
              <td class="right"><?php if ($sort == 'o.total') { ?>
                <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'status') { ?>
                <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                <?php } ?></td>
              <td class="left">Server Response ou Status</td>
            </tr>
          </thead>
          <tbody id="fbody">
            <?php if ($orders) { ?>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                <?php } ?></td>
              <td class="right"><?php echo $order['order_id']; ?></td>
              <td class="left"><?php echo $order['date_added']; ?></td>
              <td class="left"><?php echo $order['customer']; ?></td>
              <td class="left" width="40px"><?php echo $order['shipping_code']; ?></td>
              <td class="left"><?php echo $order['address']; ?></td>
              <td class="right"><input type="text" name="weight[<?php echo $order['order_id']; ?>]" value="<?php echo $order['weight']; ?>"></td>
              <td class="right"><input type="text" name="dfe[<?php echo $order['order_id']; ?>]" value="<?php echo $order['jadlog_embarcador_dfes']; ?>"></td>
              <td class="right"><?php echo $order['total']; ?></td>
              <td class="left"><?php echo $order['status']; ?></td>
              <td class="left"><?php echo $order['jadlog_embarcador_response_or_status']; ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="11"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>


<link rel='stylesheet' type='text/css' href='../image/data/dpdfrance/css/admin/AdminDPDFrance.css' media='screen'/>
<script type='text/javascript' src='../image/data/dpdfrance/js/admin/jquery/plugins/marquee/jquery.marquee.min.js'></script>
<script type="text/javascript"><!--
$(document).ready(function() {
                    $('.marquee').marquee({
                        duration: 20000,
                        gap: 50,
                        delayBeforeStart: 0,
                        direction: 'left',
                        duplicated: true,
                        pauseOnHover: true
                    });
        jQuery.expr[':'].contains = function(a, i, m) { 
                        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
                    };
                $("#tableFilter").keyup(function () {
                    //split the current value of tableFilter
                    var data = this.value.split(";");
                    //create a jquery object of the rows
                    var jo = $("#fbody").find("tr");
                    if (this.value == "") {
                        jo.show();
                        return;
                    }
                    //hide all the rows
                    jo.hide();

                    //Recusively filter the jquery object to get results.
                    jo.filter(function (i, v) {
                        var t = $(this);
                        for (var d = 0; d < data.length; ++d) {
                            if (t.is(":contains('" + data[d] + "')")) {
                                return true;
                            }
                        }
                        return false;
                    })
                    //show the rows that match.
                    .show();
                }).focus(function () {
                    this.value = "";
                    $(this).css({
                        "color": "black"
                    });
                    $(this).unbind('focus');
                }).css({
                    "color": "#808285"
                });
});
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
    if (e.keyCode == 13) {
        filter();
    }
});
//--></script> 
<?php echo $footer; ?>