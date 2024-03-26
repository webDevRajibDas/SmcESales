<?php 
// pr($offices);die();
$TransactionReports = new DistDistributorTransactionByOfficeReportsController();
//$office_parent_id = $this->UserAuth->getOfficeParentId();
//$office_id = $this->UserAuth->getOfficeId();
?>
<style>      
#content { display: none; } 
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Office Wise Distributor Transaction Reports'); ?></h3>
                <div class="box-tools pull-right">
                   <!--<button type="button" onclick="PrintElem('content')" class="btn btn-info">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </button> -->
               </div>
            </div>  
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorBalance', array('role' => 'form',)); ?>
                    <table class="search">
                        
                        <tr>
                            
                            <td>
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required' => 'required')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required' => 'required')); ?>
                            </td>
                        </tr>
                        
                        <tr>
							<?php if($office_parent_id == 0){?>
							<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => 'required', 'empty' => '---- Select Region ----','options'=>$region_offices)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td>
							<?php }else{?>
								<td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id2', 'class' => 'form-control region_office_id2', 'required' => 'required','type'=>'hidden','value'=>$region_office_id)); ?></td>
								<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id2', 'class' => 'form-control office_id2', 'required' => false, 'type'=>'hidden','value'=>$office_id)); ?></td>
							<?php }?>
                        </tr>
                     
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php //echo $this->Form->button('Download', array('id'=>'download','type' => 'button', 'class' => 'btn btn-large btn-info', 'onclick'=>"PrintElem('content')",'escape' => false)); ?>
								<?php if (isset($distributor_list_arr) || isset($offices_list)) {
                                        ?>
                                        <a class="btn btn-success" id="download_xl">Download XL</a>
                                        <?php
                                    }?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
            <?php if($office_set==2){ ?>
                <?php if(isset($distributor_list_arr)){ //here start the report for view
                    $total_opening_balance = 0;
					$total_debit_balance = 0;
					$total_credit_balance = 0;
					$total_closing = 0;
                ?>
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr> 
                            
                            <th class="text-center">Distributos</th>
                            <th class="text-center">Opening Balance</th>     
                            <th class="text-center">Debit</th>               
                            <th class="text-center">Credit</th>
                            <th class="text-center">Balance</th> 
                        </tr>
                        <?php foreach ($distributor_list_arr as $key => $value) { 
                            echo '<tr>';
                            echo '<td>'.$value[0]['db'].'</td>';
                            echo '<td>';
                            if(isset($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'])){
                                $opening_balance = round($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'],2);
                                echo $opening_balance;
                            }else{
                                $opening_balance=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit'])){
                                $debit = round($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit'],2);
                                echo $debit;
                            }else{
                                $debit=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit'])){
                                $credit = round($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit'],2);
                                echo $credit;
                            }else{
                                $credit=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit']) || isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit']) || isset($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'])){
                                $closing = round($opening_balance + ($debit-$credit),2);
                                echo $closing;
                            }else{
                                $closing=0;
                                echo '';
                            }
                            echo '</td>';
                            
                            
                            echo '</tr>';
                            $total_opening_balance += $opening_balance;
                            $total_debit_balance += $debit;
                            $total_credit_balance += $credit;
                            $total_closing += $closing;

                        } ?>
                        <tr>
								<td align="center">Total Balance</td>
                                <td align="center"><?php if($total_opening_balance!=0)echo round($total_opening_balance, 2);else echo ''; ?></td>
                                <td align="center"><?php if($total_debit_balance!=0)echo round($total_debit_balance, 2);else echo ''; ?></td>
								<td align="center"><?php if($total_debit_balance!=0)echo round($total_credit_balance, 2);else echo ''; ?></td>
                                <td align="center"><?php if($total_closing!=0)echo round($total_closing, 2);else echo ''; ?></td>
                        </tr>
                        
                        
                    </tbody>
                </table>
                <?php }?>
            <?php }else if($office_set==1 ){//for region office ?>

                <?php if(isset($offices_list)){ //here start the report for view
                    $total_opening_balance = 0;
					$total_debit_balance = 0;
					$total_credit_balance = 0;
					$total_closing = 0;
                ?>
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr> 
                            
                            <th class="text-center">Office</th>
                            <th class="text-center">Opening Balance</th>     
                            <th class="text-center">Debit</th>               
                            <th class="text-center">Credit</th>
                            <th class="text-center">Balance</th> 
                        </tr>
                        <?php foreach ($offices_list as $key => $value) { 
                            echo '<tr>';
                            echo '<td>'.$value.'</td>';
                            echo '<td>';
                            if(isset($opening_balance_store[$key]['opening_balance'])){
                                $opening_balance = round($opening_balance_store[$key]['opening_balance'],2);
                                echo $opening_balance;
                            }else{
                                $opening_balance=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['debit'])){
                                $debit = round($debit_credit_balance_store[$key]['debit'],2);
                                echo $debit;
                            }else{
                                $debit=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['credit'])){
                                $credit = round($debit_credit_balance_store[$key]['credit'],2);
                                echo $credit;
                            }else{
                                $credit=0;
                                echo '';
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['debit']) || isset($opening_balance_store[$key]['opening_balance'])||isset($debit_credit_balance_store[$key]['credit'])){
                                $closing = $opening_balance + ($debit-$credit);
                                echo $closing;
                            }else{
                                $closing=0;
                                echo '';
                            }
                            echo '</td>';
                            
                            
                            echo '</tr>';
                            $total_opening_balance += $opening_balance;
                            $total_debit_balance += $debit;
                            $total_credit_balance += $credit;
                            $total_closing += $closing;

                        } ?>
                        <tr>
								<td align="center">Total Balance</td>
                                <td align="center"><?php if($total_opening_balance!=0)echo round($total_opening_balance, 2);else echo ''; ?></td>
                                <td align="center"><?php if($total_debit_balance!=0)echo round($total_debit_balance, 2);else echo ''; ?></td>
								<td align="center"><?php if($total_credit_balance!=0)echo round($total_credit_balance, 2);else echo ''; ?></td>
                                <td align="center"><?php if($total_closing!=0)echo round($total_closing, 2);else echo ''; ?></td>
                        </tr>
                        
                        
                    </tbody>
                </table>
                <?php }?>

                <?php } ?>
            
            </div>          
        </div>
    </div>
</div>
<!-- excel part -->
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

            table, th, td {
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
        <div style="text-align:right;width:100%;">Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?></div>0
    </div> -->
    

<div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
	<h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
	<h3 style="margin:2px 0;">Office Wise Distributor Transactions Report</h3>
	<table style="width:100%;text-align:left;float:left margin-bottom: 0; padding-bottom: 0; border=0; cellspacing:0; cellpadding: 0;">
        <tr>
            <td class="text-center">Date From: <?=$date_from?></td>
			<td></td>
			<td></td>
            <td class="text-center">Date To: <?=$date_to?></td>
        </tr>

        <tr>
            <td class="text-center">Region Office: <?= $region_offices[$this->request->data['DistDistributorBalance']['region_office_id']]?></td>
			<td></td>
			<td></td>
            <?php if($office_set==2){?>
            <td class="text-center">Office Name: <?=$offices[$this->request->data['DistDistributorBalance']['office_id']]?></td>
            <?php } ?>
        </tr>
        
        
    </table>
</div>
<div>
    <table  style="width:100% font-size:11px;" border="1px solid black"  cellspacing="0" text-align="center">
        <thead>
            <tr>        
                
            <?php if($office_set==2){ ?> 
                <th class="text-center">Distributos</th>
			<?php }else{ ?>	
                <th class="text-center">Office</th>
                <?php } ?>
                <th class="text-center">Opening Balance</th>     
                <th class="text-center">Debit</th>               
                <th class="text-center">Credit</th>
                <th class="text-center">Balance</th>                
            </tr>
        </thead>  
        <tbody>
            <?php if($office_set==2 ){ //no specific office?>
            <?php  if(isset($distributor_list_arr)){ //this for excel part
                        $total_opening_balance = 0;
                        $total_debit_balance = 0;
                        $total_credit_balance = 0;
                        $total_closing = 0;
                    
                    foreach ($distributor_list_arr as $key => $value) {
                        echo '<tr>';
                        echo '<td>'.$value[0]['db'].'</td>';
                        echo '<td>';
                        if(isset($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'])){
                            $opening_balance = round($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'],2);
                            echo $opening_balance;
                        }else{
                            $opening_balance=0;
                            echo $opening_balance;
                        }
                        echo '</td>';

                        echo '<td>';
                        if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit'])){
                            $debit = round($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit'],2);
                            echo $debit;
                        }else{
                            $debit=0;
                            echo $debit;
                        }
                        echo '</td>';

                        echo '<td>';
                        if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit'])){
                            $credit = round($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit'],2);
                            echo $credit;
                        }else{
                            $credit=0;
                            echo $credit;
                        }
                        echo '</td>';

                        echo '<td>';
                        if(isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['credit']) || isset($debit_credit_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['debit']) || isset($opening_balance_store[$value[0]['office_id']][$value[0]['db_ids']]['opening_balance'])){
                            $closing = $opening_balance + ($debit-$credit);
                            echo $closing;
                        }else{
                            $closing=0;
                            echo $closing;
                        }
                        echo '</td>';
                        echo '</tr>';
                        $total_opening_balance += $opening_balance;
                        $total_debit_balance += $debit;
                        $total_credit_balance += $credit;
                        $total_closing += $closing;
                    } ?>
                    <tr>
                            <td >Total Balance</td>
                            <td ><?php echo round($total_opening_balance, 2); ?></td>
                            <td ><?php echo round($total_debit_balance, 2); ?></td>
                            <td ><?php echo round($total_credit_balance, 2); ?></td>
                            <td ><?php echo round($total_closing, 2); ?></td>
                    </tr>
                <?php }?>
            <?php }else if($office_set==1 ){ ?>
                <?php if(isset($offices_list)){ //here start the report for excel view
                    $total_opening_balance = 0;
					$total_debit_balance = 0;
					$total_credit_balance = 0;
					$total_closing = 0;
                ?>
                
                       
                        <?php foreach ($offices_list as $key => $value) { 
                            echo '<tr>';
                            echo '<td>'.$value.'</td>';
                            echo '<td>';
                            if(isset($opening_balance_store[$key]['opening_balance'])){
                                $opening_balance = round($opening_balance_store[$key]['opening_balance'],2);
                                echo $opening_balance;
                            }else{
                                $opening_balance=0;
                                echo $opening_balance;
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['debit'])){
                                $debit = round($debit_credit_balance_store[$key]['debit'],2);
                                echo $debit;
                            }else{
                                $debit=0;
                                echo $debit;
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['credit'])){
                                $credit = round($debit_credit_balance_store[$key]['credit'],2);
                                echo $credit;
                            }else{
                                $credit=0;
                                echo $credit;
                            }
                            echo '</td>';

                            echo '<td>';
                            if(isset($debit_credit_balance_store[$key]['credit']) || isset($debit_credit_balance_store[$key]['debit']) || isset($opening_balance_store[$key]['opening_balance'])){
                                $closing = $opening_balance + ($debit-$credit);
                                echo $closing;
                            }else{
                                $closing=0;
                                echo $closing;
                            }
                            echo '</td>';
                            
                            
                            echo '</tr>';
                            $total_opening_balance += $opening_balance;
                            $total_debit_balance += $debit;
                            $total_credit_balance += $credit;
                            $total_closing += $closing;

                        } ?>
                        <tr>
								<td >Total Balance</td>
                                <td ><?php echo round($total_opening_balance, 2); ?></td>
                                <td ><?php echo round($total_debit_balance, 2); ?></td>
								<td ><?php echo round($total_credit_balance, 2); ?></td>
                                <td ><?php echo round($total_closing, 2); ?></td>
                        </tr>
                        
                        
                    
                <?php }?>


    <?php } ?>
        </tbody> 
    </table>
    <br>
    <br>
</div>
</div>
<script>
    function PrintElem(elem)
    {
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
$(document).ready(function () {

    $('.region_office_id').selectChain({
        target: $('#office_id'),
        value:'name',
        url: '<?=BASE_URL.'DistDistributorTransactionByOfficeReports/get_office_list';?>',
        type: 'post',
        data:{'region_office_id': 'region_office_id' }
    });
    /*$('.office_id').selectChain({
        target: $('#dist_distributor_id'),
        value:'name',
        url: '<?=BASE_URL.'admin/DistDistributorBalances/get_distribute';?>',
        type: 'post',
        data:{'office_id': 'office_id' }
    });*/
});
$("#download_xl").click(function (e) {
        e.preventDefault();
        var html = $("#content").html();
        // console.log(html);
        var blob = new Blob([html], {type: 'data:application/vnd.ms-excel'});
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "office_wise_distributor_transaction_reports.xls";
        document.body.appendChild(a);
        a.click();
    });
</script>