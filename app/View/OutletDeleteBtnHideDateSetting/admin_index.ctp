<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Edit Outlet Delete Button Hide Date & Fiscal Year Setting For Bonus Party Report'); ?></h3>
			</div>
			<div class="box-body">
			<?php echo $this->Form->create('AppVersion', array('role' => 'form')); ?>
										<div class="form-group">
						<?php //echo $this->Form->input('id', array('class' => 'form-control')); ?>
					</div>

					<div class="form-group">
						<?php echo $this->Form->input('outlet_delete_btn_hide_date', array('type'=>'text','class' => 'form-control datepicker_outlet','required'=>true)); ?>
					</div>
					<div class="form-group">
						<?php
						echo $this->Form->input('fiscal_year_id_for_bonus_report', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears, 'required'=>true)); ?>
					</div>


				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.datepicker_outlet').datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true
			});
	});
</script>
