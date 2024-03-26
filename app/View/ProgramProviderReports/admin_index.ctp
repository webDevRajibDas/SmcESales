<?php
App::import('Controller', 'ProgramProviderReportsController');
$ProgramProviderReportsController = new ProgramProviderReportsController;
?>
<?php //pr($so_info); 
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

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
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

    .filter_type_box div.input {
        float: left;
        position: relative;
        width: 50%;
    }

    .filter_type_box div.input,
    .filter_type_box div.filter_type {
        float: left;
        position: relative;
        width: 25%;
        margin: 0px;
    }

    .filter_type_box div.input input {
        float: left;
        margin-left: 10px;
        margin-top: 2px;
        position: relative;
        width: auto;
    }

    .filter_type_box div.input label {
        float: left;
        padding-left: 10px;
        font-weight: normal;
    }

    div.filter_type {
        margin: 0px;
    }

    .filter_type_box div.filter_type input {
        float: left;
        margin-left: 0px;
        margin-top: 2px;
        position: relative;
        width: auto;
    }

    .filter_type_box div.filter_type label {
        float: left;
        padding-left: 10px;
        font-weight: normal;
        width: 80%;
        text-align: left;
    }

    .offcie_area select {
        width: 49% !important;
    }

    .offcie_box label {
        width: 44%;
    }

    #sum_table tr th,
    #sum_table tr td {
        padding: 5px;
    }
</style>



<div class="row">

    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Program Provider Report'); ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('ProgramProviderReports', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?></td>

                            <td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?></td>
                        </tr>

                        <tr>
                            <td width="50%">
                                <?php echo $this->Form->input('program_type_id', array('label' => 'Program :', 'id' => 'program_type_id', 'class' => 'form-control',  'options' => $programs)); ?>
                            </td>

                            <td width="50%">
                                <?php echo $this->Form->input('status', array('label' => 'Provider Status :', 'id' => 'status', 'class' => 'form-control',  'options' => $status, 'empty' => '---- All ----')); ?>
                            </td>
                        </tr>


                        <tr>
                            <td colspan="2">
                                <label style="width:15%;" for="program">Type :</label>
                                <div class="filter_type_box" style="width:80%; float:left; position:relative;">
                                    <?php echo $this->Form->input('filter_type', array('legend' => false, 'onclick' => 'filterSelect()', 'type' => 'radio', 'id' => 'filter_type', 'default' => '1',  'options' => $filter_types, 'separator' => '</div><div class="filter_type">', 'class' => 'form-control type')); ?>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" width="100%" class="offcie_area">
                                <div class="col-md-4 offcie_box">
                                    <div class="type_box_1 type_box">
                                        <?php echo $this->Form->input('office_id', array('label' => 'Office :', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
                                    </div>
                                    <div class="type_box_2 type_box" style="display:none;">
                                        <?php echo $this->Form->input('division_id', array('label' => 'Division :', 'id' => 'division_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="type_box_1 type_box">
                                        <?php echo $this->Form->input('territory_id', array('label' => 'Territory :', 'id' => 'territory_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
                                    </div>
                                    <div class="type_box_2 type_box" style="display:none;">
                                        <?php echo $this->Form->input('district_id', array('label' => 'District :', 'id' => 'district_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">

                                    <?php echo $this->Form->input('thana_id', array('label' => 'Thana :', 'id' => 'thana_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" width="100%" class="offcie_area">
                                <div class="col-md-4 offcie_box">
                                    <div class="type_box_1 type_box">
                                        <?php echo $this->Form->input('program_officer_id', array('id' => 'program_officer_id', 'class' => 'form-control', 'empty' => '---- All ----')); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>


                        <tr>
                            <td colspan="2">
                                <label style="width:15%;">Report Type :</label>
                                <div class="filter_type_box" style="width:80%; float:left; position:relative;">
                                    <?php echo $this->Form->input('report_type', array('legend' => false, 'type' => 'radio', 'id' => 'report_type', 'default' => '1',  'options' => $report_types, 'separator' => '</div><div class="filter_type">', 'class' => 'form-control report_type')); ?>
                                </div>
                            </td>
                        </tr>
                        <tr class='details_report'>
                            <td>
                                <?php echo $this->Form->input('outlet_coverage', array('legend' => 'Outlet Coverage', 'class' => 'outlet_coverage', 'type' => 'radio', 'options' => array('1' => 'Yes', '2' => 'No')));  ?>
                            </td>
                            <td width="50%"><?php echo $this->Form->input('product_category_id', array('id' => 'category_id', 'class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
                        </tr>

                        <tr class='details_report'>
                            <td><?php echo $this->Form->input('brand_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
                            <td><?php echo $this->Form->input('variant_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?></td>
                        </tr>

                        <tr class='details_report'>
                            <td><?php echo $this->Form->input('product_id', array('class' => 'form-control', 'required' => false, 'empty' => '---- Select ----', 'options' => $product_list)); ?></td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php if (!empty($request_data)) { ?>
                                    <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                                <?php } ?>

                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>

                    <script>
                        $(document).ready(function() {
                            $('input').iCheck('destroy');
                        });
                    </script>



                    <?php if (!empty($request_data)) { ?>
                        <div class="row">

                            <div id="content" style="width:90%; margin:0 5%;">
                                <style type="text/css">
                                    .table-responsive {
                                        color: #333;
                                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                        line-height: 1.42857;
                                    }

                                    .report_table {
                                        font-size: 13px;
                                    }

                                    .qty_val {
                                        width: 100px;
                                        margin: 0;
                                        float: left;
                                        text-transform: capitalize;
                                    }

                                    .qty,
                                    .val {
                                        width: 49%;
                                        float: left;
                                        border-right: #333 solid 1px;
                                        text-align: center;
                                        padding: 5px 0;
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
                                        padding: 0;
                                    }

                                    table {
                                        border-collapse: collapse;
                                        border-spacing: 0;
                                    }

                                    .titlerow,
                                    .totalColumn {
                                        background: #f1f1f1;
                                    }

                                    .rowDataSd,
                                    .totalCol {
                                        font-size: 85%;
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

                                    <?php /*?><div class="pull-right csv_btn" style="padding-top:20px;">
									<?=$this->Html->link(__('Download XLS'), array('action' => 'Download_xls'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                                </div><?php */ ?>

                                    <div class="pull-right csv_btn" style="padding-top:20px;">
                                        <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                                    </div>

                                    <div id="xls_body">
                                        <div style="width:100%; text-align:center; padding:20px 0;">
                                            <h2 style="margin:10px 0;">SMC Enterprise Limited</h2>
                                            <h3 style="margin:10px 0; font-size:18px;">Program Provider Report of SMC Contraceptive, ORS and Orther Helth Products</h3>
                                            <p>
                                                Time Frame : <b><?= $date_from ? date('d M, Y', strtotime($date_from)) : '' ?></b> to <b><?= $date_to ? date('d M, Y', strtotime($date_to)) : '' ?></b> <?php /*?><br>Reporting Date: <b><?php echo date('d F, Y')?></b><?php */ ?>
                                            </p>

                                        </div>

                                        <?php if ($request_data['ProgramProviderReports']['report_type'] == 1) { ?>

                                            <h5>Detail Report</h5>
                                            <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="margin-bottom:-1px;">
                                                    <tr class="titlerow">
                                                        <th style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Sales Office' : 'Division' ?></th>
                                                        <th style="text-align:left;">POF Name</th>
                                                        <th style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Territory' : 'District' ?></th>
                                                        <th style="text-align:left;">Thana</th>
                                                        <th style="text-align:left;">Market Name</th>
                                                        <th style="text-align:left;">Outlet Name</th>
                                                        <th>Code</th>
                                                        <th>Enrolled Date</th>
                                                        <th>Drop Date</th>
                                                        <th>Drop Reason</th>
                                                    </tr>


                                                    <?php if ($results) { ?>

                                                        <?php foreach ($results as $result) { ?>
                                                            <tr>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ?
                                                                                                    str_replace('Sales Office', '', $result['Office']['office_name']) : $result['Division']['name']; ?></td>
                                                                <td style="text-align:left;"><?= $result['ProgramOffice']['name'] ?></td>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? $result['Territory']['name'] . ' (' . $result['SalesPeople']['name'] . ')' : $result['District']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Thana']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Market']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Outlet']['name'] ?></td>
                                                                <td><?= $result['Program']['code'] ?></td>
                                                                <td><?= date('d-m-Y', strtotime($result['Program']['assigned_date'])) ?></td>
                                                                <td><?= $result['Program']['deassigned_date'] ? date('d-m-Y', strtotime($result['Program']['deassigned_date'])) : '' ?></td>
                                                                <td><?= $result['Program']['reason'] ?></td>
                                                            </tr>
                                                        <?php } ?>

                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="8" style="text-align:center;">
                                                                <h4>No Result Found!</h4>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>


                                                </table>
                                            </div>
                                        <?php } else if ($request_data['ProgramProviderReports']['report_type'] == 3) { ?>
                                            <h5>Detail Report</h5>
                                            <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="margin-bottom:-1px;">

                                                    <tr class="titlerow">
                                                        <th style="text-align:left;" rowspan="2"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Sales Office' : 'Division' ?></th>
                                                        <th style="text-align:left;" rowspan="2">POF Name</th>
                                                        <th style="text-align:left;" rowspan="2"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Territory' : 'District' ?></th>
                                                        <th style="text-align:left;" rowspan="2">Thana</th>
                                                        <th style="text-align:left;" rowspan="2">Market Name</th>
                                                        <th style="text-align:left;" rowspan="2">Outlet Name</th>
                                                        <th rowspan="2">Code</th>
                                                        <th rowspan="2">Enrolled Date</th>
                                                        <?php foreach ($product_columns as $product_name) { ?>
                                                            <th colspan="2"><?= $product_name ?></th>
                                                        <?php } ?>
                                                        <th rowspan="2">Total SMC Revenue</th>
                                                    </tr>

                                                    <tr class="titlerow">

                                                        <?php foreach ($product_columns as $product_name) { ?>
                                                            <th>Qty</th>
                                                            <th>Val</th>
                                                        <?php } ?>
                                                    </tr>


                                                    <?php if ($results) { 
													$outlet_id_check = array();
													
													?>
														
                                                        <?php foreach ($results as $result) {
																if(in_array($result['Outlet']['id'], $outlet_id_check))
																	continue;
																$outlet_id_check[$result['Outlet']['id']] = $result['Outlet']['id'];
																
															
                                                            $total_smc_revenue[$result['Outlet']['id']] = 0;
                                                            if (@$this->request->data['ProgramProviderReports']['outlet_coverage'] == 1) {
                                                                if (count(@$final_results[$result['Outlet']['id']]) <= 0) {
                                                                    continue;
                                                                }
                                                            } elseif (@$this->request->data['ProgramProviderReports']['outlet_coverage'] == 2) {
                                                                if (count(@$final_results[$result['Outlet']['id']]) > 0) {
                                                                    continue;
                                                                }
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ?
                                                                                                    str_replace('Sales Office', '', $result['Office']['office_name']) : $result['Division']['name']; ?></td>
                                                                <td style="text-align:left;"><?= $result['ProgramOffice']['name'] ?></td>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? $result['Territory']['name'] . ' (' . $result['SalesPeople']['name'] . ')' : $result['District']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Thana']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Market']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Outlet']['name'] ?></td>
                                                                <td><?= $result['Program']['code'] ?></td>
                                                                <td><?= date('d-m-Y', strtotime($result['Program']['assigned_date'])) ?></td>
                                                                <?php foreach ($product_columns as $product_id => $product_name) {
                                                                    $total_smc_revenue[@$result['Outlet']['id']] += @$final_results[$result['Outlet']['id']][$product_id]['value'];
                                                                ?>
                                                                    <td><?= sprintf("%01.2f", @$final_results[$result['Outlet']['id']][$product_id]['qty']); ?></td>
                                                                    <td><?= sprintf("%01.2f", @$final_results[$result['Outlet']['id']][$product_id]['value']); ?></td>
                                                                <?php } ?>
                                                                <td><?= sprintf("%01.2f", $total_smc_revenue[$result['Outlet']['id']]) ?></td>

                                                            </tr>
                                                        <?php } ?>

                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="8" style="text-align:center;">
                                                                <h4>No Result Found!</h4>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>


                                                </table>
                                            </div>
                                        <?php } else { ?>

                                            <h5>Summary Report</h5>
                                            <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center" style="margin-bottom:-1px;">

                                                    <tr class="titlerow">
                                                        <th style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Sales Office' : 'Division' ?></th>
                                                        <th>POF Name</th>
                                                        <th style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Territory' : 'District' ?></th>
                                                        <th style="text-align:left;">Thana</th>
                                                        <th>Active Provider</th>
                                                        <th>Visited Provider (OC)</th>
                                                        <th>Total Visit (EC)</th>
                                                        <th>Total Revenue</th>
                                                        <th>CYP</th>
                                                    </tr>

                                                    <?php if ($results) { ?>
                                                        <?php foreach ($results as $result) { ?>
                                                            <tr>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ?
                                                                                                    str_replace('Sales Office', '', $result['Office']['office_name']) : $result['Division']['name']; ?></td>
                                                                <td style="text-align:left;"><?= $result['ProgramOffice']['name'] ?></td>
                                                                <td style="text-align:left;"><?= ($request_data['ProgramProviderReports']['filter_type'] == 1) ? $result['Territory']['name'] . ' (' . $result['SalesPeople']['name'] . ')' : $result['District']['name'] ?></td>
                                                                <td style="text-align:left;"><?= $result['Thana']['name'] ?></td>
                                                                <td><?= $result[0]['active'] ?></td>

                                                                <td><?= @$final_results[$result['Territory']['id']][$result['Thana']['id']]['oc'] ? $final_results[$result['Territory']['id']][$result['Thana']['id']]['oc'] : 0 ?></td>
                                                                <td><?= @$final_results[$result['Territory']['id']][$result['Thana']['id']]['ec'] ? $final_results[$result['Territory']['id']][$result['Thana']['id']]['ec'] : 0 ?></td>
                                                                <td><?= @$final_results[$result['Territory']['id']][$result['Thana']['id']]['value'] ? sprintf("%01.2f", $final_results[$result['Territory']['id']][$result['Thana']['id']]['value']) : 0 ?></td>
                                                                <td><?= @$final_results[$result['Territory']['id']][$result['Thana']['id']]['cyp'] ? $final_results[$result['Territory']['id']][$result['Thana']['id']]['cyp'] : 0 ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="8" style="text-align:center;">
                                                                <h4>No Result Found!</h4>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>


                                                </table>
                                            </div>

                                        <?php } ?>



                                        <?php /*?><div style="float:left; width:100%; padding:100px 0 50px;">
                                        <div class="bottom_box">
                                            Prepared by:______________ 
                                        </div>
                                        <div class="bottom_box">
                                            Checked by:______________ 
                                        </div>
                                        <div class="bottom_box">
                                            Signed by:______________
                                        </div>		  
                                    </div><?php */ ?>

                                    </div>

                                </div>







                            </div>

                        </div>
                    <?php } ?>

                </div>
            </div>

        </div>
    </div>

</div>




<script>
    $('#office_id').selectChain({
        target: $('#territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    });


    $('#category_id').selectChain({
        target: $('#ProgramProviderReportsBrandId'),
        value: 'name',
        url: '<?= BASE_URL . 'program_provider_reports/get_product_brands' ?>',
        type: 'post',
        afterSuccess: function() {
            $('#ProgramProviderReportsVariantId').html('<option value="">--- Select --- </option>');
            $('#ProgramProviderReportsProductId').html('<option value="">--- Select --- </option>');
            $("div#divLoading_default").removeClass('show');
        },
        beforeSend: function() {
            $("div#divLoading_default").addClass('show');
        },
        data: {
            'category_id': 'category_id'
        }
    });

    $('#ProgramProviderReportsBrandId').selectChain({
        target: $('#ProgramProviderReportsVariantId'),
        value: 'name',
        url: '<?= BASE_URL . 'program_provider_reports/get_product_variant' ?>',
        type: 'post',
        afterSuccess: function() {
            $('#ProgramProviderReportsProductId').html('<option value="">--- Select --- </option>');
            $("div#divLoading_default").removeClass('show');
        },
        beforeSend: function() {
            $("div#divLoading_default").addClass('show');
        },
        data: {
            'category_id': 'category_id',
            'brand_id': 'ProgramProviderReportsBrandId'
        }
    });


    $('#ProgramProviderReportsVariantId').selectChain({
        target: $('#ProgramProviderReportsProductId'),
        value: 'name',
        url: '<?= BASE_URL . 'program_provider_reports/get_product_list_by_variant' ?>',
        type: 'post',
        beforeSend: function() {
            $("div#divLoading_default").addClass('show');
        },
        afterSuccess: function() {
            $("div#divLoading_default").removeClass('show');
        },
        data: {
            'category_id': 'category_id',
            'brand_id': 'ProgramProviderReportsBrandId',
            'variant_id': 'ProgramProviderReportsVariantId'
        }
    });

    $('#territory_id').selectChain({
        target: $('#thana_id'),
        value: 'name',
        url: '<?= BASE_URL . 'programs/get_thana_list' ?>',
        type: 'post',
        data: {
            'territory_id': 'territory_id',
            'location_type_id': ''
        }
    });

    $('#division_id').selectChain({
        target: $('#district_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_analysis_reports/get_district_list' ?>',
        type: 'post',
        data: {
            'division_id': 'division_id'
        }
    });

    $('#district_id').selectChain({
        target: $('#thana_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_analysis_reports/get_thana_list' ?>',
        type: 'post',
        data: {
            'district_id': 'district_id'
        }
    });


    $('#office_id').change(function() {
        $('#territory_id').html('<option value="">---- All -----</option>');
    });
    $('#office_id').change(function() {
        $('#thana_id').html('<option value="">---- All -----</option>');
    });
    $('#division_id').change(function() {
        $('#district_id').html('<option value="">---- All -----</option>');
    });
    $('#division_id').change(function() {
        $('#thana_id').html('<option value="">---- All -----</option>');
    });

    $('#program_type_id').change(function() {
        $('#program_officer_id').html('<option value="">---- Select -----</option>');
    });

    $('#office_id').change(function() {

        var office_id = $(this).val(); 
        var program_type_id = $("#program_type_id").val(); 
         
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL . 'program_provider_reports/get_program_officer_list' ?>',
            data: {'office_id':office_id,'program_type_id':program_type_id},
            cache: false,
            success: function (response) {
                $("#program_officer_id").html(response);
            }
        });
       
    });


    function filterSelect() {
        var type = $('.type:checked').val();


        $('.type_box').hide();

        $('.type_box_' + type).show();
        //$('.offcie_area select:visible option:nth-child(2)').prop("selected", true).change();

        $('#program_officer_id').html('<option value="">---- Select -----</option>');

    }
    $("body").on("submit", "#ProgramProviderReportsAdminIndexForm", function() {
        let report_type = $(".report_type:checked").val();
        let outlet_coverage = $(".outlet_coverage:checked").val();
        if (report_type == 3 && outlet_coverage != undefined) {
            console.log('jere');
            if ($("#category_id").val() || $("#ProgramProviderReportsBrandId").val() || $("#ProgramProviderReportsVariantId").val() || $("#ProgramProviderReportsProductId").val()) {
                return true;
            } else {
                $("div#divLoading_default").removeClass('show');
                alert("Please Category,Brand,Variant Or Product");
                return false;
            }
        }
    });
    $('body').on('change', '.report_type', function() {
        if ($(".report_type:checked").val() == 3) {
            $('.details_report').show();
        } else {
            $('.details_report').hide();
        }
    });
    $('.report_type').trigger('change');
</script>

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=600,width=960');

        mywindow.document.write('<html><head><title></title><?php echo $this->Html->css('bootstrap.min.css');
                                                            echo $this->fetch('css'); ?>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>


<script>
    $("#download_xl").click(function(e) {
        e.preventDefault();
        var html = $("#xls_body").html();

        var blob = new Blob([html], {
            type: 'data:application/vnd.ms-excel'
        });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "program_provider_report.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>