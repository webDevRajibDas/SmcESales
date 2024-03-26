<?php
App::import('Controller', 'EsalesReportsController');
$EsalesController = new EsalesReportsController;
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
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    /*.td_rank_list #rank_list label{
	clear:right;
	width:50% !important;
}*/
</style>


<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Esales Sales Report'); ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Esales Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('EsalesReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <?php if (!$office_parent_id) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Outlet Category : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection">
                                        <?php echo $this->Form->input('outlet_category_id', array('id' => 'outlet_category_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $outlet_categories)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>



                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
                        </tr>

                        <?php /*?><tr>
							<td class="required" width="50%">
							<?php echo $this->Form->input('date_from', array('class' => 'form-control', 'empty'=>'---- Select ----', 'options'=> $date_list, 'required' => true)); ?>
                            </td>
                            
                            <td class="required" width="50%">
                            <?php echo $this->Form->input('date_to', array('class' => 'form-control', 'empty'=>'---- Select ----', 'options'=> $date_list, 'required' => true)); ?>
                            </td>	
						</tr><?php */ ?>


                        <tr>
                            <td>
                                <?php echo $this->Form->input('outlet_type', array('legend' => 'Type :', 'class' => 'outlet_type', 'type' => 'radio', 'value' => $outlet_type, 'options' => $type_list, 'required' => true));  ?></td>
                            <td></td>
                        </tr>



                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Rank : </label>
                                <div id="market_list" class="td_rank_list input select" style="float:left; width:50%;">
                                    <?php //echo $this->Form->input('report_esales_setting_id', array('id' => 'report_esales_setting_id', 'label'=>false, 'class' => 'checkbox simple', 'multiple' => 'checkbox', 'options' => $ranks, 'required' => true)); 
                                    ?>
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th class="text-left">Name</th>
                                            <th class="text-center" colspan="2">Start</th>
                                            <th class="text-center" colspan="2">End</th>
                                        </tr>

                                        <tbody class="td_rank_list1">
                                            <?php foreach ($ranks as $value) { ?>
                                                <tr class="text-center">
                                                    <td class="text-center" style="width:20%;">
                                                        <div class="checkbox simple">
                                                            <?php if (in_array($value['ReportEsalesSetting']['id'], $report_esales_setting_id)) { ?>
                                                                <input type="checkbox" id="report_esales_setting_id<?= $value['ReportEsalesSetting']['id'] ?>" checked value="<?= $value['ReportEsalesSetting']['id'] ?>" name="data[EsalesReports][report_esales_setting_id][]">
                                                            <?php } else { ?>
                                                                <input type="checkbox" id="report_esales_setting_id<?= $value['ReportEsalesSetting']['id'] ?>" value="<?= $value['ReportEsalesSetting']['id'] ?>" name="data[EsalesReports][report_esales_setting_id][]">
                                                            <?php } ?>

                                                            <label for="report_esales_setting_id<?= $value['ReportEsalesSetting']['id'] ?>"><?= $value['ReportEsalesSetting']['name'] ?></label>
                                                        </div>
                                                    </td>

                                                    <td style="width:20%;"><?= $value['ReportEsalesSetting']['operator_1'] ?></td>
                                                    <td style="width:20%;"><?= $value['ReportEsalesSetting']['range_start'] ?></td>

                                                    <td style="width:20%;"><?= $value['ReportEsalesSetting']['operator_2'] ?></td>
                                                    <td style="width:20%;"><?= $value['ReportEsalesSetting']['range_end'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                    </table>

                                </div>
                            </td>
                        </tr>




                        <tr align="center">
                            <td colspan="2">

                                <?php echo $this->Form->submit('Summary Report', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'summary')); ?>

                                <?php echo $this->Form->submit('Detail Report', array('type' => 'hidden', 'id' => 'detail', 'onclick' => 'formSubmit();', 'escape' => false, 'div' => false, 'name' => 'detail')); ?>

                                <?php echo $this->Form->submit('Detail Report', array('type' => 'button', 'class' => 'btn btn-large btn-primary', 'onclick' => 'formSubmit();', 'escape' => false, 'div' => false,)); ?>


                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php if (!empty($request_data)) { ?>
                                    <a onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
                                <?php } ?>

                            </td>
                        </tr>
                    </table>


                    <?php echo $this->Form->end(); ?>
                </div>


                <script>
                    //$(input[type='checkbox']).iCheck(false); 
                    function formSubmit() {
                        region_office_id = $('#region_office_id').val();
                        office_id = $('#office_id').val();
                        EsalesReportsDateFrom = $('#EsalesReportsDateFrom').val();
                        EsalesReportsDateTo = $('#EsalesReportsDateTo').val();
                        $('#detail').val(1);
                        //alert(office_id);

                        /*if(!region_office_id){
                        	alert('Please Select Region Office!');
                        }else //commnet by naser in 05-Feb-2019 due to jitu vai requirement for using area office*/
                        if (!office_id) {
                            alert('Please Select Area Office!');
                        } else if (!EsalesReportsDateFrom) {
                            alert('Please Select Date Range!');
                        } else if (!EsalesReportsDateTo) {
                            alert('Please Select Date Range!');
                        }

                        /*if(region_office_id && office_id){ //commnet by naser in 05-Feb-2019 due to jitu vai requirement for using area office*/
                        if (office_id) {
                            $('#EsalesReportsIndexForm').submit();
                        }
                    }

                    $(document).ready(function() {
                        $("input[type='checkbox']").iCheck('destroy');
                        $('#checkall').click(function() {
                            var checked = $(this).prop('checked');
                            $('.selection').find('input:checkbox').prop('checked', checked);
                        });
                    });
                </script>



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
                                <?= $this->Html->link(__('Download XLS'), array('action' => 'dwonload_csv?data=' . serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                            </div>

                            <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
                                <h2 style="margin:2px 0;">Social Marketing Company</h2>
                                <h3 style="margin:2px 0;">List of Outlet of the basis of purchasing capacity</h3>
                                <h3 style="margin:2px 0;"><?= (!empty($request_data['summary'])) ? $request_data['summary'] : 'Detail Reports' ?></h3>
                                <p>
                                    Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b>
                                </p>
                            </div>


                            <?php if (!empty($request_data['summary'])) { ?>

                                <?php if (!$request_data['EsalesReports']['office_id']) { ?>

                                    <div style="float:left; width:100%; text-align:center; padding:10px 0px; margin-top:20px; border:#333 solid 1px; border-bottom:none;">
                                        <h4 style="margin:0px;">Area Sales Office</h4>
                                    </div>

                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">
                                            <th>Rank</th>

                                            <?php foreach ($region_offices_2 as $key => $r_value) { ?>

                                                <?php foreach ($EsalesController->get_office_list_by_region($key) as $o_value) { ?>
                                                    <th> <?= str_replace('Sales Office', '', $o_value[0]['office_name']) ?></th>
                                                <?php } ?>

                                                <th><?= $r_value ?></th>
                                            <?php } ?>

                                            <th>National Total</th>

                                        </tr>

                                        <?php foreach ($ranks_2 as $value) { ?>
                                            <tr class="rowDataSd">

                                                <td><b><?= $value['ReportEsalesSetting']['name'] ?></b></td>

                                                <?php
                                                $national_total = 0;
                                                $outlet_total_data = 0;
                                                foreach ($region_offices_2 as $key => $r_value) {
                                                ?>
                                                    <?php
                                                    $regional_total = 0;
                                                    foreach ($EsalesController->get_office_list_by_region($key) as $o_value) {
                                                    ?>

                                                        <td class="qty">
                                                            <?php
                                                            echo $outlet_total_data = $EsalesController->getEsalesSum($request_data, $o_value[0]['id'], $value['ReportEsalesSetting']['operator_1'], $value['ReportEsalesSetting']['range_start'], $value['ReportEsalesSetting']['operator_2'], $value['ReportEsalesSetting']['range_end']);

                                                            $regional_total += $outlet_total_data;
                                                            ?>
                                                        </td>

                                                    <?php } ?>

                                                    <td class="qty"><?= $regional_total ?></td>

                                                <?php
                                                    $national_total += $regional_total;
                                                }
                                                ?>

                                                <td class="qty"><?= $national_total ?></td>

                                            </tr>
                                        <?php } ?>



                                        <tr class="totalColumn">

                                            <td><b>Total:</b></td>

                                            <?php foreach ($region_offices_2 as $key => $r_value) { ?>
                                                <?php foreach ($EsalesController->get_office_list_by_region($key) as $o_value) { ?>
                                                    <td class="totalQty"></td>
                                                <?php } ?>
                                                <td class="totalQty"></td>
                                            <?php } ?>

                                            <td class="totalQty"></td>

                                        </tr>


                                    </table>

                                    <?php
                                    $total_col = '0';
                                    foreach ($region_offices_2 as $key => $r_value) {
                                    ?>
                                        <?php foreach ($EsalesController->get_office_list_by_region($key) as $o_value) { ?>
                                            <?php $total_col .= ',0'; ?>
                                        <?php } ?>
                                        <?php $total_col .= ',0'; ?>
                                    <?php
                                    }
                                    $total_col .= ',0';
                                    ?>

                                <?php } else { ?>

                                    <div style="float:left; width:100%; text-align:center; padding:10px 0px; margin-top:20px; border:#333 solid 1px; border-bottom:none;">
                                        <h4 style="margin:0px;">Territory Sales Office</h4>
                                    </div>

                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">
                                            <th>Rank</th>

                                            <?php foreach ($territories_2 as $key => $t_value) { ?>
                                                <th> <?= $t_value ?></th>
                                            <?php } ?>

                                            <th>National Total</th>

                                        </tr>

                                        <?php foreach ($ranks_2 as $value) { ?>
                                            <tr class="rowDataSd">

                                                <td><b><?= $value['ReportEsalesSetting']['name'] ?></b></td>
                                                <?php
                                                $national_total = 0;
                                                $outlet_total_data = 0;
                                                foreach ($territories_2 as $key => $t_value) {
                                                ?>
                                                    <td class="qty">
                                                        <?php
                                                        echo $outlet_total_data = $EsalesController->getEsalesSumTerritory($request_data, $key, $value['ReportEsalesSetting']['operator_1'], $value['ReportEsalesSetting']['range_start'], $value['ReportEsalesSetting']['operator_2'], $value['ReportEsalesSetting']['range_end']);

                                                        $national_total += $outlet_total_data;
                                                        ?>
                                                    </td>
                                                <?php } ?>

                                                <td class="qty"><?= $national_total ?></td>

                                            </tr>
                                        <?php } ?>

                                        <tr class="totalColumn">
                                            <td><b>Total:</b></td>
                                            <?php foreach ($territories_2 as $o_value) { ?>
                                                <td class="totalQty"></td>
                                            <?php } ?>
                                            <td class="totalQty"></td>
                                        </tr>


                                    </table>

                                    <?php
                                    $total_col = '0';
                                    ?>
                                    <?php foreach ($territories_2 as $o_value) { ?>
                                        <?php $total_col .= ',0'; ?>
                                    <?php } ?>
                                    <?php $total_col .= ',0'; ?>
                                    <?php
                                    $total_col .= ',0';
                                    ?>

                                <?php } ?>


                                <script>
                                    var totals_qty = [<?= $total_col ?>];
                                    $(document).ready(function() {

                                        var $dataRows = $("#sum_table tr:not('.totalColumn, .titlerow')");

                                        $dataRows.each(function() {
                                            $(this).find('.qty').each(function(i) {
                                                totals_qty[i] += parseFloat($(this).html());
                                            });
                                        });

                                        $("#sum_table .totalQty").each(function(i) {
                                            $(this).html(totals_qty[i].toFixed(2));
                                        });


                                    });
                                </script>


                            <?php } ?>




                            <?php if (@!$request_data['summary']) { ?>

                                <?php
                                //$detail_results = $EsalesController->getEsalesDetailOutlet($request_data);
                                //pr($detail_results);
                                ?>

                                <div style="float:left; width:100%; text-align:center; padding:10px 0px; margin-top:20px; border:#333 solid 1px; border-bottom:none;">
                                    <h4 style="margin:0px;">Detail Report</h4>
                                </div>
                                <div style="float:left; width:100%; height:400px; overflow:scroll;">

                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                        <tr class="titlerow">
                                            <th>SI No</th>
                                            <th>Sales Area</th>
                                            <th>Outlet ID</th>
                                            <th>Total Memo <br> Values</th>
                                            <th>Avg. Monthly <br> Memo Vlaue</th>
                                            <th>Total Effective <br> Call (EC)</th>
                                            <th>Pharmacy <br> Name</th>
                                            <th>Market</th>
                                            <th>Territory</th>
                                            <th>Thana</th>
                                            <th>District</th>
                                            <th>Division</th>
                                            <th>Rank</th>
                                        </tr>

                                        <?php /*?><?php foreach($detail_results as $detail_result){?>
                                    <tr class="rowDataSd">
                                        <td><?=$detail_result['si_no']?></td>
                                        <td><?=$detail_result['sales_area']?></td>
                                        <td><?=$detail_result['outlet_id']?></td>
                                        <td><?=$detail_result['total_memo_value']?></td>
                                        <td><?=$detail_result['avg_monthly_memo_value']?></td>
                                        <td><?=$detail_result['total_effective_call_(EC)']?></td>
                                        <td><?=$detail_result['pharmacy_name']?></td>
                                        <td><?=$detail_result['market']?></td>
                                        <td><?=$detail_result['territory']?></td>
                                        <td><?=$detail_result['thana']?></td>
                                        <td><?=$detail_result['district']?></td>
                                        <td><?=$detail_result['division']?></td>
                                        <td><?=$detail_result['rank']?></td>
                                    </tr>
                                    <?php } ?><?php */ ?>

                                        <?= $detail_output ?>

                                    </table>
                                </div>

                            <?php } ?>


                            <div style="float:left; width:100%; padding:100px 0 50px;">
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
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    });
</script>


<script>
    $(document).ready(function() {
        //getRankList(<?= $outlet_type ?>);
    });

    $('.outlet_type').on('ifChecked', function(event) {
        getRankList($(this).val());
    });

    function getRankList(val) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>esales_reports/get_rank_list',
            data: 'type=' + val,
            cache: false,
            success: function(response) {
                //alert(response);						
                $('.td_rank_list1').html(response);
            }
        });
    }
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
</script>