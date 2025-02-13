<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Daily So Activity Summaries'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('dailySoActivitySummaries','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Daily So Activity Summary'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="DailySoActivitySummaries" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_person_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('pharma_ec'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('nonpharma_ec'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('pharma_oc'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('nonpharma_oc'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('revenue'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($dailySoActivitySummaries as $dailySoActivitySummary): ?>
					<tr>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['id']); ?></td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['date']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($dailySoActivitySummary['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $dailySoActivitySummary['SalesPerson']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['pharma_ec']); ?></td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['nonpharma_ec']); ?></td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['pharma_oc']); ?></td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['nonpharma_oc']); ?></td>
						<td class="text-center"><?php echo h($dailySoActivitySummary['DailySoActivitySummary']['revenue']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('dailySoActivitySummaries','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dailySoActivitySummary['DailySoActivitySummary']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('dailySoActivitySummaries','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $dailySoActivitySummary['DailySoActivitySummary']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('dailySoActivitySummaries','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $dailySoActivitySummary['DailySoActivitySummary']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $dailySoActivitySummary['DailySoActivitySummary']['id'])); } ?>
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