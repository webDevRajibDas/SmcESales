<?php 
//pr($limit); exit;
?>
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor Limits'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Limit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>        
                            <th class="text-center" width="50">SL.</th>                    
                            <th class="text-center">Transaction Amount</th>
                            <th class="text-center">Limits</th>                                 
                            <th class="text-center">Transaction Type</th>                                 
                                       
                        </tr>
                        <?php
                        if(!empty($limit_history)){
                        $sl = 1; 
                        foreach ($limit_history as $key => $val) { ?>
                        <tr>        
                            <td align="center"><?php echo $sl; ?></td>
                            <td align="center"><?php echo $val['DistDistributorLimitHistory']['transaction_amount']; ?></td>
                            <td align="center"><?php echo $val['DistDistributorLimitHistory']['max_amount']; ?></td>
                            <td align="center"><?php echo ($val['DistDistributorLimitHistory']['transaction_type'] == 1)? "Debit" : "Credit" ; ?></td>
                        </tr>
                        <?php $sl++;
                            }
                        }
                        ?>				
                    </tbody>
                </table>
            </div>			
        </div>
    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

