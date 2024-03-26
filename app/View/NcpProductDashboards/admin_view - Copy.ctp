<div class='row'>
	<div class='col-xs-12'>
		<div class='box box-primary'>
			<div class='box-header'>
				<h3 class='box-title'><i class='glyphicon glyphicon-eye-open'></i> <?php echo __('NCP product type Details '); ?>
				</h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> NCP Product Dashboard'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>

			<div class="box-body">
				<table class="table table-bordered">
					<tr>
						<th>NCP Type</th>
						<th>Area Office</th>
						<th>Territory</th>
						<th>Product</th>
						<th>Quantity</th>
					</tr>
					<?php
					$prv_ncp_type=NULL;
					$prv_area_office=NULL;
					$row_span = 0;
					foreach ($data_arrays as $row):
						$data = $row[0];
						if($prv_ncp_type!=$data['NcpType']){
							$prv_ncp_type = $data['NcpType'];
							$first_row ='
								<td>'.$data['AreaOffice'].'</td>
								<td>'.$data['teritorryName'].'</td>
								<td>'.$data['ProductName'].'</td>
								<td>'.$data['totalProductQty'].'</td>';
							if($row_span>1){
								$next_row = '<tr><td rowspan='.($row_span+1).'>'.$first_col_data.'</td>'.$first_row.'</tr>'.$next_row;
								$first_col_data = $prv_ncp_type;
								$row_span = 1;
							}
						}else{
							$next_row .='
							<tr class="row11">
								<td>'.$data['AreaOffice'].'</td>
								<td>'.$data['teritorryName'].'</td>
								<td>'.$data['ProductName'].'</td>
								<td>'.$data['totalProductQty'].'</td>
							</tr>';
							$row_span+=1;
						}
					endforeach;
					if($row_span>1){
						$next_row = '<tr class="row22"><td rowspan='.($row_span+1).'>'.$prv_ncp_type.'</td>'.$first_row.'</tr>'.$next_row;
						$first_col_data = $prv_ncp_type;
						$row_span = 1;
					}
					echo $next_row;
					?>
				</table>
			</div>

			<div class="box-body">
			</div>
		</div>

	</div>
</div>





<script>

</script>



