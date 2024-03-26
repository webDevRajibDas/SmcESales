<?php 

///echo "<pre>"; print_r($maclog);

?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Mac Log'); ?></h3>
			</div>	
			<div class="box-body">
                
        		<div class="box-body">

        			<?php echo $this->Form->create('MacFreeLog', array('role' => 'form','action'=>'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from','id'=>'date_from','required'=>false)); ?>
                                
                            </td>
                            <td width="50%">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to','id'=>'date_to','required'=>false)); ?>
                                    
                            </td>                      
                        </tr>
                        <tr>
                            <td width="50%">

                                <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id','empty'=>'--- select ---','options'=>$offices,'required'=>false)); ?>

                            </td>
                            <td width="50%">
                               
                                <?php echo $this->Form->input('user_group_id', array('class' => 'form-control','required'=>false,'empty'=>'---- Select ----', 'options'=>$userGroups)); ?>

                            </td>
                                                  
                        </tr>
                        <tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
                    </table>      
                </div>
                <?php echo $this->Form->end(); ?>


                <table id="maclog" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('office'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('group'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('username'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('MAC Free Reason'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Before Mac'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('After Mac'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Date Time'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Change By'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php

						$after = 0;
						
					  foreach ($maclog as $key => $row): ?>
					<tr>
						<td class="text-center"><?php echo h($row['MacFreeLog']['id']); ?></td>
						<td class="text-center"><?php echo h($row['Office']['office_name']); ?></td>
						<td class="text-center"><?php echo h($row['UserGroup']['name']); ?></td>
						<td class="text-center"><?php echo h($row['User']['username']); ?></td>
						<td class="text-center"><?php echo h($row['MacFreeLog']['mac_free_node']); ?></td>
						<td class="text-center"><?php echo h($row['MacFreeLog']['before_mac']); ?></td>
						<td class="text-center">

							<?php 

								$curent = $row['MacFreeLog']['user_id'];
								$after = $maclog[$key + 1]['MacFreeLog']['user_id'];

								if($curent == $after){
								echo h($maclog[$key + 1]['MacFreeLog']['before_mac']);

								}else{
									echo $row['User']['mac_id'];
									
								}

							?>

						</td>
						<td class="text-center"><?php echo h($row['MacFreeLog']['created_at']); ?></td>
						<td class="text-center"><?php echo h($userList[$row['MacFreeLog']['created_by']]); ?></td>
						
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php	echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>									
								<?php
									echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>