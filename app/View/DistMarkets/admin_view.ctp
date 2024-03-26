<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('SR Market'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Market List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Markets" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo h($market['DistMarket']['id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Code'); ?></strong></td>
                            <td>
                                <?php echo h($market['DistMarket']['code']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Name'); ?></strong></td>
                            <td>
                                <?php echo h($market['DistMarket']['name']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Address'); ?></strong></td>
                            <td>
                                <?php echo h($market['DistMarket']['address']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Location Type'); ?></strong></td>
                            <td>
                                <?php echo $this->Html->link($market['LocationType']['name'], array('controller' => 'location_types', 'action' => 'view', $market['LocationType']['id']), array('class' => '')); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Route/Beat'); ?></strong></td>
                            <td>
                                <?php echo h($market['DistRoute']['name']);?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Thana'); ?></strong></td>
                            <td>
                                <?php echo $this->Html->link($market['Thana']['name'], array('controller' => 'thanas', 'action' => 'view', $market['Thana']['id']), array('class' => '')); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Territory'); ?></strong></td>
                            <td>
                                <?php echo $this->Html->link($market['Territory']['name'], array('controller' => 'territories', 'action' => 'view', $market['Territory']['id']), array('class' => '')); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Is Active'); ?></strong></td>
                            <td>
                                <?php echo ($market['DistMarket']['is_active']==1)?"Active":"Inactive"; ?>
                                &nbsp;
                            </td>
                        </tr>					
                    </tbody>
                </table>
            </div>			
        </div>
    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

