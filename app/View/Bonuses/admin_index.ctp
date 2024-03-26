<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('bonuses','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Bonus'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="Bonus" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('mother_product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('mother_product_quantity'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bonus_product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bonus_product_quantity'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('effective_date','Start Date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('end_date'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($bonuses as $bonus): ?>
					<tr>
						<td class="text-center"><?php echo h($bonus['Bonus']['id']); ?></td>
						<td class="text-left"><?php echo h($bonus['MotherProduct']['name']); ?></td>
						<td class="text-center"><?php echo h($bonus['Bonus']['mother_product_quantity']); ?></td>
						<td class="text-left"><?php echo h($bonus['BonusProduct']['name']); ?></td>
						<td class="text-center"><?php echo h($bonus['Bonus']['bonus_product_quantity']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($bonus['Bonus']['effective_date']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($bonus['Bonus']['end_date']); ?></td>
						<td class="text-center">
							<?php 
							if($office_parent_id == 0)
							{
								if($this->App->menu_permission('bonuses','admin_bonus_target')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-cog"></i>'), array('action' => 'bonus_target', $bonus['Bonus']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Bonus Target')); } 
							}else{
								if($this->App->menu_permission('bonuses','admin_territory_bonus_target')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-cog"></i>'), array('action' => 'territory_bonus_target', $bonus['Bonus']['id'],$office_id), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Bonus Target')); } 
							}							
							?>
							<?php if($this->App->menu_permission('bonuses','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $bonus['Bonus']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('bonuses','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $bonus['Bonus']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $bonus['Bonus']['id'])); } ?>
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