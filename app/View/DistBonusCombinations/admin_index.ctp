<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Open Combinations'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('DistBonusCombinations','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Combination'), array('action' => 'add/'.$product_id), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">				
                <table id="Combinations" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-left"><?php echo $this->Paginator->sort('name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('start_date'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('end_date'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($results as $result): 
					//pr($result);
					?>
					<tr>
						<td class="text-center"><?php echo h($result['DistOpenCombination']['id']); ?></td>
						<td><?php echo h($result['DistOpenCombination']['name']); ?></td>
                        <td><?php echo h($result['DistOpenCombination']['start_date']); ?></td>
                        <td><?php echo h($result['DistOpenCombination']['end_date']); ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('DistBonusCombinations','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['DistOpenCombination']['id'], $product_id), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('DistBonusCombinations','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['DistOpenCombination']['id'],$product_id), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['DistOpenCombination']['id'])); } ?>
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