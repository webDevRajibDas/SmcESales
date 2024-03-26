<?php
$maintain_dealer=$this->Session->read('Office.company_info.Company.maintain_dealer');
?>

<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<!--- Office Info --->
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Confirm Order'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Order manage List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>


		<div class="col-md-6">	

			<div class="box-body">
				<div class="box-header">
					<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Office Info:'); ?></h3>
				</div>
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="50%"><strong><?php echo 'Office Name. :'; ?></strong></td>
							<td><?php echo $order['Office']['office_name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?=($maintain_dealer)?'Distributor :':'Outlet :'; ?></strong></td>
							<td><?php echo $distributor['DistDistributor']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Market :'; ?></strong></td>
							<td><?php echo $order['Market']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Territory :'; ?></strong></td>
							<td><?php echo $order['Territory']['name']; ?></td>
						</tr>
						
						
					</tbody>
				</table>
			</div>

		</div>
            <!----- End ------>

        <div class="col-md-6">	
			<div class="box-body">
				<div class="box-header">
					<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Order Info'); ?></h3>
				</div>
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="50%"><strong><?php echo 'Order No. :'; ?></strong></td>
							<td><?php echo $order['Order']['order_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Order Date :'; ?></strong></td>
							<td><?php echo $order['Order']['order_date']; ?></td>
						</tr>
						
						<tr>		
							<td><strong><?php echo 'Sales Person :'; ?></strong></td>
							<td><?php echo $order['SalesPerson']['name']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Order Reference No :'; ?></strong></td>
							<td><?php echo $order['Order']['order_reference_no']; ?></td>
							<!--<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									if ($order['Order']['status'] == 1) {
										echo '<span class="btn btn-danger btn-xs">Due</span>';
									}elseif ($order['Order']['status'] == 2) {
										echo '<span class="btn btn-success btn-xs">Paid</span>';
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
									}
								?>
							</td>-->
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<br>
		<div class="col-md-6">	

			<div class="box-body">
				<div class="box-header">
					<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Payment Info:'); ?></h3>
				</div>
			
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td width="50%"><strong><?php echo 'Total Amount :'; ?></strong></td>
							<td><?=sprintf('%.2f', $order['Order']['gross_value'])?></td>
						</tr>
                        <tr>		
							<td width="50%"><strong><?php echo 'Collection Amount :'; ?></strong></td>
							<td><?=sprintf('%.2f', $order['Order']['cash_recieved'])?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Due Payment :'; ?></strong></td>
							<td><?=sprintf('%.2f',$order['Order']['credit_amount'])?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Instrument Type :'; ?></strong></td>
							<td><?=$order['InstrumentType']['name']?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Instrument Reference No:'; ?></strong></td>
							<td><?php echo $order['Order']['instrument_reference_no']; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Payment Status :'; ?></strong></td>
							<td>
								<?php //echo $memo['Memo']['status'] == 2 ? '<span class="btn btn-success btn-xs">Paid</span>' : '<span class="btn btn-danger btn-xs">Due</span>'; ?>
                                <?php
									if ($order['Order']['status'] == 1) {
										echo '<span class="btn btn-danger btn-xs">Due</span>';
									}elseif ($order['Order']['status'] == 2) {
										echo '<span class="btn btn-success btn-xs">Paid</span>';
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
									}
								?>
							</td>
						</tr>
						
					</tbody>
				</table>
			</div>

		</div>
            <!----- End ------>
		
        <?php if($maintain_dealer){ ?>
        <div class="col-md-6">	
			<div class="box-body">
				<div class="box-header">
					<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Credit Limit Info'); ?></h3>
				</div>
                <table class="table table-bordered table-striped">
					<tbody>
						<tr>		
							<td><strong><?php echo 'Current Limit Balance :'; ?></strong></td>
							<td><?php echo $balance; ?></td>
						</tr>
						<tr>		
							<td><strong><?php echo 'Balance When Order Placed :'; ?></strong></td>
							<td><?php echo $orderLimits; ?></td>
						</tr>
						
						
					</tbody>
				</table>
			</div>
		</div>
        <?php } ?>
		
		<br>
		<br>
        
        <?php echo $this->Form->create('OrderProces', array('action' => 'confirm/'.$id, 'role' => 'form')); ?>
            <div class="box-body col-md-12">
                <?php 
                if(!empty($warehouses))
                { 
                    echo $this->Form->input('w_store_id', array('label'=>'Warehouse :', 'id' => 'w_store_id', 'class' => 'form-control w_store_id','required' => true, 'empty'=> '--- select ---', 'options' => $warehouses)); 
                } 
                else 
                {
                    echo $this->Form->input('w_store_id', array('type'=>'hidden','id' => 'w_store_id', 'class' => 'form-control w_store_id', 'value'=> $dwh)); 
                }
               ?>
            </div>
            
            <div class="box-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>		
                            <th class="text-center" width="50">SL.</th>
                            <th class="text-left">Product Name</th>							
                            <th class="text-center">Sales Qty</th>							
                            <th class="text-right">Price</th>					
                            <th class="text-right">Total Price</th>					
                        </tr>
                        <?php
                        if(!empty($order_details)){
                        $sl = 1;
                        $total_price = 0;
                        foreach($order_details as $val){							
                        ?>
                            <tr>		
                                <td align="center"><?php echo $sl; ?></td>
                                <td><?php echo $val['Product']['name']; ?></td>
                                <td align="center"><?php echo $val['OrderDetail']['sales_qty']; ?></td>
                                <td align="right"><?php echo sprintf('%.2f',$val['OrderDetail']['price']); ?></td>
                                <td align="right">
                                    <?php
                                    $total = $val['OrderDetail']['price'] * $val['OrderDetail']['sales_qty'];
                                    echo sprintf('%.2f',$total);
                                    ?>
                                </td>
                            </tr>
                        <?php
                            $total_price =  $total_price + $total;
                            $sl++;
                        }
                        ?>
                        <tr>		
                            <td align="right" colspan="4"><strong>Total Amount :</strong></td>
                            <td align="right"><strong><?php echo sprintf('%.2f',$total_price); ?></strong></td>
                            <?php echo $this->Form->input('total_price', array('label' => false, 'type'=>'hidden', 'id' => 'total_price', 'class' => 'form-control total_price', 'value'=> sprintf('%.2f',$total_price))); ?>							
                        </tr>
                        <tr>		
                            <td align="right" colspan="4"><strong>Collection Amount :</strong></td>
                            <td align="right">
                            <?php echo $this->Form->input('cash_recieved', array('label' => false, 'type'=>'text', 'id' => 'cash_recieved', 'class' => 'form-control cash_recieved', 'value'=> sprintf('%.2f', $order['Order']['cash_recieved']))); ?>
                            </td>							
                        </tr>
                        <tr>		
                            <td align="right" colspan="4"><strong>Due Amount :</strong></td>
                            <td align="right"><strong><?php echo sprintf('%.2f',$order['Order']['credit_amount']); ?></strong></td>							
                        </tr>
                        
                        <?php
                            }else{
                        ?>
                        <tr>		
                            <td align="center" colspan="5"><strong>No product available</strong></td>	
                        </tr>
                        <?php
                            }
                        ?>	
                </table>
            </div>
                
            <style>
            .cash_recieved{
                float:right;
                text-align:right;
            }
            </style>
            
            
            
        
            <div class="row">
            <div class="col-lg-8"></div>
            <div class="col-lg-2"></div>
            <div class="col-lg-2" style="padding-bottom:20px;">
                <?php echo $this->Form->submit('Confirm', array('class' => 'submit_btn btn btn-large btn-primary confirm', 'div'=>false, 'name'=>'confirm')); ?>
            </div>
        <?php echo $this->Form->end(); ?>
       
       </div>
       
       </div>
       
	</div>
</div>

