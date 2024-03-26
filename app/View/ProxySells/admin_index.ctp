<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Proxy Sells'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('proxySells','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Proxy Sell'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <table id="ProxySells" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('proxy_for_so_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('proxy_for_territory_id'); ?></th>
							<th class="text-center">Proxy By</th>
							<?php /*?><th class="text-center"><?php echo $this->Paginator->sort('proxy_by_territory_id')?></th><?php */?>
							<th class="text-center"><?php echo $this->Paginator->sort('from_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('to_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($proxySells as $proxySell): ?>
					<tr>
						<td class="text-center"><?php echo h($proxySell['ProxySell']['id']); ?></td>
						<td class="text-center"><?php echo h($proxySell['proxyForSo']['name']); ?></td>
						<td class="text-center"><?php echo h($proxySell['proxyForTerritory']['name']); ?></td>
						<td class="text-center"><?php echo h($proxySell['proxyBySo']['name']); ?></td>
						<?php /*?><td class="text-center"><?php echo h($proxySell['proxyByTerritory']['name']); ?></td><?php */?>
						<td class="text-center"><?php echo $this->App->dateformat($proxySell['ProxySell']['from_date']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($proxySell['ProxySell']['to_date']); ?></td>
						<td class="text-center"><?php echo h($proxySell['ProxySell']['remarks']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('proxySells','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $proxySell['ProxySell']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php if($this->App->menu_permission('proxySells','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $proxySell['ProxySell']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('proxySells','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $proxySell['ProxySell']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $proxySell['ProxySell']['id'])); } ?>
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