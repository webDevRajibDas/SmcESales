<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Visit Plan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Visit Plan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('VisitPlanList', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('so_id', array('class' => 'form-control so_id','empty' => '---- Select SO ----','options' => $so_list)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('visit_plan_date', array('type'=>'text','class' => 'form-control datepicker')); ?>
				</div>
				<div class="form-group">
					<label>Markets : </label>
					<div id="market_list" style="margin-left:23%">
					<?php //echo $this->Form->input('market_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $markets,'required'=>true)); ?>
					</div>
				</div>				
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>

<script>
$('.so_id').change(function(){
	var so_id = $('.so_id').val();	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>admin/visit_plan_lists/get_market_list',
		data: 'so_id='+so_id,
		cache: false, 
		success: function(response){						
			$('#market_list').html(response);				
		}
	});		
});
</script>