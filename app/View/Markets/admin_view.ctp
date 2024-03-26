<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Market'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Market List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Markets" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($market['Market']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Code'); ?></strong></td>
		<td>
			<?php echo h($market['Market']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($market['Market']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Address'); ?></strong></td>
		<td>
			<?php echo h($market['Market']['address']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Location Type'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($market['LocationType']['name'], array('controller' => 'location_types', 'action' => 'view', $market['LocationType']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Thana'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($market['Thana']['name'], array('controller' => 'thanas', 'action' => 'view', $market['Thana']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Territory'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($market['Territory']['name'], array('controller' => 'territories', 'action' => 'view', $market['Territory']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($market['Market']['is_active']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Market People'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Market Person'), array('controller' => 'market_people', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($market['MarketPerson'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Market Id'); ?></th>
		<th class="text-center"><?php echo __('Sales Person Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($market['MarketPerson'] as $marketPerson): ?>
		<tr>
			<td class="text-center"><?php echo $marketPerson['id']; ?></td>
			<td class="text-center"><?php echo $marketPerson['market_id']; ?></td>
			<td class="text-center"><?php echo $marketPerson['sales_person_id']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'market_people', 'action' => 'view', $marketPerson['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'market_people', 'action' => 'edit', $marketPerson['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'market_people', 'action' => 'delete', $marketPerson['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $marketPerson['id'])); ?>
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
					<h3 class="box-title"><?php echo __('Related Outlets'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Outlet'), array('controller' => 'outlets', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($market['Outlet'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Code'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('In Charge'); ?></th>
		<th class="text-center"><?php echo __('Address'); ?></th>
		<th class="text-center"><?php echo __('Telephone'); ?></th>
		<th class="text-center"><?php echo __('Mobile'); ?></th>
		<th class="text-center"><?php echo __('Market Id'); ?></th>
		<th class="text-center"><?php echo __('Category Id'); ?></th>
		<th class="text-center"><?php echo __('Is Pharma Type'); ?></th>
		<th class="text-center"><?php echo __('Is Ngo'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($market['Outlet'] as $outlet): ?>
		<tr>
			<td class="text-center"><?php echo $outlet['id']; ?></td>
			<td class="text-center"><?php echo $outlet['code']; ?></td>
			<td class="text-center"><?php echo $outlet['name']; ?></td>
			<td class="text-center"><?php echo $outlet['in_charge']; ?></td>
			<td class="text-center"><?php echo $outlet['address']; ?></td>
			<td class="text-center"><?php echo $outlet['telephone']; ?></td>
			<td class="text-center"><?php echo $outlet['mobile']; ?></td>
			<td class="text-center"><?php echo $outlet['market_id']; ?></td>
			<td class="text-center"><?php echo $outlet['category_id']; ?></td>
			<td class="text-center"><?php echo $outlet['is_pharma_type']; ?></td>
			<td class="text-center"><?php echo $outlet['is_ngo']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'outlets', 'action' => 'view', $outlet['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'outlets', 'action' => 'edit', $outlet['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'outlets', 'action' => 'delete', $outlet['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $outlet['id'])); ?>
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

