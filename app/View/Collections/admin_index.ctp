<?php //pr($collections);exit;?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Collection List'); ?></h3>
				<div class="box-tools pull-right">
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Collection', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$offices)); ?></td>
							<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?></td>

						</tr>					
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Collection']['date_from'])=='' ? $current_date : $this->request->data['Collection']['date_from']),'required'=>false)); ?></td>

						</tr>
						<tr>
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?></td>
							<td><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Collection']['date_to'])=='' ? $current_date : $this->request->data['Collection']['date_to']),'required'=>false)); ?></td>

						</tr>
						<tr>
							<td><?php echo $this->Form->input('type', array('class' => 'form-control','value'=>(isset($this->request->data['Collection']['type'])=='' ? 2 : $this->request->data['Collection']['type']),'required'=>false,'options'=>array('1'=>'Cash','2'=>'Instrument'))); ?></td>
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
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					foreach ($collections as $collection): ?>					
					<tr>
						<td class="text-center"><?php echo h($collection['Collection']['id']); ?></td>
						<td class="text-left"><?php echo h($collection['Collection']['memo_no']); ?></td>
						<td class="text-left"><?php echo h($collection['Collection']['instrumentRefNo']); ?></td>						
						<td class="text-left"><?php echo h($collection['Outlet']['name']); ?></td>
						<td class="text-center"><?php echo  h($instrument_type[$collection['Collection']['type']]) ?></td>
						<td class="text-center"><?php if($collection['Collection']['instrument_type'] !=1)echo h($instrument_type[$collection['Collection']['instrument_type']]); ?></td>
						<td class="text-center"><?php echo  h($collection['Collection']['instrument_no']); ?></td>	
						<td class="text-center"><?php echo $this->App->dateformat($collection['Collection']['collectionDate']); ?></td>						
						<td class="text-right"><?php echo sprintf('%.2f',$collection['Collection']['collectionAmount']); ?></td>
						<td class="text-center">
                    		<?php if ($collection['Collection']['editable'] == 1) {
								if ($this->App->menu_permission('collections', 'admin_edit')) {
									echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $collection['Collection']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
								}

								if($this->App->menu_permission('collections','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $collection['Collection']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $collection['Collection']['id'])); }
							}?>

                        </td>
					</tr>
					<?php 
					$total_amount = $total_amount + $collection['Collection']['collectionAmount'];
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