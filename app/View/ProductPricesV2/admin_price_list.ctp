<?php
	$price_controller=new ProductPricesV2Controller();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __($product['Product']['name'].' Price List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('product_prices','admin_set_unique_price')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Price'), array('action' => "admin_set_unique_price/$id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">
                <!--<div class="search-box">
					<?php echo $this->Form->create('ProductPrice', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('product_category_id', array('id' => 'category_id', 'class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>							
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
				-->
				<table id="ProductCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('effective_date'); ?></th>
							<th class="text-center">Create For</th>
							<th class="text-center"><?php echo $this->Paginator->sort('vat'); ?></th>
							<th class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php  foreach ($product_prices as $key=>$product_price): ?>
					<tr>
						<td class="text-center"><?php echo h($product_price['ProductPricesV2']['id']); ?></td>
						<td class="text-center"><?php echo h($product_price['ProductPricesV2']['effective_date']); ?></td>
						<td class="text-center"><?php echo h($price_controller->get_price_create_for_by_price_id($product_price['ProductPricesV2']['id'])); ?></td>
						<td class="text-center"><?php echo h($product_price['ProductPricesV2']['vat']); ?></td>
						<td class="text-center">
						<?php if($product_price['ProductPricesV2']['effective_date'] >= $product_prices[0]['ProductPricesV2']['effective_date']){?>
							<?php echo $this->Html->link('<i class="glyphicon glyphicon-edit"></i>', array('action' => 'set_price',$product_price['ProductPricesV2']['product_id'],$product_price['ProductPricesV2']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Edit Price'));  ?>
							
							<?php echo $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view_price',$product_price['ProductPricesV2']['product_id'],$product_price['ProductPricesV2']['id']), array('class' => 'btn btn-info btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'View Price'));  ?>
						<?php }else{?>

							<?php echo $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view_price',$product_price['ProductPricesV2']['product_id'],$product_price['ProductPricesV2']['id']), array('class' => 'btn btn-info btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'View Price'));  ?>
						<?php } ?>
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-arrow-left"></i>', array('action' => 'assign_special_group',$product_price['ProductPricesV2']['product_id'],$product_price['ProductPricesV2']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Assign Special Group'));  ?>
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