<?php 
	
	//echo '<pre>';print_r($VatexecutingProduct);exit;
	
?>

<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Vat Executing Product'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Vat Executing Product'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Designations" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong><?php echo __('Id'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['VatexecutingProduct']['id']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('Product Type'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['ProductType']['name']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('Product'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['Product']['name']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('Effective Date'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['VatexecutingProduct']['effective_date']); ?>
								&nbsp;
							</td>
						</tr>	
						<tr>
							<td><strong><?php echo __('Price'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['VatexecutingProduct']['price']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('Vat'); ?></strong></td>
							<td>
								<?php echo h($VatexecutingProduct['VatexecutingProduct']['vat']); ?>
								&nbsp;
							</td>
						</tr>						
					</tbody>
			</table>
		</div>
	</div>		
</div>


