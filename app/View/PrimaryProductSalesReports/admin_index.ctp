<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">



      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Sales Report'); ?></h3>
        <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
      </div>


      <div class="box-body">

        <div class="search-box">
          <?php echo $this->Form->create('PrimaryProductSalesReports', array('role' => 'form', 'action' => 'index')); ?>
          <table class="search">

            <tr>
              <td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>

              <td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
            </tr>


            <tr align="center">
              <td colspan="2">
                <?php echo $this->Form->button('<i class="fa fa-search"></i> Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                <?php if (!empty($requested_data)) { ?>
                  <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                <?php } ?>

              </td>
            </tr>
          </table>

          <?php echo $this->Form->end(); ?>
        </div>



        <?php if (!empty($requested_data)) { ?>

          <div id="content" style="width:90%; margin:0 5%;">

            <style type="text/css">
              .table-responsive {
                color: #333;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                line-height: 1.42857;
              }

              .report_table {
                font-size: 12px;
              }

              .qty_val {
                width: 125px;
                margin: 0;
                float: left;
                text-transform: capitalize;
              }

              .val {
                border-right: none;
              }

              p {
                margin: 2px 0px;
              }

              .bottom_box {
                float: left;
                width: 33.3%;
                text-align: center;
              }

              td,
              th {
                padding: 5px;
              }

              table {
                border-collapse: collapse;
                border-spacing: 0;
              }

              .titlerow,
              .totalColumn {
                background: #f1f1f1;
              }

              .report_table {
                margin-bottom: 18px;
                max-width: 100%;
                width: 100%;
              }

              .table-responsive {
                min-height: 0.01%;
                overflow-x: auto;
              }
            </style>


            <div class="table-responsive">
              <div class="pull-right csv_btn" style="padding-top:20px;">
                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
              </div>
              <div class="xls_body">
                <div style="width:100%; text-align:center; padding:20px 0;">
                  <h2 style="margin:2px 0;">Social Marketing Company</h2>
                  <h3 style="margin:2px 0;">Product Sales Volume and Value by Brand</h3>
                  <p>
                    Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */ ?>
                  </p>
                  <p>Print Unit : Sale Unit</p>
                </div>

                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="width:50%;">

                  <tr class="titlerow">
                    <th style="width:60%;">Product Name</th>
                    <th style="text-align:right;width=10%;">Product Qty</th>
                    <th style="text-align:right;width=10%">Product Value</th>
                  </tr>

                  <?php
                  foreach ($product_list as $source => $p_data) {
                    $soruce_total_qty = 0;
                    $soruce_total_val = 0;
                  ?>
                    <tr class="rowDataSd" style="background:#f7f7f7; font-size:13px;">
                      <td colspan="3" style="text-align:left;">
                        <b><?= $source ?></b>
                      </td>
                    </tr>
                    <?php if ($primary_sales_data && isset($product_list_primary[$source])) {
                      $sec_total_qty = 0;
                      $sec_total_val = 0; ?>
                      <tr class="rowDataSd" style="background:#f7f7f7; font-size:13px;">
                        <td colspan="3" style="text-align:left;">
                          <b>Primary Sales</b>
                        </td>
                      </tr>
                      <?php foreach ($product_list_primary[$source] as $pid => $pdata) { ?>
                        <?php if (isset($primary_sales_data[$pid])) { ?>
                          <tr class="rowDataSd">
                            <td style="text-align:left;"><?= $pdata['product_name'] ?></td>
                            <td class="qty" style="text-align:right;"><?= $primary_sales_data[$pid]['qty'] ? sprintf("%01.2f", $primary_sales_data[$pid]['qty']) : '0.00' ?></td>
                            <td class="val" style="text-align:right;"><?= $primary_sales_data[$pid]['val'] ? sprintf("%01.2f", $primary_sales_data[$pid]['val']) : '0.00' ?></td>
                          </tr>
                        <?php
                          $sec_total_qty += $primary_sales_data[$pid]['qty'];
                          $sec_total_val += $primary_sales_data[$pid]['val'];
                        }
                        ?>
                      <?php } ?>
                      <tr class="totalColumn">
                        <td style="text-align:right;"><b>Sub Total:</b></td>
                        <td style="text-align:right;"><b><?= $sec_total_qty ?></b></td>
                        <td style="text-align:right;"><b><?= $sec_total_val ?></b></td>
                      </tr>
                    <?php

                      $soruce_total_qty += $sec_total_qty;
                      $soruce_total_val += $sec_total_val;
                    }
                    ?>

                    <?php if ($secondary_sales_data) {
                      $sec_total_qty = 0;
                      $sec_total_val = 0; ?>
                      <tr class="rowDataSd" style="background:#f7f7f7; font-size:13px;">
                        <td colspan="3" style="text-align:left;">
                          <b>Secondary Sales</b>
                        </td>
                      </tr>
                      <?php foreach ($p_data as $pid => $pdata) { ?>
                        <?php if (isset($secondary_sales_data[$pid])) { ?>
                          <tr class="rowDataSd">
                            <td style="text-align:left;"><?= $pdata['product_name'] ?></td>
                            <td class="qty" style="text-align:right;"><?= $secondary_sales_data[$pid]['qty'] ? sprintf("%01.2f", $secondary_sales_data[$pid]['qty']) : '0.00' ?></td>
                            <td class="val" style="text-align:right;"><?= $secondary_sales_data[$pid]['val'] ? sprintf("%01.2f", $secondary_sales_data[$pid]['val']) : '0.00' ?></td>
                          </tr>
                        <?php
                          $sec_total_qty += $secondary_sales_data[$pid]['qty'];
                          $sec_total_val += $secondary_sales_data[$pid]['val'];
                        }
                        ?>
                      <?php } ?>
                      <tr class="totalColumn">
                        <td style="text-align:right;"><b>Sub Total:</b></td>
                        <td style="text-align:right;"><b><?= $sec_total_qty ?></b></td>
                        <td style="text-align:right;"><b><?= $sec_total_val ?></b></td>
                      </tr>
                    <?php

                      $soruce_total_qty += $sec_total_qty;
                      $soruce_total_val += $sec_total_val;
                    }
                    ?>
                    <tr class="totalColumn">
                      <td style="text-align:right;"><b>Sub Total:</b></td>
                      <td style="text-align:right;"><b><?= $soruce_total_qty ?></b></td>
                      <td style="text-align:right;"><b><?= $soruce_total_val ?></b></td>
                    </tr>
                  <?php } ?>


                  <tr class="totalColumn">
                    <td style="text-align:right;"><b>Total:</b></td>
                    <td class="totalQty" style="text-align:right;"><b></b></td>
                    <td class="totalVal" style="text-align:right;"><b></b></td>
                  </tr>

                </table>
              </div>




              <script>
                <?php $total_v = '0,0'; ?>
                var totals_qty = [<?= $total_v ?>];
                var totals_val = [<?= $total_v ?>];
                $(document).ready(function() {

                  var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");


                  $dataRows.each(function() {
                    $(this).find('.qty').each(function(i) {
                      totals_qty[i] += parseFloat($(this).html());
                    });
                  });

                  $("#sum_table .totalQty b").each(function(i) {
                    $(this).html(totals_qty[i].toFixed(2));
                  });


                  $dataRows.each(function() {
                    $(this).find('.val').each(function(i) {
                      totals_val[i] += parseFloat($(this).html());
                    });
                  });
                  $("#sum_table .totalVal b").each(function(i) {
                    $(this).html(totals_val[i].toFixed(2));
                  });


                });
              </script>


              <div style="width:100%; padding:100px 0 50px;">
                <div class="bottom_box">
                  Prepared by:______________
                </div>
                <div class="bottom_box">
                  Checked by:______________
                </div>
                <div class="bottom_box">
                  Signed by:______________
                </div>
              </div>
            </div>

          </div>
        <?php } ?>

      </div>
    </div>
  </div>
</div>


<script>
  $('.outlet_type').on('ifChecked', function(event) {
    //alert($(this).val()); // alert value
    $.ajax({
      type: "POST",
      url: '<?php echo BASE_URL; ?>ProductSalesReports/get_category_list',
      data: 'outlet_type=' + $(this).val(),
      cache: false,
      success: function(response) {
        //alert(response);						
        $('.td_product_categories').html(response);
      }
    });
  });
</script>
<script>
  function PrintElem(elem) {
    var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

    //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('<html><head><title></title>');
    mywindow.document.write('</head><body >');
    //mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    //mywindow.close();

    return true;
  }
  $("#download_xl").click(function(e) {

    e.preventDefault();

    var html = $(".xls_body").html();

    var blob = new Blob([html], {
      type: 'data:application/vnd.ms-excel'
    });

    var downloadUrl = URL.createObjectURL(blob);

    var a = document.createElement("a");

    a.href = downloadUrl;

    a.download = "inventory_statemanet_report.xls";

    document.body.appendChild(a);

    a.click();

  });
</script>