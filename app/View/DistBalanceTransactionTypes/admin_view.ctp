<?php
//pr($distributor_balance);die();
?>
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
                            <td style="text-align: center;"><strong><?php echo 'Name :'; ?></strong></td>
                            <td style="text-align: center;"><?php echo $distributor_balance['DistBalanceTransactionType']['name']; ?></td>
                        </tr>
                        <tr>        
                            <td style="text-align: center;"><strong><?php echo 'Debit/Credit :'; ?></strong></td>
                            <td style="text-align: center;"><?php echo ($distributor_balance['DistBalanceTransactionType']['inout'] == 0)?  "Credit" : "Debit"; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>			
        </div>
    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

