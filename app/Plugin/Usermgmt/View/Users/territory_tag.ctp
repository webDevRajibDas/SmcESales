<?php 
//pr($this->request->data);die();
$sp_name = $this->request->data['SalesPerson']['name'];
?>
<div class="row">
    <div class="col-md-12">
		<?php echo $message; ?>
		<div class="box box-primary">			
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Territory'); ?></h3>
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
                <?php if($office_parent_id){ ?>
					<?php echo $this->Form->input("office_id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
                <?php }else{ ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty'=>'---- Select ----','options'=>$offices, 'required'=>true)); ?>
                    </div>
                <?php } ?>
                
				<div class="form-group">
					<?php echo $this->Form->input('SalesPerson.territory_id', array('id' => 'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','options'=>$territorys,'required'=>true)); ?>
				</div>
                				
				<?php echo $this->Form->submit('Assign to Territory', array('class' => 'btn btn-large btn-primary')); ?>
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