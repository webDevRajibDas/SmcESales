<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('SR Market List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistMarkets', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New SR Market'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistMarket', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td>							
                                <?php echo $this->Form->input('name', array('class' => 'form-control', 'required' => false)); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('location_type_id', array('class' => 'form-control', 'options' => $location_list, 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>						
                        </tr>					
                        <tr>
                            <td>							
                                <?php echo $this->Form->input('district_id', array('class' => 'form-control', 'options' => $district_list, 'required' => false, 'empty' => '---- Select ----', 'id' => 'district_id')); ?>
                            </td>
                            <td>							
                                <?php echo $this->Form->input('thana_id', array('class' => 'form-control', 'options' => $thana_list, 'required' => false, 'empty' => '---- Select ----', 'id' => 'thana_id')); ?>
                            </td>							
                        </tr>	
                        <tr>
                            <td>
                                <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'options' => $offices, 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control', 'options' => $territory_list, 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>

                        </tr>	

                        <tr>
                            <td>
                                <?php echo $this->Form->input('dist_route_id', array('label' => 'Route/Beat', 'options' => $route_list,'id' => 'dist_route_id', 'class' => 'form-control', 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>

                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>						
                        </tr>
                    </table>	
                    <?php echo $this->Form->end(); ?>
                </div>
                <table id="Markets" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('address'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('location_type_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_route_id','Route/Beat'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('thana_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        // pr($distmarkets);die();
                        foreach ($distmarkets as $market):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo h($market['DistMarket']['id']); ?></td>
                                <td class="text-center"><?php echo h($market['DistMarket']['name']); ?></td>
                                <td class="text-center"><?php echo h($market['DistMarket']['address']); ?></td>
                                <td class="text-center"><?php echo h($market['LocationType']['name']); ?></td>
                                <td class="text-center"><?php echo h($market['DistRoute']['name']); ?></td>
                                <td class="text-center"><?php echo h($market['Thana']['name']); ?></td>
                                <td class="text-center"><?php echo h($market['Territory']['name']); ?></td>
                                <td class="text-center"><?php echo h($market['Office']['office_name']); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($market['DistMarket']['is_active'] == 1) {
                                        echo h('Yes');
                                    } else {
                                        echo h('No');
                                    }
                                    ?>
                                </td>
                                <td class="text-center">

                                    <?php
                                    if ($this->App->menu_permission('DistMarkets', 'admin_view')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $market['DistMarket']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistMarkets', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'admin_edit', $market['DistMarket']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistMarkets', 'admin_delete')) {
                                        echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'admin_delete', $market['DistMarket']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $market['DistMarket']['id']));
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
    $(document).ready(function () {
        $('#district_id').selectChain({
            target: $('#thana_id'),
            value: 'title',
            url: '<?= BASE_URL . 'markets/get_thana_list' ?>',
            type: 'post',
            data: {'district_id': 'district_id'}
        });

        $('#office_id').selectChain({
            target: $('#territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
        });

        function get_route_by_office_id(office_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistMarkets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
        }


    });
</script>
