<script>
var users='<?php echo json_encode($users);?>';
</script>


<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Distributors Balance'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorBalance', array('role' => 'form')); ?>
                
                <div class="form-group">
                   <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                   }
                   else
                   {
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','required' => TRUE, 'options' => $offices));
                   }
                   ?>
               </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributors :','id'=>'dist_distributor_id','class' => 'form-control dist_distributor_id','empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('balance_transaction_type_id', array('label'=>'Transaction Type :','id'=>'balance_transaction_type_id','class' => 'form-control balance_transaction_type_id','empty' => '---- Select ----')); ?>
                    <?php echo $this->Form->input('balance_inout', array('id'=>'balance_inout','class' => 'balance_inout form-control', 'type'=>'hidden')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('balance', array('label'=>'Deposits Amount :','id'=>'balance','class' => 'form-control', 'type'=>'number', 'step'=>'any')); ?>
                </div>
                
                <!-- <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                    </div>     -->	
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<?php 
$startDate = date('d-m-Y', strtotime('0 day'));
?>

<script>
$(document).ready(function () {
	
    
	$('.office_id').selectChain({
		target: $('.dist_distributor_id'),
		value:'name',
		url: '<?=BASE_URL.'admin/DistDistributorBalances/get_distribute';?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
});
</script>

<script>

    
/*Challan Datepicker : End*/

    $(document).ready(function () {
        $(document).on("change", "#dist_distributor_id", function () {
           var distributor_id = $(this).val();
            if(distributor_id)
            {
               $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributorBalances/get_balance_transaction_type' ?>',
                    data: {'dist_distributor_id': distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        console.log(data);
                        $('.balance_transaction_type_id').html(data);     
                    }
                });
            }
        });
        $(document).on("change", "#balance_transaction_type_id", function () {
            var transaction_type_id = $(this).val();
            var distributor_id = $('#dist_distributor_id').val();
            
            $.ajax({
                url: '<?= BASE_URL . 'admin/DistDistributorBalances/get_last_balance_ammount' ?>',
                data: {'dist_distributor_id': distributor_id,'transaction_type_id':transaction_type_id},
                type: 'POST',
                success: function (data)
                {
                    console.log(data);
                    var obj = JSON.parse(data);
                    var balance_inout = obj.balance_inout;
                    if(balance_inout == 1){
                        $('#balance').attr('max',obj.balance);
                    }   
                    $('#balance_inout').val(balance_inout);     
                }
            });
            
        });
    });
</script>