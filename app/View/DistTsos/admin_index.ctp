<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('TSO'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistTsos', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New TSO'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                        echo " " . $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> TSO History'), array('action' => 'history'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistTso', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----', 'options' => $offices)); ?></td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('status', array('id' => 'status', 'class' => 'form-control status', 'required' => false, 'empty' => '---- Select ----', 'options' => array('1' => 'Active', '2' => 'In-Active'))); ?></td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('name', array('id' => 'name', 'class' => 'form-control name', 'required' => false, 'type' => 'text')); ?></td>

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
                            <th class="text-center"><?php echo $this->Paginator->sort('office_order',"Office"); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('is_active'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tsos as $tso) : ?>
                            <tr>
                                <td class="text-center"><?php echo h($tso['DistTso']['id']); ?></td>
                                <td class="text-center"><?php echo h($tso['DistTso']['name']); ?></td>
                                <td class="text-center"><?php echo h($tso['Office']['office_name']); ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($tso['DistTso']['is_active'] == 1) {
                                        echo h('Yes');
                                    } else {
                                        echo h('No');
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistTsos', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $tso['DistTso']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistTsos', 'admin_view')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $tso['DistTso']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistTsos', 'admin_delete')) {
                                        echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $tso['DistTso']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $tso['DistTso']['id']));
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
    $(document).ready(function() {
        $('#downloadexcel').click(function() {
            var formData = $(this).closest('form').serialize();
            // var arrStr = encodeURIComponent(formData);
            /*console.log(formData);
            console.log(arrStr);*/
            window.open("<?= BASE_URL; ?>DistTsos/download_xl?" + formData);
        });
    });
</script>