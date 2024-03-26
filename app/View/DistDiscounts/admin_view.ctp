<?php 
//pr($distDiscount['DistDiscountDetail']);die();
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Distributor Sales Discount'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Discount Lists'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
				<div class="box-body">
		            <table id="DistDiscounts" class="table table-bordered table-striped">
					<tbody>
						<tr>
                            <td><strong><?php echo __('Office'); ?></strong></td>
                            <td>
                            <?php if(!empty($distDiscount['DistDiscount']['office_id'])){
                                echo h($distDiscount['Office']['office_name']); }
                                else{
                                    echo 'N/A';
                                }?>
                            &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __('Distributor'); ?></strong></td>
                            <td>
                            <?php if(!empty($distDiscount['DistDiscount']['distributor_id'])){
                                echo h($distDiscount['Distributor']['name']);
                                }else{
                                    echo 'N/A';
                                } ?>
                            &nbsp;
                            </td>
                        </tr>
						<tr>
							<td><strong><?php echo __('From Date'); ?></strong></td>
							<td>
							<?php echo h(date('d-m-Y',strtotime($distDiscount['DistDiscount']['date_from']))); ?>
							&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('To Date'); ?></strong></td>
							<td>
							<?php echo h(date('d-m-Y',strtotime($distDiscount['DistDiscount']['date_to']))); ?>
							&nbsp;
							</td>
						</tr>
                        <tr>
                            <td><strong><?php echo __('Description'); ?></strong></td>
                            <td>
                            <?php echo h($distDiscount['DistDiscount']['description']); ?>
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
                            <th class="text-center">Memo Value</th>							
                            <th class="text-center">Discount</th>	
                            <th class="text-center">Discount Type</th>						
                            				
                        </tr>
                        <?php
                        if(!empty($distDiscount['DistDiscountDetail'])){
                        $sl = 1;
                        foreach($distDiscount['DistDiscountDetail'] as $val){ 
                       					
                        ?>
                            <tr>		
                                <td align="center"><?php echo $sl; ?></td>
                                <td align="center"><?php echo $val['memo_value']; ?></td>
                                <td align="center"><?php echo $val['discount_percent']; ?></td>
                                <td align="center"><?php echo $val['discount_type']==2?'Taka':'Percentage';?></td>
                            </tr>
                        <?php $sl++; }?>
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

