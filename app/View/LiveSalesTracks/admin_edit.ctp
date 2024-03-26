<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Sales Tracking'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Tracking List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('LiveSalesTrack', array('role' => 'form')); ?>
                
                    <div class="form-group">
                        <?php echo $this->Form->input('start_time', array('label'=>'Starting Time', 'value'=>$start_time,  'type'=>'text', 'class' => 'form-control timepicker')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('end_time', array('label'=>'Ending Time', 'value'=>$end_time, 'type'=>'text', 'class' => 'form-control timepicker')); ?>
                    </div>
                    
                    <div class="form-group">
                        <?php echo $this->Form->input('interval', array('label'=>'Interval', 'class' => 'form-control', 'empty' => '---- Select Interval ----', 'options' => $interval)); ?>
                    </div>
                   
                                
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                
				<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script type="text/javascript">
$('.timepicker').timepicker();
</script>