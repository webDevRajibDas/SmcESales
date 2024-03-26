<?php
//App::import('Controller', 'SalesReportsController');
//$SalesReportsController = new SalesReportsController;
?>


<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
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

    .pro_label_title {
        width: 92%;
        margin-right: 3%;
    }

    .left_align {
        text-align: left;
       
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

    td, th {
        padding: 2px 5px !important;
    }

    td.left {
        border-right: #c7c7c7 solid 1px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('Memo', array('role' => 'form')); ?>

                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_id', array('label' => 'Product', 'class' => 'form-control product_id', 'options' => $product_list)); ?>
                            </td>
                            <td>
                                <div style="margin-left: 95px;">


                                    <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true));  ?>
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


            </div>

            <?php if ($dataresult == 1) { ?>
                <h2 style="text-align: center;">No Data Found.</h2>
            <?php } else { ?>

                <?php
                if (!empty($request_data)) {
                ?>
                    <div id="content" style="width:90%; margin: 0 5%;">
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
                                width: 90px;
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
                                overflow-y: hidden;
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

                        <div class="table-responsive">
                            <div class="pull-right csv_btn" style="padding-top:20px;">

                                <?php echo $this->Html->link(__('Download XLS'), array('action' => '#'), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">

                                <table class="downloadborder">
                                    <tr>
                                        <td>



                                            <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">

                                                <?php
                                                $source = $productInfo['source'];
                                                if ($source == 'SMCEL') {
                                                    $cname = "SMC Enterprise Limited";
                                                } else {
                                                    $cname = "SOCIAL MARKETING COMPNAY";
                                                }

                                                ?>

                                                <h2 style="margin:2px 0;"><?= $cname; ?></h2>
                                                <h3 style="margin:2px 0;"><?= $productInfo['name'] . ' Report'; ?></h3>
                                                <b> Time Frame : <?= @date('d M, Y', strtotime($this->request->data['Memo']['date_from'])) ?> to <?= @date('d M, Y', strtotime($this->request->data['Memo']['date_to'])) ?></b>
                                                <h5 style="margin:2px 0;">The Report is Generated Using eSales Software of SMC</h5>

                                            </div>

                                            <div style="float:left; width:100%; ">
                                                <h3>Volume Sold By Division</h3>

                                                <table id="sum_table" style="width: 65%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                                    <tr class="titlerow" height="35">
                                                        <th style="text-align:left;">
                                                            <div>Division Name</div>
                                                        </th>
                                                        <th>
                                                            <div>Urban</div>
                                                        </th>

                                                        <th>
                                                            <div>Rural</div>
                                                        </th>
                                                        <th>
                                                            <div>Total</div>
                                                        </th>

                                                    </tr>
                                                    <?php
                                                    $nUrban = 0;
                                                    $nRural = 0;
                                                    $nToral = 0;
                                                    foreach ($resData as $key => $val) {
                                                        $toal = $val['urban'] + $val['rural'];
                                                    ?>
                                                        <tr>
                                                            <td style="text-align:left;">
                                                                <?php echo $divisions[$val['division_id']]; ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <?php
                                                                $nUrban += $val['urban'];
                                                                if ($val['urban'] - (int)$val['urban'] > 0) {
                                                                    echo number_format($val['urban'], 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$val['urban'], 0, '', ',');
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <?php
                                                                $nRural += $val['rural'];
                                                                //echo $val['rural'];
                                                                if ($val['rural'] - (int)$val['rural'] > 0) {
                                                                    echo number_format($val['rural'], 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$val['rural'], 0, '', ',');
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <?php
                                                                $nToral += $toal;
                                                                if ($toal - (int)$toal > 0) {
                                                                    echo number_format($toal, 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$toal, 0, '', ',');
                                                                }

                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <th style="text-align:left;">National</th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($nUrban - (int)$nUrban > 0) {
                                                                echo number_format($nUrban, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$nUrban, 0, '', ',');
                                                            }
                                                            ?>
                                                        </th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($nRural - (int)$nRural > 0) {
                                                                echo number_format($nRural, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$nRural, 0, '', ',');
                                                            }
                                                            ?>

                                                        </th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($nToral - (int)$nToral > 0) {
                                                                echo number_format($nToral, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$nToral, 0, '', ',');
                                                            }
                                                            ?>
                                                        </th>
                                                    </tr>
                                                </table>
                                                <!-- <br><br> -->

                                                <!-- <h3>Sales Revenue Generated(TK)</h3>
                        <table id="sum_table" style="width: 50%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                              
                              <tr class="titlerow" height="35">
                                <th  style="text-align:left;">
                                    Division Name
                                </th>
                                
                                <th>
                                    Revenue
                                </th>
                                 
                              </tr>
                            <?php
                            $tkTotal = 0;
                            foreach ($resData as $key => $val) {
                            ?>
                                <tr>
                                    <td style="text-align:left;">
                                        <?php echo $divisions[$val['division_id']]; ?>
                                    </td>
                                    
                                    <td style="text-align:right;">
                                        <?php
                                        $tkTotal += round($val['amount']);

                                        echo number_format(round($val['amount']), 0, '', ',');

                                        ?>
                                    </td>
                                </tr>
                            <?php } ?> 
                                <tr>
                                    <th style="text-align:left;">National</th>
                                    <th style="text-align:right;">
                                        <?php
                                        echo number_format(round($tkTotal), 0, '', ',');
                                        ?>
                                    </th>
                                </tr>              
                        </table>
                        <br><br> -->
                                                <h3>Geographical Area Coverage</h3>
                                                <table id="sum_table" style="width: 50%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">


                                                    <tr height="35">
                                                        <th style="text-align:left;">
                                                            <div>District Covered</div>
                                                        </th>

                                                        <td>
                                                            <div><?= $totalgeocovereged['total_district_id']; ?></div>
                                                        </td>

                                                    </tr>
                                                    <tr height="35">
                                                        <th style="text-align:left;">
                                                            <div>Upazila/Thana Covered</div>
                                                        </th>

                                                        <td>
                                                            <div><?= $totalgeocovereged['total_thanaid']; ?></div>
                                                        </td>

                                                    </tr>

                                                </table>
                                                <!-- <br><br> -->
                                                <h3>Outlet Covered, Effective Call Held and Volume Sold</h3>
                                                <table id="sum_table" style="width: 55%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                                    <tr class="titlerow" height="35">
                                                        <th style="text-align:left;">
                                                            <div>Type Of Outlet</div>
                                                        </th>

                                                        <th>
                                                            <div>Outlets</div>
                                                        </th>
                                                        <th>
                                                            <div>Effective Call</div>
                                                        </th>
                                                        <th>
                                                            <div>Volume Sold</div>
                                                        </th>
                                                    </tr>
                                                    <?php
                                                    $totaloutlet  = 0;
                                                    $totaleffetivecall  = 0;
                                                    $totalVol  = 0;
                                                    foreach ($outResutl_BSP_PCHP as $key => $val) {
                                                        $totaloutlet  += $val['outletid'];
                                                        $totaleffetivecall  += $val['memoid'];
                                                        $totalVol  += $val['vol'];
                                                    ?>
                                                        <tr>
                                                            <td style="text-align:left;">
                                                                <?php
                                                                if ($val['program_type_id'] == 1) {
                                                                    echo 'GSP';
                                                                } else {
                                                                    echo $programstype[$val['program_type_id']];
                                                                }
                                                                ?>
                                                            </td>

                                                            <td style="text-align:right;">
                                                                <?php
                                                                if ($val['outletid'] - (int)$val['outletid'] > 0) {
                                                                    echo number_format($val['outletid'], 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$val['outletid'], 0, '', ',');
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <?php

                                                                if ($val['memoid'] - (int)$val['memoid'] > 0) {
                                                                    echo number_format($val['memoid'], 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$val['memoid'], 0, '', ',');
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <?php

                                                                if ($val['vol'] - (int)$val['vol'] > 0) {
                                                                    echo number_format($val['vol'], 2, '.', ',');
                                                                } else {
                                                                    echo number_format((int)$val['vol'], 0, '', ',');
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td style="text-align:left;">
                                                            <?php echo "Goldstar Upazila (Notundin)"; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_nutundin[0]['outletid'] - (int)$outResutl_nutundin[0]['outletid'] > 0) {
                                                                echo number_format($outResutl_nutundin[0]['outletid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_nutundin[0]['outletid'], 0, '', ',');
                                                            }

                                                            $totaloutlet  += $outResutl_nutundin[0]['outletid'];
                                                            $totaleffetivecall  += $outResutl_nutundin[0]['memoid'];
                                                            $totalVol  += $outResutl_nutundin[0]['vol'];
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_nutundin[0]['memoid'] - (int)$outResutl_nutundin[0]['memoid'] > 0) {
                                                                echo number_format($outResutl_nutundin[0]['memoid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_nutundin[0]['memoid'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_nutundin[0]['vol'] - (int)$outResutl_nutundin[0]['vol'] > 0) {
                                                                echo number_format($outResutl_nutundin[0]['vol'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_nutundin[0]['vol'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:left;">
                                                            <?php echo "Other NGO"; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_ngo[0]['outletid'] - (int)$outResutl_ngo[0]['outletid'] > 0) {
                                                                echo number_format($outResutl_ngo[0]['outletid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_ngo[0]['outletid'], 0, '', ',');
                                                            }
                                                            $totaloutlet  += $outResutl_ngo[0]['outletid'];
                                                            $totaleffetivecall  += $outResutl_ngo[0]['memoid'];
                                                            $totalVol  += $outResutl_ngo[0]['vol'];
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_ngo[0]['memoid'] - (int)$outResutl_ngo[0]['memoid'] > 0) {
                                                                echo number_format($outResutl_ngo[0]['memoid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_ngo[0]['memoid'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outResutl_ngo[0]['vol'] - (int)$outResutl_ngo[0]['vol'] > 0) {
                                                                echo number_format($outResutl_ngo[0]['vol'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outResutl_ngo[0]['vol'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:left;">
                                                            <?php echo "Other Pharma"; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outletnpharmaResult['outletid'] - (int)$outletnpharmaResult['outletid'] > 0) {
                                                                echo number_format($outletnpharmaResult['outletid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outletnpharmaResult['outletid'], 0, '', ',');
                                                            }
                                                            $totaloutlet  += $outletnpharmaResult['outletid'];
                                                            $totaleffetivecall  += $outletnpharmaResult['memoid'];
                                                            $totalVol  += $outletnpharmaResult['vol'];
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outletnpharmaResult['memoid'] - (int)$outletnpharmaResult['memoid'] > 0) {
                                                                echo number_format($outletnpharmaResult['memoid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outletnpharmaResult['memoid'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($outletnpharmaResult['vol'] - (int)$outletnpharmaResult['vol'] > 0) {
                                                                echo number_format($outletnpharmaResult['vol'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$outletnpharmaResult['vol'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:left;">
                                                            <?php echo "Others"; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($ortehrpharmaResult['outletid'] - (int)$ortehrpharmaResult['outletid'] > 0) {
                                                                echo number_format($ortehrpharmaResult['outletid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$ortehrpharmaResult['outletid'], 0, '', ',');
                                                            }
                                                            $totaloutlet  += $ortehrpharmaResult['outletid'];
                                                            $totaleffetivecall  += $ortehrpharmaResult['memoid'];
                                                            $totalVol += $ortehrpharmaResult['vol'];
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($ortehrpharmaResult['memoid'] - (int)$ortehrpharmaResult['memoid'] > 0) {
                                                                echo number_format($ortehrpharmaResult['memoid'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$ortehrpharmaResult['memoid'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php

                                                            if ($ortehrpharmaResult['vol'] - (int)$ortehrpharmaResult['vol'] > 0) {
                                                                echo number_format($ortehrpharmaResult['vol'], 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$ortehrpharmaResult['vol'], 0, '', ',');
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th style="text-align:left;">National</th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($totaloutlet - (int)$totaloutlet > 0) {
                                                                echo number_format($totaloutlet, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$totaloutlet, 0, '', ',');
                                                            }
                                                            ?>
                                                        </th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($totaleffetivecall - (int)$totaleffetivecall > 0) {
                                                                echo number_format($totaleffetivecall, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$totaleffetivecall, 0, '', ',');
                                                            }
                                                            ?>
                                                        </th>
                                                        <th style="text-align:right;">
                                                            <?php
                                                            if ($totalVol - (int)$totalVol > 0) {
                                                                echo number_format($totalVol, 2, '.', ',');
                                                            } else {
                                                                echo number_format((int)$totalVol, 0, '', ',');
                                                            }
                                                            ?>
                                                        </th>

                                                    </tr>
                                                </table>
                                                <!-- <br><br> -->
                                                <h3>Performance by Intervention Upazila</h3>
                                                <table id="sum_table" style="width: 55%; margin: 0;" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">

                                                    <?php
                                                    $thana_array = array("10485"=>"Lalpur", "100"=>"Monohardi", "265"=>"Nasirnagar", "10562"=>"Raj Nagar", "224"=>"Wazirpur");
                                                    $blue_star_array = array();
                                                    $green_star_array = array();
                                                    $gold_star_array = array();
                                                    $other_ngo_array  = array();
                                                    $other_pharma_array  = array();
                                                    $other_array  = array();
                                                    
                                                 ?>
                                                    <tr class="titlerow" height="35">
                                                        <th style="text-align:left;">
                                                            <div>Division</div>
                                                        </th>

                                                        <th style="text-align:left;">
                                                            <div>District</div>
                                                        </th>
                                                        <th style="text-align:left;">
                                                            <div>Upazila</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Blue Star</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Green Star</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Goldstar</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Other NGO</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Other Pharma</div>
                                                        </th>
                                                        <th style="text-align:center;">
                                                            <div>Others</div>
                                                        </th>
                                                        <th style="text-align:center;" >
                                                            <div>Total Volume</div>
                                                        </th>
                                                    </tr>

                                                    

                                                    <tr>
                                                        
                                                        <td style="text-align:left;">Rajshahi</td>
                                                        <td style="text-align:left;">Natore</td>
                                                        <td style="text-align:left;">Lalpur</td>
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_bsp_store['10485']))$blue_star_array['10485']=$thana_wise_bsp_store['10485'];
                                                            else $blue_star_array['10485'] = 0;
                                                            echo $blue_star_array['10485']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_gsp_store['10485']))$green_star_array['10485']=$thana_wise_gsp_store['10485'];
                                                            else $green_star_array['10485']= 0;
                                                            echo $green_star_array['10485']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_notundin_store['10485']))$gold_star_array['10485']=$thana_wise_notundin_store['10485'];
                                                            else $gold_star_array['10485'] = 0;
                                                            echo $gold_star_array['10485']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_ngo_store['10485']))$other_ngo_array['10485']=$thana_wise_outlet_ngo_store['10485'];
                                                            else $other_ngo_array['10485'] = 0;
                                                            echo $other_ngo_array['10485']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_pharma_store['10485']))$other_pharma_array['10485']=$thana_wise_outlet_pharma_store['10485'];
                                                            else $other_pharma_array ['10485'] = 0;
                                                            echo $other_pharma_array ['10485']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_other_pharma_store['10485']))$other_array['10485']=$thana_wise_other_pharma_store['10485'];
                                                            else $other_array ['10485'] = 0;
                                                            echo $other_array ['10485']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            echo ($blue_star_array['10485']+$green_star_array['10485']+$gold_star_array['10485']+$other_ngo_array['10485']+$other_pharma_array['10485']+$other_array ['10485']); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td class="left_align" style="text-align:left;" >Dhaka</td>
                                                        <td class="left_align" style="text-align:left;">Narsingdi</td>
                                                        <td class="left_align" style="text-align:left;">Monohardi</td>

                                                        
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_bsp_store['100']))$blue_star_array['100']=$thana_wise_bsp_store['100'];
                                                            else $blue_star_array['100'] = 0;
                                                            echo $blue_star_array['100']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_gsp_store['100']))$green_star_array['100']=$thana_wise_gsp_store['100'];
                                                            else $green_star_array['100']= 0;
                                                            echo $green_star_array['100']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_notundin_store['100']))$gold_star_array['100']=$thana_wise_notundin_store['100'];
                                                            else $gold_star_array['100'] = 0;
                                                            echo $gold_star_array['100']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_ngo_store['100']))$other_ngo_array['100']=$thana_wise_outlet_ngo_store['100'];
                                                            else $other_ngo_array['100'] = 0;
                                                            echo $other_ngo_array['100']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_pharma_store['100']))$other_pharma_array['100']=$thana_wise_outlet_pharma_store['100'];
                                                            else $other_pharma_array ['100'] = 0;
                                                            echo $other_pharma_array ['100']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_other_pharma_store['100']))$other_array['100']=$thana_wise_other_pharma_store['100'];
                                                            else $other_array ['100'] = 0;
                                                            echo $other_array ['100']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            echo ($blue_star_array['100']+$green_star_array['100']+$gold_star_array['100']+$other_ngo_array['100']+$other_pharma_array['100']+$other_array ['100']); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td class="left_align" style="text-align:left;">Chattogram</td>
                                                        <td class="left_align" style="text-align:left;">Brahmanbaria</td>
                                                        <td class="left_align" style="text-align:left;">Nasirnagar</td>

                                                        
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_bsp_store['265']))$blue_star_array['265']=$thana_wise_bsp_store['265'];
                                                            else $blue_star_array['265'] = 0;
                                                            echo $blue_star_array['265']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_gsp_store['265']))$green_star_array['265']=$thana_wise_gsp_store['265'];
                                                            else $green_star_array['265']= 0;
                                                            echo $green_star_array['265']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_notundin_store['265']))$gold_star_array['265']=$thana_wise_notundin_store['265'];
                                                            else $gold_star_array['265'] = 0;
                                                            echo $gold_star_array['265']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_ngo_store['265']))$other_ngo_array['265']=$thana_wise_outlet_ngo_store['265'];
                                                            else $other_ngo_array['265'] = 0;
                                                            echo $other_ngo_array['265']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_pharma_store['265']))$other_pharma_array['265']=$thana_wise_outlet_pharma_store['265'];
                                                            else $other_pharma_array ['265'] = 0;
                                                            echo $other_pharma_array ['265']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_other_pharma_store['265']))$other_array['265']=$thana_wise_other_pharma_store['265'];
                                                            else $other_array ['265'] = 0;
                                                            echo $other_array ['265']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            echo ($blue_star_array['265']+$green_star_array['265']+$gold_star_array['265']+$other_ngo_array['265']+$other_pharma_array['265']+$other_array ['265']); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td class="left_align" style="text-align:left;">Sylhet</td>
                                                        <td class="left_align" style="text-align:left;">Moulavibazar</td>
                                                        <td class="left_align" style="text-align:left;">Raj Nagar</td>

                                                        
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_bsp_store['10562']))$blue_star_array['10562']=$thana_wise_bsp_store['10562'];
                                                            else $blue_star_array['10562'] = 0;
                                                            echo $blue_star_array['10562']; ?>
                                                        </td style="text-align:right;">
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_gsp_store['10562']))$green_star_array['10562']=$thana_wise_gsp_store['10562'];
                                                            else $green_star_array['10562']= 0;
                                                            echo $green_star_array['10562']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_notundin_store['10562']))$gold_star_array['10562']=$thana_wise_notundin_store['10562'];
                                                            else $gold_star_array['10562'] = 0;
                                                            echo $gold_star_array['10562']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_ngo_store['10562']))$other_ngo_array['10562']=$thana_wise_outlet_ngo_store['10562'];
                                                            else $other_ngo_array['10562'] = 0;
                                                            echo $other_ngo_array['10562']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_pharma_store['10562']))$other_pharma_array['10562']=$thana_wise_outlet_pharma_store['10562'];
                                                            else $other_pharma_array ['10562'] = 0;
                                                            echo $other_pharma_array ['10562']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_other_pharma_store['10562']))$other_array['10562']=$thana_wise_other_pharma_store['10562'];
                                                            else $other_array ['10562'] = 0;
                                                            echo $other_array ['10562']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            echo ($blue_star_array['10562']+$green_star_array['10562']+$gold_star_array['10562']+$other_ngo_array['10562']+$other_pharma_array['10562']+$other_array ['10562']); ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td class="left_align" style="text-align:left;">Barishal</td>
                                                        <td class="left_align" style="text-align:left;">Barishal</td>
                                                        <td class="left_align" style="text-align:left;">Wazirpur</td>

                                                        
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_bsp_store['224']))$blue_star_array['224']=$thana_wise_bsp_store['224'];
                                                            else $blue_star_array['224'] = 0;
                                                            echo $blue_star_array['224']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_gsp_store['224']))$green_star_array['224']=$thana_wise_gsp_store['224'];
                                                            else $green_star_array['224']= 0;
                                                            echo $green_star_array['224']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_notundin_store['224']))$gold_star_array['224']=$thana_wise_notundin_store['224'];
                                                            else $gold_star_array['224'] = 0;
                                                            echo $gold_star_array['224']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_ngo_store['224']))$other_ngo_array['224']=$thana_wise_outlet_ngo_store['224'];
                                                            else $other_ngo_array['224'] = 0;
                                                            echo $other_ngo_array['224']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_outlet_pharma_store['224']))$other_pharma_array['224']=$thana_wise_outlet_pharma_store['224'];
                                                            else $other_pharma_array ['224'] = 0;
                                                            echo $other_pharma_array ['224']; ?>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            if(isset($thana_wise_other_pharma_store['224']))$other_array['224']=$thana_wise_other_pharma_store['224'];
                                                            else $other_array ['224'] = 0;
                                                            echo $other_array ['224']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            echo ($blue_star_array['224']+$green_star_array['224']+$gold_star_array['224']+$other_ngo_array['224']+$other_pharma_array['224']+$other_array ['224']); ?>
                                                        </td>
                                                    </tr>

                                                    <?php $total_volume =array()  ?>

                                                    <tr>
                                                        
                                                        <td style="text-align:right;" colspan="3">Total:</td>
                                                        

                                                        
                                                        
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['blue_star'] = ($blue_star_array['10485']+$blue_star_array['100']+$blue_star_array['265']+$blue_star_array['10562']+$blue_star_array['224']);
                                                            echo $total_volume['blue_star']; ?>
                                                        </td>
                                                       
                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['green_star'] = ($green_star_array['10485']+$green_star_array['100']+$green_star_array['265']+$green_star_array['10562']+$green_star_array['224']);
                                                            echo $total_volume['green_star']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['gold_star'] = ($gold_star_array['10485']+$gold_star_array['100']+$gold_star_array['265']+$gold_star_array['10562']+$gold_star_array['224']);
                                                            echo $total_volume['gold_star']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['other_ngo'] = ($other_ngo_array['10485']+$other_ngo_array['100']+$other_ngo_array['265']+$other_ngo_array['10562']+$other_ngo_array['224']);
                                                            echo $total_volume['other_ngo']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['other_pharma'] = ($other_pharma_array['10485']+$other_pharma_array['100']+$other_pharma_array['265']+$other_pharma_array['10562']+$other_pharma_array['224']);
                                                            echo $total_volume['other_pharma']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            $total_volume['other'] = ($other_array['10485']+$other_array['100']+$other_array['265']+$other_array['10562']+$other_array['224']);
                                                            echo $total_volume['other']; ?>
                                                        </td>

                                                        <td style="text-align:right;">
                                                            <?php 
                                                            
                                                            
                                                            echo ($total_volume['blue_star']+$total_volume['green_star']+$total_volume['gold_star']+$total_volume['other_ngo']+$total_volume['other_pharma']+$total_volume['other']); ?>
                                                        </td>

                                                        
                
                                                    </tr>
                                                   
                                                    
                                                </table>
                                               
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </div>
</div>

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
        mywindow.close();

        return true;
    }
</script>

<script>
    $(document).ready(function() {

        $("#download_xl").click(function(e) {

            e.preventDefault();

            $(".downloadborder").attr("border", "1");

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });


            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            console.log(a);

            a.href = downloadUrl;

            a.download = "<?= $productInfo['name'] . '_report'; ?>.xls";

            document.body.appendChild(a);

            a.click();

            $(".downloadborder").removeAttr("border");

        });

    });
</script>