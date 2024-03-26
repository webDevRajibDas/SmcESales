<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sales Representatives'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistSalesRepresentatives', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Sales Representatives'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    } ?>

                    <?php
                    if ($this->App->menu_permission('DistSalesRepresentatives', 'admin_add_replacement')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>Replace New SR'), array('action' => 'add_replacement'), array('class' => 'btn btn-info', 'escape' => false));
                    }

                    ?>

                    <?php
                    if ($this->App->menu_permission('DistRoutes', 'admin_sr_history')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR and Route/Beat Mapping History'), array('controller' => 'dist_routes', 'action' => 'sr_history'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistSalesRepresentative', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select ----', 'options' => $offices)); ?></td>
                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor', 'id' => 'dist_distributor_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select ----')); ?></td>
                        </tr>

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('name', array('id' => 'name', 'class' => 'form-control name', 'label' => 'Name or Code', 'placeholder' => 'Search SR name or Code', 'required' => false, 'type' => 'text')); ?></td>
                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('status', array('id' => 'status', 'class' => 'form-control status', 'required' => false, 'empty' => '---- Select ----', 'options' => array('1' => 'Active', '2' => 'In-Active'))); ?></td>

                        </tr>
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                <?php echo $this->Form->button('<i class="fa fa-download"></i> Download Excel', array('type' => 'button', 'class' => 'btn btn-large btn-primary', 'id' => 'downloadexcel', 'escape' => false)); ?>
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
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_distributor_id', "Distributor"); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('office_order', "Office"); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($territories as $territory) :

                            //if($territory['DistSalesRepresentative']['is_active']== 1){
                        ?>
                            <tr>
                                <td class="text-center"><?php echo h($territory['DistSalesRepresentative']['id']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistSalesRepresentative']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistSalesRepresentative']['code']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['Office']['office_name']); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($territory['DistSalesRepresentative']['is_active'] == 1) {
                                        echo h('Yes');
                                    } else {
                                        echo h('No');
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistSalesRepresentatives', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $territory['DistSalesRepresentative']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }

                                    if ($this->App->menu_permission('DistSalesRepresentatives', 'admin_view')) {
                                        echo " " . $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $territory['DistSalesRepresentative']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                    }
                                    if ($this->App->menu_permission('DistRoutes', 'admin_sr_route_mapping')) {
                                        echo " " . $this->Html->link(__('<i class="glyphicon glyphicon-link"></i>'), array('controller' => 'DistRoutes', 'action' => 'admin_sr_route_mapping', $territory['Office']['id'], $territory['DistDistributor']['id'], $territory['DistSalesRepresentative']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Route Mapping'));
                                    }

                                    if ($this->App->menu_permission('DistSalesRepresentatives', 'admin_delete')) {
                                        echo " " . $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $territory['DistSalesRepresentative']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $territory['DistSalesRepresentative']['id']));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php //} 
                        endforeach; ?>
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
        data: {
            'office_id': 'office_id'
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#downloadexcel').click(function() {
            var formData = $(this).closest('form').serialize();
            // var arrStr = encodeURIComponent(formData);
            /*console.log(formData);
            console.log(arrStr);*/
            window.open("<?= BASE_URL; ?>DistSalesRepresentatives/download_xl?" + formData);
        });
    });
</script>