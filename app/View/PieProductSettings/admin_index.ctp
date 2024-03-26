<?php
	//echo "<pre>";
	//print_r($pieProductSettings);die();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Setting List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('pieProductSettings', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				
                <?php echo $this->Form->create('PieProductSetting', array('role' => 'form')); ?>
                <table id="ProductSettingCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th><?php echo $this->Paginator->sort('brand_id'); ?></th>
							<th><?php echo $this->Paginator->sort('product_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sort'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($pieProductSettings as $result): ?>
					<tr>
						<td class="text-center"><?php echo h($result['PieProductSetting']['id']); ?></td>
						<td><?php echo h($result['Brand']['name']); ?></td>
						<td><?php echo h($result['Product']['name']); ?></td>                        
                        <?php /*?><td class="text-center"><?php echo h($result['PieProductSetting']['sort']); ?></td><?php */?>
                        
                        <td class="text-center">
							<input name="product_setting_id[]" type="hidden" value="<?php echo $result['PieProductSetting']['id']; ?>"/>
							<input id="order" class="form-control order" style="width:100%;text-align:center;" name="sort[]" type="text" value="<?php echo $result['PieProductSetting']['sort']; ?>" required/>
						</td>
						
						<td class="text-center">
							<?php if($this->App->menu_permission('pieProductSettings', 'admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['PieProductSetting']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							
                            
                            <?php if($this->App->menu_permission('pieProductSettings','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['PieProductSetting']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['PieProductSetting']['id'])); } ?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
                
                <div class="form-group text-center" style="padding-top:20px;">
                	<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                </div>
                
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
