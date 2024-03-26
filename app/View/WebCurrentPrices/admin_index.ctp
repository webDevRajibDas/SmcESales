<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Current Active Price'); ?></h3>
				<div class="box-tools pull-right">
					<div class="pull-right csv_btn">
						<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div id="price_list">
					<table id="price_table" class="table table-bordered table-condensed table-responsive">
						<thead>
							<tr>
								<th class="text-center">Product</th>
								<th class="text-center">MRP</th>
								<th class="text-center">Min Qty</th>
								<th class="text-center">Trade Price</th>
								<th class="text-center">Sr Price</th>
								<th class="text-center">DB Price</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$prev_product_id = 0;
							foreach ($prices as $data) :
							?>
							<tr>
									<?php if ($prev_product_id != $data['product_id']) {
										$prev_product_id = $data['product_id'];
									?>
										<td><?= $data['product_name'] ?></td>
										<td><?= $data['mrp'] ; ?></td>
									<?php } else { ?>
										<td></td>
										<td></td>
									<?php } ?>
									<td><?= $data['min_qty'] ?></td>
									<td>
										<?php 
											if($data['so_price'] > 0){
												echo $data['so_price'] ;
											}else{
												echo '';
											}
										?>
									</td>
									<td><?php 
											if($data['sr_price'] > 0){
												echo $data['sr_price'] ;
											}else{
												echo '';
											}
										?>
										
									</td>
									<td>
										<?php 
											if($data['db_price'] > 0){
												echo $data['db_price'] ;
											}else{
												echo '';
											}
										?>
										
									</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		 function spanRow(column) {
			var categoryColumn = $("table tr td:nth-child(" + column + ")");
			var rowspan = 1;
			var lastElement = categoryColumn.first();
			categoryColumn.each(function() {
				var element = $(this);
				if ($.trim(element.html()) == '') {
					element.remove();
					rowspan++;
				} else {
					lastElement.attr("rowspan", rowspan);
					lastElement = element;
					rowspan = 1;
				}
			});
			lastElement.attr("rowspan", rowspan);
		}
		spanRow(2);
		spanRow(1);
		$("#download_xl").click(function(e) {

			e.preventDefault();
			$("#price_list table").attr('border', 1);
			var html = $("#price_list").html();

			var blob = new Blob([html], {
				type: 'data:application/vnd.ms-excel'
			});

			var downloadUrl = URL.createObjectURL(blob);

			var a = document.createElement("a");

			a.href = downloadUrl;

			a.download = "current_price.xls";

			document.body.appendChild(a);

			a.click();
			$("#price_list table").attr('border', 0);

		});
	});
</script>