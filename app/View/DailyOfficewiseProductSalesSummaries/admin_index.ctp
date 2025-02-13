<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Daily Officewise Product Sales Summaries'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('dailyOfficewiseProductSalesSummaries','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Daily Officewise Product Sales Summary'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="DailyOfficewiseProductSalesSummaries" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product_measurement_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_quantity'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('bonus_quantity'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sales_amount'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($dailyOfficewiseProductSalesSummaries as $dailyOfficewiseProductSalesSummary): ?>
					<tr>
						<td class="text-center"><?php echo h($dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['id']); ?></td>
						<td class="text-center"><?php echo h($dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['date']); ?></td>
						<td class="text-center">
			<?php echo $this->Html->link($dailyOfficewiseProductSalesSummary['Office']['office_name'], array('controller' => 'offices', 'action' => 'view', $dailyOfficewiseProductSalesSummary['Office']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($dailyOfficewiseProductSalesSummary['Product']['name'], array('controller' => 'products', 'action' => 'view', $dailyOfficewiseProductSalesSummary['Product']['id'])); ?>
		</td>
						<td class="text-center">
			<?php echo $this->Html->link($dailyOfficewiseProductSalesSummary['ProductMeasurement']['id'], array('controller' => 'product_measurements', 'action' => 'view', $dailyOfficewiseProductSalesSummary['ProductMeasurement']['id'])); ?>
		</td>
						<td class="text-center"><?php echo h($dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['sales_quantity']); ?></td>
						<td class="text-center"><?php echo h($dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['bonus_quantity']); ?></td>
						<td class="text-center"><?php echo h($dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['sales_amount']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('dailyOfficewiseProductSalesSummaries','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('dailyOfficewiseProductSalesSummaries','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('dailyOfficewiseProductSalesSummaries','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $dailyOfficewiseProductSalesSummary['DailyOfficewiseProductSalesSummary']['id'])); } ?>
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