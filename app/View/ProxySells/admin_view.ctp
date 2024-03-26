<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Proxy Sell'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Proxy Sell List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="ProxySells" class="table table-bordered table-striped">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($proxySell['ProxySell']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Proxy For So'); ?></strong></td>
		<td>
			<?php echo h($proxySell['proxyForSo']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Proxy By So'); ?></strong></td>
		<td>
			<?php echo h($proxySell['proxyBySo']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Proxy For Territory'); ?></strong></td>
		<td>
			<?php echo h($proxySell['proxyForTerritory']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Proxy By Territory'); ?></strong></td>
		<td>
			<?php echo h($proxySell['proxyByTerritory']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('From Date'); ?></strong></td>
		<td>
			<?php echo $this->App->dateformat($proxySell['ProxySell']['from_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('To Date'); ?></strong></td>
		<td>
			<?php echo $this->App->dateformat($proxySell['ProxySell']['to_date']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Remarks'); ?></strong></td>
		<td>
			<?php echo h($proxySell['ProxySell']['remarks']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table>
			</div>			
		</div>

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

