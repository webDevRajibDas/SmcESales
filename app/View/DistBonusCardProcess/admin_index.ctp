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
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Card Process'); ?></h3>
			</div>
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('search', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('fiscal_year_id', array('id'=>'fiscal_year_id','class' => 'form-control','empty'=>'---- Select Fiscal Year ----','required'=>true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('bonus_card_id', array('id'=>'bonus_card_id','class' => 'form-control','empty'=>'---- Select Bonus Card ----','required'=>true)); ?></td>
						</tr>
						<tr>			
							<td class="required" width="50%">
								<?php
								if(count($offices)>1)
								{
									echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id',  'empty'=>'---- Select Office ----'));
								} 
								else
								{
									echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id'));
								} 
								?>

							</td>
							<td class="so_list">
								<?php 
									echo $this->Form->input('route_id', array('class' => 'form-control route_id','empty' => '--- Select---'));
								?>
							</td>
						</tr>
						<!-- <tr>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>true)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>true)); ?></td>
						</tr> -->
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-spinner"></i> Process', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false,'name'=>'calculate')); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<!-- Process Data -->
				<?php //pr($eligible_outlet); ?>
				<?php if($data) {?>
					<?php echo $this->Form->create('DistBonusCardProcess', array('role' => 'form','url'=>array('controller'=>'DistBonusCardProcess','action'=>'set_eligible_bonus_outlet'))); ?>
				<div style="float:left; width:100%; height:450px; overflow:scroll;">
					<table class="table table-bordered table-striped table-condensed">
						<thead>
							<tr>
								<td></td>
								<td>Route</td>
								<td>Market</td>
								<td>Outlet</td>
								<td>Total Stamp</td>
							</tr>
						</thead>
						<tbody>
						<?php echo $this->Form->input('bonus_card_id', array('label'=>false,'type'=>'hidden','value'=>$this->request->data['search']['bonus_card_id'])); ?>
						<?php echo $this->Form->input('fiscal_year_id', array('label'=>false,'type'=>'hidden','value'=>$this->request->data['search']['fiscal_year_id'])); ?>
						<?php foreach($data as $outlet_data) {?>
							<tr>
								<td>
								<?php echo $this->Form->input('DistBonusCardProcess.eligible_outlet.'.$outlet_data['DistOutlet']['id'], array('label'=>false,'div'=>false,'class'=>'eligible_outlet','type'=>'checkbox','value'=>1,'checked'=>(isset($eligible_outlet) && isset($eligible_outlet[$outlet_data['DistOutlet']['id']]) && $eligible_outlet[$outlet_data['DistOutlet']['id']]==1?'checked':false))); ?>
								</td>
								<td><?=$outlet_data['DistRoute']['name']?></td>
								<td><?=$outlet_data['DistMarket']['name']?></td>
								<td><?=$outlet_data['DistOutlet']['name']?></td>
								<td><?=$outlet_data[0]['total_stamp']?></td>
							</tr>
						<?php } ?>
						</tbody>
						
					</table>
				</div>
					<?php echo $this->Form->button('<i class="fa fa-spinner"></i> Save', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
					<button class="btn btn-large btn-danger" type="reset">Reset</button>
					<?php echo $this->Form->end(); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		get_bonus_card_data();
		
		if($('#office_id').val())
		{
			get_route_list($('#office_id').val());
		}
		
		$('#office_id').change(function() {

			get_route_list($(this).val());
		});
		$("#fiscal_year_id").change(function(){
			get_bonus_card_data();
		});
		function get_bonus_card_data()
		{
			$.post('DistBonusCardCalculate/get_bonus_card',{'fiscal_year_id':$("#fiscal_year_id").val()}, function(data,status){
				$("#bonus_card_id").html(data);
				<?php if(isset($this->request->data['search']['bonus_card_id'])) {?>
					$("#bonus_card_id").val(<?=$this->request->data['search']['bonus_card_id']?>);
				<?php } ?>
			});
		}
		function get_route_list(office_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>DistBonusCardProcess/get_route_list',
				data: 'office_id='+office_id,
				cache: false, 
				success: function(response)
				{          
					if($('.so_list').html(response))
					{
						<?php if(isset($this->request->data['search']['route_id'])) {?>
							$(".route_id").val(<?=$this->request->data['search']['route_id']?>);
						<?php } ?> 
					}
					       
				}
			});
		}
		
	})
</script>