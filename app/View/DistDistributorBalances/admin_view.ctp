<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor Balance'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Balance List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                    <!--a class="btn btn-success" id="download_exel">Download</a-->
                </div>
            </div>			
            <div class="box-body">
            <div class="search-box">
                    <form method="post" action="<?=BASE_URL;?>admin/dist_distributor_balances/view/<?=$id;?>">
                    <table class="search">
                        <tr>
                            <td width="50%">
                              <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['date_from'])=='' ? '' : $this->request->data['date_from']),'required'=>true)); ?>    
                            </td>
                            <td width="50%">
                            <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['date_to'])=='' ? '' : $this->request->data['date_to']),'required'=>true)); ?>    
                            </td>
                        </tr>
                      
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>

                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => 'view/'.$id), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <a class="btn btn-success" id="download_exel">Download</a>

                            </td>
                        </tr>


                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>

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
							<td align="center"><?php echo date('d-m-Y', strtotime($val['DistDistributorBalanceHistory']['transaction_date'])); ?></td>
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


<script>
	$('#download_exel').click(function(){
        var dbid = "<?=$id;?>";
        var date_from = $("#date_from").val();
        var date_to = $("#date_to").val();

        if(date_from == ''){
            date_from = 0;
        }

        if(date_to == ''){
            date_to = 0;
        }


        window.open("<?=BASE_URL;?>dist_distributor_balances/balance_history_download_xl/"+dbid+"/"+date_from+"/"+date_to);
    });


	
</script>

