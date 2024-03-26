<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Disributor Current Inventory'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Disributor Current Inventory List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="CurrentInventories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		<td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('Store Id'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['dist_store_id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('Inventory Status Id'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['inventory_status_id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('Product'); ?></strong></td>
                            <td>
                                <?php echo $this->Html->link($currentInventory['Product']['name'], array('controller' => 'products', 'action' => 'view', $currentInventory['Product']['id']), array('class' => '')); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('Batch Number'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['batch_number']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('M Unit'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['m_unit']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		<td><strong><?php echo __('Qty'); ?></strong></td>
                            <td>
                                <?php echo h($currentInventory['DistCurrentInventory']['qty']); ?>
                                &nbsp;
                            </td>
                        </tr>					</tbody>
                </table>
            </div>			
        </div>


    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

