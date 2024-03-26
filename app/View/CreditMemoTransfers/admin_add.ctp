<?php //echo 'done';?><div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
               
                <div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('CreditMemoTransfers', array('role' => 'form')); ?>
                <div class="form-group">
				<?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control','empty'=>'---- Select Receiver Store ----','options'=>$receiver_store,'required'=>true)); ?>
                </div>
                
                <div class="form-group">
				<?php echo $this->Form->input('challan_referance_no', array('class' => 'form-control challan_referance_no','onBlur' => 'challanReferance()', 'required'=>true)); ?>
                </div>
                
                <div class="form-group">
				<?php echo $this->Form->input('challan_date', array('type'=>'text','class' => 'form-control challan-datepicker','required'=>true, 'readonly' => true)); ?>
                </div>
                <div class="form-group">
				<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
                </div>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Type</th>
                            <th>Product Name</th>
                            <th class="text-center">Batch No.</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Expire Date</th>
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_type', array('label'=>false,'id'=>'product_type', 'class' => 'full_width form-control product_type','empty'=>'---- Select Product Type ----')); ?>
                            </td>
                            <td>
							<?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'full_width form-control product_id chosen','id'=>'product_id','empty'=>'---- Select Product ----')); ?>
                            </td>
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('batch_no', array('label'=>false, 'class' => 'full_width form-control batch_no')); ?>
                            </td>
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('challan_qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
                            </td>
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('expire_date', array('label'=>false, 'class' => 'full_width form-control expire_date expireDatepicker')); ?>
                            </td>
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
                        </tr>				
                    </tbody>
                </table>	
                
                <div class="table-responsive">		
                <table class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center" width="12%">Unit</th>
                            <th class="text-center" width="12%">Batch No.</th>
                            <th class="text-center" width="10%">Quantity</th>
                            <th class="text-center" width="10%">Total Quantity</th>
                            <th class="text-center" width="12%">Expire Date</th>
                            <th class="text-center" width="15%">Remarks</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                    </thead>					
                </table>
                </div>
                
                </br>
<div class="pull-right">
            <?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save', 'disabled')); ?>
            <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
            </div>
			<?php echo $this->Form->end(); ?>
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
$startDate = date('d-m-Y', strtotime('-1 day'));
?>
<script>
/*Challan Datepicker : Start*/
$(document).ready(function () {
	var today = new Date(new Date().setDate(new Date().getDate()));
	$('.challan-datepicker').datepicker({
		startDate: '<?php echo $startDate; ?>',
		format: "dd-mm-yyyy",
		autoclose: true,
		todayHighlight: true,
		endDate: today
	});
});
/*Challan Datepicker : End*/


</script>
