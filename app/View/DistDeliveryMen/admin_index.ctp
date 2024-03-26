<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Delivery Mans'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistDeliveryMans', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Delivery Mans'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                    
                    <?php
                    if ($this->App->menu_permission('DistRoutes', 'admin_dm_history')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Delivery Mans and Route/Beat Mapping History'), array('controller'=>'dist_routes','action' => 'dm_history'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDeliveryMan', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr><td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----', 'options' => $offices)); ?></td> </tr>
                        <tr><td width="50%"><?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor', 'id' => 'dist_distributor_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----')); ?></td></tr>
<!--                        <tr><td width="50%"><?php // echo $this->Form->input('name', array('id' => 'name', 'class' => 'form-control name', 'required' => false, 'type' => 'text')); ?></td> </tr>-->

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('code'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_distributor_id',"Distributor"); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($territories as $territory):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo h($territory['DistDeliveryMan']['id']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDeliveryMan']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDeliveryMan']['code']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['Office']['office_name']); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($territory['DistDeliveryMan']['is_active'] == 1) {
                                        echo h('Yes');
                                    } else {
                                        echo h('No');
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistDeliveryMans', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $territory['DistDeliveryMan']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    
                                    if ($this->App->menu_permission('DistDeliveryMans', 'admin_view')) {
                                        echo " ".$this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $territory['DistDeliveryMan']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                    }
                                    if ($this->App->menu_permission('DistRoutes', 'admin_sr_route_mapping')) {
                                   // echo " ".$this->Html->link(__('<i class="glyphicon glyphicon-link"></i>'), array('controller' => 'DistRoutes','action' => 'admin_sr_route_mapping',$territory['Office']['id'],$territory['DistDistributor']['id'],$territory['DistDeliveryMan']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Route Mapping'));
                                    }
                                    
                                    if ($this->App->menu_permission('DistDeliveryMans', 'admin_delete')) {
                                        echo " ".$this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $territory['DistDeliveryMan']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $territory['DistDeliveryMan']['id']));
                                    }
                                    ?>
                                </td>
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

<script>
    $('#office_id').selectChain({
        target: $('#dist_distributor_id'),
        value: 'name',
        url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
        type: 'post',
        data: {'office_id': 'office_id'}
    });

</script>