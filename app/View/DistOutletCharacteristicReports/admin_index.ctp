<?php
App::import('Controller', 'DistOutletCharacteristicReportsController');
$OutletCharacteristicController = new DistOutletCharacteristicReportsController;
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

    #route_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #route_list .checkbox {
        width: 33.33%;
        float: left;
        margin: 1px 0;
    }

    #route_list .checkbox label {
        text-align: left;
        float: none;
        margin: 0;
        padding-left: 2px;
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
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('DistOutletCharacteristicReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('report_type', array('legend' => 'Report Type :', 'class' => 'report_type', 'type' => 'radio', 'default' => 'visited', 'options' => $report_types, 'required' => true));  ?></td>
                        </tr>

                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- Select ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <tr>
                            <td>
                                <?php echo $this->Form->input('db_id', array('label' => 'Distributor :', 'id' => 'db_id', 'class' => 'form-control db_id', 'empty' => '--- All ---')); ?>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
                            </td>
                        </tr>


                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:12.5%;">Outlet Category : </label>
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
                            <td colspan="2">
                                <label style="float:left; width:12.5%;">Routes : </label>
                                <div id="route_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                        <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection2 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($markets) ? 'display:block' : '' ?>">
                                        <?php echo $this->Form->input('route_id', array('id' => 'route_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:12.5%;">Markets : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
                                        <label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection3 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($markets) ? 'display:block' : '' ?>">
                                        <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $markets)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:12.5%;">Outlets : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall4" />
                                        <label for="checkall4" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection4 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($outlets) ? 'display:block' : '' ?>">
                                        <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $outlets)); ?>
                                    </div>
                                </div>
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


                                    <h3 style="margin:2px 0;"><?= $report_types[$report_type] ?></h3>

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
                                            <span>, Distributor- Name: <span id="header_db_name"></span></span>
                                        <?php } ?>
                                    </p>

                                    <?php if ($report_type == 'detail' || $report_type == 'summary') { ?>
                                        <p><b>Measuring Unit: <?= $unit_type_text ?></b></p>
                                    <?php } ?>

                                </div>

                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <?php if ($report_type == 'visited') { ?>
                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <tbody>
                                                <tr class="titlerow">
                                                    <th style="text-align:left;">Outlet</th>
                                                    <th style="text-align:left;">Outlet Type</th>
                                                    <th>No of Visited Day</th>
                                                    <th width="30%">Visit Date</th>
                                                    <th style="text-align:right;">Memo Total</th>
                                                    <th style="text-align:left;">Visited By</th>
                                                </tr>


                                                <?php if ($results) { ?>

                                                    <?= $output ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="6">No data found!</td>
                                                    </tr>
                                                <?php } ?>




                                            </tbody>
                                        </table>
                                    <?php } ?>


                                    <?php if ($report_type == 'non_visited') { ?>
                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <tbody>
                                                <tr class="titlerow">
                                                    <th style="text-align:left;">Market</th>
                                                    <th style="text-align:left;">Outlet Name</th>
                                                    <th style="text-align:left;">Outlet Type</th>
                                                </tr>

                                                <?php if ($results) { ?>
                                                    <?= $output ?>
                                                <?php } else { ?>

                                                    <tr>
                                                        <td colspan="3">No Data Found!</td>
                                                    </tr>

                                                <?php } ?>


                                            </tbody>
                                        </table>
                                    <?php } ?>


                                    <?php if ($report_type == 'detail') { ?>
                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <tbody>
                                                <tr class="titlerow">
                                                    <th style="text-align:left;">Outlet</th>
                                                    <th>Memo No</th>
                                                    <th width="10%">Date</th>
                                                    <th style="text-align:left;">Product</th>
                                                    <th style="text-align:right;">Qty</th>
                                                    <th style="text-align:right;">Total Value</th>
                                                    <th style="text-align:left;">Sales Officer</th>
                                                </tr>


                                                <?php if ($results) { ?>

                                                    <?= $output ?>



                                                <?php } else { ?>

                                                    <tr>
                                                        <td colspan="7"><b>No Data Found!</b></td>
                                                    </tr>

                                                <?php } ?>


                                            </tbody>
                                        </table>
                                    <?php } ?>


                                    <?php if ($report_type == 'summary') { ?>
                                        <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                            <tbody>
                                                <tr class="titlerow">
                                                    <th style="text-align:left;">Outlet</th>

                                                    <?php foreach ($product_list as $pro_name) { ?>
                                                        <th style="text-align:left;"><?= $pro_name ?></th>
                                                    <?php } ?>
                                                </tr>

                                                <?php if ($results) { ?>



                                                    <?= $output; ?>

                                                <?php } else { ?>

                                                    <tr>
                                                        <td colspan="<?= count($product_list) ?>"><b>No Data Found!</b></td>
                                                    </tr>

                                                <?php } ?>


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
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('#checkall').click(function() {
            var checked = $(this).prop('checked');
            $('.selection').find('input:checkbox').prop('checked', checked);

        });

        $('#checkall1').click(function() {
            var checked = $(this).prop('checked');
            $('.selection1').find('input:checkbox').prop('checked', checked);
            thanaBoxList();
        });

        $('#checkall2').click(function() {
            var checked = $(this).prop('checked');
            $('.selection2').find('input:checkbox').prop('checked', checked);
            marketBoxList();
        });

        $('#checkall3').click(function() {
            var checked = $(this).prop('checked');
            $('.selection3').find('input:checkbox').prop('checked', checked);
            outletBoxList();
        });

        $('#checkall4').click(function() {
            var checked = $(this).prop('checked');
            $('.selection4').find('input:checkbox').prop('checked', checked);
        });

    });

    if ($('#office_id').val() && $('.date_from').val() && $('.date_to').val()) {
        get_db_list();
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

    function get_db_list() {
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>dist_outlet_characteristic_reports/get_db_list',
            data: 'office_id=' + $('#office_id').val() + '&date_from=' + date_from + '&date_to=' + date_to,
            cache: false,
            success: function(response) {
                //alert(response);                      
                $('#db_id').html(response);
                <?php if (isset($this->request->data['DistOutletCharacteristicReports']['db_id'])) { ?>
                    if ($('#db_id').val(<?= $this->request->data['DistOutletCharacteristicReports']['db_id'] ?>)) {
                        routelistBox("req");
                        var db_name = $('#db_id option:selected').text();
                        $("#header_db_name").text(db_name);
                    }
                <?php } ?>
            }
        });
    }
    $(document).ready(function() {
        $('[name="data[DistOutletCharacteristicReports][db_id]"]').change(function() {

            routelistBox();
        });
    });

    function routelistBox(from = "change") {
        var val = $('[name="data[DistOutletCharacteristicReports][db_id]"]').val();

        //alert(val);
        $('.selection2').hide();
        $('.selection3').hide();
        $('.selection3').html('');
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        if (date_from && date_to) {
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>dist_outlet_characteristic_reports/get_route_list',
                data: 'db_id=' + val + '&date_from=' + date_from + '&date_to=' + date_to,
                beforeSend: function() {
                    $("div#divLoading").addClass('show');
                },
                cache: false,
                success: function(response) {
                    if (response != '') {
                        $('.selection2').show();
                    }
                    $('.selection2').html(response);
                    $("div#divLoading").removeClass('show');

                    <?php if (isset($this->request->data['DistOutletCharacteristicReports']['route_id'])) { ?>
                        if (from == "req") {
                            checked_route = <?= json_encode($this->request->data['DistOutletCharacteristicReports']['route_id']) ?>;
                            $.each(checked_route, function(i, e) {
                                $('[name="data[DistOutletCharacteristicReports][route_id][]"][value=' + e + ']').attr("checked", true);
                            });
                            marketBoxList("req");
                        }
                    <?php } ?>
                }
            });
        } else {
            alert('Please select date range!');
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('[name="data[DistOutletCharacteristicReports][route_id][]"]').change(function() {
            marketBoxList();
        });
    });

    function marketBoxList(from = "change") {
        var val = [];
        $('[name="data[DistOutletCharacteristicReports][route_id][]"]:checked').each(function(i) {
            val[i] = $(this).val();
        });

        $('.selection3').hide();


        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL; ?>dist_outlet_characteristic_reports/get_market_list',
            data: 'route_id=' + val + '&db_id=' + $('#db_id').val(),
            beforeSend: function() {
                $("div#divLoading").addClass('show');
            },
            cache: false,
            success: function(response) {
                //alert(response);  
                if (response != '') {
                    $('.selection3').show();
                }
                $('.selection3').html(response);
                $("div#divLoading").removeClass('show');

                <?php if (isset($this->request->data['DistOutletCharacteristicReports']['market_id'])) { ?>
                    if (from == "req") {
                        checked_market = <?= json_encode($this->request->data['DistOutletCharacteristicReports']['market_id']) ?>;
                        $.each(checked_market, function(i, e) {
                            $('[name="data[DistOutletCharacteristicReports][market_id][]"][value=' + e + ']').attr("checked", true);
                        });
                        outletBoxList("req");
                    }
                <?php } ?>
            }
        });
    }
</script>


<script>
    $(document).ready(function() {
        $('[name="data[DistOutletCharacteristicReports][market_id][]"]').change(function() {
            //alert($(this).val()); // alert value
            //$('.selection').find('input:checkbox').prop('checked', checked);
            outletBoxList();
        });
    });

    function outletBoxList(from = "change") {
        var val = [];
        $('[name="data[DistOutletCharacteristicReports][market_id][]"]:checked').each(function(i) {
            val[i] = $(this).val();
        });

        $('.selection4').hide();

        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL; ?>dist_outlet_characteristic_reports/get_outlet_list',
            data: 'market_id=' + val,
            beforeSend: function() {
                $("div#divLoading").addClass('show');
            },
            cache: false,
            success: function(response) {
                //alert(response);	
                if (response != '') {
                    $('.selection4').show();
                }
                $('.selection4').html(response);
                $("div#divLoading").removeClass('show');
                <?php if (isset($this->request->data['DistOutletCharacteristicReports']['outlet_id'])) { ?>
                    if (from == "req") {
                        checked_outlet = <?= json_encode($this->request->data['DistOutletCharacteristicReports']['outlet_id']) ?>;
                        $.each(checked_outlet, function(i, e) {
                            $('[name="data[DistOutletCharacteristicReports][outlet_id][]"][value=' + e + ']').attr("checked", true);
                        });
                    }
                <?php } ?>
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

    $(document).ready(function() {

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            //console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "outlet_characteristic_reports.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>