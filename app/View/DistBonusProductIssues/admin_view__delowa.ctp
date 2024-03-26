<style>      
#content { display: none; } 
</style>

<div class="row">
    <div class="col-xs-12">     
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Product Issue Details'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>                                            
                    <button type="button" onclick="PrintElem('content')" class="btn btn-primary">
                        <i class="glyphicon glyphicon-print"></i> Print
                    </button>
                </div>
            </div>          
            <div class="box-body">

             <table class="table table-bordered table-striped" border="1px solid black">
                <tbody>
                    <tr>        
                        <td width="25%"><strong><?php echo 'Challan No.'; ?></strong></td>
                        <td><?php echo h($challan['Challan']['challan_no']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Transaction Type'; ?></strong></td>
                        <td><?php echo h($challan['TransactionType']['name']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Sender Store'; ?></strong></td>
                        <td><?php echo h($challan['SenderStore']['name']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Challan Date'; ?></strong></td>
                        <td><?php echo $this->App->dateformat($challan['Challan']['challan_date']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Receiver Store'; ?></strong></td>
                        <td><?php echo h($challan['ReceiverStore']['name']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Received Date'; ?></strong></td>
                        <td><?php echo $this->App->dateformat($challan['Challan']['received_date']); ?></td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Status'; ?></strong></td>
                        <td align="left">
                            <?php
                            if ($challan['Challan']['status'] == 1) {
                                echo '<span class="btn btn-warning btn-xs">Pending</span>';
                            }elseif ($challan['Challan']['status'] == 2) {
                                echo '<span class="btn btn-success btn-xs">Received</span>';
                            }else{
                                echo '<span class="btn btn-primary btn-xs draft_size">Draft</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>        
                        <td><strong><?php echo 'Remarks'; ?></strong></td>
                        <td><?php echo h($challan['Challan']['remarks']); ?></td>
                    </tr>                       
                </table>
            </div>

            <div class="box-body">
            	<div class="table-responsive">	
                <table class="table table-bordered">
                    <tr>        
                        <th class="text-center">SL.</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Batch No.</th>
                        <th class="text-center">Expire Date</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Quantity</th>                           
                        <th class="text-center">Remarks</th>                            
                    </tr>
                    <?php
                    if(!empty($challandetail))
                    {
                        $sl = 1;
                        $total_quantity = 0;
                        $total_received_quantity = 0;
                        foreach($challandetail as $val){
                            ?>
                            <tr>        
                                <td align="center"><?php echo $sl; ?></td>
                                <td><?php echo $val['Product']['name']; ?></td>
                                <td align="center"><?php echo $val['ChallanDetail']['batch_no']; ?></td>
                                <td align="center"><?=$val['ChallanDetail']['expire_date']==' '?'':$this->App->expire_dateformat($val['ChallanDetail']['expire_date']); ?></td>
                                <td><?php echo $val['MeasurementUnit']['name']; ?></td>
                                <td align="center"><?php echo $val['ChallanDetail']['challan_qty']; ?></td>
                                <td><?php echo $val['ChallanDetail']['remarks']; ?></td>
                            </tr>
                            <?php
                            $total_quantity = $total_quantity + $val['ChallanDetail']['challan_qty'];
                            
                            $sl++;
                        }                           
                    }
                    ?>                              
                </table>
                </div>
            </div>          
        </div>

    </div>





</div>



<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;margin-top: 50px; font-size: 11px;">
    <style type="text/css">
      @media print
      {
        #non-printable { display: none; }
        #content { 
            display: block;
        }
        table{
            width:100%;
            font-size: 11px;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        footer{
            position: fixed;
            bottom: 0;
            font-size: 10px;
        }
        .footer1{
            width:100%;
            height: 100px;
            position: absolute;
            font-size: 10px;
            overflow-y: inherit;
        }
        
        .font_size{
            font-size: 11px;
        }
        .page-break{
            page-break-after: always;
        }
        #heading_name{
            font-size: 24px;
        }
        #heading_add{
            font-size: 18px;
        }
        .page_header{
            width:100%;
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
    @page {size: auto;  margin: 30px; }
}

</style>
   <!--  <div style="width: 100%; height:30px;float: right;font-size: 11px;">
        <div style="text-align:right;width:100%;">Page No :1 Of 1</div>
        <div style="text-align:right;width:100%;">Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?></div>0
    </div> -->
    <?php
    if(!empty($challandetail))
    {
        $sl = 1;
        $in=1;
        $total_quantity = 0;
        $total_received_quantity = 0;
        $data=array();
        $tr='';
        foreach($challandetail as $val){
            $tr.='<tr>      
            <td align="center">'.$sl.'</td>            
            <td>'.$val['Product']['name'].'</td>
            <td align="center">'.$val['ChallanDetail']['batch_no'].'</td>
            <td align="center">'.$val['ChallanDetail']['expire_date'].'</td>
            <td>'.$val['ChallanDetail']['source'].'</td>
            <td>'.$val['MeasurementUnit']['name'].'</td>
            <td align="center">'.$val['ChallanDetail']['challan_qty'].'</td>
            <td>'. $val['ChallanDetail']['remarks'].'</td>
        </tr>';

        $total_quantity = $total_quantity + $val['ChallanDetail']['challan_qty'];

        $sl++;
        if($in==15){
            $in=1;
            $data[]=$tr;
            $tr='';
        }
        $in++;
    }
    $data[]=$tr;
}
$total_page=count($data); 
?>
<div class="page_header">
Page No :<?php echo '1 Of '.$total_page;?><br>
    Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
</div>
<div style="width: 100%;height:30px;margin-top: 10px">
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

<div style="width:100%;">
    <div style="width:25%;text-align:left;float:left">
        &nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <div style="width:50%;text-align:center;float:left">
        <font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
        <span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
        <font><b><u>Delivery Challan</u></b></font><br>
        <font><b>Issueing Office : <?php echo h($so_info['Office']['office_name']); ?></b></font>
    </div>
    <div style="width:25%;text-align:right;float:left">
        &nbsp;&nbsp;&nbsp;&nbsp;
    </div>        
</div>

<div style="width:100%;">
    <div style="width:50%;text-align:left;float:left">
        <?php echo "To : ". h($so_info['SalesPerson']['name']); ?><br>
        <span style="margin-left: 25px;"><?=$so_info['st']['address']?></span>
    </div>

    <div style="width:50%;text-align:right;float:left">
        Challan No : <?php echo h($challan['Challan']['challan_no']); ?> <br>
        Date : <?php echo $this->App->dateformat($challan['Challan']['challan_date']); ?>
    </div>        
</div>
<?php $page_count=1; foreach($data as $data){?>
<?php if($page_count>1){?><div class="page_header">
Page No :<?php echo $page_count.' Of '.$total_page;?><br>
Print Date :<?php echo $this->App->dateformat(date('Y-m-d H:i:s'));?>
</div><?php }?>
<table style="width:100% font-size:11px; <?php if($page_count>1){echo 'margin-top: 15px';}?>" border="1px solid black"  cellspacing="0" text-align="center">
    <thead>
        <tr>
            <th>SL #</th>            
            <th>Product/Item</th>
            <th>Lot/Batch #</th>
            <th>Exp. Date</th> 
            <th>Source</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Remarks</th>
        </tr>
    </thead>  
    <tbody>
        <?php echo $data;?>
    </tbody> 
</table>
<div class="footer1">
    <div style="width:100%;padding-top:100px;" class="font_size">
        <div style="width:33%;text-align:left;float:left">
            Prepared by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;<?=$user_name['User']['username']?></span>
        </div>
        <div style="width:30%;text-align:center;float:left;margin-left: 3%;">
            Checked by:______________ 
        </div>
        <div style="width:33%;float:left">
            Carried by:<span style="border-bottom: 1px solid black;width: 70%;display: inline-block;">&nbsp;<?=$challan['Challan']['carried_by']?></span><br><br>
            Truck No:&nbsp;<span style="border-bottom: 1px solid black;width: 70%;display: inline-block"><?=$challan['Challan']['truck_no']?></span>
        </div>        
    </div>
    
    <div style="width:100%;" class="font_size">
        <div style="width:53%;text-align:left;float:left">
            <h4> Received the goods for Delivery </h4>
            Driver's Signature :______________________ <br><br>
            Name of the Driver :<span style="border-bottom: 1px solid black;width: 30%;display: inline-block;text-align:center;"> <?=$challan['Challan']['driver_name']?></span>
            <br><br><br>
            <h4> Approved by </h4>
            Signature :_____________________________ <br><br>
            Name :________________________________  <br> <br>
            Designation :___________________________ <br> <br>
        </div>

        <div style="width:33%;text-align:left;float:right;margin-right: 3px;">
            <h4> Received the goods in good condition: </h4>
            Signature of the Recipient:___________________ <br> <br>                            
            Name :___________________________________  <br><br>
            Designation :______________________________  <br><br>
            Date :____________________________________ <br><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            (Seal)&nbsp;&nbsp;&nbsp;&nbsp;
            <br><br><br><br><br><br><br><br>
        </div>        
    </div></div>
    <br><br><br><br>
    <footer style="width:100%;text-align:center;">
        "This Report has been generated from SMC Automated Sales System at [<?php echo h($so_info['Office']['office_name']); ?>]. This information is confidential and for internal use only."
    </footer>
    <div class="page-break"></div>

    <?php $page_count++; }?>
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