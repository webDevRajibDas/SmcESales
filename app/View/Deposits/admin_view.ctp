<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Deposit'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Deposit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Deposits" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Memo'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($deposit['Memo']['id'], array('controller' => 'memos', 'action' => 'view', $deposit['Memo']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Sales Person'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($deposit['SalesPerson']['name'], array('controller' => 'sales_people', 'action' => 'view', $deposit['SalesPerson']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Bank Account'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($deposit['BankAccount']['id'], array('controller' => 'bank_accounts', 'action' => 'view', $deposit['BankAccount']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Deposit Amount'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['deposit_amount']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Deposit Date'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['deposit_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Instrument Type'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['instrument_type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Instrument Bank Branch'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['instrument_bank_branch']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Intrument Clearing Date'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['intrument_clearing_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Cleared At'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['cleared_at']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Instrument Clearing Status'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['instrument_clearing_status']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Remarks'); ?></strong></td>
		<td>
			<?php echo h($deposit['Deposit']['remarks']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Collections'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Collection'), array('controller' => 'collections', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($deposit['Collection'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Memo Id'); ?></th>
		<th class="text-center"><?php echo __('Is Credit Collection'); ?></th>
		<th class="text-center"><?php echo __('Instrument Type'); ?></th>
		<th class="text-center"><?php echo __('Bank Account Id'); ?></th>
		<th class="text-center"><?php echo __('InstrumentRefNo'); ?></th>
		<th class="text-center"><?php echo __('Instrument Date'); ?></th>
		<th class="text-center"><?php echo __('CollectionAmount'); ?></th>
		<th class="text-center"><?php echo __('CllectionDate'); ?></th>
		<th class="text-center"><?php echo __('Deposit Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($deposit['Collection'] as $collection): ?>
		<tr>
			<td class="text-center"><?php echo $collection['id']; ?></td>
			<td class="text-center"><?php echo $collection['memo_id']; ?></td>
			<td class="text-center"><?php echo $collection['is_credit_collection']; ?></td>
			<td class="text-center"><?php echo $collection['instrument_type']; ?></td>
			<td class="text-center"><?php echo $collection['bank_account_id']; ?></td>
			<td class="text-center"><?php echo $collection['instrumentRefNo']; ?></td>
			<td class="text-center"><?php echo $collection['instrument_date']; ?></td>
			<td class="text-center"><?php echo $collection['collectionAmount']; ?></td>
			<td class="text-center"><?php echo $collection['cllectionDate']; ?></td>
			<td class="text-center"><?php echo $collection['deposit_id']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'collections', 'action' => 'view', $collection['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'collections', 'action' => 'edit', $collection['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'collections', 'action' => 'delete', $collection['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $collection['id'])); ?>
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

