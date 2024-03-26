<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Message Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Message List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="MessageCategories" class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="25%"><strong><?php echo __('Message'); ?></strong></td>
							<td><?php echo h($messageList['MessageList']['message']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Sender'); ?></strong></td>
							<td><?php echo h($messageList['SalesPerson']['name']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Message Category'); ?></strong></td>
							<td><?php echo h($messageList['MessageCategory']['title']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Message Type'); ?></strong></td>
							<td>
							<?php 
							if($messageList['MessageList']['message_type'] == 0)
							{
								echo 'Inbox and Ticker Both';
							}elseif($messageList['MessageList']['message_type'] == 1)
							{
								echo 'Ticker';
							}elseif($messageList['MessageList']['message_type'] == 2)
							{
								echo 'Inbox';
							}							
							?>
						</td>
						<tr>		
							<td><strong><?php echo __('Date'); ?></strong></td>
							<td><?php echo $this->App->dateformat($messageList['MessageList']['created_at']); ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Products'); ?></strong></td>
							<td>
								<?php 
								$i = 1;
								foreach($messageProduct as $val)
								{
									echo $i.'. '.$val['Product']['name'].'<br>';
									$i++;
								}
								?>
							</td>
						</tr>
						</tr>
					</tbody>
				</table>
			</div>			
		</div>
	</div>
</div>
