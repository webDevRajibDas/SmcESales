<?php 
//pr($distBonusCards);die();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Incentive Affiliation'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistBonusCards', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Add New'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>  
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistBonusCard', array('role' => 'form','action'=>'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('name', array('class' => 'form-control','required'=>false)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('bonus_card_type_id', array('label'=>'Type :','class' => 'form-control','required'=>false,'empty'=>'---- Select ----')); ?></td>                            
                        </tr>                   
                                           
                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
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
                            <th class="text-center"><?php echo $this->Paginator->sort('Type'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('From Date'); ?></th>
                            <th class="text-center"><?php echo $this->Paginator->sort('To Date'); ?></th>
                            <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distBonusCards as $val): ?>
                            <tr>
                                <td class="text-center"><?php echo h($val['DistBonusCard']['id']); ?></td>
                                 <td class="text-center"><?php echo h($val['DistBonusCard']['name']); ?></td>
                                <td class="text-center"><?php echo h($val['DistBonusCardType']['name']); ?></td>
                                <td class="text-center"><?php echo h(date('d-m-Y',strtotime($val['DistBonusCard']['date_from']))); ?></td>
                                <td class="text-center"><?php echo h(date('d-m-Y',strtotime($val['DistBonusCard']['date_to']))); ?></td>
                               
                                <td class="text-center">
                                    <?php
                                    if ($this->App->menu_permission('DistBonusCards', 'admin_edit')) {
                                        echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $val['DistBonusCard']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistBonusCards', 'admin_view')) {
                                         echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $val['DistBonusCard']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                        
                                    }
                                    ?>
                                    <?php
                                    if ($this->App->menu_permission('DistBonusCards', 'admin_delete')) {}
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
