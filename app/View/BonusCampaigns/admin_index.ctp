<?php
	App::import('Controller', 'BonusCampaignsController');
	$BonusCampaignsController = new BonusCampaignsController;	

?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Bonus Campaign'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('bonus_campaigns','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Bonus Campaign'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('BonusCampaign', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%">
							<?php echo $this->Form->input('date_from', array('class' => 'form-control date_picker', 'label'=>'Start Date', 'required' => true)); ?>	
							</td>
							<td width="50%">
							<?php echo $this->Form->input('date_to', array('class' => 'form-control date_picker', 'label'=>'End Date', 'required' => true)); ?>
							</td>							
						</tr>
						<tr>
							<td width="50%">
								<?php echo $this->Form->input('product_id', array('class' => 'form-control', 'empty'=>'---- Select Product ----', 'options' => $products)); ?>
							</td>
							<td></td>
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
                <table id="Brands" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th width="120" class="text-center"><?php echo __('Product'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('start_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('end_date'); ?></th>
							<!--th class="text-center"><?php echo $this->Paginator->sort('bonus_details', 'Details'); ?></th-->
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($bonuscampaign as $v): ?>
					 <tr>
						<td class="text-center"><?php echo h($v['BonusCampaign']['id']); ?></td>
						<td class="text-left">
							<?php 
								$prductName = $BonusCampaignsController->get_product_name($v['BonusCampaign']['id']);
								echo $prductName;
							?>

						</td>
						<td class="text-center"><?php echo h($v['BonusCampaign']['start_date']); ?></td>
						<td class="text-center"><?php echo h($v['BonusCampaign']['end_date']); ?></td>
						<!---td class="text-center"><?php echo h($v['BonusCampaign']['bonus_details']); ?></td--->
						<td class="text-center">
							<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $v['BonusCampaign']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
							<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $v['BonusCampaign']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); ?>
							<?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $v['BonusCampaign']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $v['BonusCampaign']['id'])); ?>
						</td>
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


<script>
    $(document).ready(function() {
        var yesterday = new Date(new Date().setDate(new Date().getDate() - 1));
        $('.date_picker').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true,
        });

    });
</script>