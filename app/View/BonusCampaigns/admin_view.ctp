<?php
	App::import('Controller', 'BonusCampaignsController');
	$BonusCampaignsController = new BonusCampaignsController;	

?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('BonusCampaign'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus Campaign List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Brands" class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td><strong><?php echo __('Id'); ?></strong></td>
							<td>
								<?php echo h($bonuscampaign['BonusCampaign']['id']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('IS SO'); ?></strong></td>
							<td>
								<?php
									if($bonuscampaign['BonusCampaign']['is_so'] == 1){
										echo "Yes";
									}else{
										echo 'No';
									}
								?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('IS SR'); ?></strong></td>
							<td>
								<?php
									if($bonuscampaign['BonusCampaign']['is_sr'] == 2){
										echo "Yes";
									}else{
										echo 'No';
									}
								?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Start Date'); ?></strong></td>
							<td>
								<?php echo h($bonuscampaign['BonusCampaign']['start_date']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('End Date'); ?></strong></td>
							<td>
								<?php echo h($bonuscampaign['BonusCampaign']['end_date']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Product'); ?></strong></td>
							<td>
								<?php 
									$prductName = $BonusCampaignsController->get_product_name($bonuscampaign['BonusCampaign']['id']);
									echo $prductName;
								?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Details'); ?></strong></td>
							<td>
								<?php echo h($bonuscampaign['BonusCampaign']['bonus_details']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>		
							<td><strong><?php echo __('Attachment'); ?></strong></td>
							<td>
								<?php 
									if(!empty($bonuscampaign['BonusCampaign']['attachment'] )){
										$at = $bonuscampaign['BonusCampaign']['attachment'];
										echo '<img src='.BASE_URL.'app/webroot/img/bonus_attachment/'.$at.' >';
									}else{
										echo 'No Attachment';
									}
								?>
								
								&nbsp;
							</td>
						</tr>
					</tbody>
				</table>
			</div>			
		</div>
		
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

