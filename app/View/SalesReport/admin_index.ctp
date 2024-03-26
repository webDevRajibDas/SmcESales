<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Sales Report'); ?></h3>
			</div>						
			<div class="box-body">
                <!--
				<div class="search-box">
					<?php echo $this->Form->create('Store', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('store_type_id', array('class' => 'form-control','empty'=>'---- Select Type ----','options'=>$store_types,'required'=>false)); ?></td>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id'=>'office_id','class' => 'form-control','empty'=>'---- Select Office ----','required'=>false)); ?></td>							
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
				<table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center" width="50">SL.</th>
							<th class="text-center">Product Name</th>							
							<th width="15%" class="text-center">Price</th>							
							<th width="15%" class="text-center">Quantity</th>							
							<th width="15%" class="text-center">Total Price</th>					
						</tr>
						<?php
						if(!empty($sales_report)){
						$sl = 1;
						$total_price = 0;
						foreach($sales_report as $val){							
						?>
							<tr>		
								<td align="center"><?php echo $sl; ?></td>
								<td><?php echo $val['product_info']['Product']['name']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['price']); ?></td>
								<td align="center"><?php echo $val['quantity']; ?></td>
								<td align="right"><?php echo sprintf('%.2f',$val['subtotal_price']); ?></td>								
							</tr>
						<?php
							$total_price =  $total_price + $val['subtotal_price'];
							$sl++;
						}
						?>
						<tr>		
							<td align="right" colspan="4"><strong>Total Amount :</strong></td>
							<td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>							
						</tr>
						<?php
							}else{
						?>
						<tr>		
							<td align="center" colspan="5"><strong>No product available</strong></td>	
						</tr>
						<?php
							}
						?>	
				</table>
			</div>
		</div>			
	</div>
</div>

