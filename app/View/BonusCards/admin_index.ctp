<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Card List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('bonusCards','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Bonus Card'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('BonusCard', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('name', array('class' => 'form-control','required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('bonus_card_type_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>							
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('product_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>
							<td><?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>							
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
                <table id="BonusCards" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="20" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('fiscal_year_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bonus_card_type_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('min_qty_per_memo'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('min_qty_per_year'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($bonusCards as $bonusCard): ?>
					<tr>
						<td class="text-center"><?php echo h($bonusCard['BonusCard']['id']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['BonusCard']['name']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['FiscalYear']['year_code']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['BonusCardType']['name']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['Product']['name']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['BonusCard']['min_qty_per_memo']); ?></td>
						<td class="text-center"><?php echo h($bonusCard['BonusCard']['min_qty_per_year']); ?></td>
						<td class="text-center"><?php echo ($bonusCard['BonusCard']['is_active'] == 1 ? 'Active' : 'Inactive'); ?></td>
						<td class="text-center">
							<?php //if($this->App->menu_permission('bonusCards','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $bonusCard['BonusCard']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('bonusCards','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $bonusCard['BonusCard']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('bonusCards','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $bonusCard['BonusCard']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $bonusCard['BonusCard']['id'])); } ?>
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