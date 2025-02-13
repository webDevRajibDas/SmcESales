<?php //pr($territories);die();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor List '); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistDistributors', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i>New Distributor'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>

                    <?php
                    if ($this->App->menu_permission('DistDistributors', 'admin_distributor_transfer')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Distributor Replace'), array('action' => 'distributor_transfer'), array('class' => 'btn btn-info', 'escape' => false));
                    }
                    ?>
                    <?php
                    if ($this->App->menu_permission('DistDistributors', 'p')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor and Route/Beat Mapping History'), array('controller' => 'DistDistributors', 'action' => 'dist_history'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>

                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributor', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----', 'options' => $offices)); ?></td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('status', array('id' => 'status', 'class' => 'form-control status', 'required' => false, 'empty' => '---- Select ----', 'options' => array('1' => 'Active', '2' => 'In-Active'))); ?></td>

                        </tr>
                        <!----------add search for name or db_code----------->

                        <tr>
                            <td width="50%"><?php echo $this->Form->input('name', array('id' => 'name', 'class' => 'form-control name', 'label' => 'Name or DB_code:', 'required' => false, 'type' => 'text', 'placeholder' => 'Search name or DB Code')); ?></td>

                        </tr>
                        <!----------add search for name or db_code----------->
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

                            <th class="text-center"><?php echo $this->Paginator->sort('DB_code'); ?></th>

                            <th class="text-center"><?php echo $this->Paginator->sort('office_order',"Office"); ?></th>
                            <th class="text-center"><?php echo 'Area Executive'; ?></th>
                            <th class="text-center"><?php echo 'Mapped TSO Name'; ?></th>
                            <th class="text-center"><?php echo 'Address'; ?></th>
                            <th class="text-center"><?php echo 'Mobile Number'; ?></th>
                            <th class="text-center"><?php echo 'Active Status'; ?></th>
                            <th width="320" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        foreach ($territories as $territory) :
                            //if($territory['DistDistributor']['is_active']==1){
                            $dist_outlet_id = $territory['DistOutletMap']['outlet_id'];
                            $dist_outlet_name = "";
                            $dist_outlet_map_id = 0;

                            if ($dist_outlet_id) {
                                $dist_outlet_name = $outlets[$dist_outlet_id];
                                $dist_outlet_map_id = $territory['DistOutletMap']['id'];
                            }

                            $idd = $territory['DistDistributor']['id'];
                        ?>
                            <tr>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['id']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['name']); ?></td>
                                <!---------add field name and create db field--------------->
                                <td class="text-center"><?php echo h($territory['DistDistributor']['db_code']); ?></td>

                                <td class="text-center"><?php echo h($territory['Office']['office_name']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistAE']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistTso']['name']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['address']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['mobile_number']); ?></td>
                                <td class="text-center"><?php echo h($territory['DistDistributor']['is_active'] == 1 ? 'Active' : 'Inactive'); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistDistributors', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $territory['DistDistributor']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistDistributors', 'admin_delete')) {
                                        echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $territory['DistDistributor']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $territory['DistDistributor']['id']));
                                    }
                                    echo " ";
                                    //echo $this->Html->link(__('<i class="glyphicon">Route/Beat Assign</i>'), array('controller' => 'DistRoutes','action' => 'dist_route_mapping', $territory['Office']['id'],$territory['DistDistributor']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Route/Beat Assign'));
                                    echo " ";
                                    echo $this->Html->link(__('<i class="glyphicon glyphicon-link"> E-sales Mapping</i>'), array('controller' => 'dist_outlet_maps', 'action' => 'admin_mapping', $territory['Office']['id'], $territory['DistDistributor']['id'], $dist_outlet_map_id), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'E-sales Outlet Mapping'));

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
    $(document).ready(function() {
        $('#downloadexcel').click(function() {
            var formData = $(this).closest('form').serialize();
            // var arrStr = encodeURIComponent(formData);
            /*console.log(formData);
            console.log(arrStr);*/
            window.open("<?= BASE_URL; ?>DistDistributors/download_xl?" + formData);
        });
    });
</script>