<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Bank Branch'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bank Branch List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Designations" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong><?php echo __('Id'); ?></strong></td>
							<td>
								<?php echo h($branch['BankBranch']['id']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Bank Branch Name'); ?></strong></td>
							<td>
								<?php echo h($branch['BankBranch']['name']); ?>
								&nbsp;
							</td>
						</tr>
							<tr>		
							<td><strong><?php echo __('Bank Branch Name'); ?></strong></td>
							<td>
								<?php echo h($branch['Bank']['name']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Bank Address'); ?></strong></td>
							<td>
								<?php echo h($branch['BankBranch']['address']); ?>
								&nbsp;
							</td>
						</tr>		
					</tbody>
			</table>
		</div>
	</div>		
</div>


