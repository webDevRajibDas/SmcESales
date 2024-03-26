<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Achievement Effective Call & Outlet coverage'); ?></h3>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('ec_oc_achievement', array('role' => 'form')); ?>
				
				<div class="form-group">
					<?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','empty'=>'---- Select ----','required')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'class' => 'form-control','empty'=>'---- Select ----','required')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('fiscal_year_id',array('id'=>'fiscal_year_id','class'=>'form-control','options'=>array(' '=>'--- Select ---',$fiscal_years),'required'));?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('month_id',array('id'=>'month_id','class'=>'form-control','empty'=>'--- Select ---','required'));?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('effective_call_pharma',array('class'=>'form-control','type'=>'number'));?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('effective_call_non_pharma',array('class'=>'form-control','type'=>'number'));?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('outlet_coverage_pharma',array('class'=>'form-control','type'=>'number'));?>
				</div>	
				<div class="form-group">
					<?php echo $this->Form->input('outlet_coverage_non_pharma',array('class'=>'form-control','type'=>'number'));?>
				</div>	
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function(){
	$(".chosen").chosen();
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	$('#fiscal_year_id').selectChain({            
		target: $('#month_id'),
		value:'name',
		url: '<?= BASE_URL.'AchievementEffectiveCallOutletCoverage/get_month_by_fiscal_year_id'?>',
		type: 'post',
		data:{'month_id': 'fiscal_year_id' }
	});
});
</script>