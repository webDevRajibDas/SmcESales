<?php
//pr($balance_history);die();
$TransactionReports = new DistDistributorTransactionReportsController();
$opening_balance_data = array();
if (!empty($opening_balance)) {
    $opening_balance_data = $opening_balance;
}
?>
<style>
    #content {
        display: none;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Wise Transation Ledger Reports'); ?></h3>
                <div class="box-tools pull-right">
                    <!--<button type="button" onclick="PrintElem('content')" class="btn btn-info">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </button>-->
                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorBalance', array('role' => 'form',)); ?>
                    <table class="search">

                        <tr>

                            <td>
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => 'required')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => 'required')) ?>
                            </td>
                        </tr>

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----', 'required' => 'required')); ?></td>
                            <td width="50%"><?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor :', 'id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'required' => 'required', 'empty' => '---- Select Distributor ----')); ?></td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                <?php //echo $this->Form->button('Download', array('id'=>'download','type' => 'button', 'class' => 'btn btn-large btn-info', 'onclick'=>"PrintElem('content')",'escape' => false)); 
                                ?>
                                <?php if (isset($balance_history)) {
                                ?>
                                    <a class="btn btn-success" id="download_xl">Download XL</a>
                                <?php
                                } ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <div><?php if (isset($balance_history)) {
                            $total_balance = 0;
                            $total_debit_balance = 0;
                            $total_credit_balance = 0;
                        ?>
                        <table id="Territories" class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Particulars</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Credit</th>
                                    <th class="text-center">Balance</th>
                                </tr>
                                <tr>
                                    <td align="center"></td>
                                    <td align="center">Opening Balance</td>
                                    <td align="center">
                                        <?php if (!empty($opening_balance)) {
                                            echo round($opening_balance['DistDistributorBalanceHistory']['balance'], 2);
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                    <td align="center">-</td>
                                    <td align="center"><?php
                                                        if (!empty($opening_balance)) {
                                                            echo round($opening_balance['DistDistributorBalanceHistory']['balance'], 2);
                                                            $total_balance = $opening_balance['DistDistributorBalanceHistory']['balance'];
                                                            $total_debit_balance = $total_balance;
                                                        } else {
                                                            echo 0;
                                                        }
                                                        ?></td>
                                </tr>
                                <?php
                                if (!empty($balance_history)) {
                                    $sl = 0;
                                    $total = 0;
                                    $total_price = 0;
                                    $transaction_name = "";
                                    //foreach($balance_history as $val){
                                    $i = 0;
                                    // pr($balance_history);die();
                                    $from_date = $date_from;
                                    for ($i; $date_from <= $date_to; $i++) {
                                        if (isset($balance_history[$sl][0]['date']) && $date_from == $balance_history[$sl][0]['date']) {
                                            $debit = 0;
                                            $credit = 0;
                                            $transaction_name_cr = '';
                                            $transaction_name_dr = '';
                                            if ($balance_history[$sl][0]['credit'] != "" || $balance_history[$sl][0]['credit'] > 0) {
                                                $credit = $balance_history[$sl][0]['credit'];
                                                $transaction_name_cr = "Collection, Invoice ";
                                            }
                                            if ($balance_history[$sl][0]['dabit'] != "" || $balance_history[$sl][0]['dabit'] > 0) {
                                                $transaction_name_dr = "Sales, Invoice";
                                                $debit = $balance_history[$sl][0]['dabit'];
                                            }
                                            $balance = $debit - $credit;

                                            $total_balance_cr = $total_balance - $credit;
                                            $total_balance_dr = $total_balance_cr + $debit;


                                            $total_balance = $total_balance + $balance;

                                            $total_debit_balance = $total_debit_balance + $debit;
                                            $total_credit_balance = $total_credit_balance + $credit;
                                ?>
                                            <?php if ($credit > 0) { ?>
                                                <tr>
                                                    <td align="center" <?php if ($debit > 0) { ?>rowspan="2" <?php } ?>><?php echo date('d-m-Y', strtotime($date_from)); ?></td>
                                                    <td align="center"><?php echo $transaction_name_cr; ?></td>
                                                    <?php ?>

                                                    <td align="center">-</td>
                                                    <td align="center"><?php
                                                                        echo round($credit, 2); ?></td>
                                                    <?php ?>

                                                    <td align="center"><?php echo round($total_balance_cr, 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($debit > 0) { ?>
                                                <tr>
                                                    <?php if ($credit <= 0) { ?>
                                                        <td align="center"><?php echo date('d-m-Y', strtotime($date_from)); ?></td>
                                                    <?php } ?>
                                                    <td align="center"><?php echo $transaction_name_dr; ?></td>

                                                    <td align="center"><?php echo round($debit, 2); ?></td>
                                                    <td align="center">-</td>
                                                    <td align="center"><?php echo round($total_balance_dr, 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php $sl++; ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td align="center"><?php echo date('d-m-Y', strtotime($date_from)); ?></td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center"><?php echo round($total_balance, 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php
                                        $date_from = date("Y-m-d", strtotime("$date_from +1 day"));
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" align="right">Total Balance</td>
                                        <td align="center"><?php echo round($total_debit_balance, 2); ?></td>
                                        <td align="center"><?php echo round($total_credit_balance, 2); ?></td>
                                        <td align="center"><?php echo round($total_balance, 2); ?></td>
                                    </tr>
                                    <?php } else {
                                    $i = 0;
                                    for ($i; $date_from <= $date_to; $i++) {
                                    ?>
                                        <tr>
                                            <td align="center"><?php echo date('d-m-Y', strtotime($date_from)); ?></td>
                                            <td align="center">-</td>
                                            <td align="center">-</td>
                                            <td align="center">-</td>
                                            <td align="center"><?php echo round($total_balance, 2); ?></td>
                                        </tr>
                                    <?php $date_from = date("Y-m-d", strtotime("$date_from +1 day"));
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" align="right">Total Balance</td>
                                        <td align="center"><?php echo round($total_debit_balance, 2); ?></td>
                                        <td align="center"><?php echo round($total_credit_balance, 2); ?></td>
                                        <td align="center"><?php echo round($total_balance, 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="content" style="width:90%;height:100%; font-size: 11px;">
    <!--<style media="print">
        @page {
            size: auto;
            margin: 0;
        }
    </style>-->
    <style type="text/css">
        @media screen {
            div.divFooter {
                display: none;
            }
        }

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
                /* position: relative;
                 top:0px;normal
                 right:0px;
                 width:30%;
                 font-size: 8px;
                 margin-bottom: 10px;*/
            }

            @page {
                size: auto;
                margin: 0;
                /*margin: 30px;*/
            }

            body {
                margin: 12.7mm;
            }
        }
    </style>
    <!--  <div style="width: 100%; height:30px;float: right;font-size: 11px;">
        <div style="text-align:right;width:100%;">Page No :1 Of 1</div>
        <div style="text-align:right;width:100%;">Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s')); ?></div>0
    </div> -->
    <?php
    $tr = "";
    $total_balance = 0;
    $total_debit_balance = 0;
    $total_credit_balance = 0;
    if (!empty($opening_balance_data)) {
        $tr .= '<tr> 
					<td align="center"></td>
					<td align="center">Opening Balance</td>
					<td align="center">' . $opening_balance_data['DistDistributorBalanceHistory']['balance'] . '</td>
					<td align="center">-</td>
					<td align="center">' . $opening_balance_data['DistDistributorBalanceHistory']['balance'] . '</td>
				</tr>';
        $total_debit_balance = $total_balance = $opening_balance_data['DistDistributorBalanceHistory']['balance'];
    }
    if (!empty($balance_history)) {
        $sl = 0;
        $total = 0;
        $total_price = 0;
        $transaction_name = "";
        //foreach($balance_history as $val){
        $i = 0;
        // pr($balance_history);die();
        $date_from = $from_date;
        for ($i; $date_from <= $date_to; $i++) {
            if (isset($balance_history[$sl][0]['date']) && $date_from == $balance_history[$sl][0]['date']) {
                $debit = 0;
                $credit = 0;
                $transaction_name_cr = '';
                $transaction_name_dr = '';
                if ($balance_history[$sl][0]['credit'] != "" || $balance_history[$sl][0]['credit'] > 0) {
                    $credit = $balance_history[$sl][0]['credit'];
                    $transaction_name_cr = "Collection, Invoice ";
                }
                if ($balance_history[$sl][0]['dabit'] != "" || $balance_history[$sl][0]['dabit'] > 0) {
                    $transaction_name_dr = "Sales, Invoice";
                    $debit = $balance_history[$sl][0]['dabit'];
                }
                $balance = $debit - $credit;
                $total_balance_dr = $total_balance + $debit;
                $total_balance_cr = $total_balance_dr - $credit;
                $total_balance = $total_balance + $balance;
                $total_debit_balance = $total_debit_balance + $debit;
                $total_credit_balance = $total_credit_balance + $credit;

                // if($debit>0)
                // {
                //     $rowspan=($credit > 0)?'rowspan="2"':'';
                //     $tr.= "<tr> 
                //         <td align='center'  ".$rowspan.">".date('d-m-Y', strtotime($date_from))."</td>
                //         <td align='center'>$transaction_name_dr</td>
                //         <td align='center'>".round($debit, 2)."</td>
                //         <td align='center'>-</td>
                //         <td align='center'>".round($total_balance_dr, 2)."</td>
                //     </tr>";
                // } 
                // if($credit > 0)
                // {
                //     $date_td=($debit <= 0)?'<td align="center">'.date('d-m-Y', strtotime($date_from)).'</td>':'';
                //     $tr.="<tr>
                //         $date_td
                //         <td align='center'>$transaction_name_cr</td>
                //         <td align='center'>-</td>
                //         <td align='center'>".round($credit, 2)."</td>
                //         <td align='center'>".round($total_balance_cr, 2)."</td>
                //     </tr>";
                // }

                if ($credit > 0) {
                    $rowspan = ($debit > 0) ? 'rowspan="2"' : '';
                    $tr .= "<tr> 
                        <td align='center'  " . $rowspan . ">" . date('d-m-Y', strtotime($date_from)) . "</td>
                        <td align='center'>$transaction_name_cr</td>
                        <td align='center'>-</td>
                        <td align='center'>" . round($credit, 2) . "</td>
                        
                        <td align='center'>" . round($total_balance_cr, 2) . "</td>
                    </tr>";
                }
                if ($debit > 0) {
                    $date_td = ($credit <= 0) ? '<td align="center">' . date('d-m-Y', strtotime($date_from)) . '</td>' : '';
                    $tr .= "<tr>
                        $date_td
                        <td align='center'>$transaction_name_dr</td>
                        
                        <td align='center'>" . round($debit, 2) . "</td>
                        <td align='center'>-</td>
                        <td align='center'>" . round($total_balance_dr, 2) . "</td>
                    </tr>";
                }
                $sl++;
            } else {
                $tr .= "<tr> 
                    <td align='center'>" . date('d-m-Y', strtotime($date_from)) . "</td>
                    <td align='center'>-</td>
                    <td align='center'>-</td>
                    <td align='center'>-</td>
                    <td align='center'>" . round($total_balance, 2) . "</td>
                </tr>";
            }
            $date_from = date("Y-m-d", strtotime("$date_from +1 day"));
        }

        $tr .= "<tr> 
            <td colspan='2' align='right'>Total Balance</td>
            <td align='center'>" . round($total_debit_balance, 2) . "</td>
            <td align='center'>" . round($total_credit_balance, 2) . "</td>
            <td align='center'>" . round($total_balance, 2) . "</td>
        </tr>";
    } else {
        $i = 0;
        for ($i; $date_from <= $date_to; $i++) {
            $tr .= "<tr> 
                <td align='center'>" . date('d-m-Y', strtotime($date_from)) . "</td>
                <td align='center'>-</td>
                <td align='center'>-</td>
                <td align='center'>-</td>
                <td align='center'>" . round($total_balance, 2) . "</td>
            </tr>";
            $date_from = date("Y-m-d", strtotime("$date_from +1 day"));
        }
        $tr .= "<tr> 
            <td colspan='2' align='right'>Total Balance</td>
            <td align='center'>" . round($total_debit_balance, 2) . "</td>
            <td align='center'>" . round($total_credit_balance, 2) . "</td>
            <td align='center'>" . round($total_balance, 2) . "</td>
        </tr>";
    }
    ?>

    <!--<div style="width:100%;text-align:center;float:left">
    <p style="margin-bottom: 0; padding-bottom: 0"><font id="heading_name">Distributor wise Transactions Report  (Customer's Ledger)</font></p>
</div>
<div>
    <table style="width:100%;text-align:left;float:left margin-bottom: 0; padding-bottom: 0; border=0; cellspacing:0; cellpadding: 0;">
        <tr>
            <td>Date From:</td>
            <td>Date To:</td>
        </tr>
        
        <tr>
            <td width="50%">Customer's ID</td>
            <td width="50%">Customer's Name :</td>
        </tr>
        <tr>
            <td width="50%">Customer's Address :</td>
            <td width="50%">Customer's Category :  Distributor</td>
        </tr>
    </table>
</div>-->
    <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
        <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
        <h3 style="margin:2px 0;">Distributor wise Transactions Report (Customer's Ledger)</h3>
        <table style="width:100%;text-align:left;float:left margin-bottom: 0; padding-bottom: 0; border=0; cellspacing:0; cellpadding: 0;">
            <tr>
                <td class="text-center">Date From: <?= $date_from_for_excel ?></td>
                <td></td>
                <td></td>
                <td class="text-center">Date To: <?= $date_to_for_excel ?></td>
            </tr>

            <tr>
                <td width="50%" class="text-center">Distributor's ID : <?php if ($dist_info['DistDistributor']['db_code']) echo $dist_info['DistDistributor']['db_code']; ?></td>
                <td></td>
                <td></td>
                <td width="50%" class="text-center">Distributor's Name :<?php if ($dist_info['DistDistributor']['name']) echo $dist_info['DistDistributor']['name']; ?></td>
            </tr>
            <tr>
                <td width="50%" class="text-center">Customer's Address :<?php if ($dist_info['DistDistributor']['address']) echo $dist_info['DistDistributor']['address']; ?></td>
                <td></td>
                <td></td>


            </tr>
        </table>
    </div>
    <div>
        <table style="width:100% font-size:11px;" border="1px solid black" cellspacing="0" text-align="center">
            <thead>
                <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">Particulars</th>
                    <th class="text-center">Debit</th>
                    <th class="text-center">Credit</th>
                    <th class="text-center">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $tr; ?>
            </tbody>
        </table>
        <br>
        <br>
    </div>
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

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>
<script>
    /*$('#office_id').selectChain({
     target: $('#name'),
     value:'name',
     url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
     type: 'post',
     data:{'office_id': 'office_id' }
     });*/
</script>

<script>
    $(document).ready(function() {

        /* $('.office_id').selectChain({
             target: $('#dist_distributor_id'),
             value:'name',
             url: '<?= BASE_URL . 'admin/doctors/get_distribute'; ?>',
             type: 'post',
             data:{'office_id': 'office_id' }
         });*/
        $('.office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/DistDistributorBalances/get_distribute'; ?>',
            type: 'post',
            data: {
                'office_id': 'office_id'
            }
        });
    });
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
        a.download = "distributor_wise_transaction_reports.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>