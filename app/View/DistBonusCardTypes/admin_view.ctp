<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Incentive Affiliation Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="DistBonusCardTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($bonusCardType['DistBonusCardType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($bonusCardType['DistBonusCardType']['name']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Bonus Cards'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Bonus Card'), array('controller' => 'bonus_cards', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($bonusCardType['BonusCard'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Fiscal Year Id'); ?></th>
		<th class="text-center"><?php echo __('Bonus Card Type Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Min Qty Per Memo'); ?></th>
		<th class="text-center"><?php echo __('Min Qty Per Year'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Created By'); ?></th>
		<th class="text-center"><?php echo __('Updated At'); ?></th>
		<th class="text-center"><?php echo __('Updated By'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($bonusCardType['BonusCard'] as $bonusCard): ?>
		<tr>
			<td class="text-center"><?php echo $bonusCard['id']; ?></td>
			<td class="text-center"><?php echo $bonusCard['name']; ?></td>
			<td class="text-center"><?php echo $bonusCard['fiscal_year_id']; ?></td>
			<td class="text-center"><?php echo $bonusCard['bonus_card_type_id']; ?></td>
			<td class="text-center"><?php echo $bonusCard['product_id']; ?></td>
			<td class="text-center"><?php echo $bonusCard['min_qty_per_memo']; ?></td>
			<td class="text-center"><?php echo $bonusCard['min_qty_per_year']; ?></td>
			<td class="text-center"><?php echo $bonusCard['is_active']; ?></td>
			<td class="text-center"><?php echo $bonusCard['created_at']; ?></td>
			<td class="text-center"><?php echo $bonusCard['created_by']; ?></td>
			<td class="text-center"><?php echo $bonusCard['updated_at']; ?></td>
			<td class="text-center"><?php echo $bonusCard['updated_by']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'bonus_cards', 'action' => 'view', $bonusCard['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'bonus_cards', 'action' => 'edit', $bonusCard['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'bonus_cards', 'action' => 'delete', $bonusCard['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $bonusCard['id'])); ?>
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

