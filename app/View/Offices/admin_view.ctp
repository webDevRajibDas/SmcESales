<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Type'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Office Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="OfficeTypes" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($officeType['OfficeType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Type Name'); ?></strong></td>
		<td>
			<?php echo h($officeType['OfficeType']['type_name']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Offices'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Office'), array('controller' => 'offices', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($officeType['Office'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Office Code'); ?></th>
		<th class="text-center"><?php echo __('Office Name'); ?></th>
		<th class="text-center"><?php echo __('Office Type Id'); ?></th>
		<th class="text-center"><?php echo __('Parent Office Id'); ?></th>
		<th class="text-center"><?php echo __('Office Head Id'); ?></th>
		<th class="text-center"><?php echo __('Is Shop'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($officeType['Office'] as $office): ?>
		<tr>
			<td class="text-center"><?php echo $office['id']; ?></td>
			<td class="text-center"><?php echo $office['office_code']; ?></td>
			<td class="text-center"><?php echo $office['office_name']; ?></td>
			<td class="text-center"><?php echo $office['office_type_id']; ?></td>
			<td class="text-center"><?php echo $office['parent_office_id']; ?></td>
			<td class="text-center"><?php echo $office['office_head_id']; ?></td>
			<td class="text-center"><?php echo $office['is_shop']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'offices', 'action' => 'view', $office['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'offices', 'action' => 'edit', $office['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'offices', 'action' => 'delete', $office['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $office['id'])); ?>
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

