
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Credit Memo Transfer'); ?></h3>
                <div class="box-tools pull-right">
                
	            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Credit Memo Transfer List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
            
            </div>	
			<?php echo $this->Form->create('CreditMemoTransfer', array('role' => 'form')); ?>	
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td width="25%"><strong><?php echo 'Memo No.'; ?></strong></td>
                            <td><?php echo h($SoCreditCollection['SoCreditCollection']['memo_no']); ?></td>
                        </tr>
                        <tr>		
                            <td width="25%"><strong><?php echo 'Area Office.'; ?></strong></td>
                            <td><?php echo h($SoCreditCollection['Office']['office_name']); ?></td>
                        </tr>
                        
                       
                        <tr>		
                            <td><strong><?php echo 'Memo Date'; ?></strong></td>
                            <td><?php echo h($SoCreditCollection['SoCreditCollection']['date']); ?></td>
                        </tr>

                        <tr>		
                            <td><strong><?php echo 'Trasfer From'; ?></strong></td>
                            <td><?php echo h($SoCreditCollection['Territory']['name']); ?></td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo 'Sales Person'; ?></strong></td>
                            <td><?php echo h($SoCreditCollection['SalesPerson']['name']); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php echo 'Transfer To Territory'; ?></strong></td>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'label'=>false, 'class' => 'form-control territory_id', 'required'=>false, 'empty'=>'---- Select Territory ----')); ?></td>
									
						</tr>
                   
                    						
                </table>
            </div>			

            <!-- <div class="box-body">
            	<div class="table-responsive">	
                <table class="table table-bordered">
                    <tbody>
                        <tr>		
                            <th class="text-center">Area Office</th>
                            <th class="text-center">Territory</th>
                            <th class="text-center">Memo No</th>
                            <th class="text-center">Memo Created By</th>
                           							
                        </tr>
                           
                        <tr>		
                            
                            <td align="center"><?php echo h($SoCreditCollection['Office']['office_name']); ?></td>
                            <td align="center"><?php echo h($SoCreditCollection['Territory']['name']); ?></td>
                            <td align="center"><?php  echo h($SoCreditCollection['SoCreditCollection']['memo_no']); ?></td>
                            <td align="center"><?php echo h($SoCreditCollection['SalesPerson']['name']); ?></td>
                        
                        </tr>
                                
                </table>
                </div>
            </div>	 -->
			
                <?php echo $this->Form->submit('Transfer', array('class' => 'btn btn-large btn-primary')); ?>
                
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
$startDate = date('d-m-Y', strtotime($challan['Challan']['challan_date']));
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
