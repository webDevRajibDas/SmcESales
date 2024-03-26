<?php 
echo $this->Html->css('select2/select2');
?>
<style>
	#divLoading {
		display : none;
	}
	#divLoading.show {
		display : block;
		position : fixed;
		z-index: 100;
		background-image : url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
		background-color: #666;   
		opacity : 0.4;
		background-repeat : no-repeat;
		background-position : center;
		left : 0;
		bottom : 0;
		right : 0;
		top : 0;
	}
	#loadinggif.show {
		left : 50%;
		top : 50%;
		position : absolute;
		z-index : 101;
		width : 32px;
		height : 32px;
		margin-left : -16px;
		margin-top : -16px;
	}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<div id="divLoading" class=""> </div>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Product List'); ?></h3>
				<h3 class="box-title"><i class="glyphicon glyphicon-th-list"></i> <?php echo __('Select Product Category'); ?></h3>
				<select name="product_category_id" id="product_category_id" class="form-control select2auto" data-route="GetCategoryID" data-allowClear="false" data-placeholder="Select a category first!">
					<option value=""></option>
				</select>
				<div class="box-tools pull-right">
					<?php if($this->UserAuth->getUserId()==1){ ?>
					<a href="#" id="process_auto_arrange" class="btn btn-danger"><i class="glyphicon glyphicon-play-circle"></i>Auto Arrange</a>
					<?php 
					} ?>
					<?php if($this->App->menu_permission('products','admin_index')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Product List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<?php /*?><?php echo $this->Form->create('Product', array('role' => 'form')); ?>
				<table id="sortable" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center">ID</th>
							<th class="text-center">Name</th>
							<th class="text-center">Product Category</th>
							<th class="text-center">Product Type</th>
							<th class="text-center">Measurement Unit</th>
							<th class="text-center">Brand</th>
							<th class="text-center">Order</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($products as $product): ?>
					<tr class="ui-state-default">
						<td class="text-center"><?php echo h($product['Product']['id']); ?></td>
						<td class="text-left"><?php echo h($product['Product']['name']); ?></td>
						<td class="text-center"><?php echo h($product['ProductCategory']['name']); ?></td>
						<td class="text-center"><?php echo h($product['ProductType']['name']); ?></td>
						<td class="text-center"><?php echo h($product['BaseMeasurementUnit']['name']); ?></td>
						<td class="text-center"><?php echo h($product['Brand']['name']); ?></td>
						<td class="text-center">
							<input name="product_id[]" type="hidden" value="<?php echo $product['Product']['id']; ?>"/>
							<input id="order" class="form-control order" style="width:100%;text-align:center;" name="order[]" type="text" value="<?php echo $product['Product']['order']; ?>" required/>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				</br>
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
				</br><?php */?>

				<style>
					#sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
					#sortable li { width: 100%; float:left; margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 0px; font-size: 1.4em; height: auto; font-size:15px;}
					#sortable li:hover{ cursor:move; }
				</style>


				<div style="float:left; width:100%; text-align:center; font-size:15px; font-weight:bold;">
					<div class="col-md-1">SL.</div>
					<div class="col-md-1">ID</div>
					<div class="col-md-3" style="text-align:left;">Name</div>
					<div class="col-md-2">Product Category</div>
					<div class="col-md-2">Product Type</div>
					<div class="col-md-1">Measurement Unit</div>
					<div class="col-md-2">Brand</div>
				</div>

				<ul id="sortable" data-CatId='null' style="text-align:center;">
					<?php //foreach ($products as $product): ?>
						<!--<li id="item-<?=$product['Product']['id']?>" class="ui-state-default">
							<div class="col-md-1"><?=$product['Product']['id']?></div>
							<div class="col-md-3" style="text-align:left;"><?=$product['Product']['name']?></div>
							<div class="col-md-2"><?=$product['ProductCategory']['name']?></div>
							<div class="col-md-2"><?=$product['ProductType']['name']?></div>
							<div class="col-md-2"><?=$product['BaseMeasurementUnit']['name']?></div>
							<div class="col-md-2"><?=$product['Brand']['name']?></div>
						</li>-->
					<?php //endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Html->script('select2/js/select2.min'); ?>
<?php echo $this->Html->script('select2/select2'); ?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
var ArenaSortableSource = function() {
	//$( function() {
	var ArenaSortable = function(selector,url) {
		var SortableSelector = $(selector);
		var currentlyScrolling = true;
		var SCROLL_AREA_HEIGHT = 140;
		
		SortableSelector.sortable({
			axis: 'y',
			scroll: true,
			scrollSensitivity: 80,
			scrollSpeed: 3,
			sort: function(event, ui) {
			
				if (currentlyScrolling) {
					return;
				}

				var windowHeight = $(window).height();
				var mouseYPosition = event.clientY;

				if (mouseYPosition < SCROLL_AREA_HEIGHT) {
					currentlyScrolling = true;

					$('html, body').animate({
						scrollTop: "-=" + windowHeight / 2 + "px"
					}, 
					400, 
					function() {
						currentlyScrolling = false;
					});
				} else if (mouseYPosition > (windowHeight - SCROLL_AREA_HEIGHT)) {

					currentlyScrolling = true;

					$('html, body').animate({
						scrollTop: "+=" + windowHeight / 2 + "px"
					}, 
					400, 
					function() {
						currentlyScrolling = false;
					});
				}
			},
			update: function (event, ui) {
				var data = $(this).sortable('serialize')+'&CategoryID=' + $(this).attr("data-CatId");
				// POST to server using $.post or $.ajax
				$.ajax({
					data:data,
					beforeSend: function() {$("div#divLoading").addClass('show');},
					type: 'POST',
					url: '<?= BASE_URL?>'+url,
					success: function(response){
						$("div#divLoading").removeClass('show');
					}
				});
			}
		});
		SortableSelector.disableSelection();
	};
	return {
		init: function(elm='#sortable',url='products/product_rearrange_update') {
			ArenaSortable(elm,url);
		},
	};
}();
jQuery(document).ready(function() {
	/*
	ArenaSortableSource
	optional argument 
	1. selector.		[default-'#sortable']
	2. data-send url 	[default-'products/product_rearrange_update']
	*/
	var SortableObj = ArenaSortableSource.init();
	
	$("#process_auto_arrange").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			data: {userID:<?=$this->UserAuth->getUserId();?>},
			dataType: 'text',
			url: '<?= BASE_URL.'products/product_auto_rearrange'?>',
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			success: function(rData){
				response = $.parseJSON(rData);
				alert(response.messege);
				$("div#divLoading").removeClass('show');
			}
		});
	});
	$("#product_category_id").change(function (e) {
		e.preventDefault();
		var CatID = $(this).val();
		$.ajax({
			type: 'POST',
			data: {CategoryId:CatID},
			dataType: 'text',
			url: '<?= BASE_URL.'products/get_product_list'?>',
			beforeSend: function() {
				$("div#divLoading").addClass('show');
				$("#sortable").html('');
				$("#sortable").attr('data-CatId',CatID);
			},
			success: function(rData){
				response = $.parseJSON(rData);
				var i =0;
				$.each(response, function (i, item) {
					i++;
					var text = '<div class="col-md-1">'+i+'</div>'+
						'<div class="col-md-1">'+item.Product.id+'</div>'+
						'<div class="col-md-3" style="text-align:left;">'+item.Product.name+'</div>'+
						'<div class="col-md-2">'+item.ProductCategory.name+'</div>'+
						'<div class="col-md-2">'+item.ProductType.name+'</div>'+
						'<div class="col-md-1">'+item.BaseMeasurementUnit.name+'</div>'+
						'<div class="col-md-2">'+item.Brand.name+'</div>';
					var $li = $('<li id="item-'+item.Product.id+'" class="ui-state-default"/>').html(text);
					$("#sortable").append($li);
				});
				$("div#divLoading").removeClass('show');
				$("#sortable").sortable('refresh');
			}
		});
	});
});
</script>