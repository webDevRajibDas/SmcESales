<style>
    #content {
        display: none;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('PrimaryMemo Details'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> PrimaryMemo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                    <button type="button" onclick="PrintElem('content')" class="btn btn-primary">
                        <i class="glyphicon glyphicon-print"></i> Print
                    </button>
                </div>
            </div>
            <?php echo $this->Form->create('PrimaryMemo', array('role' => 'form')); ?>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td width="25%"><strong><?php echo 'challan No.'; ?></strong></td>
                            <td><?php echo h($primarymemo['PrimaryMemo']['challan_no']); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo 'PrimaryMemo Referance No.'; ?></strong></td>
                            <td><?php echo h($primarymemo['PrimaryMemo']['challan_referance_no']); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php echo 'Sender Store'; ?></strong></td>
                            <td><?php echo h($primarymemo['SenderStore']['name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo 'PrimaryMemo Date'; ?></strong></td>
                            <td><?php if ($primarymemo['PrimaryMemo']['challan_date']) echo $this->App->dateformat(($primarymemo['PrimaryMemo']['challan_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo 'Receiver Store'; ?></strong></td>
                            <td><?php echo h($primarymemo['ReceiverStore']['name']); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php echo 'Delivery Point'; ?></strong></td>
                            <td><?php echo h($primarymemo['PrimaryMemo']['delivery_point']); ?></td>
                        </tr>


                        <tr>
                            <td><strong><?php echo 'Status'; ?></strong></td>
                            <td align="left">
                                <?php
                                if ($primarymemo['PrimaryMemo']['status'] == 1) {
                                    echo '<span class="btn btn-success btn-xs">Submitted</span>';
                                } else {
                                    echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo 'Remarks'; ?></strong></td>
                            <td><?php echo h($primarymemo['PrimaryMemo']['remarks']); ?></td>
                        </tr>
                </table>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="text-center">SL</th>
                                <th class="text-center">Product Name</th>
                                <!-- <th class="text-center">Batch No.</th> -->
                                <th class="text-center">Unit</th>
                                <th class="text-center">Product Quantity</th>
                                <th class="text-center">Product Price</th>
                                <th class="text-center">Vat(%)</th>
                                <!-- <th class="text-center">Expire Date</th> -->
                                <th class="text-center">Total Price</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                            <?php
                            $primarymemodetailPrint = $primarymemodetail;
                            if (!empty($primarymemodetail)) {
                                $sl = 1;
                                $total_price = 0;
                                $total_received_quantity = 0;
                                foreach ($primarymemodetail as $key => $val) {
                                    if ($val['PrimaryMemoDetail']['product_price'] == 0) {
                                        continue;
                                    }
                            ?>
                                    <tr>
                                        <td align="center"><?php echo $sl; ?></td>
                                        <td><?php echo $val['Product']['name']; ?></td>
                                        <!-- <td align="center"><?php echo $val['PrimaryMemoDetail']['batch_no']; ?></td> -->
                                        <td><?php echo $val['MeasurementUnit']['name']; ?></td>
                                        <td align="center"><?php echo $val['PrimaryMemoDetail']['challan_qty']; ?></td>
                                        <td align="center"><?php echo $val['PrimaryMemoDetail']['product_price']; ?></td>
                                        <td align="center"><?php echo $val['PrimaryMemoDetail']['vat']; ?></td>
                                        <!-- <td align="center"><?php if ($val['PrimaryMemoDetail']['expire_date'] != ' ') echo $this->App->expire_dateformat($val['PrimaryMemoDetail']['expire_date']); ?></td> -->

                                        <td align="center">
                                            <?php
                                            $total = $val['PrimaryMemoDetail']['challan_qty'] * $val['PrimaryMemoDetail']['product_price'];
                                            echo sprintf('%.2f', $total);
                                            ?>
                                        </td>
                                        <td><?php echo $val['PrimaryMemoDetail']['remarks']; ?></td>
                                    </tr>
                                <?php
                                    $total_price =  $total_price + $total;
                                    $sl++;

                                    unset($primarymemodetail[$key]);
                                }
                                ?>
                        </tbody>
                        <tr>
                            <td colspan="6" align="right"><b>Total : </b></td>
                            <td align="center"><strong><?php echo sprintf('%.2f', $total_price); ?></strong></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                    <?php if (!empty($primarymemodetail)) { ?>
                        <h3 class="box-title">Bonus</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Product Name</th>
                                    <!-- <th class="text-center">Batch No.</th> -->
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Product Quantity</th>
                                    <!-- <th class="text-center">Vat(%)</th> -->
                                    <!-- <th class="text-center">Expire Date</th> -->
                                    <th class="text-center">Remarks</th>
                                </tr>
                                <?php

                                    $sl = 1;
                                    $total_price = 0;
                                    $total_received_quantity = 0;
                                    foreach ($primarymemodetail as $val) {
                                        if ($val['PrimaryMemoDetail']['product_price'] > 0) {
                                            continue;
                                        }
                                ?>
                                    <tr>
                                        <td align="center"><?php echo $sl; ?></td>
                                        <td><?php echo $val['Product']['name']; ?></td>
                                        <!-- <td align="center"><?php echo $val['PrimaryMemoDetail']['batch_no']; ?></td> -->
                                        <td><?php echo $val['MeasurementUnit']['name']; ?></td>
                                        <td align="center"><?php echo $val['PrimaryMemoDetail']['challan_qty']; ?></td>
                                        <!-- <td align="center"><?php //echo $val['PrimaryMemoDetail']['vat']; 
                                                                ?></td> -->
                                        <!-- <td align="center"><?php if ($val['PrimaryMemoDetail']['expire_date'] != ' ') echo $this->App->expire_dateformat($val['PrimaryMemoDetail']['expire_date']); ?></td> -->

                                        <td><?php echo $val['PrimaryMemoDetail']['remarks']; ?></td>
                                    </tr>
                            <?php
                                        unset($val);
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>

            <?php echo $this->Form->end(); ?>
            </br>
        </div>
    </div>
</div>
</div>

<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;margin-top: 50px; font-size: 11px;">
    <style type="text/css">
        @media print {
            #non-printable {
                display: none;
            }

            #content {
                display: block;
            }

            .table_content {
                padding-top: 50px;
            }

            table {
                width: 100%;
                font-size: 11px;
                margin-top: 50px;
            }

            table,
            th,
            td {
                border: 1px solid black;
                border-collapse: collapse;
            }

            footer {
                position: fixed;
                left: 10%;
                bottom: 6mm;
                font-size: 10px;
            }

            .footer1 {
                width: 100%;
                height: 100px;
                /*position: relative;*/
                font-size: 10px;
                overflow-y: inherit;
            }

            .font_size {
                font-size: 11px;
            }

            .page-break {
                page-break-after: always;
            }

            #heading_name {
                font-size: 18px;
            }

            #heading_add {
                font-size: 16px;
            }

            .page_header {
                position: relative;
                width: 100%;
                font-weight: normal;
                font-size: 8px;
                float: right;
                text-align: right;
                margin-right: 3%;
            }

            @page {
                size: auto;
                margin: 0;
                /*margin: 30px;*/
            }

            body {
                margin: 12.7mm 12.7mm 0 12.7mm;
            }
        }
    </style>
    <?php

    if (!empty($primarymemodetailPrint)) {
        $sl = 1;
        $total_price = 0;
        $total_vat = 0;
        $in = 1;
        $data = array();
        $tr = '';
        foreach ($primarymemodetailPrint as $val) {
            $total = $val['PrimaryMemoDetail']['product_price'] * $val['PrimaryMemoDetail']['challan_qty'];
            // $productPrice = $memos->getProductPrice($val['MemoDetail']['product_id'], $val['Memo']['memo_date']);
            $tr .= '<tr>      
				<td align="center">' . $sl . '</td>            
				<td>' . $val['Product']['name'] . '</td>
				<td align="right">' . $val['PrimaryMemoDetail']['challan_qty'] . '</td>
				<td align="right">' . $val['PrimaryMemoDetail']['vat'] . '</td>
				<td align="right">' . sprintf('%.2f', $val['PrimaryMemoDetail']['product_price']) . '</td>
				<td align="right">' . sprintf('%.2f', $total) . '</td>
       		 </tr>
       		 ';


            $total_price =  $total_price + $total;
            $vat = $total - ((100 * $total) / (100 + $val['PrimaryMemoDetail']['vat']));
            $total_vat += $vat;
            $sl++;
            if ($in == 15) {
                $in = 1;
                $data[] = $tr;
                $tr = '';
            }
            $in++;
        }
        $data[] = $tr;
        $total_page = count($data);
    }
    ?>
    <div class="page-top">
        <p style="font-size: 15px; border: 2px solid #000000; padding: 3px 5px; display: inline-block; margin: 0; float: right">
            Mushak-6.3</p>
    </div>
    <div style="margin-right : 0;float: right;text-align:right;" class="page_header">
        <br>
        <!--Page No :<?php /*echo '1 Of '.$total_page;*/ ?><br>
		Print Date :--><?php /*echo $this->App->dateformat(date('Y-m-d H:i:s'));*/ ?>
    </div>
    <div style="width: 100%;height:25px;margin-top: 10px">
        <div style="width: 33%;float: left;">
            Form No: ADM/FORM/003
        </div>
        <div style="width: 33%;float: left;text-align: center;">
            Effective Date : 01-08-2015<br>
        </div>
        <div style="width: 33%;float: right; text-align:right;">
            Version No: 03
        </div>
    </div>
    <div style="width:100%;text-align:center;float:left">
        <p style="margin-bottom: 0; padding-bottom: 0">
            <font id="heading_name">Government of the People's Republic of
                Bangladesh, NBR, Dhaka</font>
        </p>
        <p style="margin: 0; padding: 5px 0 0; font-size: 16px">
            <!-- <font>Central Registered Affiliate Organization Delivery Vat Challan</font> -->
            <font>VAT CHALLAN PATRA</font>
        </p>
        <p style="margin: 0; padding: 0 0 0 5px; font-size: 16px">
            <!-- <font>Central Registered Affiliate Organization Delivery Vat Challan</font> -->
            <font> Clause Ga & Cha of Subrules 1 of Rules 40</font>
        </p>
    </div>
    <div style="width:100%;">
        <div style="width:25%;text-align:left;float:left">
            &nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div style="width:50%;text-align:center;float:left">
            <font id="heading_name">SMC Enterprise Limited</font><br>
            <span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
            <span id="heading_add">Central BIN:000049992-0101</span><br>
            <font>Sender : <?php echo h($primarymemo['SenderStore']['name']); ?></font>
            <font>Address : <?php echo h($primarymemo['SenderStore']['address']); ?></font>
        </div>
        <div style="width:25%;text-align:right;float:left">
            &nbsp;&nbsp;&nbsp;&nbsp;
        </div>
    </div>

    <div style="width:100%;">
        <div style="width:50%;text-align:left;float:left">
            <?php echo "Recipient : " . h($primarymemo['ReceiverStore']['name']); ?><br>
            <span style="margin-left: 25px;"><?= $primarymemo['ReceiverStore']['address'] ?></span><br>
            <?php /*echo "Address : " . h($so_info['Territory']['address']); */ ?>
            <!--<br>-->
        </div>

        <div style="width:50%;text-align:right;float:left">
            Memo No : <?php echo h($primarymemo['PrimaryMemo']['challan_no']); ?> <br>
            Date : <?php echo $this->App->dateformat($primarymemo['PrimaryMemo']['challan_date']); ?>
        </div>
    </div>
    <?php $page_count = 1;
    foreach ($data as $data) { ?>
        <?php if ($page_count > 1) { ?><div class="page_header">
                Page No :<?php echo $page_count . ' Of ' . $total_page; ?><br>
                Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s')); ?>
            </div><?php } ?>
        <div <?php if ($page_count > 1) {
                    echo ' class="table_content"';
                } ?>>
            <table style="width:100% font-size:11px; <?php if ($page_count > 1) {
                                                            echo 'margin-top: 50px';
                                                        } ?>" border="1px solid black" cellspacing="0" text-align="center">
                <thead>
                    <tr>
                        <th>SL #</th>
                        <th>Product</th>
                        <th align="right">Sales Qty</th>
                        <th align="right">VAT</th>
                        <th align="right">Price</th>
                        <th align="right">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $data; ?>
                </tbody>
                <tr>
                    <td align="right" colspan="5"><strong>Total Amount :</strong></td>
                    <td align="right"><strong><?php echo sprintf('%.2f', $total_price); ?></strong></td>
                </tr>
                <tr>
                    <td align="right" colspan="5"><strong>Total VAT :</strong></td>
                    <td align="right"><strong><?php echo sprintf('%.2f', $total_vat); ?></strong></td>
                </tr>
            </table>
            <p style="padding: 5px 0; margin: 0;font-size: small">* Product(s) price are SD free & including
                VAT</p>
        </div>

        <div class="footer1">
            <div style="width:100%;padding-top:20px;" class="font_size">
                <div style="width:33%;text-align:left;float:left">
                    Prepared by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;"></span>
                </div>
                <div style="width:30%;text-align:center;float:left;margin-left: 3%;">
                    Checked by:______________
                </div>
                <div style="width:33%;float:left">
                    Carried by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;"></span><br><br>
                </div>
            </div>

            <div style="width:100%;" class="font_size">
                <div style="width:53%;text-align:left;float:left">
                    <h4> Received the goods for Delivery </h4>
                    Driver's Signature :______________________ <br><br>
                    <!--Name of the Driver :<span style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;"> <?/*=$challan['Challan']['driver_name']*/ ?></span>-->
                    <br><br><br>
                    <h4> Approved by </h4>
                    Signature :_____________________________ <br><br>
                    Name :________________________________ <br> <br>
                    Designation :___________________________ <br> <br>
                </div>

                <div style="width:33%;text-align:left;float:right;margin-right: 3px;">
                    <h4> Received the goods in good condition: </h4>
                    Signature of the Recipient:___________________ <br> <br>
                    Name :___________________________________ <br><br>
                    Designation :______________________________ <br><br>
                    Date :____________________________________ <br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    (Seal)&nbsp;&nbsp;&nbsp;&nbsp;
                    <br><br><br><br><br><br><br><br>
                </div>
            </div>
        </div>
        <br><br><br><br>
        <footer style="width:100%;">
            "This Report has been generated from SMC Automated Sales System at [CWH]. This information is confidential and for internal use only."
        </footer>
        <div class="page-break"></div>

    <?php $page_count++;
    } ?>
</div>

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=600,width=960');

        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        var is_chrome = Boolean(window.chrome);
        if (is_chrome) {
            mywindow.onload = function() {
                setTimeout(function() { // wait until all resources loaded 
                    mywindow.print(); // change window to winPrint
                    mywindow.close(); // change window to winPrint
                }, 200);
            };
        } else {
            mywindow.print();
            mywindow.close();
        }

        return true;
    }
</script>

<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        color: #c7c7c7;
    }
</style>
<?php
$todayDate = date('Y-m-d');
$startDate = date('d-m-Y', strtotime($primarymemo['PrimaryMemo']['challan_date']));
$endDateOfMonth = date('Y-m-t', strtotime($startDate));
if (strtotime($todayDate) < strtotime($endDateOfMonth)) {
    $endDate = date('d-m-Y');
} else {
    $endDate = date('t-m-Y', strtotime($startDate));
}
?>
<div style="display:none;">
    <style>
        .draft {
            padding: 0px 15px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.datepicker_range').datepicker({
                startDate: '<?php echo $startDate; ?>',
                endDate: '<?php echo $endDate; ?>',
                format: "dd-mm-yyyy",
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>
</div>