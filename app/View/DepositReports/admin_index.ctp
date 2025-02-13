<?php //pr($closing_followup_result);die();
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

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="float:left;">

            <div class="box-header">
                <h3 class="box-title"><i
                            class="glyphicon glyphicon-th-large"></i> <?php echo __('Sales, Collection and Deposit Statement'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php //if($this->App->menu_permission('deposits','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Deposit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } 
                    ?>
                </div>
            </div>


            <div class="box-body">
                <div class="search-box" style="float:left; width:100%;">
                    <?php echo $this->Form->create('OutletSalesReports', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control date_picker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control date_picker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <?php if (isset($region_offices)) { ?>
                                <?php if ($office_parent_id) { ?>
                                    <td class="required"
                                        width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'options' => $region_offices,)); ?></td>
                                <?php } else { ?>
                                    <td class="required"
                                        width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?></td>
                                <?php } ?>
                            <?php } ?>

                            <?php if (!isset($region_offices)) { ?>
                                <td width="50%">
                                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id')); ?>
                                </td>
                            <?php } else { ?>
                                <td width="50%">
                                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id',/* 'required'=>true,*/ 'empty' => '---- All ----')); ?>
                                </td>
                            <?php } ?>

                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Sales Officers : </label>
                                <div id="market_list" class="input select"
                                     style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2"/>
                                        <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select /
                                            Unselect All</label>
                                    </div>
                                    <div class="selection2 so_list">
                                        <?php echo $this->Form->input('so_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>


                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'id' => 'search_button', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php
                                if (!empty($office_id)) {
                                    ?>
                                    <button onclick="PrintElem('content')" class="btn btn-primary"><i
                                                class="fa fa-print"></i> Print
                                    </button>
                                    <?php
                                }
                                ?>

                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>

                <script>
                    $(document).ready(function () {
                        $("input[type='checkbox']").iCheck('destroy');
                        $('#checkall2').click(function () {
                            var checked = $(this).prop('checked');
                            $('.selection2').find('input:checkbox').prop('checked', checked);
                        });
                        $('#checkall').click(function () {
                            var checked = $(this).prop('checked');
                            $('.selection').find('input:checkbox').prop('checked', checked);
                        });
                    });
                </script>

                <div class='row' style="float:left; width:100%;">
                    <div class='col-xs-6'>
                        <div id='Users_info' class='dataTables_info'>
                            <?php //echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total")));
                            ?>
                        </div>
                    </div>
                    <div class='col-xs-6'>
                        <div class='dataTables_paginate paging_bootstrap'>
                            <ul class='pagination'>
                                <?php
                                //echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
                                //echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
                                //echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>


                <style>
                    .print-table tr:hover {
                        cursor: pointer;
                        background: #ccc;
                    }
                </style>


                <?php if ($request_data) { ?>

                    <div class="pull-right csv_btn" style="padding-top:20px;">
                        <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
                    </div>

                    <div id="content" style="width:98%; height:100%;margin-left:1%;margin-right:1%;">

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

                        <div style="width:100%; text-align:center; font-size:12px;">
                            <div style="font-size:20px;">SMC Enterprise Limited</div>
                            <div style="font-size:14px;"><strong>SALES, COLLECTION AND DEPOSITION STATEMENT</strong>
                            </div>
                            <div style="font-size:11px;">
                                Between:&nbsp;&nbsp;<?php if (!empty($date_range_first)) echo date('d-M-Y', strtotime($date_range_first)); ?>
                                &nbsp;to&nbsp;<?php if (!empty($date_range_last)) echo date('d-M-Y', strtotime($date_range_last)); ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;Reporting
                                Date:&nbsp;&nbsp;<?php if (!empty($current_date)) echo date('d-M-Y', strtotime($current_date)); ?></div>
                            <div style="font-size:11px;padding-bottom:40px;">Area
                                : <?php if (!empty($office_id)) echo $offices[$office_id]; ?></div>
                        </div>

                        <div style="float:left; width:100%; height:450px; overflow:scroll;">
                            <table class="print-table table table-bordered table-responsive" style="width:100%"
                                   border="1px solid black" cellpadding="2px" cellspacing="0">
                                <tr>
                                    <th>
                                        <div style="width:100px; text-align:left;">Area Office</div>
                                    </th>
                                    <th>
                                        <div style="width:100px; text-align:left;">SO Name</div>
                                    </th>
                                    <th colspan="3">OPENING BALANCE</th>
                                    <th colspan="3">SALES</th>
                                    <th colspan="3">CREDIT COLLECTION</th>
                                    <th>TOTAL COLLECTION</th>
                                    <th colspan="3">DEPOSIT</th>
                                    <th colspan="3">CLOSING BALANCE</th>
                                    <th colspan="3">CLOSING FOLLOW-UP</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>IN THE HANDS OF SO</td>
                                    <td>MARKET OUTSTANDING</td>
                                    <td>RECEIVABLE FORM A.O/Z.O</td>
                                    <td>CASH</td>
                                    <td>CREDIT</td>
                                    <td>TOTAL</td>
                                    <td>AGAINST CUR PERIOD'S SALE</td>
                                    <td>AGAINST PRE PERIOD'S SALE</td>
                                    <td>TOTAL</td>
                                    <td></td>
                                    <td>AGAINST CUR PERIOD'S SALE</td>
                                    <td>AGAINST PRE PERIOD'S SALE</td>
                                    <td>TOTAL</td>
                                    <td>IN HANDS OF SO</td>
                                    <td>MARKET OUTSTANDING</td>
                                    <td>RECEIVABLE FORM A.O/Z.O</td>
                                    <td>Date of Subsequent Period</td>
                                    <td>Subsequent Period Deposit</td>
                                    <td>Balance</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td align="center">[A]</td>
                                    <td align="center">[B]</td>
                                    <td align="center">[C=A+B]</td>
                                    <td align="center">[D]</td>
                                    <td align="center">[E]</td>
                                    <td align="center">[F=D+E]</td>
                                    <td align="center">[G]</td>
                                    <td align="center">[H]</td>
                                    <td align="center">[I=G+H]</td>
                                    <td align="center">[J=I+D]</td>
                                    <td align="center">[K1]</td>
                                    <td align="center">[K2]</td>
                                    <td align="center">[K=K1+K2]</td>
                                    <td align="center">[L=(J-K)+A]</td>
                                    <td align="center">[M=(F-J)+B]</td>
                                    <td align="center">[N=L+M]</td>
                                    <td align="center">[O]</td>
                                    <td align="center">[P]</td>
                                    <td align="center">[Q=L-P]</td>
                                </tr>

                                <script>
                                    var id_number = 0;
                                </script>

                                <?php
                                $t_a = 0;
                                $t_b = 0;

                                $t_d = 0;
                                $t_e = 0;
                                $t_e_d_discount = 0;

                                $t_g = 0;
                                $t_h = 0;

                                $t_k1 = 0;
                                $t_k2 = 0;

                                $t_o = 0;
                                $t_p = 0;

                                if (!empty($so_list1)) {
                                    $i = 0;
                                    foreach ($so_list1 as $sales_person_id => $so_name) {
                                        $i = $i + 1;
                                        //$territory_id = $sales_person['Territory']['id'];
                                        //$sales_person_id = $sales_person['SalesPerson']['id'];
                                        if (!empty($sales_person_id)) {
                                            $hands_of_so_opening = $collection_amount[$sales_person_id][0][0]['total_collection'] - $deposit_amount[$sales_person_id][0][0]['total_deposit'];
                                            if (date('Y-m', strtotime($date_range_first)) == '2018-10' || date('Y-m', strtotime($date_range_last)) == '2018-10') {
                                                $hands_of_so_opening = 0;
                                            }
                                            //$market_outstanding = $sale_amount[$sales_person_id][0][0]['total_sale']-$collection_amount[$territory_id][0][0]['total_collection'];


                                            $current_periods_credit_collection = $current_credit_collection[$sales_person_id][0][0]['total_current_credit_collection'];
                                            $previous_periods_credit_collection = $previous_credit_collection[$sales_person_id][0][0]['total_previous_credit_collection'];

                                            //$market_outstanding = $opening_market_outstading[$sales_person_id]+$previous_periods_credit_collection;
                                            $market_outstanding = $opening_market_outstading[$sales_person_id]; // Added by naser comenting previous one 07 Feb 2019

                                        }
                                        $sales_cash = $cash_credit_amount[$sales_person_id][0][0]['total_cash'];
                                        $sales_credit = $cash_credit_amount[$sales_person_id][0][0]['total_credit'];
                                        $total_discount_sales_cash = $cash_credit_amount[$sales_person_id][0][0]['total_discount'];

                                        $current_period_sale_deposit = $current_deposit[$sales_person_id][0][0]['total_current_deposit'];
                                        $previous_period_sale_deposit = $previous_deposit[$sales_person_id][0][0]['total_previous_deposit'];

                                        //pr($sales_person);die();
                                        ?>

                                        <tr title="Click to view market outstanding!" class="closing_outstanding"
                                            data-href="<?= BASE_URL . 'deposit_reports/closing_market_outstanding'; ?>">

                                            <td align="left"><?= $offices[$so_offices[$sales_person_id]] ?></td>

                                            <td align="left"><?php echo $so_name ?></td>

                                            <td align="center" id="a<?php echo '_' . $i; ?>">
                                                <?php
                                                $a = $hands_of_so_opening;
                                                echo sprintf("%01.2f", $a);
                                                $t_a += $a;
                                                ?>
                                            </td>

                                            <td align="center" id="b<?php echo '_' . $i; ?>">
                                                <?php
                                                $b = $market_outstanding;
                                                echo sprintf("%01.2f", $b);
                                                $t_b += $market_outstanding;
                                                ?>
                                            </td>

                                            <td align="center" id="c<?php echo '_' . $i; ?>">
                                                <?php echo $c = ($a + $b) ?>
                                            </td>
                                            <td align="center" id="d<?php echo '_' . $i; ?>">
                                                <?php
                                                $d = $sales_cash;
                                                echo sprintf("%01.2f", $d);
                                                $t_d += $sales_cash;
                                                ?>
                                            </td>

                                            <td align="center" id="e<?php echo '_' . $i; ?>">
                                                <?php
                                                $e = $sales_credit;
                                                echo sprintf("%01.2f", $e);
                                                $t_e += $e;
                                                ?>
                                            </td>

                                            <td align="center" id="f<?php echo '_' . $i; ?>">
                                                <?php
                                                $t_e_d_discount += $total_discount_sales_cash;
                                                $f = ($d + $e - $total_discount_sales_cash);
                                                echo sprintf("%01.2f", ($f));
                                                ?>
                                            </td>

                                            <td align="center" id="g<?php echo '_' . $i; ?>">
                                                <?php
                                                echo $g = sprintf("%01.2f", $current_periods_credit_collection);
                                                ?>
                                            </td>

                                            <td align="center" id="h<?php echo '_' . $i; ?>">
                                                <?php echo $h = sprintf("%01.2f", $previous_periods_credit_collection); ?>
                                            </td>

                                            <td align="center" id="i<?php echo '_' . $i; ?>">
                                                <?php
                                                $t_g += $g;
                                                $t_h += $h;
                                                $i = $g + $h;
                                                echo sprintf("%01.2f", $i);
                                                ?>
                                            </td>

                                            <td align="center" id="j<?php echo '_' . $i; ?>">
                                                <?php
                                                $j = $i + $d;
                                                echo sprintf("%01.2f", $j);
                                                ?>
                                            </td>

                                            <td align="center" id="k1<?php echo '_' . $i; ?>">
                                                <?php echo $k1 = sprintf("%01.2f", $current_period_sale_deposit); ?>
                                            </td>

                                            <td align="center" id="k2<?php echo '_' . $i; ?>">
                                                <?php echo $k2 = sprintf("%01.2f", $previous_period_sale_deposit); ?>
                                            </td>

                                            <td align="center" id="k<?php echo '_' . $i; ?>">
                                                <?php
                                                $t_k1 += $k1;
                                                $t_k2 += $k2;
                                                $k = $k1 + $k2;
                                                echo sprintf("%01.2f", $k);
                                                ?>
                                            </td>


                                            <td align="center"
                                                id="l<?php echo '_' . $i; ?>"><?php echo $l = sprintf("%01.2f", (($j - $k) + $a)) ?></td>
                                            <td align="center"
                                                id="m<?php echo '_' . $i; ?>"><?php echo $m = sprintf("%01.2f", (($f - $j) + $b)) ?></td>
                                            <td align="center"
                                                id="n<?php echo '_' . $i; ?>"><?php echo $n = sprintf("%01.2f", ($l + $m)) ?></td>

                                            <td align="center" id="o<?php echo '_' . $i; ?>"><?php $o = 0;
                                                echo implode(array_keys($closing_followup_result[$sales_person_id]['deposited_date']), ',') ?></td>
                                            <td align="center"
                                                id="p<?php echo '_' . $i; ?>"><?= $p = sprintf("%01.2f", $closing_followup_result[$sales_person_id]['total_deposit_amount']); ?>
                                                <!-- <br><?= sprintf("%01.2f", $closing_followup_result[$sales_person_id]['cash_hands_of_so']); ?> -->
                                            </td>
                                            <td align="center" id="q<?php echo '_' . $i; ?>">
                                                <?php
                                                $t_o += $o;
                                                $t_p += $p;
                                                // echo $q=$o+$p;
                                                echo $q = $l - $p;
                                                ?>
                                            </td>
                                        </tr>


                                        <?php
                                        //break;
                                    }
                                }
                                ?>

                                <tr style="font-weight:bold;">

                                    <td colspan="2" align="center" style="text-align:right;">Total :</td>

                                    <td align="center"><?= sprintf("%01.2f", $t_a) ?></td>
                                    <td align="center"><?= sprintf("%01.2f", $t_b) ?></td>
                                    <td align="center"><?= $t_c = sprintf("%01.2f", $t_a + $t_b) ?></td>

                                    <td align="center"><?= sprintf("%01.2f", $t_d) ?></td>
                                    <td align="center"><?= sprintf("%01.2f", $t_e) ?></td>
                                    <td align="center"><?= $t_f = sprintf("%01.2f", ($t_d + $t_e - $t_e_d_discount)) ?></td>

                                    <td align="center"><?= sprintf("%01.2f", $t_g) ?></td>
                                    <td align="center"><?= sprintf("%01.2f", $t_h) ?></td>
                                    <td align="center"><?= $t_i = sprintf("%01.2f", $t_g + $t_h) ?></td>
                                    <td align="center"><?= $t_j = sprintf("%01.2f", ($t_i + $t_d)) ?></td>

                                    <td align="center"><?= sprintf("%01.2f", $t_k1) ?></td>
                                    <td align="center"><?= sprintf("%01.2f", $t_k2) ?></td>
                                    <td align="center"><?= $t_k = sprintf("%01.2f", ($t_k1 + $t_k2)) ?></td>

                                    <td align="center"><?= $t_l = sprintf("%01.2f", (($t_j - $t_k) + $t_a)) ?></td>

                                    <td align="center"><?php echo $t_m = sprintf("%01.2f", (($t_f - $t_j) + $t_b)) ?></td>
                                    <td align="center"><?php echo $t_n = sprintf("%01.2f", ($t_l + $t_m)) ?></td>

                                    <td align="center"><?= $t_o ?></td>
                                    <td align="center"><?= $t_p ?></td>
                                    <td align="center"><?= $t_q = $t_o + $t_p ?></td>
                                </tr>

                            </table>
                        </div>


                    </div>
                <?php } ?>

            </div>


        </div>
    </div>
</div>
<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        color: #c7c7c7;
    }
</style>
<script>
    $(document).ready(function () {
        //var date = new Date();
        var yesterday = new Date(new Date().setDate(new Date().getDate() - 1));
        //alert(yesterday);
        $('.date_picker').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true,
            startDate: '01-10-2018'
        });

    });
</script>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'deposit_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function () {
        $('.so_list').html('');
    });

    $('.office_id').change(function () {
        //alert($(this).val());
        date_from = $('#OutletSalesReportsDateFrom').val();
        date_to = $('#OutletSalesReportsDateTo').val();
        //if(date_from && date_to){
        $.ajax({
            type: "POST",
            //url: '<?= BASE_URL ?>sales_analysis_reports/get_office_so_list',
            url: '<?= BASE_URL ?>deposit_reports/get_territory_so_list',
            data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
            cache: false,
            success: function (response) {
                //alert(response);						
                $('.so_list').html(response);
            }
        });
        /*}
	else
	{
		$('#office_id option:nth-child(1)').prop("selected", true);
		alert('Please select date range!');
	}
*/
    });
</script>

<script>
    $(document).ready(function () {
        $('.date_from').change(function () {
            //getdate();
        });

        /*$('.date_to').change(function(){
        	var date_to_hidden = $('#date_to_hidden').val();
        	var get_date_to = $('#date_to').val();
        	//console.log(date_to_hidden);
        	//console.log(get_date_to);
            if(get_date_to<date_to_hidden){
            	alert('Minimum date range 7 days required');
            	$('#date_to').val('');
            }
        });*/

        function getdate() {
            var get_date_from = document.getElementById('date_from').value;
            var datearray = get_date_from.split("-");
            var date_from = datearray[1] + '-' + datearray[0] + '-' + datearray[2];

            var date = new Date(date_from);
            var newdate = new Date(date);
            newdate.setDate(newdate.getDate() + 6);

            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();

            var len = dd.toString().length;
            if (len == 1) {
                dd = '0' + dd;
            }

            var someFormattedDate = mm + '-' + dd + '-' + y;
            var datearray2 = someFormattedDate.split("-");
            var new_date_to = datearray2[1] + '-' + datearray2[0] + '-' + datearray2[2];

            document.getElementById('date_to_hidden').value = new_date_to;

        }


    });
</script>


<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindw.close();

        return true;
    }

    $(document).ready(function () {
        $(".closing_outstanding").click(function () {
            var url = $(this).data('href');
            //window.open(url, '_blank', 'titlebar=no, status=no, menubar=no, resizable=yes, scrollbars=yes, toolbar=no,location=no, height=800, width=800, top=200, left=50');
            var win = window.open(url, '_blank');
            win.focus();
        });
    });

    $(document).ready(function () {
        $("#download_xl").click(function (e) {
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
    });
</script>