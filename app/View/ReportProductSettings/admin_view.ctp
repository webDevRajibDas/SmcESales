<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Outlet Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">                
				<table id="Outlet" class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td><strong>Name </strong></td>
							<td><?php echo $outlet['Outlet']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong>Owner Name </strong></td>
							<td><?php echo $outlet['Outlet']['ownar_name']; ?></td>
						</tr>
						<tr>		
							<td><strong>In charge </strong></td>
							<td><?php echo $outlet['Outlet']['in_charge']; ?></td>
						</tr>
						<tr>		
							<td><strong>Address </strong></td>
							<td><?php echo $outlet['Outlet']['address']; ?></td>
						</tr>
						
						<tr>		
							<td><strong>Mobile </strong></td>
							<td><?php echo $outlet['Outlet']['mobile']; ?></td>
						</tr>
						
						
						<tr>		
							<td><strong>Market </strong></td>
							<td><?php echo $outlet['Market']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong>Outlet Type </strong></td>
							<td><?php echo $outlet['OutletCategory']['category_name']; ?></td>
						</tr>
						<tr>		
							<td><strong>Is Pharma Type </strong></td>
							<td>
							<?php
								if($outlet['Outlet']['is_pharma_type']==1){
									echo h('Yes');
								}elseif($outlet['Outlet']['is_pharma_type']==0){
									echo h('No');
								}
							?>
							</td>
						</tr>
						<tr>		
							<td><strong>Is NGO Type </strong></td>
							<td>
								<?php 
								if($outlet['Outlet']['is_ngo']==1){
									echo h('Yes');
								}elseif($outlet['Outlet']['is_ngo']==0){
									echo h('No');
								} 
								?>
							</td>
						</tr>
						<tr>
							<td><strong>Bonus Type</strong></td>
							<td>
								<?php
								if($outlet['Outlet']['bonus_type_id'] == 1){
									echo h('Small Bonus');
								}elseif ($outlet['Outlet']['bonus_type_id'] == 2) {
									echo h('Big Bonus');
								}else{
									echo h('Not Applicable');
								} 
							?>
							</td>
						</tr>
						<?php 
						if($outlet['Outlet']['is_ngo']==1){
						?>
						<tr>		
							<td><strong>Type </strong></td>
							<td>
								<?php 
								if($outlet['Institute']['type']==1){
									echo h('NGO');
								}elseif($outlet['Institute']['type']==2){
									echo h('Institute');
								} 
								?>
							</td>
						</tr>
						<tr>		
							<td><strong>Institute </strong></td>
							<td><?php echo $outlet['Institute']['name']; ?></td>
						</tr>
						<?php 
						}
						?>						
					</tbody>
				</table>
			</div>			
		</div>		
	</div>
</div>

