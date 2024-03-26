<?php
	//echo "<pre>";
	//print_r($GroupWiseDiscountBonusPolicies);die();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Discount/Bonus Policy List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('GroupWiseDiscountBonusPolicies', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add New'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				
                <?php echo $this->Form->create('GroupWiseDiscountBonusPolicy', array('role' => 'form')); ?>
                <table id="ProductSettingCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th><?php echo $this->Paginator->sort('name'); ?></th>
							<th><?php echo $this->Paginator->sort('remarks'); ?></th>
							<th width="12%"><?php echo $this->Paginator->sort('start_date'); ?></th>
							<th width="12%"><?php echo $this->Paginator->sort('end_date'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($results as $result): ?>
					<tr>
						<td class="text-center"><?php echo h($result['GroupWiseDiscountBonusPolicy']['id']); ?></td>
						<td><?php echo h($result['GroupWiseDiscountBonusPolicy']['name']); ?></td>                       
						<td><?php echo h($result['GroupWiseDiscountBonusPolicy']['remarks']); ?></td>
						<td><?php echo $this->App->dateformat($result['GroupWiseDiscountBonusPolicy']['start_date']); ?></td>
						<td><?php echo $this->App->dateformat($result['GroupWiseDiscountBonusPolicy']['end_date']); ?></td>	
                        <?php /*?><td class="text-center"><?php echo h($result['GroupWiseDiscountBonusPolicy']['sort']); ?></td><?php */?>
                        
                        <?php /*?><td class="text-center">
							<input name="product_setting_id[]" type="hidden" value="<?php echo $result['GroupWiseDiscountBonusPolicy']['id']; ?>"/>
							<input id="order" class="form-control order" style="width:100%;text-align:center;" name="sort[]" type="text" value="<?php echo $result['GroupWiseDiscountBonusPolicy']['sort']; ?>" required/>
						</td><?php */?>
						
						<td class="text-center">
							<?php if($this->App->menu_permission('GroupWiseDiscountBonusPolicies', 'admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['GroupWiseDiscountBonusPolicy']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							
                            
                            <?php /*?><?php if($this->App->menu_permission('GroupWiseDiscountBonusPolicies','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['GroupWiseDiscountBonusPolicy']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['GroupWiseDiscountBonusPolicy']['id'])); } ?><?php */?>
							
							<?php if($this->App->menu_permission('GroupWiseDiscountBonusPolicies','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['GroupWiseDiscountBonusPolicy']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['GroupWiseDiscountBonusPolicy']['id'])); } ?>
							
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
                
                <?php echo $this->Form->end(); ?>
                
                
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
