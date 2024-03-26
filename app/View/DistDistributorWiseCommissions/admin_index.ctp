<?php 
//pr($dist_commissions);die();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Commission Rate List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistDistributorWiseCommissions', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Commission Rate'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistDistributorWiseCommission', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- Select Office ----', 'options' => $offices)); ?></td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor :','class' => 'form-control dist_distributor_id','id'=>'dist_distributor_id', 'empty' => '---- Select ----','options' => $distributors)); ?></td>

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
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center"><?php echo $this->Paginator->sort('SL'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Distributor Name'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Effective Date'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('Commission Rate'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dist_commissions as $dist_commission): ?>
                            <tr>
                                <td class="text-center"><?php echo h($dist_commission['DistDistributorWiseCommission']['id']); ?></td>
                                <td class="text-center"><?php echo h($dist_commission['DistDistributor']['name']); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $effective_date =date('d-m-Y', strtotime($dist_commission['DistDistributorWiseCommission']['effective_date'])) ;
                                        echo h($effective_date); 
                                    ?></td>
                                <td class="text-center"> <?php echo h($dist_commission['DistDistributorWiseCommission']['commission_rate']); echo '%';?></td>
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
    $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributorWiseCommissions/get_distributor_list_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
});
</script>