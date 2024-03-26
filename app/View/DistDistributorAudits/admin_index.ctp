
<style>
    .checkbox label{
        font-weight:700;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Audit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('currentInventories', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add Distributor Audit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	

            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorAudit', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="30%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '------- Please Select ------', 'required' => false)); ?></td>							
                            <td width="30%"><?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributors', 'id' => 'dist_distributor_id', 'class' => 'form-control', 'empty' => '------- Please Select ------', 'required' => false,'options'=>$distributors)); ?></td>
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
                <?php
                if ($distDistributorAudits > 0) {
                    ?>

                    <table id="CurrentInventories" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo $this->Paginator->sort('office_id'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('dist_distributor_id'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('audit_date'); ?></th>
                                <th class="text-center"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($distDistributorAudits as $distDistributorAudit): ?>
                                <tr>
                                    <td><?php echo h($distDistributorAudit['Office']['office_name']); ?></td>
                                    <td><?php echo h($distDistributorAudit['DistDistributor']['name']); ?></td>
                                    <td><?php echo h($distDistributorAudit['DistDistributorAudit']['audit_date']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        if ($this->App->menu_permission('DistDistributorAudits', 'admin_edit')) {
                                            echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $distDistributorAudit['DistDistributorAudit']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                        }
                                        if ($this->App->menu_permission('DistDistributorAudits', 'admin_view')) {
                                            echo '&nbsp;'. $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $distDistributorAudit['DistDistributorAudit']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                        }
                                        if ($this->App->menu_permission('DistDistributorAudits', 'admin_delete')) {
                                        echo '&nbsp;'.$this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $distDistributorAudit['DistDistributorAudit']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?',$distDistributorAudit['DistDistributorAudit']['id']));
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

            <?php } ?>

            </div>			
        </div>
    </div>

</div>


<script>
    $(document).ready(function () {
        $('body').on('change', '#office_id', function () {
            var office_id = $(this).val();
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributors/get_dist_distributor_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        if (result.length != 0) {
                            var options //= '<option >------ Please Select ------</option>'
                            for (var x in result) {
                                options += '<option value=' + '"' + result[x].id + '">' + result[x].name + '</option>'
                            }
                            $('#dist_distributor_id').html(options);
                        } else {
                            $('#dist_distributor_id').html('');
                        }
                    }
                });
            }

        });
    });
    $('#product_category_id').selectChain({
        target: $('#product_id'),
        value: 'name',
        url: '<?= BASE_URL . 'current_inventories/get_product_list'; ?>',
        type: 'post',
        data: {'product_category_id': 'product_category_id'}
    });
</script>