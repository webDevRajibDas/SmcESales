<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Gift Item Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Gift Item List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">                
				<table id="GiftItem" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong>Outlet Name :</strong></td>
							<td><?php echo h($GiftItem['Outlet']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Memo No. :</strong></td>
							<td><?php echo h($GiftItem['GiftItem']['memo_no']); ?></td>
						</tr>
						<tr>
							<td><strong>SO Name :</strong></td>
							<td><?php echo h($GiftItem['SalesPerson']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Date :</strong></td>
							<td><?php echo $this->App->dateformat($GiftItem['GiftItem']['date']); ?></td>
						</tr>												
					</tbody>
				</table>
			</div>
			
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<?php
						if(!empty($GiftItemDetails))
						{							
						?>
						<tr>		
							<th width="100" class="text-center">SL.</th>
							<th class="text-center">Product Name</th>														
							<th class="text-center">Quantity</th>						
						</tr>
						<?php						
						$sl = 1;
						foreach($GiftItemDetails as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['GiftItemDetail']['quantity']; ?></td>							
						</tr>
						<?php							
							$sl++;
							}							
						}
						?>							
				</table>
			</div>			
		</div>			
	</div>
</div>

