<?php
App::import('Controller', 'CollectionLogsController');
$CollectionLogsController = new CollectionLogsController;	


//pr($collections);exit();

?>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Collection Log List'); ?></h3>
				
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('CollectionLog', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$offices)); ?></td>
							<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----')); ?></td>

						</tr>					
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['CollectionLog']['date_from'])=='' ? $current_date : $this->request->data['CollectionLog']['date_from']),'required'=>false)); ?></td>

						</tr>
						<tr>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?></td>
							<td><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['CollectionLog']['date_to'])=='' ? $current_date : $this->request->data['CollectionLog']['date_to']),'required'=>false)); ?></td>

						</tr>
						<tr>
							<td><?php echo $this->Form->input('type', array('class' => 'form-control','value'=>(isset($this->request->data['CollectionLog']['type'])=='' ? 2 : $this->request->data['CollectionLog']['type']),'required'=>false,'options'=>array('1'=>'Cash','2'=>'Instrument'))); ?></td>
							<td></td>

						</tr>					
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
                
                <div class="table-responsive">	
				<table id="Collections" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('instrumentRefNo','Instrument Ref.No'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('instrument_type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('instrument_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('collectionDate','Collection Date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('collectionAmount','Collection Amount'); ?></th>
							<th class="text-center">Last Log Status</th>
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					//pr($collections);exit();
					foreach ($collections as $collection): ?>					
					<tr>
						<td class="text-center"><?php echo h($collection['CollectionLog']['id']); ?></td>
						<td class="text-left"><?php echo h($collection['CollectionLog']['memo_no']); ?></td>
						<td class="text-left"><?php echo h($collection['CollectionLog']['instrumentRefNo']); ?></td>						
						<td class="text-left"><?php echo h($collection['Outlet']['name']); ?></td>
						<td class="text-center"><?php echo  h($instrument_type[$collection['CollectionLog']['type']]) ?></td>
						<td class="text-center"><?php if($collection['CollectionLog']['instrument_type'] !=1)echo h($instrument_type[$collection['CollectionLog']['instrument_type']]); ?></td>
						<td class="text-center"><?php echo  h($collection['CollectionLog']['instrument_no']); ?></td>	
						<td class="text-center"><?php echo $this->App->dateformat($collection['CollectionLog']['collectionDate']); ?></td>						
						<td class="text-right"><?php echo sprintf('%.2f',$collection['CollectionLog']['collectionAmount']); ?></td>
						<td align="center">
							<?php if($collection['CollectionLog']['is_deleted']==0) {echo '<span class="btn btn-warning btn-xs">Edited</span>';}else{echo '<span class="btn btn-danger btn-xs">Deleted</span>';} ?>
						</td>
                        <td>
                        <?php if($this->App->menu_permission('collection_logs', 'admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $collection['CollectionLog']['collection_id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
                        	
                        </td>						
					</tr>
					<?php 
					$total_amount = $total_amount + $collection['CollectionLog']['collectionAmount'];
					endforeach; 
					?>
					<tr>
						<td align="right" colspan="8"><b>Total Amount :</b></td>
						<td align="right"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
					</tr>
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

	$('#office_id').selectChain({
		target: $('#territory_id'),
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

$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});

$('.territory_id').change(function(){
	$('.outlet_id').html('<option value="">---- Select Outlet ----');
});

</script>