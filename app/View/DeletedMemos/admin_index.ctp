<?php
//print_r($product_name);die();
?>
	<style>
		table, th, td {
			/*border: 1px solid black;*/
			border-collapse: collapse;
		}
		#content { display: none; }
		@media print
			{
				#non-printable { display: none; }
				#content { display: block; }
				table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
			}
    </style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('DeletedMemo List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('DeletedMemos','admin_create_DeletedMemo')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New DeletedMemo'), array('action' => 'create_DeletedMemo'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
					<?php if($this->App->menu_permission('DeletedMemos','admin_DeletedMemo_map')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-map-marker"></i> DeletedMemo on Map'), array('action' => 'DeletedMemo_map'), array('class' => 'btn btn-success', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DeletedMemo', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td width="50%"><?php echo $this->Form->input('memo_no', array('class' => 'form-control','required'=>false)); ?></td>							
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td><?php echo $this->Form->input('memo_reference_no', array('class' => 'form-control','required'=>false)); ?></td>							
						</tr>
						<tr>
							<td class="thana_list">
								<?php 
								
									echo $this->Form->input('thana_id', array('id'=>'thana_id','class' => 'form-control thana_id','empty'=>'--- Select---','options' => '','label'=>'Thana'));
								?>

							</td>
							<td>
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DeletedMemo']['date_from'])=='' ? $current_date : $this->request->data['DeletedMemo']['date_from']),'required'=>false)); ?>
							</td>
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?>
							</td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DeletedMemo']['date_to'])=='' ? $current_date : $this->request->data['DeletedMemo']['date_to']),'required'=>false)); ?>
							</td>
														
						</tr>	
						<tr>
							<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?></td>
							<td>
								<div class="operator_DeletedMemo_value"><?php echo $this->Form->input('mamo_value', array('class' => 'form-control')); ?></div>
							</td>
							
						</tr>
						<!-- <tr>
							<td  class="text-left">
									<?php echo $this->Form->input('operator', array('class' => 'form-control operator','empty'=>'---Select---','options'=>array('1'=>'Less than (<)','2'=>'Gretter than (>)','3'=>'Between'))); ?>
							</td>
							
						</tr>
						<tr class="between_value">
							<td  class="text-left">
								<?php echo $this->Form->input('memo_value_from', array('class' => 'form-control operator_between_DeletedMemo_value')); ?>
							</td>
							<td>
								<?php echo $this->Form->input('memo_value_to', array('class' => 'form-control operator_between_DeletedMemo_value')); ?>
							</td>
						</tr> -->
						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>	
							</td>						
						</tr>

					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
                <div class="table-responsive">
				<table id="DeletedMemo" class="table table-bordered">
					<thead>
						<tr>
							<!-- <th width="50" class="text-center">Serial</th> -->
							<th class="text-center"><?php echo $this->Paginator->sort('Memo_no'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Memo_reference_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Office.office_name','Office'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.name','Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Thana.name','Thana'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.name','Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name','Outlet'); ?></th>
							<th class="text-center">Last Log Status</th>
							<!-- <th class="text-center"><?php //echo $this->Paginator->sort('gross_value','Memo Total'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('memo_date'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('deleted_at','Deleted/Edited Time'); ?></th>
							<th class="text-center"><?php //echo $this->Paginator->sort('is_delete','Log Status'); ?></th>
							<th class="text-center">Memo Type</th> -->
							
							<th width="80" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					foreach ($DeletedMemos as $DeletedMemo): 
					// pr($DeletedMemo);exit;
					/*$date1=date_create(date('Y-m-d', strtotime($DeletedMemo['DeletedMemo']['Memo_time'])));
					$date2=date_create(date('Y-m-d'));
					$diff=date_diff($date1,$date2);
					$dare_diff = $diff->format("%a");*/
					?>
					<tr style="background-color:<?php //echo $DeletedMemo['DeletedMemo']['from_app']==0 ? '#f5f5f5':'white'?>">
						<!-- <td align="center"><?php //echo h($DeletedMemo['DeletedMemo']['id']); ?></td> -->
						<td align="center"><?php echo h($DeletedMemo['DeletedMemo']['Memo_no']); ?></td>
                        <td align="center"><?php echo h($DeletedMemo['DeletedMemo']['Memo_reference_no']); ?></td>
						<td align="center"><?php echo h($DeletedMemo['Office']['office_name']); ?></td>
						<td align="center"><?php echo h($DeletedMemo['Territory']['name']); ?></td>
						<td align="center"><?php echo h($DeletedMemo['Thana']['name']); ?></td>
						<td align="center"><?php echo h($DeletedMemo['Market']['name']); ?></td>
						<td align="center"><?php echo h($DeletedMemo['Outlet']['name']); ?></td>
						<td align="center">
							<?php if($DeletedMemo['0']['deletion_status']==0) {echo '<span class="btn btn-warning btn-xs">Edited</span>';}else{echo '<span class="btn btn-danger btn-xs">Deleted</span>';} ?>
						</td>
						<!-- <td align="center"><?php //echo sprintf('%.2f',$DeletedMemo['DeletedMemo']['gross_value']); ?></td> -->
						<!-- <td align="center"><?php //echo $this->App->datetimeformat($DeletedMemo['DeletedMemo']['Memo_time']); ?></td> -->
						<!-- <td align="center"><?php //echo $this->App->datetimeformat($DeletedMemo['DeletedMemo']['deleted_at']); ?></td> -->
						
						<!-- <td align="center"><?php //($DeletedMemo['DeletedMemo']['is_delete']==0)?'<i class="label label-warning">Edited</i>':'<i class="label label-danger">Deleted</i>'; ?></td> -->
						<!-- <td align="center"><?php //($DeletedMemo['DeletedMemo']['credit_amount']>0)?'Credit':'Cash'; ?></td> -->
                        <td class="text-center">
                        
							<?php if($this->App->menu_permission('DeletedMemos','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $DeletedMemo['DeletedMemo']['Memo_no']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
                            
						</td>
                        
                        
					</tr>
					<?php 
					// $total_amount = $total_amount + $DeletedMemo['DeletedMemo']['gross_value'];
					endforeach; 					
					?>
					<!-- <tr>
						<td align="right" colspan="5"><b>Total Amount :</b></td>
						// <td align="center"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
						<td class="text-center" colspan="3"></td>
					</tr> -->
					</tbody>
				</table>
                </div>
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


<script>
$('.office_id').selectChain({
	target: $('.territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_market';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});
function get_thana_list(territory_id)
{
	$.ajax
	({
		type: "POST",
		url: '<?=BASE_URL?>DeletedMemos/get_thana_by_territory_id',
		data: 'territory_id='+territory_id,
		cache: false, 
		success: function(response)
		{          
			$('.thana_id').html(response); 
			<?php if(isset($this->request->data['DeletedMemo']['thana_id'])){?> 
				$('.thana_id option[value="<?=$this->request->data['DeletedMemo']['thana_id']?>"]').attr("selected",true);
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
$('.thana_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'DeletedMemos/market_list';?>',
	type: 'post',
	data:{'thana_id': 'thana_id' }
});
$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});

$('.office_id').change(function(){
	$('.market_id').html('<option value="">---- Select Market ----');
	$('.outlet_id').html('<option value="">---- Select Outlet ----');
});

$('.territory_id').change(function(){
	$('.outlet_id').html('<option value="">---- Select Outlet ----');
});
$(".operator").change(function(){
	operator_value_set();
});
operator_value_set();
function operator_value_set()
{
	var operator_value=$(".operator").val();
	if(operator_value==3)
	{
		$('.between_value').show();
		$('.operator_DeletedMemo_value').hide();
	}
	else if(operator_value==1 || operator_value==2)
	{
		$('.operator_DeletedMemo_value').show();
		$('.between_value').hide();
	}
	else
	{
		$('.operator_DeletedMemo_value').hide();
		$('.between_value').hide();
	}
}
</script>
<script>
	function PrintElem(elem)
	{
		var mywindow = window.open('', 'PRINT', 'height=400,width=1000');
		

		//mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('</head><body >');
		//mywindow.document.write('<h1>' + document.title  + '</h1>');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		mywindow.close();

		return true;
	}
</script>