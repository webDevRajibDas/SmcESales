<?php 
//pr($distBonusCard);die();
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Incentive Party'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
				<div class="box-body">
		            <table id="DistBonusCards" class="table table-bordered table-striped">
					<tbody>
						
						<tr>		
						</tr>
						<tr>
							<td><strong><?php echo __('Incentive Party Name'); ?></strong></td>
							<td>
							<?php echo h($distBonusCard['DistBonusCard']['name']); ?>
							&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('Incentive Party Type'); ?></strong></td>
							<td>
							<?php echo h($distBonusCard['DistBonusCardType']['name']); ?>
							&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('From Date'); ?></strong></td>
							<td>
							<?php echo h($distBonusCard['DistBonusCard']['date_from']); ?>
							&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('To Date'); ?></strong></td>
							<td>
							<?php echo h($distBonusCard['DistBonusCard']['date_to']); ?>
							&nbsp;
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="box-body">
		        <table class="table table-bordered">
                    <tbody>
                        <tr>		
                            <th class="text-center" width="50">SL.</th>
                            <th class="text-left">Product Name</th>							
                            <th class="text-center">Quantity</th>							
                            				
                        </tr>
                        <?php
                        if(!empty($distBonusCard['DistProductsBonusCard'])){
                        $sl = 1;
                        foreach($distBonusCard['DistProductsBonusCard'] as $val){ 
                       					
                        ?>
                            <tr>		
                                <td align="center"><?php echo $sl; ?></td>
                                <td><?php echo $products[$val['product_id']]; ?></td>
                                <td align="center"><?php echo $val['qty']; ?></td>
                            </tr>
                        <?php }?>
                        <?php }else{ ?>
                        <tr>		
                            <td align="center" colspan="5"><strong>No product available</strong></td>	
                        </tr>
                        <?php } ?>
                    </tbody>	
                </table>
			</div>
			
			<br>
			<br>
			<br>

			<div class="box-body">
		        <table class="table table-bordered">
                    <tbody>
                        <tr>		
                            <th class="text-center" width="50">SL.</th>
                            <th class="text-left">Product Name</th>							
                            <th class="text-left">From Date</th>							
                            <th class="text-left">To Date</th>							
                            <th class="text-center">Quantity</th>							
                            				
                        </tr>
                        <?php
                        if(!empty($distBonusCard['DistPeriodsBonusCard'])){
                        $sl = 1;
                        foreach($distBonusCard['DistPeriodsBonusCard'] as $val){ 
                       					
                        ?>
                            <tr>		
                                <td align="center"><?php echo $sl; ?></td>
                                <td><?php echo $products[$val['product_id']]; ?></td>
                                <td><?php echo date("Y-m-d", strtotime($val['date_from'])); ?></td>
                                <td><?php echo date("Y-m-d", strtotime($val['date_to'])); ?></td>
                                <td align="center"><?php echo $val['qty']; ?></td>
                            </tr>
                        <?php }?>
                        <?php }else{ ?>
                        <tr>		
                            <td align="center" colspan="5"><strong>No product available</strong></td>	
                        </tr>
                        <?php } ?>
                    </tbody>	
                </table>
			</div>			
		</div>


	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

