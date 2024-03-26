<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('DO Challan Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> DO Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo 'Challan No.'; ?></strong></td>
							<td><?php echo h($challan['Challan']['challan_no']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Sender Store'; ?></strong></td>
							<td><?php echo h($challan['SenderStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Challan Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['Challan']['challan_date']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Receiver Store'; ?></strong></td>
							<td><?php echo h($challan['ReceiverStore']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Received Date'; ?></strong></td>
							<td><?php echo $this->App->dateformat($challan['Challan']['received_date']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Status'; ?></strong></td>
							<td><?php echo $challan['Challan']['status'] == 1 ? '<span class="btn btn-warning btn-xs">Pending</span>' : '<span class="btn btn-success btn-xs">Received</span>'; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Remarks'; ?></strong></td>
							<td><?php echo h($challan['Challan']['remarks']); ?></td>
						</tr>						
				</table>
			</div>
			<?php echo $this->Form->create('Challan', array('role' => 'form')); ?>					
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">Batch No.</th>
							<th class="text-center">Expire Date</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Quantity</th>							
							<th class="text-center">Remarks</th>							
						</tr>
						<?php
						if(!empty($challandetail))
						{
							$sl = 1;
							$total_quantity = 0;
							foreach($challandetail as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['ChallanDetail']['batch_no']; ?></td>
							<td align="center"><?php if($val['ChallanDetail']['expire_date']!='0000-00-00'){ echo $this->App->dateformat($val['ChallanDetail']['expire_date']); } ?></td>
							<td><?php echo $val['MeasurementUnit']['name']; ?></td>
							<td align="center">
								<?php echo $val['ChallanDetail']['challan_qty']; ?>
								<input type="hidden" name="id[]" value="<?php echo $val['ChallanDetail']['id']; ?>"/>
								<input type="hidden" name="product_id[]" value="<?php echo $val['ChallanDetail']['product_id']; ?>"/>
								<input type="hidden" name="measurement_unit_id[]" value="<?php echo $val['ChallanDetail']['measurement_unit_id']; ?>"/>
								<input type="hidden" name="quantity[]" value="<?php echo $val['ChallanDetail']['challan_qty']; ?>"/>
								<input type="hidden" name="batch_no[]" value="<?php echo $val['ChallanDetail']['batch_no']; ?>"/>
								<input type="hidden" name="expire_date[]" value="<?php echo $val['ChallanDetail']['expire_date']; ?>"/>
							</td>							
							<td><?php echo $val['ChallanDetail']['remarks']; ?></td>
						</tr>
						<?php
							$total_quantity = $total_quantity + $val['ChallanDetail']['challan_qty'];
							
							$sl++;
							}							
						}
						?>							
					</tbody>	
				</table>
			</div>	
			<?php
			if($challan['Challan']['status'] == 1 AND CakeSession::read('Office.parent_office_id') != 0 AND CakeSession::read('Office.store_id') != $challan['SenderStore']['id']){
			?>
			<?php echo $this->Form->submit('Received', array('class' => 'btn btn-large btn-primary')); ?>
			<?php
			}
			?>
			<?php echo $this->Form->end(); ?>
			</br>	
		</div>		
	</div>
</div>

