<?php 
//pr($territory); exit;
?>

<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Sales Representative'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Representative List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistSalesRepresentative']['id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Name'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistSalesRepresentative']['name']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo __('Code'); ?></strong></td>
                            <td>
                                <?php echo h($territory['Office']['office_code'])."".h($territory['DistSalesRepresentative']['code']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Mobile Number'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistSalesRepresentative']['mobile_number']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Office'); ?></strong></td>
                            <td>
                                <?php echo h($territory['Office']['office_name']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Is Active'); ?></strong></td>
                            <td>
                                <?php 
                                if(($territory['DistSalesRepresentative']['is_active']))
                                {
                                    echo "Active";
                                }
                                else 
                                {
                                    echo "Inactive";
                                }
                               ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Remarks'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistSalesRepresentative']['remarks']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </div>			
        </div>


        


       


    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

