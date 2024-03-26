<?php 
 	// pr($list);exit;
?>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Memo Notifications'); ?></h3>
			</div>	
			<div class="box-body">
			<div class="search-box" style="float:left; width:100%;">
					<?php echo $this->Form->create('MemoNotification', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','id'=>'date_from', 'required'=>true)); ?>
							</td>
							<td class="required">
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','id'=>'date_to', 'required'=>true)); ?>
							</td>						
						</tr>
						<tr>
							<?php if(isset($region_offices)){?>
							<td class="required" width="50%">
								<?php 
								if(count($region_offices)>1)
								{
									echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id','empty'=>'---- Head Office ----', 'options' => $region_offices,)); 
								}
								else
								{
									echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id','options' => $region_offices)); 
								}
								?>

							</td>
							<?php }?>					
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
						</tr>	
						<tr>

							<td class="territory_list">
								<?php 
								if(isset($territory_list))
									echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','empty'=>'--- Select---','options' => $territory_list,'label'=>'Territory'));
								?>

							</td>
							<td class="thana_list">
								<?php 
								if(isset($thana_list))
									echo $this->Form->input('thana_id', array('class' => 'form-control thana_id','empty'=>'--- Select---','options' => $thana_list,'label'=>'Thana'));
								?>

							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>				
				<table id="MemoNotification" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('SalesPerson.name','SO Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name','Outlet'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_amount'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach($list as $val): ?>
					<tr>
						<td align="center"><?php echo h($val['MemoNotification']['id']); ?></td>
						<td align="center"><?php echo h($val['MemoNotification']['memo_no']); ?></td>
						<td align="center"><?php echo h($val['SalesPerson']['name']); ?></td>
						<td align="center"><?php echo h($val['Outlet']['name']); ?></td>
						<td align="center"><?php echo $this->App->datetimeformat($val['MemoNotification']['memo_date']); ?></td>						
						<td align="center"><?php echo h($val['MemoNotification']['memo_amount']); ?></td>
					</tr>
					<?php 
					endforeach; 					
					?>					
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php	echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>									
								<?php
									echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>								
							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>
<!-- <script>
$('.office_id').selectChain({
	target: $('.territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.sales_person_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_so_list';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.sales_person_id').html('<option value="">---- Select SO ----');
});
</script> -->

<script type="text/javascript">
	$(document).ready(function(){
		if($('#office_id').val()!='')
		{
			get_territory_list($('#office_id').val());
		}
		$('#office_id').change(function() {

			get_territory_list($(this).val());
		});
		function get_territory_list(office_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>visited_outlets/get_territory_list',
				data: 'office_id='+office_id,
				cache: false, 
				success: function(response)
				{          
					$('.territory_list').html(response);
					<?php if(isset($this->request->data['VisitedOutlet']['territory_id'])){?> 
					$('.territory_id option[value="<?=$this->request->data['VisitedOutlet']['territory_id']?>"]').attr("selected",true);
					get_thana_list($('.territory_id').val());
					<?php }?>       
				}
			});
		}
		$('.region_office_id').selectChain({
			target: $('.office_id'),
			value:'name',
			url: '<?= BASE_URL.'weekly_bank_deposition_information/get_office_list';?>',
			type: 'post',
			data:{'region_office_id': 'region_office_id' }
		});

		function get_thana_list(territory_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>visited_outlets/get_thana_list',
				data: 'territory_id='+territory_id,
				cache: false, 
				success: function(response)
				{          
					$('.thana_list').html(response); 
					<?php if(isset($this->request->data['VisitedOutlet']['thana_id'])){?> 
					$('.thana_id option[value="<?=$this->request->data['VisitedOutlet']['thana_id']?>"]').attr("selected",true);
					<?php }?>   
				}
			});
		}
		if($('.territory_id').val()!='')
		{
			get_thana_list($('.territory_id').val());
		}
		$('body').on('change','.territory_id',function() {

			get_thana_list($(this).val());
		});
		
	})
</script>