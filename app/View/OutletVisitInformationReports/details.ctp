<?php //pr($offices);die();
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


                                <h3 style="margin:2px 0;">Outlet Visit Information Details</h3>

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
                                        <span>, Area Office: <?= $all_offices[$office_id] ?></span>
                                    <?php } ?>
                                    <?php if ($territory_id) { ?>
                                        <span>, Territory Name: <?= $territories[$territory_id] ?></span>
                                    <?php } ?>
                                </p>

                            </div>


                            <div style="float:left; width:100%; height:430px; overflow:scroll;">

                                <table cellspacing="0" cellpadding="10px" border="1px solid black" align="center" class="text-center report_table" id="sum_table">
                                    <thead>
                                        <tr class="titlerow">
                                            <th>Sales Officer</th>
                                            <th>Sales Area</th>
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Market</th>
                                            <th>EC-1</th>
                                            <th>EC-2</th>
                                            <th>EC-3</th>
                                            <th>EC-4</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if ($results1 || $results2 || $results3 || $results4) { ?>
                                            <?php foreach ($results1 as $district_name => $thana_datas) { ?>
                                                <?php foreach ($thana_datas as $thana_name => $market_datas) { ?>
                                                    <?php foreach ($market_datas as $market_name => $outlet_datas) { ?>
                                                        <?php foreach ($outlet_datas as $outlet_id => $datas) { ?>
                                                            <tr>
                                                                <td><?= $datas['so_name'] ?></td>
                                                                <td><?= $all_offices[$datas['office_id']] ?></td>
                                                                <td><?= $district_name ?></td>
                                                                <td><?= $thana_name ?></td>
                                                                <td><?= $market_name ?></td>

                                                                <?php
                                                                $outlet_name1 = '';
                                                                $outlet_name2 = '';
                                                                $outlet_name3 = '';
                                                                $outlet_name4 = '';

                                                                if ($datas['ec'] == 1) {
                                                                    $outlet_name1 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 2) {
                                                                    $outlet_name2 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 3) {
                                                                    $outlet_name3 = $datas['outlet_name'];
                                                                } else {
                                                                    $outlet_name4 = $datas['outlet_name'];
                                                                }

                                                                ?>
                                                                <td><?= $outlet_name1; ?></td>
                                                                <td><?= $outlet_name2; ?></td>
                                                                <td><?= $outlet_name3; ?></td>
                                                                <td><?= $outlet_name4; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php foreach ($results2 as $district_name => $thana_datas) { ?>
                                                <?php foreach ($thana_datas as $thana_name => $market_datas) { ?>
                                                    <?php foreach ($market_datas as $market_name => $outlet_datas) { ?>
                                                        <?php foreach ($outlet_datas as $outlet_id => $datas) { ?>
                                                            <tr>
                                                                <td><?= $datas['so_name'] ?></td>
                                                                <td><?= $all_offices[$datas['office_id']] ?></td>
                                                                <td><?= $district_name ?></td>
                                                                <td><?= $thana_name ?></td>
                                                                <td><?= $market_name ?></td>

                                                                <?php
                                                                $outlet_name1 = '';
                                                                $outlet_name2 = '';
                                                                $outlet_name3 = '';
                                                                $outlet_name4 = '';

                                                                if ($datas['ec'] == 1) {
                                                                    $outlet_name1 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 2) {
                                                                    $outlet_name2 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 3) {
                                                                    $outlet_name3 = $datas['outlet_name'];
                                                                } else {
                                                                    $outlet_name4 = $datas['outlet_name'];
                                                                }

                                                                ?>
                                                                <td><?= $outlet_name1; ?></td>
                                                                <td><?= $outlet_name2; ?></td>
                                                                <td><?= $outlet_name3; ?></td>
                                                                <td><?= $outlet_name4; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php foreach ($results3 as $district_name => $thana_datas) { ?>
                                                <?php foreach ($thana_datas as $thana_name => $market_datas) { ?>
                                                    <?php foreach ($market_datas as $market_name => $outlet_datas) { ?>
                                                        <?php foreach ($outlet_datas as $outlet_id => $datas) { ?>
                                                            <tr>
                                                                <td><?= $datas['so_name'] ?></td>
                                                                <td><?= $all_offices[$datas['office_id']] ?></td>
                                                                <td><?= $district_name ?></td>
                                                                <td><?= $thana_name ?></td>
                                                                <td><?= $market_name ?></td>

                                                                <?php
                                                                $outlet_name1 = '';
                                                                $outlet_name2 = '';
                                                                $outlet_name3 = '';
                                                                $outlet_name4 = '';

                                                                if ($datas['ec'] == 1) {
                                                                    $outlet_name1 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 2) {
                                                                    $outlet_name2 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 3) {
                                                                    $outlet_name3 = $datas['outlet_name'];
                                                                } else {
                                                                    $outlet_name4 = $datas['outlet_name'];
                                                                }

                                                                ?>
                                                                <td><?= $outlet_name1; ?></td>
                                                                <td><?= $outlet_name2; ?></td>
                                                                <td><?= $outlet_name3; ?></td>
                                                                <td><?= $outlet_name4; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php foreach ($results4 as $district_name => $thana_datas) { ?>
                                                <?php foreach ($thana_datas as $thana_name => $market_datas) { ?>
                                                    <?php foreach ($market_datas as $market_name => $outlet_datas) { ?>
                                                        <?php foreach ($outlet_datas as $outlet_id => $datas) { ?>
                                                            <tr>
                                                                <td><?= $datas['so_name'] ?></td>
                                                                <td><?= $all_offices[$datas['office_id']] ?></td>
                                                                <td><?= $district_name ?></td>
                                                                <td><?= $thana_name ?></td>
                                                                <td><?= $market_name ?></td>

                                                                <?php
                                                                $outlet_name1 = '';
                                                                $outlet_name2 = '';
                                                                $outlet_name3 = '';
                                                                $outlet_name4 = '';

                                                                if ($datas['ec'] == 1) {
                                                                    $outlet_name1 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 2) {
                                                                    $outlet_name2 = $datas['outlet_name'];
                                                                } elseif ($datas['ec'] == 3) {
                                                                    $outlet_name3 = $datas['outlet_name'];
                                                                } else {
                                                                    $outlet_name4 = $datas['outlet_name'];
                                                                }

                                                                ?>
                                                                <td><?= $outlet_name1; ?></td>
                                                                <td><?= $outlet_name2; ?></td>
                                                                <td><?= $outlet_name3; ?></td>
                                                                <td><?= $outlet_name4; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="9"><b>No Result Found!</b></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>


                            </div>


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



        </div>
    </div>
</div>


<script>
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

            a.download = "outlet_visit_information_details.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>