<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Institute'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Institute List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Institutes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Short Name'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['short_name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Type'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['type']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Address'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['address']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Email'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['email']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Telephone'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['telephone']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Contactname'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['contactname']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Active'); ?></strong></td>
		<td>
			<?php echo h($institute['Institute']['is_active']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Product Prices'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Product Price'), array('controller' => 'product_prices', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($institute['ProductPrice'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Product Id'); ?></th>
		<th class="text-center"><?php echo __('Target Custommer'); ?></th>
		<th class="text-center"><?php echo __('Measurement Unit Id'); ?></th>
		<th class="text-center"><?php echo __('Institute Id'); ?></th>
		<th class="text-center"><?php echo __('Effective Date'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
		<th class="text-center"><?php echo __('General Price'); ?></th>
		<th class="text-center"><?php echo __('Has Combination'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($institute['ProductPrice'] as $productPrice): ?>
		<tr>
			<td class="text-center"><?php echo $productPrice['id']; ?></td>
			<td class="text-center"><?php echo $productPrice['product_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['target_custommer']; ?></td>
			<td class="text-center"><?php echo $productPrice['measurement_unit_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['institute_id']; ?></td>
			<td class="text-center"><?php echo $productPrice['effective_date']; ?></td>
			<td class="text-center"><?php echo $productPrice['is_active']; ?></td>
			<td class="text-center"><?php echo $productPrice['general_price']; ?></td>
			<td class="text-center"><?php echo $productPrice['has_combination']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'product_prices', 'action' => 'view', $productPrice['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'product_prices', 'action' => 'edit', $productPrice['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'product_prices', 'action' => 'delete', $productPrice['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $productPrice['id'])); ?>
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
					<h3 class="box-title"><?php echo __('Related Projects'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Project'), array('controller' => 'projects', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($institute['Project'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Name'); ?></th>
		<th class="text-center"><?php echo __('Start Date'); ?></th>
		<th class="text-center"><?php echo __('End Date'); ?></th>
		<th class="text-center"><?php echo __('Is Active'); ?></th>
		<th class="text-center"><?php echo __('Institute Id'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($institute['Project'] as $project): ?>
		<tr>
			<td class="text-center"><?php echo $project['id']; ?></td>
			<td class="text-center"><?php echo $project['name']; ?></td>
			<td class="text-center"><?php echo $project['start_date']; ?></td>
			<td class="text-center"><?php echo $project['end_date']; ?></td>
			<td class="text-center"><?php echo $project['is_active']; ?></td>
			<td class="text-center"><?php echo $project['institute_id']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'projects', 'action' => 'view', $project['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'projects', 'action' => 'edit', $project['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'projects', 'action' => 'delete', $project['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $project['id'])); ?>
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

