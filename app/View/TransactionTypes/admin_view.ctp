<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Transaction Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Transaction Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="TransactionTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Transaction Code'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['transaction_code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Side'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['side']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Inout'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['inout']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created At'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['created_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created By'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['created_by']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated At'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['updated_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Updated By'); ?></strong></td>
		<td>
			<?php echo h($transactionType['TransactionType']['updated_by']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Challans'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Challan'), array('controller' => 'challans', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($transactionType['Challan'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Challan No'); ?></th>
		<th class="text-center"><?php echo __('Challan Type'); ?></th>
		<th class="text-center"><?php echo __('Challan Date'); ?></th>
		<th class="text-center"><?php echo __('Remarks'); ?></th>
		<th class="text-center"><?php echo __('Challan Referance No'); ?></th>
		<th class="text-center"><?php echo __('Sender Id'); ?></th>
		<th class="text-center"><?php echo __('Sender Type'); ?></th>
		<th class="text-center"><?php echo __('Transaction Type Id'); ?></th>
		<th class="text-center"><?php echo __('Receiver'); ?></th>
		<th class="text-center"><?php echo __('Receiver Type'); ?></th>
		<th class="text-center"><?php echo __('Receiving Transaction Type'); ?></th>
		<th class="text-center"><?php echo __('Received By'); ?></th>
		<th class="text-center"><?php echo __('Received Date'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($transactionType['Challan'] as $challan): ?>
		<tr>
			<td class="text-center"><?php echo $challan['id']; ?></td>
			<td class="text-center"><?php echo $challan['challan_no']; ?></td>
			<td class="text-center"><?php echo $challan['challan_type']; ?></td>
			<td class="text-center"><?php echo $challan['challan_date']; ?></td>
			<td class="text-center"><?php echo $challan['remarks']; ?></td>
			<td class="text-center"><?php echo $challan['challan_referance_no']; ?></td>
			<td class="text-center"><?php echo $challan['sender_id']; ?></td>
			<td class="text-center"><?php echo $challan['sender_type']; ?></td>
			<td class="text-center"><?php echo $challan['transaction_type_id']; ?></td>
			<td class="text-center"><?php echo $challan['receiver']; ?></td>
			<td class="text-center"><?php echo $challan['receiver_type']; ?></td>
			<td class="text-center"><?php echo $challan['receiving_transaction_type']; ?></td>
			<td class="text-center"><?php echo $challan['received_by']; ?></td>
			<td class="text-center"><?php echo $challan['received_date']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'challans', 'action' => 'view', $challan['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'challans', 'action' => 'edit', $challan['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'challans', 'action' => 'delete', $challan['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $challan['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

