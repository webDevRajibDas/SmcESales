<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __($product['Product']['name'].' Commission List'); ?></h3>
				<div class="box-tools pull-right">
					<?php $product_id=$product['Product']['id']; if($this->App->menu_permission('product_prices','admin_set_commission_price')){echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Commission'), array('action' => "admin_set_commission_price/$product_id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>

					<?php $product_id=$product['Product']['id']; if($this->App->menu_permission('product_prices','admin_distributor_wsie_commission_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Distributor Wise Commission'), array('action' => "admin_distributor_wsie_commission_list/$product_id"), array('class' => 'btn btn-info', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">
				<table id="ProductCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th  class="text-center"><?php echo 'Outlet Category' ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('effective_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('commission_amount','Commission'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php  foreach ($product_prices as $key=>$product_price): ?>
					<tr>
						<td class="text-center"><?php echo h($product_price['DistributorCommission']['id']); ?></td>
						<td class="text-center"><?php echo $outletCategories[17]; ?></td>
						<td class="text-center"><?php echo h($product_price['DistributorCommission']['effective_date']); ?></td>
						<td class="text-center"><?php echo $product_price['DistributorCommission']['commission_amount']; ?></td>
						<td class="text-center">
						<?php if($product_price['DistributorCommission']['effective_date'] >= $product_prices[0]['DistributorCommission']['effective_date']){?>
							<?php echo $this->Html->link('Edit Commission', array('action' => 'edit_commission_price',$product_price['DistributorCommission']['product_id'],$product_price['DistributorCommission']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Edit Commission Price'));  ?>
						<?php }?>

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