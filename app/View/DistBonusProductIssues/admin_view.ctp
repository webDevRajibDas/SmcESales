<?php //pr($challandetail);die();?>
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Distributor Challan Details'); ?></h3>
                <div class="box-tools pull-right">              
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Distributor Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>	
			<?php echo $this->Form->create('DistChallan', array('role' => 'form')); ?>	
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td width="25%"><strong><?php echo 'Memo No.'; ?></strong></td>
                            <td><?php echo h($challan['DistChallan']['memo_no']); ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo 'Sales Territory'; ?></strong></td>
                            <td><?php echo h($territories[$challan['SalesPerson']['territory_id']].' ('.$challan['SalesPerson']['name'].')'); ?></td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Challan Date'; ?></strong></td>
                            <td><?php if($challan['DistChallan']['challan_date'])echo $this->App->dateformat(($challan['DistChallan']['challan_date'])); ?></td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Distributor Name'; ?></strong></td>
                            <td><?php
                            
                             $d_id=$challan['DistChallan']['dist_distributor_id'];
                                    echo $distributors_all[$d_id];
                            
                            ?></td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Received Date'; ?></strong></td>
                            <td>
                                <?php 
                                if($challan['DistChallan']['status'] == 1)
                                {	
                                        echo $this->Form->input('received_date', array('type'=>'text','label'=>false, 'class' => 'form-control datepicker_range','required'=>false, 'readonly' => true));
                                }
                                else
                                {								
                                        echo $this->App->dateformat(($challan['DistChallan']['received_date'])); 
                                }
                                ?>							
                            </td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Status'; ?></strong></td>
                            <td align="left">
                            <?php
                                if ($challan['DistChallan']['status'] == 1) {
                                    echo '<span class="btn btn-warning btn-xs">Pending</span>';
                                }elseif ($challan['DistChallan']['status'] == 2) {
                                    echo '<span class="btn btn-success btn-xs">Received</span>';
                                }else{
                                    echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
                                }
                            ?>
                        </td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Remarks'; ?></strong></td>
                            <td><?php echo h($challan['DistChallan']['remarks']); ?></td>
                        </tr>						
                </table>
            </div>	
            <div class="box-body">
                <div class="table-responsive">  
                <table class="table table-bordered">
                    <tbody>
                        <tr>        
                            <th class="text-center">SL.</th>
                            <th class="text-center">Bonus Product</th>
                            <!-- <th class="text-center">Unit</th> -->
                            <th class="text-center">Bonus Quantity</th>
                            <!-- <th class="text-center">Remarks</th>   -->                          
                        </tr>
                            <?php
                            if(!empty($challandetail))
                            {
                                $sl = 1;
                                $total_quantity = 0;
                                $total_received_quantity = 0;
                                foreach($challandetail as $val){
                                    if($val['DistChallanDetail']['price'] != 0) continue;
                            ?>
                        <tr>        
                            <td align="center"><?php echo $sl; ?></td>
                            <td><?php echo $val['Product']['name']; ?></td>
                            <!-- <td><?php //echo $val['MeasurementUnit']['name']; ?></td> -->
                            <td align="center"><?php echo $val['DistChallanDetail']['challan_qty']; ?></td>
                            
                                <?php
                                if($challan['DistChallan']['status'] == 1){
                                ?>
                                <input type="hidden" name="id[]" value="<?php echo $val['DistChallanDetail']['id']; ?>"/>
                                <input type="hidden" name="product_id[]" value="<?php echo $val['DistChallanDetail']['product_id']; ?>"/>
                                <input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['DistChallanDetail']['measurement_unit_id']; ?>"/>
                                <input type="hidden" name="quantity[]" value="<?php echo $val['DistChallanDetail']['challan_qty']; ?>"/>
                                <input type="hidden" name="batch_no[]" value="<?php echo $val['DistChallanDetail']['batch_no']; ?>"/>
                                <input type="hidden" name="expire_date[]" value="<?php echo $val['DistChallanDetail']['expire_date']; ?>"/>
                                <input type="hidden" class="full_width form-control" name="receive_quantity[]" value="<?php echo $val['DistChallanDetail']['challan_qty']; ?>" readonly/>
                                    <?php
                                }
                                ?>
                           
                            <!-- <td><?php //echo $val['DistChallanDetail']['remarks']; ?></td> -->
                        </tr>
                                <?php
                                        $total_quantity = $total_quantity + $val['DistChallanDetail']['challan_qty'];
                                        $total_received_quantity = $total_received_quantity + $val['DistChallanDetail']['received_qty'];

                                        $sl++;
                                        }                           
                                }

                                ?>  
                        <!-- <tr>       
                                <td align="right" colspan="4"><strong>Total Quantity :</strong></td>
                                <td align="center"><?php echo $total_quantity; ?></td>
                                <td align="center"></td>
                                <td align="center">
                                <?php
                                        if($challan['DistChallan']['status'] == 2){ echo $total_received_quantity; }
                                ?>                          
                                </td>   
                                <td align="center"></td>
                        </tr> -->
                </table>
                </div>
            </div>	
			<?php if($challan['DistChallan']['status'] == 1){ ?>
                	<?php echo $this->Form->submit('Received', array('class' => 'btn btn-large btn-primary')); ?>
			<?php } ?>
			<?php echo $this->Form->end(); ?>
            </br>
        </div>

    </div>
</div>
</div>


<style>
.datepicker table tr td.disabled, .datepicker table tr td.disabled:hover {
    color: #c7c7c7;
}
</style>
<?php
$todayDate = date('Y-m-d');
$startDate = date('d-m-Y', strtotime($challan['DistChallan']['challan_date']));
$endDateOfMonth = date('Y-m-t', strtotime($startDate));

if(strtotime($todayDate) < strtotime($endDateOfMonth) ){
	$endDate = date('d-m-Y');
}else{
	$endDate = date('t-m-Y', strtotime($startDate));
}
?>

<div style="display:none;">
<style>
    .draft{
        padding: 0px 15px;
    }
</style>
<script>
    $(document).ready(function () {
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
