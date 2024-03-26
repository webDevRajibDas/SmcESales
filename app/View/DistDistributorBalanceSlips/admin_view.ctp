<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor Balance'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Balance List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>        
                            <th class="text-center" width="50">SL.</th>  
							<th class="text-center">Transaction Date</th> 
							<th class="text-center">Transaction Head</th>     
							<th class="text-center">Transaction Type</th>               
                            <th class="text-center">Transaction Amount</th>
                            <th class="text-center">Balance</th>
                        </tr>
                        <?php
                        if(!empty($distributor_balance)){
						
                        $sl = 1;
                        $total =0;
                        $total_price = 0;
                        foreach($distributor_balance as $val){ ?>
                        <tr>        
                            <td align="center"><?php echo $sl; ?></td>
							<td align="center"><?php echo date('d-m-Y', strtotime($val['DistDistributorBalanceHistory']['created_at'])); ?></td>
							<td align="center"><?php echo $val['DistBalanceTransactionType']['name']; ?></td>
							<td align="center"><?php echo $val['DistDistributorBalanceHistory']['balance_type']==1?'Credit':'Debit'; ?></td>
                            <td align="center"><?php echo $val['DistDistributorBalanceHistory']['transaction_amount']; ?></td>
                            <td align="center"><?php echo $val['DistDistributorBalanceHistory']['balance']; ?></td>
                        </tr>
                        <?php
                            $total_price =  $total_price + $total;
                            $sl++;
                        }}
                        ?>
                    </tbody>
                </table>
            </div>			
        </div>
    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

