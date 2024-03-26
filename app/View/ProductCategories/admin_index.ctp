<?php ?>
<style>
.editable{
	width:15%;
}
.editable input{
	text-align: center;
	width:60%;
}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product Categories'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('productCategories','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Product Category'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<table id="ProductCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('sap_product_category_code'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('parent_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_pharma_product'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('order'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($productCategories as $productCategory): ?>
					<tr id="<?php echo $productCategory['ProductCategory']['id']; ?>">
						<td class="text-center"><?php echo h($productCategory['ProductCategory']['id']); ?></td>
						<td class="text-left"><?php echo h($productCategory['ProductCategory']['name']); ?></td>
						<td class="text-left"><?php echo h($productCategory['ProductCategory']['sap_product_category_code']); ?></td>
						<td class="text-center"><?php echo h($productCategory['ParentProductCategory']['name']); ?></td>
						<td class="text-center"><?php if($productCategory['ProductCategory']['is_pharma_product']==1){ echo 'Yes'; }else{ echo 'No'; } ?></td>
						<td class="text-center editable"><?php echo $productCategory['ProductCategory']['order']; ?></td>
						<td class="text-center">
							<?php if($this->App->menu_permission('productCategories','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $productCategory['ProductCategory']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('productCategories','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $productCategory['ProductCategory']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $productCategory['ProductCategory']['id'])); } ?>
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
<script type="text/javascript">
$.fn.makeEditable = function(url) {
	$(this).dblclick(function(){
		if($(this).find('input').is(':focus')) return this;
		var cell = $(this);
		var content = $(this).html();
		$(this).html('<input type="text" value="' + $(this).html() + '" />')
		.find('input')
		.trigger('focus')
		.on({
			'blur': function(){
				$(this).trigger('closeEditable');
			},
			'keyup':function(e){
				if(e.which == '13'){ // enter
					$(this).trigger('saveEditable');
				} else if(e.which == '27'){ // escape
					$(this).trigger('closeEditable');
				}
			},
			'closeEditable':function(){
				cell.html(content);
			},
			'saveEditable':function(){
				content = $(this).val();
				$(this).trigger('closeEditable');
			},
			'change':function(){
				order_value = $(this).val();
				CatID = $(this).closest('tr').attr('id');
				//alert(value);
				$.ajax({
					type: 'POST',
					data: {
						CategoryId:CatID,
						OrderVal:order_value,
						LastOrderVal:content
					},
					dataType: 'text',
					url: '<?= BASE_URL?>'+url,
					success: function(data){
						content = data;
						$(this).trigger('closeEditable');
					}
				});
			}
		});
	});
	return this;
}
$('.editable').makeEditable('product_categories/change_order');
</script>
