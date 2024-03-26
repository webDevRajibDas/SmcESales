<?php 
//pr($this->request->data);die();
$sp_name = $this->request->data['SalesPerson']['name'];
$submit = 0;
if(!empty($exitingInfo)){
    if($exitingInfo['AeSoMapping']['is_assign'] == 1){
        $message = '<div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            This So already assigned. Assign Date : '. $exitingInfo['AeSoMapping']['assign_date'] .'
        </div>';
        $submit = 1;
        $this->request->data['SalesPerson']['ae_id'] = $exitingInfo['AeSoMapping']['ae_id'];
    }

}

?>
<div class="row">
    <div class="col-md-12">
		<?php echo $message; ?>
		<div class="box box-primary">			
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Ae Assing'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">		
			<?php echo $this->Form->create('SalesPerson'); ?>
				<?php echo $this->Form->input("id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
               
                <div class="form-group">
                <?php echo $this->Form->input('sales_person_name', array('id' => 'name', 'class' => 'form-control','value'=>$sp_name,'readonly'=>true,'disable'=>true)); ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('ae_id', array('id' => 'ae_id', 'class' => 'form-control', 'empty'=>'---- Select ----','options'=>$ae_list, 'required'=>true)); ?>
                </div>
                <?php if($submit == 0){ ?>		
				<?php echo $this->Form->submit('Assign to Ae', array('class' => 'btn btn-large btn-primary')); ?>
                <?php } ?>
			<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>


<script>
$(document).ready(function(){
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});	
	
	
});
</script>