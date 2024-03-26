<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Instrument Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Instrument Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="InstrumentTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($instrumentType['InstrumentType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($instrumentType['InstrumentType']['name']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Deposits'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Deposits'), array('controller' => 'deposits', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($instrumentType['deposits'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Temp Id'); ?></th>
		<th class="text-center"><?php echo __('Memo No'); ?></th>
		<th class="text-center"><?php echo __('Memo Id'); ?></th>
		<th class="text-center"><?php echo __('Sales Person Id'); ?></th>
		<th class="text-center"><?php echo __('Deposit Amount'); ?></th>
		<th class="text-center"><?php echo __('Deposit Date'); ?></th>
		<th class="text-center"><?php echo __('Instrument Type'); ?></th>
		<th class="text-center"><?php echo __('Bank Branch Id'); ?></th>
		<th class="text-center"><?php echo __('Slip No'); ?></th>
		<th class="text-center"><?php echo __('Instrument Clearing Status'); ?></th>
		<th class="text-center"><?php echo __('Week Id'); ?></th>
		<th class="text-center"><?php echo __('Fiscal Year Id'); ?></th>
		<th class="text-center"><?php echo __('Month Id'); ?></th>
		<th class="text-center"><?php echo __('Remarks'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Action'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($instrumentType['deposits'] as $deposits): ?>
		<tr>
			<td class="text-center"><?php echo $deposits['id']; ?></td>
			<td class="text-center"><?php echo $deposits['temp_id']; ?></td>
			<td class="text-center"><?php echo $deposits['memo_no']; ?></td>
			<td class="text-center"><?php echo $deposits['memo_id']; ?></td>
			<td class="text-center"><?php echo $deposits['sales_person_id']; ?></td>
			<td class="text-center"><?php echo $deposits['deposit_amount']; ?></td>
			<td class="text-center"><?php echo $deposits['deposit_date']; ?></td>
			<td class="text-center"><?php echo $deposits['instrument_type']; ?></td>
			<td class="text-center"><?php echo $deposits['bank_branch_id']; ?></td>
			<td class="text-center"><?php echo $deposits['slip_no']; ?></td>
			<td class="text-center"><?php echo $deposits['instrument_clearing_status']; ?></td>
			<td class="text-center"><?php echo $deposits['week_id']; ?></td>
			<td class="text-center"><?php echo $deposits['fiscal_year_id']; ?></td>
			<td class="text-center"><?php echo $deposits['month_id']; ?></td>
			<td class="text-center"><?php echo $deposits['remarks']; ?></td>
			<td class="text-center"><?php echo $deposits['created_at']; ?></td>
			<td class="text-center"><?php echo $deposits['action']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'deposits', 'action' => 'view', $deposits['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'deposits', 'action' => 'edit', $deposits['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'deposits', 'action' => 'delete', $deposits['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $deposits['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				
			</div><!-- /.related -->

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Collections'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Collection'), array('controller' => 'collections', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($instrumentType['Collection'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('So Id'); ?></th>
		<th class="text-center"><?php echo __('Memo No'); ?></th>
		<th class="text-center"><?php echo __('Memo Id'); ?></th>
		<th class="text-center"><?php echo __('Memo Value'); ?></th>
		<th class="text-center"><?php echo __('Credit Or Due'); ?></th>
		<th class="text-center"><?php echo __('Memo Date'); ?></th>
		<th class="text-center"><?php echo __('Is Credit Collection'); ?></th>
		<th class="text-center"><?php echo __('Type'); ?></th>
		<th class="text-center"><?php echo __('Instrument Type'); ?></th>
		<th class="text-center"><?php echo __('Tax Ammount'); ?></th>
		<th class="text-center"><?php echo __('Tax No'); ?></th>
		<th class="text-center"><?php echo __('Instrument No'); ?></th>
		<th class="text-center"><?php echo __('Payment Id'); ?></th>
		<th class="text-center"><?php echo __('Bank Account Id'); ?></th>
		<th class="text-center"><?php echo __('InstrumentRefNo'); ?></th>
		<th class="text-center"><?php echo __('Instrument Date'); ?></th>
		<th class="text-center"><?php echo __('CollectionAmount'); ?></th>
		<th class="text-center"><?php echo __('CollectionDate'); ?></th>
		<th class="text-center"><?php echo __('Deposit Amount'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Outlet Id'); ?></th>
		<th class="text-center"><?php echo __('Action'); ?></th>
		<th class="text-center"><?php echo __('Is Settled'); ?></th>
		<th class="text-center"><?php echo __('Deposit Id'); ?></th>
		<th class="text-center"><?php echo __('Territory Id'); ?></th>
		<th class="text-center"><?php echo __('Market Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($instrumentType['Collection'] as $collection): ?>
		<tr>
			<td class="text-center"><?php echo $collection['id']; ?></td>
			<td class="text-center"><?php echo $collection['so_id']; ?></td>
			<td class="text-center"><?php echo $collection['memo_no']; ?></td>
			<td class="text-center"><?php echo $collection['memo_id']; ?></td>
			<td class="text-center"><?php echo $collection['memo_value']; ?></td>
			<td class="text-center"><?php echo $collection['credit_or_due']; ?></td>
			<td class="text-center"><?php echo $collection['memo_date']; ?></td>
			<td class="text-center"><?php echo $collection['is_credit_collection']; ?></td>
			<td class="text-center"><?php echo $collection['type']; ?></td>
			<td class="text-center"><?php echo $collection['instrument_type']; ?></td>
			<td class="text-center"><?php echo $collection['tax_ammount']; ?></td>
			<td class="text-center"><?php echo $collection['tax_no']; ?></td>
			<td class="text-center"><?php echo $collection['instrument_no']; ?></td>
			<td class="text-center"><?php echo $collection['payment_id']; ?></td>
			<td class="text-center"><?php echo $collection['bank_account_id']; ?></td>
			<td class="text-center"><?php echo $collection['instrumentRefNo']; ?></td>
			<td class="text-center"><?php echo $collection['instrument_date']; ?></td>
			<td class="text-center"><?php echo $collection['collectionAmount']; ?></td>
			<td class="text-center"><?php echo $collection['collectionDate']; ?></td>
			<td class="text-center"><?php echo $collection['deposit_amount']; ?></td>
			<td class="text-center"><?php echo $collection['created_at']; ?></td>
			<td class="text-center"><?php echo $collection['outlet_id']; ?></td>
			<td class="text-center"><?php echo $collection['action']; ?></td>
			<td class="text-center"><?php echo $collection['is_settled']; ?></td>
			<td class="text-center"><?php echo $collection['deposit_id']; ?></td>
			<td class="text-center"><?php echo $collection['territory_id']; ?></td>
			<td class="text-center"><?php echo $collection['market_id']; ?></td>
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

