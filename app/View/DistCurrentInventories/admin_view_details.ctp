<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Current Inventories'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Current Inventory List'), array('action' => 'admin_index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>	
            <div class="box-body">
                <table id="CurrentInventories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_store_id','Distributor Name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('product_code'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('qty', 'Quantity'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Quantity in Sale Unit'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currentInventories as $currentInventory): ?>
                            <tr>
                                <td class="text-center"><?php echo h($currentInventory['DistCurrentInventory']['id']); ?></td>
                                <td><?php echo h($currentInventory['DistStore']['name']); ?></td>
                                <td><?php echo h($currentInventory['Product']['name']); ?></td>
                                <td class="text-center"><?php echo h($currentInventory['Product']['product_code']); ?></td>
                                <td class="text-center"><?php echo h($currentInventory['InventoryStatuses']['name']); ?></td>
                                <td class="text-center"><?php echo h($currentInventory['DistCurrentInventory']['qty']); ?></td>
                                <td class="text-center"><?php echo h($currentInventory['DistCurrentInventory']['sale_unit_qty']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class='row'>
                    <div class='col-xs-6'>
                        <div id='Users_info' class='dataTables_info'>
                            <?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
                        </div>
                    </div>
                    <div class='col-xs-6'>
                        <div class='dataTables_paginate paging_bootstrap'>
                            <ul class='pagination'>									
                                <?php
                                echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
                                echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                ?>								
                            </ul>
                        </div>
                    </div>
                </div>				
            </div>			
        </div>
    </div>
</div>