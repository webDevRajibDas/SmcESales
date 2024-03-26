<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Report Esales Setting'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Esales Setting List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('ReportEsalesSetting', array('role' => 'form')); ?>
                
                <div class="form-group">
					<?php echo $this->Form->input('type', array('id'=>'type', 'class' => 'form-control', 'empty' => '---- Select Type ----', 'options' => $type_list, 'required'=>true)); ?>
				</div>
                                
				<div class="form-group">
					<?php echo $this->Form->input('name', array('label'=>'Rank :', 'id'=>'name', 'class' => 'form-control')); ?>
				</div>
                
                <div class="form-group">
					<?php echo $this->Form->input('operator_1', array('label'=>'Start Operator :', 'class' => 'form-control', 'empty' => '---- Select Operator ----', 'options' => $operator_list, 'required'=>false));  ?>
				</div>
                                
				<div class="form-group">
					<?php echo $this->Form->input('range_start', array('label'=>'Start :', 'class' => 'form-control', 'type' => 'number', 'required'=>false)); ?>
				</div>
                
                <div class="form-group">
					<?php echo $this->Form->input('operator_2', array('label'=>'End Operator :', 'class' => 'form-control', 'empty' => '---- Select Operator ----', 'options' => $operator_list, 'required'=>false)); ?>
				</div>
                
                <div class="form-group">
					<?php echo $this->Form->input('range_end', array('label'=>'End :', 'class' => 'form-control', 'type' => 'number', 'required'=>false)); ?>
				</div>
								
				<?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>



