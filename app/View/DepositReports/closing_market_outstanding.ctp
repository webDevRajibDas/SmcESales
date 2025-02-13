<?php
$request_data = $this->Session->read('request_data');
//pr($offices);die();
App::import('Controller', 'DepositReportsController');
$DepositReportsController = new DepositReportsController;
?>


<style>
  .search .radio label {
    width: auto;
    float: none;
    padding-left: 5px;
  }

  .search .radio legend {
    float: left;
    margin: 5px 20px 0 0;
    text-align: right;
    width: 30%;
    display: inline-block;
    font-weight: 700;
    font-size: 14px;
    border-bottom: none;
  }

  #market_list .checkbox label {
    padding-left: 10px;
    width: auto;
  }

  #market_list .checkbox {
    width: 33%;
    float: left;
    margin: 1px 0;
  }
</style>

<style type="text/css">
  .table-responsive {
    color: #333;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    line-height: 1.42857;
  }

  .print-table {
    font-size: 11px;
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

  .titlerow th div {
    text-transform: capitalize;
    min-width: 100px;
    float: left;
    position: relative;
  }

  .titlerow th {
    text-align: center;
  }
</style>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary" style="float:left;">


      <div class="box-body">

        <?php //if($request_data){ 
        ?>

        <div class="pull-right csv_btn" style="padding:20px 0;">
          <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
        </div>

        <div id="content" style="width:98%; height:100%;margin-left:1%;margin-right:1%;">

          <div style="width:100%; text-align:center; font-size:12px;">
            <div style="font-size:20px;">SMC Enterprise Limited</div>
            <div style="font-size:14px;"><strong>Statement Of Closing Market Outstanding</strong></div>
          </div>


          <div style="float:left; width:100%; height:450px; overflow:scroll;">
            <table class="print-table table table-bordered table-responsive closing_outstanding_data" style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
              <thead>
                <tr style="font-weight:bold;">
                  <td style="text-align:left;">Area Office</td>
                  <td style="text-align:left;">Name Of Sales Officer</td>
                  <td>Memo No.</td>
                  <td>Memo Date</td>
                  <td style="text-align:left;">Customer Name</td>
                  <td style="text-align:right;">Market Outstanding</td>
                  <td>No of Days</td>
                </tr>
              </thead>

              <tbody>

                <?php /*?><?php foreach($due_memo as $data){ ?>
                                <tr>
                                    <td style="text-align:left;"><?=$data[0]['office_name']?></td>
                                    <td style="text-align:left;"><?=$data[0]['so_name'].' ('.$data[0]['territory_name'].')'?></td>
                                    <td><?=$data[0]['memo_no']?></td>
                                    <td><?=date('d-M-y',strtotime($data[0]['memo_date']))?></td>
                                    <td style="text-align:left;"><?=$data[0]['outlet']?></td>
                                    <td style="text-align:right;"><?=sprintf("%01.2f", $data[0]['memo_value'])?></td>
                                    <td>
                                    <?php
                                    $date2=date_create(date('Y-m-d'));
                                    $date1=date_create($data[0]['collection_date']? $data[0]['collection_date']:$data[0]['memo_date']);
                                    $diff=date_diff($date1,$date2);
                                    // echo $diff->format("%R%a days");
                                    echo $diff->format("%a days");
                                    ?>
                                    </td>
                                </tr>
                             <?php } ?><?php */ ?>

                <?php
                $g_total = 0;
                foreach ($results as $office_name => $due_memo) {
                ?>

                  <?php
                  $office_total = 0;
                  foreach ($due_memo as $data) {
                  ?>
                    <tr>
                      <td style="text-align:left;"><?= $data['office_name'] ?></td>
                      <td style="text-align:left;"><?= $data['so_name'] . ' (' . $data['territory_name'] . ')' ?></td>
                      <td style="mso-number-format:\@;"><?= $data['memo_no'] ?></td>
                      <td><?= date('d-M-y', strtotime($data['memo_date'])) ?></td>
                      <td style="text-align:left;"><?= $data['outlet'] ?></td>
                      <td style="text-align:right;">
                        <?= sprintf("%01.2f", $data['memo_value'] - $data['collection_amount']) ?>
                        <?php /*?><?=sprintf("%01.2f", $data['memo_value'])?><?php */ ?>
                      </td>
                      <td <?php if ($DepositReportsController->get_collection_date($data['memo_no'], date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to'])))) {
                            echo 'class="collected collected_office_' . $data['office_id'] . '"';
                          } else {
                            echo 'class="not_collected not_collected_office_' . $data['office_id'] . '"';
                          } ?>>
                        <?php
                        $date2 = date_create($DepositReportsController->get_collection_date($data['memo_no'], date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']))) ? $DepositReportsController->get_collection_date($data['memo_no'], date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']))) : date('Y-m-d'));
                        $date1 = date_create($data['collection_date'] ? $data['collection_date'] : $data['memo_date']);
                        $diff = date_diff($date1, $date2);
                        // echo $diff->format("%R%a days");
                        echo $diff->format("%a days");

                        if ($DepositReportsController->get_collection_date($data['memo_no'], date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to'])))) echo ' (Collected : ' . $DepositReportsController->get_collection_date($data['memo_no'], date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']))) . ')';

                        ?>
                      </td>
                    </tr>
                  <?php
                    // $office_total+=$data['memo_value'];  // comment by naser due to jitu vai requirement
                    $office_total += $data['memo_value'] - $data['collection_amount'];
                  }
                  ?>

                  <tr style="font-weight:bold; background:#eee;" class="office_total_<?= $data['office_id'] ?>">
                    <td colspan="5" class="text-right">Total :</td>
                    <td class="text-right"><?= sprintf("%01.2f", $office_total) ?></td>
                    <td></td>
                  </tr>

                <?php
                  $g_total += $office_total;
                }
                ?>


                <tr style="font-weight:bold; background:#ccc;;">
                  <td colspan="5" class="text-right">Grand Total :</td>
                  <td class="text-right"><?= sprintf("%01.2f", $g_total) ?></td>
                  <td></td>
                </tr>
              </tbody>

            </table>
          </div>

          <?php /*?><div style="float:left; width:100%; padding:100px 0 50px;; font-size:13px;">
                            <div style="width:33%;text-align:left;float:left">
                                Prepared by:______________ 
                            </div>
                            <div style="width:33%;text-align:center;float:left">
                                Checked by:______________ 
                            </div>
                            <div style="width:33%;text-align:right;float:left">
                                Signed by:______________
                            </div>		  
                        </div><?php */ ?>


        </div>
        <?php //} 
        ?>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#download_xl").click(function(e) {
      e.preventDefault();
      var html = $("#content").html();
      // console.log(html);
      var blob = new Blob([html], {
        type: 'data:application/vnd.ms-excel'
      });
      var downloadUrl = URL.createObjectURL(blob);
      var a = document.createElement("a");
      a.href = downloadUrl;
      a.download = "downloadFile.xls";
      document.body.appendChild(a);
      a.click();
    });
    /*if($(".not_collected").parent().html()!='')
    {
      var not_collected='<tr>\
      <td colspan="7">NOt Collected</td>\
      </tr>';
      $(".closing_outstanding_data>tbody").prepend(not_collected);
    }*/
    /*var collected='';
    var i=0;*/
    var collected_office_class = new Array();
    var i = 0;
    $(".collected").each(function() {
      var class_attr = $(this).attr('class');
      class_attr = class_attr.split(" ");
      if ($.inArray(class_attr[1], collected_office_class) == -1) {
        collected_office_class[i++] = class_attr[1];
      }
    });

    $(".not_collected").each(function() {
      var class_attr = $(this).attr('class');
      class_attr = class_attr.split(" ");
      class_attr = class_attr[1];
      class_attr = class_attr.replace('not_', '');
      if ($.inArray(class_attr, collected_office_class) == -1) {
        collected_office_class[i++] = class_attr;
      }
    });

    if (collected_office_class) {
      collected_office_class.forEach(function(item, index) {
        var j = 0;
        var office_id = item.split("_");
        var office_id = office_id[2];
        if ($(".not_collected_office_" + office_id).parent().html() != '') {
          var not_collected = '<tr>\
          <td colspan="7"  style="text-align:center; background:#ffff007d;">Not Collected</td>\
        </tr>';
          $(".not_collected_office_" + office_id).parent().eq(0).before(not_collected);
          var total_not_collected = 0.00;
          $(".not_collected_office_" + office_id).each(function() {

            total_not_collected += parseFloat($(this).prev().text());
          });
          var not_collected_subtotal = '<tr style="font-weight:bold; background:#ccc;;">\
          <td colspan="5"  style="text-align:right">Not Collected Sub Total</td>\
          <td style="text-align:right">' + total_not_collected + '</td>\
          <td style="text-align:center"></td>\
        </tr>';
          $(".not_collected_office_" + office_id).parent().last().after(not_collected_subtotal);
          // console.log(total_not_collected);
        }

        var collected_info = '';
        var total_collected = 0.00;
        $("." + item).each(function() {
          if (j == 0) {
            collected_info += '<tr>\
          <td colspan="7"  style="text-align:center; background:#0080008f;">Collected</td>\
          </tr>';
          }
          collected_info += '<tr>';
          collected_info += $(this).parent().html();
          collected_info += '</tr>';
          j++;
          total_collected += parseFloat($(this).prev().text());
          $(this).parent().remove();
        });
        if (collected_info) {
          $(".closing_outstanding_data>tbody").find('.office_total_' + office_id).before(collected_info);
          var collected_subtotal = '<tr style="font-weight:bold; background:#ccc;;">\
          <td colspan="5"  style="text-align:right">Collected Sub Total</td>\
          <td style="text-align:right">' + total_collected + '</td>\
          <td style="text-align:center"></td>\
        </tr>';
          $(".closing_outstanding_data>tbody").find('.office_total_' + office_id).before(collected_subtotal);
        }
      });
    }
    /* if(collected)
     {
       $(".closing_outstanding_data>tbody").append(collected);
     }*/

  });
</script>