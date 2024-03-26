
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __($page_title); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('OpeningBalanceDeposites', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add New'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('OpeningBalanceDeposite', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----','options'=>$offices)); ?></td>

							<td><?php echo $this->Form->input('date', array('class' => 'form-control datepicker','required'=>false)); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>

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
                <table id="ProductSettingCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th>Office</th>
							<th>Territory</th>
                            <!--<th>Fiscale Year</th>-->
                            <th class="text-right">Amount</th>
                            <th class="text-center">Entry Date</th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($OpeningBalanceDeposites as $result): ?>
					<tr>
						<td class="text-center"><?php echo h($result['OpeningBalanceDeposite']['id']); ?></td>
						
                        <td><?php echo h($result['OpeningBalance']['Office']['office_name']); ?></td>                        
                        <td><?php echo h($result['OpeningBalance']['Territory']['name']); ?></td>
                        <?php /*?><td><?php echo h($result['OpeningBalance']['FiscalYear']['year_code']); ?></td><?php */?>
                        <td class="text-right"><?php echo h($result['OpeningBalanceDeposite']['amount']); ?></td>
                        <td class="text-center"><?php echo h(date('d-m-dY', strtotime($result['OpeningBalanceDeposite']['entry_date']))); ?></td>
                        
                        
						
						<td class="text-center">
							<?php //if($this->App->menu_permission('OpeningBalanceDeposites', 'admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['OpeningBalanceDeposite']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							
                            
                            <?php if($this->App->menu_permission('OpeningBalanceDeposites','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['OpeningBalanceDeposite']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['OpeningBalanceDeposite']['id'])); } ?>
						</td>
					</tr>
					<?php endforeach; ?>
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


<script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list';?>',
		type: 'post',
		data:{'office_id': 'office_id' }
});
</script>