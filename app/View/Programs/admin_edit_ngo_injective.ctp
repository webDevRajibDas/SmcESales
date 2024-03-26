<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Deassigned NGO For Injectable Outlet'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> NGO For Injectable Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php 			
			$deassigned_date = ($this->request->data['Program']['deassigned_date']!=NULL ? date('d-m-Y',strtotime($this->request->data['Program']['deassigned_date'])) : '');
			echo $this->Form->create('Program', array('role' => 'form')); 			
			?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
                <div class="form-group" style="display:none;">
                    <?php echo $this->Form->input('outlet_id', array('type'=>'text', 'class' => 'form-control')); ?>
				</div>
				<div class="form-group required">
					<?php echo $this->Form->input('deassigned_date', array('type'=>'text', 'class' => 'form-control datepicker','value'=>$deassigned_date, 'required'=>true)); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('reason', array('id'=>'reason', 'class' => 'form-control','empty'=>'---- Select ----', 'options' => $reasons, 'required'=>false)); ?>
				</div>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>