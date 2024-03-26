<style>
    .draft{
        padding: 0px 15px;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Challan List'); ?></h3>
            </div>	
            <div class="box-body">
                <div class="search-box">
                <?php echo $this->Form->create('DistChallan', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%">                              
                                <?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?>
                            </td>
                            <td width="50%">
                                <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor:','class' => 'form-control ', 'id' => 'dist_distributor_id','empty'=>'--- Select Distributor ---','default'=>$dist_distributor_id,'options'=>$distributors)); ?>
                            </td>	
                        </tr>
                        <tr>
                            <td width="50%">                              
                                <?php echo $this->Form->input('memo_no', array('class' => 'form-control', 'required' => false)); ?>
                                
                            </td>
                            <td width="50%">
                                <?php echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'required' => false, 'empty' => '---- Select ----', 'options' => array(1 => 'Pending', 2 => 'Received'))); ?>
                            </td>
                        </tr>					
                        <tr>
                            <td>
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => false)); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => false)); ?>
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
                <div class="table-responsive">	
                    <table id="Challan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('challan_no'); ?></th>                                        
                                <th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>                                        
                                <th class="text-center"><?php echo $this->Paginator->sort('sender_store_id','Sales Territory'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('challan_date'); ?></th>
                                <th class="text-center" >Area Office</th>
                                <th class="text-center">Area Executive</th>
                                <th class="text-center">TSO</th>                                
                                <th class="text-center"><?php echo $this->Paginator->sort('receiver_store_id','Distributor Name'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('received_date'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('status'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('remarks'); ?></th>
                                <th width="120" class="text-center"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $serial = 1;

                        foreach ($challans as $challan):
                            ?>					
                                <tr>
                                    <td align="center"><?php echo h($serial++); ?></td>
                                    <td align="center"><?php echo h($challan['DistChallan']['challan_no']); ?></td>                                    
                                    <td align="center"><?php echo h($challan['DistChallan']['memo_no']); ?></td>                                    
                                    <td class="text-left"><?php echo h($territories[$challan['SalesPerson']['territory_id']].' ('.$challan['SalesPerson']['name'].')'); ?></td>
                                    <td align="center"><?php echo $this->App->dateformat($challan['DistChallan']['challan_date']); ?></td>
                                    <td align="center"><?php echo h($challan['Office']['office_name']); ?></td>
                                    <td align="center"><?php echo h($challan['DistAE']['name']); ?></td>
                                    <td align="center"><?php echo h($challan['DistTso']['name']); ?></td>
                                    <td class="text-left"><?php
                                        $d_id=$challan['DistChallan']['dist_distributor_id'];
                                        echo $distributors_all[$d_id]; ?>
                                    </td>
                                    <td align="center">
                                        <?php echo $this->App->dateformat($challan['DistChallan']['received_date']); ?>
                                    </td>
                                    <td align="center">
                                        <?php
                                            if ($challan['DistChallan']['status'] == 1) 
                                            {
                                                echo '<span class="btn btn-warning btn-xs">Pending</span>';
                                            }
                                            elseif ($challan['DistChallan']['status'] == 2) {
                                                echo '<span class="btn btn-success btn-xs">Received</span>';
                                            }
                                            else
                                            {
                                                echo '<span class="btn btn-primary btn-xs draft">Draft</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo h($challan['DistChallan']['remarks']); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($this->App->menu_permission('dist_challans', 'admin_view')) {
                                            echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $challan['DistChallan']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view'));
                                        } ?>
										<?php if ($challan['DistChallan']['status'] == 1) {
											if ($this->App->menu_permission('dist_challans', 'admin_edit')) {
												echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $challan['DistChallan']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit'));
											}
										}?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
    $(document).ready(function(){
if ($(".office_id").val())
            {
                //get_dist_by_office_id($(".office_id").val());
            }
            $(".office_id").change(function () {
                get_dist_by_office_id($(this).val());
            });


            function get_dist_by_office_id(office_id)
            {

                $.ajax({
                    url: '<?= BASE_URL . 'DistChallans/get_dist_list_by_office_id' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#dist_distributor_id").html(data);
                    }
                });
            }
            });
</script>