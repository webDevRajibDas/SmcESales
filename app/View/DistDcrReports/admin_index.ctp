<?php

App::import('Controller', 'DistDcrReportsController');
$DcrController = new DistDcrReportsController;
?>


<style>
  .search .radio label {
    width: auto;
    float: none;
    padding: 0px 5% 0px 5px;
    margin: 0px;
  }

  .search .radio legend {
    float: left;
    margin: 5px 20px 0 0;
    text-align: right;
    width: 12.5%;
    display: inline-block;
    font-weight: 700;
    font-size: 14px;
    border-bottom: none;
  }

  #market_list .checkbox label {
    padding-left: 0px;
    width: auto;
  }

  #market_list .checkbox {
    width: 25%;
    float: left;
    margin: 1px 0;
  }

  body .td_rank_list .checkbox {
    width: auto !important;
    padding-left: 20px !important;
  }

  .radio input[type="radio"],
  .radio-inline input[type="radio"] {
    margin-left: 0px;
    position: relative;
    margin-top: 8px;
  }

  .search label {
    width: 25%;
  }

  #market_list {
    padding-top: 5px;
  }

  .market_list2 .checkbox {
    width: 15% !important;
  }

  .market_list3 .checkbox {
    width: 20% !important;
  }

  .box_area {
    display: none;
  }
</style>


<style>
  #divLoading {
    display: none;
  }

  #divLoading.show {
    display: block;
    position: fixed;
    z-index: 100;
    background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
    background-color: #666;
    opacity: 0.4;
    background-repeat: no-repeat;
    background-position: center;
    left: 0;
    bottom: 0;
    right: 0;
    top: 0;
  }

  #loadinggif.show {
    left: 50%;
    top: 50%;
    position: absolute;
    z-index: 101;
    width: 32px;
    height: 32px;
    margin-left: -16px;
    margin-top: -16px;
  }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">



      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>
        <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Dcr Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
      </div>


      <div class="box-body">

        <div class="search-box">
          <?php echo $this->Form->create('DistDcrReports', array('role' => 'form', 'action' => 'index')); ?>
          <table class="search">

            <tr>
              <td class="required" width="50%">
                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?>
              </td>

              <td class="required" width="50%">
                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?>

              </td>
            </tr>




            <tr>
              <?php if ($office_parent_id == 0) { ?>
                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
              <?php } ?>
              <?php if ($office_parent_id == 14) { ?>
                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
              <?php } ?>
              <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- All ----')); ?></td>
              <?php } else { ?>
                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- All ----')); ?></td>
              <?php } ?>
            </tr>
            <tr>
              <td>
                <?php echo $this->Form->input('db_id', array('label' => 'Distributor :', 'id' => 'db_id', 'class' => 'form-control db_id', 'empty' => '--- All ---')); ?>
              </td>
              <td>
                <?php echo $this->Form->input('sr_id', array('label' => 'SR :', 'id' => 'sr_id', 'class' => 'form-control sr_id', 'empty' => '--- All ---')); ?>
              </td>
            </tr>
            <tr align="center">
              <td colspan="2">

                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>

                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

              </td>
            </tr>
          </table>

          <?php echo $this->Form->end(); ?>
        </div>






        <?php if (!empty($request_data)) { ?>

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

              <div id="xls_body">
                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                  <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>


                  <h3 style="margin:2px 0;">Sales Officer's Daily Call Report</h3>

                  <p>
                    <b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
                  </p>

                  <p>
                    <?php if ($region_office_id) { ?>
                      <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                    <?php } else { ?>
                      <span>Head Office</span>
                    <?php } ?>
                    <?php if ($office_id) { ?>
                      <span>, Area Office: <?= $offices[$office_id] ?></span>
                    <?php } ?>
                    <?php if ($db_id) { ?>
                      <span>, Distributor Name: <span id="header_db_name"></span></span>
                    <?php } ?>
                  </p>

                </div>


                <div style="float:left; width:100%; height:430px; overflow:scroll;">
                  <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                    <tbody>
                      <tr class="titlerow">
                        <th>Outlet</th>
                        <th>Type</th>
                        <?php foreach ($product_list as $product_id => $pro_name) { ?>
                          <th class="remove_for_<?= $product_id ?>"><?= $pro_name ?></th>
                        <?php } ?>

                        <th>Order Value</th>
                        <th>Remarks</th>
                      </tr>


                      <?php if ($results) { ?>

                        <?php
                        $total_order_value = 0;
                        foreach ($results as $market_name => $outlet_datas) {
                        ?>
                          <tr style="background:#f9f9f9;">
                            <td class="market_column" style="text-align:left; font-size:12px;" colspan="<?= count($product_list) + 2 ?>"><b>Market :- <?= $market_name ?></b></td>
                          </tr>
                          <?php
                          $sub_total = array();
                          foreach ($outlet_datas as $outlet_name => $order_datas) {
                          ?>
                            <tr>
                              <td class="not_use_in_zero_remove"><?= $outlet_name ?></td>

                              <?php
                              $outlet_category_name = '';
                              $order_value = 0;
                              $remarks = '';
                              $total_order_in_row = 0;
                              foreach ($order_datas as $order_id => $order_info) {
                                $outlet_category_name = $order_info['dist_order']['outlet_category_name'];
                                $order_value += $order_info['dist_order']['gross_value'];
                                $total_order_in_row += 1;
                                $status = $order_info['dist_order']['status'];
                                $processing_status = $order_info['dist_order']['processing_status'];
                              }
                              if ($total_order_in_row > 1) {
                                $remarks = 'Multiple Order';
                              } else {
                                if ($status == 3)
                                  $remarks = 'Cancelled';
                                else if ($status == 2 && $processing_status == 1)
                                  $remarks = 'Invoice Created';
                                else if ($status == 2 && $processing_status == 2)
                                  $remarks = 'Delivered';
                                else
                                  $remarks = 'Pending';
                              }
                              ?>

                              <td class="not_use_in_zero_remove"><?= $outlet_category_name ?></td>

                              <?php foreach ($product_list as $product_id => $pro_name) { ?>
                                <td class="remove_for_<?= $product_id ?>">
                                  <?php
                                  $sales_qty = 0;

                                  foreach ($order_datas as $order_id => $order_info) {
                                    $sales_qty += @$order_info['dist_order_detial'][$product_id]['sales_qty'];
                                  }
                                  ?>
                                  <?= $sales_qty ? $sales_qty : '' ?>
                                </td>
                              <?php } ?>
                              <td><?= $order_value ?></td>
                              <td><?= $remarks ?></td>
                            </tr>
                        <?php
                            $total_order_value += $order_value;
                          }
                        }
                        ?>



                        <tr style="font-weight:bold; background:#f2f2f2;" class="totalrow">
                          <td colspan="2" style="text-align:right;">Total :</td>
                          <td colspan="<?= count($product_list); ?>" class="total_column">
                            <b>Order :</b> <?php echo count($dist_order_ids) ?>
                          </td>
                          <td><?= $total_order_value ?></td>
                        </tr>

                      <?php } else { ?>
                        <tr>
                          <td colspan="<?= count($product_list) + 1 ?>"><b>No Data Found!</b></td>
                        </tr>
                      <?php } ?>


                    </tbody>
                  </table>

                  <?php if ($results) { ?>
                    <h2>Summary</h2>
                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                      <tbody>
                        <tr class="titlerow">
                          <th colspan="2"></th>
                          <?php foreach ($product_list as $product_id => $pro_name) { ?>
                            <th class="remove_summery_<?= $product_id ?>"><?= $pro_name ?></th>
                          <?php } ?>

                          <th colspan="3">Total</th>

                        </tr>
                        <tr>
                          <td colspan="2">Total Sales</td>
                          <?php foreach ($product_list as $product_id => $pro_name) {  ?>
                            <td class="total_sales remove_summery_<?= $product_id ?>" data-id="<?= $product_id ?>"><?= @sprintf("%01.2f", @$total_sales_results[$product_id]) ?></td>
                          <?php } ?>
                          <td colspan="3"></td>
                        </tr>

                        <tr>
                          <td colspan="2">Bonus</td>
                          <?php
                          $total_bonus = 0;
                          foreach ($product_list as $product_id => $pro_name) {
                          ?>
                            <td class="bonus_qty_<?= $product_id ?> remove_summery_<?= $product_id ?>">
                              <?= @$bonus_results[$product_id]['sales_qty'] ? $bonus_results[$product_id]['sales_qty'] : 0 ?>
                            </td>
                          <?php
                            @$total_bonus += $bonus_results[$product_id]['sales_qty'];
                          }
                          ?>
                          <td colspan="3"><?= $total_bonus ?></td>
                        </tr>
                      </tbody>
                    </table>
                  <?php } ?>

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
  $('.region_office_id').selectChain({
    target: $('.office_id'),
    value: 'name',
    url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
    type: 'post',
    data: {
      'region_office_id': 'region_office_id'
    }
  });
  $('.region_office_id').change(function() {
    $('#territory_id').html('<option value="">---- All ----');
  });
</script>

<script>
  $(document).ready(function() {
    remove_zero_value_column();
    $("input[type='checkbox']").iCheck('destroy');
    $("input[type='radio']").iCheck('destroy');
    if ($('#office_id').val() && $('.date_from').val() && $('.date_to').val()) {
      get_db_list("req");
    }
    $('#office_id').change(function() {
      date_from = $('.date_from').val();
      date_to = $('.date_to').val();
      if (date_from && date_to) {
        get_db_list();
      } else {
        $('#office_id option:nth-child(1)').prop("selected", true);
        alert('Please select date range!');
      }
    });

    $('#db_id').change(function() {
      date_from = $('.date_from').val();
      date_to = $('.date_to').val();
      if (date_from && date_to) {
        get_sr_list();
      } else {
        $('#db_id option:nth-child(1)').prop("selected", true);
        alert('Please select date range!');
      }
    });

    function get_db_list(from = "change") {
      date_from = $('.date_from').val();
      date_to = $('.date_to').val();
      $.ajax({
        type: "POST",
        url: '<?= BASE_URL ?>dist_dcr_reports/get_db_list',
        data: 'office_id=' + $('#office_id').val() + '&date_from=' + date_from + '&date_to=' + date_to,
        cache: false,
        success: function(response) {
          //alert(response);                      
          $('#db_id').html(response);
          <?php if (isset($this->request->data['DistDcrReports']['db_id'])) { ?>
            if (from == "req") {
              if ($('#db_id').val(<?= $this->request->data['DistDcrReports']['db_id'] ?>)) {
                get_sr_list("req");
                var db_name = $('#db_id option:selected').text();
                $("#header_db_name").text(db_name);
              }
            }
          <?php } ?>
        }
      });
    }

    function get_sr_list(from = "change") {
      date_from = $('.date_from').val();
      date_to = $('.date_to').val();
      $.ajax({
        type: "POST",
        url: '<?= BASE_URL ?>dist_dcr_reports/get_sr_list_by_distributot_id_date_range',
        data: 'distributor_id=' + $('#db_id').val() + '&date_from=' + date_from + '&date_to=' + date_to,
        cache: false,
        success: function(response) {
          //alert(response);                      
          $('#sr_id').html(response);
          <?php if (isset($this->request->data['DistDcrReports']['sr_id'])) { ?>
            if (from == "req") {
              $('#sr_id').val(<?= $this->request->data['DistDcrReports']['sr_id'] ?>);

            }

          <?php } ?>
        }
      });
    }

    function remove_zero_value_column() {

      $('.total_sales').each(function(i, value) {
        var product_id = $(this).data('id');
        var total_sales = parseFloat($(this).text());
        var total_bonus = parseFloat($('.bonus_qty_' + product_id).text());

        if (total_sales == 0.00) {
          $(".remove_for_" + product_id).remove();
          $('.market_column').attr('colspan', ($('.market_column').attr('colspan') - 1));
          $('.total_column').attr('colspan', ($('.total_column').attr('colspan') - 1));
        }
        if (total_sales == 0.00 && total_bonus == 0.00) {
          $('.remove_summery_' + product_id).remove();
        }
      });
    }
  });
</script>


<script>
  function PrintElem(elem) {
    var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

    //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('<html><head><title></title><style>.csv_btn{display:none;}</style>');
    mywindow.document.write('</head><body>');
    //mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    //mywindow.close();

    return true;
  }

  $(document).ready(function() {

    $("#download_xl").click(function(e) {

      e.preventDefault();

      var html = $("#xls_body").html();

      // console.log(html);

      var blob = new Blob([html], {
        type: 'data:application/vnd.ms-excel'
      });

      var downloadUrl = URL.createObjectURL(blob);

      var a = document.createElement("a");

      a.href = downloadUrl;

      a.download = "dcr_reports.xls";

      document.body.appendChild(a);

      a.click();

    });

  });
</script>