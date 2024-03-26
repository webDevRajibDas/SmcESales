<?php
	//echo "<pre>";
	//print_r($reportEsalesSettings);die();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Esales Setting List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('reportEsalesSettings', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Esales Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				
                <?php echo $this->Form->create('ReportEsalesSetting', array('role' => 'form')); ?>
                <table id="EsalesSettingCategories" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
                            <th><?php echo $this->Paginator->sort('type'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Rank'); ?></th>
                            
                            <th class="text-center">Start Operator</th>
                            <th class="text-right"><?php echo $this->Paginator->sort('Start'); ?></th>
							
                            
                            <th class="text-center">End Operator</th>
                            <th class="text-right"><?php echo $this->Paginator->sort('End'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($reportEsalesSettings as $result): ?>
					<tr>
						<td class="text-center"><?php echo h($result['ReportEsalesSetting']['id']); ?></td>
                        <td><?php echo h($result['ReportEsalesSetting']['type']==1?'Monthly Sales':'Total Sales'); ?></td>  						
                        <td class="text-center"><?php echo h($result['ReportEsalesSetting']['name']); ?></td>
                        
                        <td class="text-center"><?php echo h($result['ReportEsalesSetting']['operator_1']); ?></td> 
                        <td class="text-right"><?php echo h($result['ReportEsalesSetting']['range_start']); ?></td>
                         
                                                
                        <td class="text-center"><?php echo h($result['ReportEsalesSetting']['operator_2']); ?></td>
                        <td class="text-right"><?php echo h($result['ReportEsalesSetting']['range_end']); ?></td>
                        
						
						<td class="text-center">
							<?php if($this->App->menu_permission('reportEsalesSettings', 'admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $result['ReportEsalesSetting']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							
                            
                            <?php if($this->App->menu_permission('reportEsalesSettings','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $result['ReportEsalesSetting']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $result['ReportEsalesSetting']['id'])); } ?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
                
                <?php /*?><div class="form-group text-center" style="padding-top:20px;">
                	<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                </div><?php */?>
                
                <?php echo $this->Form->end(); ?>
                
                
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
								?>								
							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>
