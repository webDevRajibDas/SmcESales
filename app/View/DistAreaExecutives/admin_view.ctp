<?php 
//pr($tso); exit;
?>
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Area Executive'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Area Executive List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo h($tso['DistAreaExecutive']['id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Name'); ?></strong></td>
                            <td>
                                <?php echo h($tso['DistAreaExecutive']['name']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Office'); ?></strong></td>
                            <td>
                                 <?php echo h($tso['Office']['office_name']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Effective Date'); ?></strong></td>
                            <td>
                                 <?php 
                                // echo h($tso['DistAreaExecutive']['effective_date']); 
                                 echo date("d-m-Y",strtotime($tso['DistAreaExecutive']['effective_date']));?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Is Active'); ?></strong></td>
                            <td>
                                <?php
                                if($tso['DistAreaExecutive']['is_active'])
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
                    </tbody>
                </table>
            </div>			
        </div>
    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

