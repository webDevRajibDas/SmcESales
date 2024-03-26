<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Message Category'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Message Category List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="MessageCategories" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($messageCategory['MessageCategory']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Title'); ?></strong></td>
		<td>
			<?php echo h($messageCategory['MessageCategory']['title']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

					
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __('Related Message Lists'); ?></h3>
					<div class="box-tools pull-right">
						<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('New Message List'), array('controller' => 'message_lists', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
				</div>
				<?php if (!empty($messageCategory['MessageList'])): ?>
					
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
											<th class="text-center"><?php echo __('Id'); ?></th>
		<th class="text-center"><?php echo __('Message Category Id'); ?></th>
		<th class="text-center"><?php echo __('Message'); ?></th>
		<th class="text-center"><?php echo __('Sender Id'); ?></th>
		<th class="text-center"><?php echo __('Message Type'); ?></th>
		<th class="text-center"><?php echo __('Is Promotional'); ?></th>
		<th class="text-center"><?php echo __('Created At'); ?></th>
		<th class="text-center"><?php echo __('Created By'); ?></th>
									<th class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($messageCategory['MessageList'] as $messageList): ?>
		<tr>
			<td class="text-center"><?php echo $messageList['id']; ?></td>
			<td class="text-center"><?php echo $messageList['message_category_id']; ?></td>
			<td class="text-center"><?php echo $messageList['message']; ?></td>
			<td class="text-center"><?php echo $messageList['sender_id']; ?></td>
			<td class="text-center"><?php echo $messageList['message_type']; ?></td>
			<td class="text-center"><?php echo $messageList['is_promotional']; ?></td>
			<td class="text-center"><?php echo $messageList['created_at']; ?></td>
			<td class="text-center"><?php echo $messageList['created_by']; ?></td>
			<td class="text-center">
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'message_lists', 'action' => 'view', $messageList['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'message_lists', 'action' => 'edit', $messageList['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
				<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'message_lists', 'action' => 'delete', $messageList['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $messageList['id'])); ?>
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

