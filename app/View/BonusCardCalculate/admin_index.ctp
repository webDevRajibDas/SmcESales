<?php //pr($bonus_cards);die; ?>
<style type="text/css">
	.border,.border td {
		border: 1px solid black;
		white-space: nowrap;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Summery Report'); ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">

					<div class="alert_data">
					</div>
					<?php echo $this->Form->create('search', array('role' => 'form')); ?>
					<table class="search">
					<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('class' => 'form-control','empty'=>'---- Select Office ----')); ?></td>
							<td width="50%"><?php //echo $this->Form->input('bonus_card_id', array('class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('fiscal_year_id', array('id'=>'fiscal_year_id','class' => 'form-control','empty'=>'---- Select Fiscal Year ----','required'=>true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('bonus_card_id', array('id'=>'bonus_card_id','class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); ?></td>
						</tr>
						<!-- <tr>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>true)); ?></td>
						</tr> -->
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-spinner"></i> Calculate', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false,'name'=>'calculate')); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>
					 <?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$("#fiscal_year_id").change(function(){
			$.post('BonusCardCalculate/get_bonus_card',{'fiscal_year_id':$(this).val()}, function(data,status){
				$("#bonus_card_id").html(data);
			});
		});

		$("#fiscal_year_id,#bonus_card_id,#searchOfficeId").change(function(){
			var fiscal_year_id=$("#fiscal_year_id").val();
			var bonus_card_id = $("#bonus_card_id").val();
			var office_id = $("#searchOfficeId").val();
			if(fiscal_year_id && bonus_card_id)
			{
				$.post('BonusCardCalculate/check_calculate_before',{'fiscal_year_id':fiscal_year_id,'bonus_card_id':bonus_card_id,'office_id':office_id}, function(data,status){
					$(".alert_data").html(data);
				
				});
			}
			
		});

	})
</script>