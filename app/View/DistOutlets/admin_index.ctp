<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('SR Outlet List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistOutlets', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New SR Outlet'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistOutlet', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td>
                                <?php echo $this->Form->input('name', array('class' => 'form-control', 'required' => false)); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select ----', 'required' => false)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('category_id', array('label' => 'Outlet Type', 'class' => 'form-control', 'options' => $categories, 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id', 'empty' => '--- Select Distributor ---', 'options' => $distributors)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'required' => false)); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('sr_id', array('label' => 'SR', 'class' => 'form-control ', 'id' => 'sr_id', 'empty' => '--- Select SR ---', 'options' => $sr_list)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td> <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id', 'label' => 'Route/Beat', 'class' => 'form-control dist_market_id', 'empty' => '---- Select ----', 'required' => false)); ?></td>
                            <td>
                                <?php echo $this->Form->input('dist_market_id', array('id' => 'dist_market_id', 'label' => 'Distributor Market', 'class' => 'form-control dist_market_id', 'empty' => '---- Select ----', 'options' => $market_list, 'required' => false)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('bonus_type', array('label' => 'Bonus Type', 'class' => 'form-control', 'options' => $bonus_types, 'required' => false, 'empty' => '---- Select ----')); ?>
                            </td>
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
                <table id="OutletCategories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('name'); ?></th>
                            <!-- <th class="text-center"><?php echo $this->Paginator->sort('address'); ?></th> -->

                            <th class="text-center"><?php echo $this->Paginator->sort('mobile'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_market_id', 'Distributor Market'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('dist_route_id', 'Route/Beat'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('thana_name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('territory_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('category_id', 'Outlet Type'); ?></th>

                            <th class="text-center"><?php echo $this->Paginator->sort('bonus_type_id'); ?></th>

                            <th class="text-center"><?php echo $this->Paginator->sort('is_active', 'Stauts'); ?></th>

                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($outlets as $outlet) : ?>
                            <tr>
                                <td class="text-center"><?php echo h($outlet['DistOutlet']['id']); ?></td>
                                <td><?php echo h($outlet['DistOutlet']['name']); ?></td>
                                <!-- <td><?php echo h($outlet['DistOutlet']['address']); ?></td> -->

                                <td><?php echo h($outlet['DistOutlet']['mobile']); ?></td>
                                <td><?php echo h($outlet['DistMarket']['name']); ?></td>
                                <td><?php echo h($outlet['DistRoute']['name']); ?></td>
                                <td><?php echo h($outlet['Thana']['name']); ?></td>
                                <td><?php echo h($outlet['Territory']['name']); ?></td>
                                <td><?php echo h($outlet['Office']['office_name']); ?></td>
                                <td><?php echo h($outlet['OutletCategory']['category_name']); ?></td>
                                <td>
                                    <?php
                                    if ($outlet['DistOutlet']['bonus_type_id'] == 2) {
                                        echo h('Big Bonus');
                                    } elseif ($outlet['DistOutlet']['bonus_type_id'] == 1) {
                                        echo h('Small Bonus');
                                    } else {
                                        echo h('Not Applicable');
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($outlet['DistOutlet']['is_active'] == 1) {
                                        echo ('<i class="label label-success">Active</i>');
                                    } else {
                                        echo ('<i class="label label-danger">In-Active</i>');
                                    }
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistOutlets', 'admin_view')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $outlet['DistOutlet']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistOutlets', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $outlet['DistOutlet']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php //if($this->App->menu_permission('outlets','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $outlet['Outlet']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $outlet['Outlet']['id'])); }   
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
    $(document).ready(function() {
        $('#office_id').selectChain({
            target: $('#territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
            type: 'post',
            data: {
                'office_id': 'office_id'
            }
        });


        $('#territory_id').selectChain({
            target: $('#thana_id'),
            value: 'name',
            url: '<?= BASE_URL . 'programs/get_thana_list' ?>',
            type: 'post',
            data: {
                'territory_id': 'territory_id',
                'location_type_id': ''
            }
        });

        $("#office_id").change(function() {
            //get_route_by_office_id($(this).val());
        });

        /*function get_route_by_office_id(office_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
        }*/

        $("#thana_id").change(function() {
            get_market_data();
        });

        $("#dist_route_id").change(function() {
            get_market_data();
        });

        function get_market_data() {
            var dist_route_id = $("#dist_route_id").val();
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
                data: {
                    'dist_route_id': dist_route_id
                },
                type: 'POST',
                success: function(data) {
                    $("#dist_market_id").html(data);
                }
            });
        }
        /*function get_market_data()
        {
            var dist_route_id = $("#dist_route_id").val();
            var thana_id = $("#thana_id").val();
            var territory_id = $("#territory_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
                data: {'dist_route_id': dist_route_id, 'thana_id': thana_id,'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_market_id").html(data);
                }
            });
        }*/

        $('#office_id').change(function() {
            $('#dist_market_id').html('<option value="">---- Select -----</option>');
        });

        /*$('#office_id').change(function () {
            $('#thana_id').html('<option value="">---- Select -----</option>');
        });

        $('#territory_id').change(function () {
            $('#dist_market_id').html('<option value="">---- Select -----</option>');
        });*/

        $('#territory_id').change(function() {
            $('#thana_id').html('<option value="">---- Select -----</option>');
        });
        $('.office_id').selectChain({
            target: $('#distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/DistDistributorBalances/get_distribute'; ?>',
            type: 'post',
            data: {
                'office_id': 'office_id'
            }
        });

        $("#distributor_id").change(function() {
            get_sr_list_by_distributor_id($(this).val());
        });

        function get_sr_list_by_distributor_id(distributor_id) {
            $.ajax({
                url: '<?= BASE_URL . 'DistOutlets/get_sr_list_by_distributot_id' ?>',
                data: {
                    'distributor_id': distributor_id
                },
                type: 'POST',
                success: function(data) {
                    $("#sr_id").html(data);
                }
            });
        }
        $("#sr_id").change(function() {
            get_route_list_by_sr_id($(this).val());
        });

        function get_route_list_by_sr_id(sr_id) {
            $.ajax({
                url: '<?= BASE_URL . 'DistOutlets/get_route_list_by_sr_id' ?>',
                data: {
                    'sr_id': sr_id
                },
                type: 'POST',
                success: function(data) {
                    $("#dist_route_id").html(data);
                }
            });
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
            window.open("<?= BASE_URL; ?>DistOutlets/download_xl?" + formData);
        });
    });
</script>