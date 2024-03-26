<?php 
$sp_name = $this->request->data['SalesPerson']['name'];
?>

<div class="row">
    <div class="col-md-12">
		<?php echo $message; ?>
		<div class="box box-primary">			
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('De-assigned Territory'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> User List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">		
			<?php echo $this->Form->create('SalesPerson'); ?>
				<?php echo $this->Form->input("id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
				<?php echo $this->Form->input("office_id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
				<?php echo $this->Form->input("territory_id" ,array('type' => 'hidden', 'label' => false,'div' => false))?>
				<div class="form-group">
				<?php echo $this->Form->input('sales_person_name', array('id' => 'name', 'class' => 'form-control','value'=>$sp_name,'readonly'=>true,'disable'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('SalesPerson.date', array('class' => 'form-control datepicker','required'=>true)); ?>
				</div>				
				<?php echo $this->Form->submit('De-assigned', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>
