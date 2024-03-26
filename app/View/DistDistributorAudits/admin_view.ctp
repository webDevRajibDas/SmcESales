<?php
$obj = new DistDistributorAuditsController();
?>
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor Audit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Audit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <?php
          //  pr($distDistributorAudit);
            ?>
            <div class="box-body">
                <table id="CurrentInventories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo $distDistributorAudit['Office']['id']; ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Office Name'); ?></strong></td>
                            <td>
                                <?php echo $distDistributorAudit['Office']['office_name']; ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Distributor name'); ?></strong></td>
                            <td>
                                <?php echo $distDistributorAudit['DistDistributor']['name']; ?>
                                &nbsp;
                            </td>
                        </tr>	
                        
                        <tr>		
                            <td><strong><?php echo __('AE name'); ?></strong></td>
                            <td>
                                <?php echo $ae_list[$distDistributorAudit['DistDistributorAudit']['dist_ae_id']]; ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('TSO name'); ?></strong></td>
                            <td>
                                <?php echo $tso_list[$distDistributorAudit['DistDistributorAudit']['dist_tso_id']]; ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                         <tr>		
                            <td><strong><?php echo __('Audit Date'); ?></strong></td>
                            <td>
                                <?php echo $distDistributorAudit['DistDistributorAudit']['audit_date']; ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Audit By'); ?></strong></td>
                            <td>
                                <?php echo ($distDistributorAudit['DistDistributorAudit']['audit_by']==1)?"AE":"TSO"; ?>
                                &nbsp;
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="box-body">
                <div class="table-responsive">	
                    <table class="table table-bordered">
                        <tbody>
                            <tr>		
                                <th class="text-center">SL.</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Batch No.</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Quantity (Sale Unit)</th>							
                                <th class="text-center">Expire Date</th>												
                                <th class="text-center">Remarks</th>							
                            </tr>
                            <?php
                            if (count($distDistributorAudit) > 0) {
                                $i = 1;
                                foreach ($distDistributorAudit['DistDistributorAuditDetail'] as $key => $value) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $i ?></td>
                                        <td class="text-center"><?= $products[$value['product_id']] ?></td>
                                        <td class="text-center"><?= $value['batch_number'] ?></td>
                                        <td class="text-center">
                                            <?php
                                            $measurement_unit_id = $obj->get_sales_measurement_unit($value['measurement_unit_id'], $value['product_id']);
                                            echo $measurementUnits[$measurement_unit_id];
                                            ?>
                                            <?= $obj->$value['measurement_unit_id'] ?></td>
                                        <td class="text-center"><?= $value['qty'] ?></td>
                                        <td class="text-center"><?= $value['expire_date'] ?></td>
                                        <td class="text-center"><?= '' ?></td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                    </table>
                </div>
            </div>	
        </div>


    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

