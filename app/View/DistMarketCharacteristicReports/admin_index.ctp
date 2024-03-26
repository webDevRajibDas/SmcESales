<?php
App::import('Controller', 'DistMarketCharacteristicReportsController');
$OutletCharacteristicController = new DistMarketCharacteristicReportsController;
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

    #route_list {
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
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New OutletCharacteristic Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('DistMarketCharacteristicReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('report_type', array('legend' => 'Report Type :', 'class' => 'report_type', 'type' => 'radio', 'default' => 'visit_info', 'options' => $report_types, 'required' => true));  ?></td>
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
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- Please Select ----')); ?></td>
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
                                            <span>, Distributor Name: <span id="header_db_name"></span></span>
                                        <?php } ?>
                                    </p>

                                </div>

                                <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                    <?php //if($report_type=='visited'){ 
                                    ?>
                                    <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                        <tbody>
                                            <tr class="titlerow">
                                                <th style="text-align:left;">Market</th>
                                                <th>No of<br> Visited Day</th>
                                                <th width="30%">Visit Date</th>
                                                <th style="text-align:right;">Visited Outlet</th>
                                                <th style="text-align:right;">No Of Memo</th>
                                                <th style="text-align:right;">Memo Total</th>
                                                <th style="text-align:left;">Visited By</th>
                                            </tr>


                                            <?php if ($results) { ?>

                                                <?php
                                                foreach ($results as $route_name => $market_datas) {
                                                ?>
                                                    <tr>
                                                        <td style="text-align:left;" colspan="7"><b>Route :- <?= $route_name ?> </b></td>
                                                    </tr>

                                                    <?php
                                                    $thana_visited_market = 0;
                                                    $thana_visited_outlet = 0;
                                                    $thana_total_outlet = 0;
                                                    $thana_no_memo_total = 0;
                                                    $thana_memo_total = 0;
                                                    foreach ($market_datas as $market_name => $outlet_datas) {
                                                        $memo_dates = '';
                                                        $memo_total = 0;
                                                        $so_name = '';
                                                        $m_date_temp = array();
                                                        $no_of_memo = 0;

                                                        if ($report_type == 'non_visited') {
                                                            if (!$outlet_datas[0]) {
                                                                $outlet_datas = array();
                                                            }
                                                        } elseif ($report_type == 'visit_info') {
                                                            $outlet_datas = @$results1[$route_name][$market_name] ? $results1[$route_name][$market_name] : array();
                                                        }

                                                        foreach ($outlet_datas as $m_results) {
                                                            $m_total = count($m_results);

                                                            foreach ($m_results as $m_result) {
                                                                if (!in_array($m_result['memo_date'], $m_date_temp)) {
                                                                    $memo_dates .= date('m-d-Y', strtotime($m_result['memo_date'])) . ', ';
                                                                    array_push($m_date_temp, $m_result['memo_date']);
                                                                    //$m_date_temp = $m_result['memo_date'];
                                                                }

                                                                @$so_name =  $m_result['so_name'];
                                                                $memo_total += $m_result['memo_total'];
                                                                $no_of_memo++;
                                                            }
                                                        }
                                                    ?>

                                                        <tr>
                                                            <td style="text-align:left;"><?= $market_name ?></td>
                                                            <td><?= $memo_dates ? count(explode(', ', rtrim($memo_dates, ', '))) : '' ?></td>
                                                            <td><?= rtrim($memo_dates, ', ') ?></td>
                                                            <td style="text-align:right;"><?= @count($outlet_datas) ?> of <?= @$total_outlets_market_wise[($market_name)] ? $total_outlets_market_wise[($market_name)] : 0 ?></td>
                                                            <td style="text-align:right;"><?= $no_of_memo ? $no_of_memo : '' ?></td>
                                                            <td style="text-align:right;"><?= $memo_total ? sprintf("%01.2f", $memo_total) : '' ?></td>
                                                            <td style="text-align:left;"><?= $so_name ?></td>
                                                        </tr>

                                                    <?php
                                                        if ($memo_dates) $thana_visited_market++;
                                                        if ($memo_dates) $thana_visited_outlet += count($outlet_datas);
                                                        @$thana_total_outlet += $total_outlets_market_wise[($market_name)] ? $total_outlets_market_wise[($market_name)] : 0;
                                                        $thana_no_memo_total += $no_of_memo;
                                                        $thana_memo_total += $memo_total;
                                                    }
                                                    ?>


                                                    <tr style="font-weight:bold; background:#eee;">
                                                        <td colspan="3" style="text-align:right;">Total :</td>
                                                        <td style="text-align:right;"><?= $thana_visited_outlet ?> of <?= $thana_total_outlet ?></td>
                                                        <td style="text-align:right;"><?= $thana_no_memo_total; ?></td>
                                                        <td style="text-align:right;">
                                                            <?= sprintf("%01.2f", $thana_memo_total) ?>
                                                        </td>
                                                        <td></td>
                                                        </td>
                                                    </tr>

                                                    <?php if ($report_type != 'non_visited') { ?>
                                                        <tr style="background:#ccc;">
                                                            <td style="text-align:center;" colspan="7">
                                                                <b>
                                                                    Total Market : <?= $total_markets_thana_wise[($route_name)] ?>,
                                                                    Visited Market : <?= $thana_visited_market ?>,
                                                                    Non Visited Market : <?= $total_markets_thana_wise[($route_name)] - $thana_visited_market ?>
                                                                </b>
                                                            </td>
                                                        </tr>
                                                    <?php } else { ?>
                                                        <tr style="background:#ccc;">
                                                            <td style="text-align:center;" colspan="7">
                                                                <b>
                                                                    Total Market : <?= $total_markets_thana_wise[($route_name)] ?>,
                                                                    Visited Market : <?= $total_markets_thana_wise[($route_name)] - count($market_datas) ?>,
                                                                    Non Visited Market : <?= count($market_datas) ?>
                                                                </b>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                <?php } ?>

                                                <?php /*?><?=$output?><?php */ ?>

                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="7">No data found!</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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
        url: '<?= BASE_URL . 'dist_market_characteristic_reports/get_office_list'; ?>',
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



        $('#checkall2').click(function() {
            var checked = $(this).prop('checked');
            $('.selection2').find('input:checkbox').prop('checked', checked);
            marketBoxList();
        });

        $('#checkall3').click(function() {
            var checked = $(this).prop('checked');
            $('.selection3').find('input:checkbox').prop('checked', checked);
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
            url: '<?= BASE_URL ?>dist_market_characteristic_reports/get_db_list',
            data: 'office_id=' + $('#office_id').val() + '&date_from=' + date_from + '&date_to=' + date_to,
            cache: false,
            success: function(response) {
                //alert(response);                      
                $('#db_id').html(response);
                <?php if (isset($this->request->data['DistMarketCharacteristicReports']['db_id'])) { ?>
                    if ($('#db_id').val(<?= $this->request->data['DistMarketCharacteristicReports']['db_id'] ?>)) {
                        routelistBox("req");
                        var db_name = $('#db_id option:selected').text();
                        $("#header_db_name").text(db_name);
                    }
                <?php } ?>
            }
        });
    }
    $(document).ready(function() {
        $('[name="data[DistMarketCharacteristicReports][db_id]"]').change(function() {

            routelistBox();
        });
    });

    function routelistBox(from = "change") {
        var val = $('[name="data[DistMarketCharacteristicReports][db_id]"]').val();

        //alert(val);
        $('.selection2').hide();
        $('.selection3').hide();
        $('.selection3').html('');
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        if (date_from && date_to) {
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>dist_market_characteristic_reports/get_route_list',
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

                    <?php if (isset($this->request->data['DistMarketCharacteristicReports']['route_id'])) { ?>
                        if (from == "req") {
                            checked_route = <?= json_encode($this->request->data['DistMarketCharacteristicReports']['route_id']) ?>;
                            $.each(checked_route, function(i, e) {
                                $('[name="data[DistMarketCharacteristicReports][route_id][]"][value=' + e + ']').attr("checked", true);
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
        $('[name="data[DistMarketCharacteristicReports][route_id][]"]').change(function() {
            marketBoxList();
        });
    });

    function marketBoxList(from = "change") {
        var val = [];
        $('[name="data[DistMarketCharacteristicReports][route_id][]"]:checked').each(function(i) {
            val[i] = $(this).val();
        });

        $('.selection3').hide();


        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL; ?>dist_market_characteristic_reports/get_market_list',
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

                <?php if (isset($this->request->data['DistMarketCharacteristicReports']['market_id'])) { ?>
                    if (from == "req") {
                        checked_market = <?= json_encode($this->request->data['DistMarketCharacteristicReports']['market_id']) ?>;
                        $.each(checked_market, function(i, e) {
                            $('[name="data[DistMarketCharacteristicReports][market_id][]"][value=' + e + ']').attr("checked", true);
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

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "market_characteristic_reports.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>