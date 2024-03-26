<?php 

?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('SR Visit Plan List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('DistSrVisitPlanLists','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Plan'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                    <?php if($this->App->menu_permission('DistSrVisitPlanLists','admin_set_visit_plan')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Visit Plan'), array('action' => 'set_visit_plan'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistSrVisitPlanList', array('role' => 'form','action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td>
								<?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id','empty'=>'--- Select Distributor ---','options'=>$dist_distributors,'default'=>$distributor_id)); ?> 
							</td>							
						</tr>					
						<tr>
							
							<td><?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','class' => 'form-control dist_route_id','required'=>false,'empty'=>'---- Select Route/Beat ----','options'=>$dist_routes,'default'=>$dist_route_id)); ?></td>
                            <td>
                                 <?php echo $this->Form->input('sr_id', array('label'=>'SR','class' => 'form-control ', 'id' => 'sr_id','empty'=>'--- Select SR ---','default'=>$sr_id)); ?> 
                            </td>						
						</tr>
						
						<tr>
							
							<td><?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets,'default'=>$market_id)); ?></td>
							
						
							<td class="required">
								<?php echo $this->Form->input('date_from', array('label' => 'Visited Date','id' => 'date_from','class' => 'form-control datepicker1','value'=>(isset($this->request->data['DistSrVisitPlanList']['date_from'])=='' ? date('Y-m-d') : $this->request->data['DistSrVisitPlanList']['date_from']),'required'=>TRUE)); ?>
							</td>
						
							
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
                <table id="DistSrVisitPlanLists" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center">Distributor</th>
							<th class="text-center"><?php echo $this->Paginator->sort('sr_id','SR Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('dist_market_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visit_plan_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visited_date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('is_out_of_plan'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('visit_status'); ?></th>
							<th width="120" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($visitPlanLists as $visitPlanList): ?>
					<tr>
						<td class="text-center"><?php echo h($visitPlanList['DistSrVisitPlanList']['id']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['DistDistributor']['name']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['DistSalesRepresentative']['name']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['DistMarket']['name']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($visitPlanList['DistSrVisitPlanList']['visit_plan_date']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($visitPlanList['DistSrVisitPlanList']['visited_date']); ?></td>
						<td class="text-center"><?php echo h($visitPlanList['DistSrVisitPlanList']['is_out_of_plan']==1?'YES':'NO'); ?></td>
						<td class="text-center">
							<?php 
							if($visitPlanList['DistSrVisitPlanList']['visit_status'] == 0)
							{
								echo '<span class="btn btn-warning btn-xs">Pending</span>';
							}elseif($visitPlanList['DistSrVisitPlanList']['visit_status'] == 1){
								echo '<span class="btn btn-success btn-xs">Visited</span>';
							}							
							?>
						</td>
						<td class="text-center">
							<?php // if($this->App->menu_permission('DistSrVisitPlanLists','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $visitPlanList['DistSrVisitPlanList']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							<?php // if($this->App->menu_permission('DistSrVisitPlanLists','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $visitPlanList['DistSrVisitPlanList']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
							<?php if($this->App->menu_permission('DistSrVisitPlanLists','admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $visitPlanList['DistSrVisitPlanList']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $visitPlanList['DistSrVisitPlanList']['id'])); } ?>
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

		$('.datepicker1').datepicker({
            startDate: new Date(),
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

		 $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });
        //get_route_by_office_id($("#office_id").val());
        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
        });
		
		
		

        function get_route_by_office_id(office_id)
        {
            
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                    <?php if(isset($this->request->data['DistOrder']['dist_route_id'])){ ?>
                    	if($("#dist_route_id").val(<?=$this->request->data['DistOrder']['dist_route_id']?>))
                    	{
                    		get_market_data();
                    	}
                    <?php } ?>
                }
            });

           
        }
        
         $("#dist_route_id").change(function () {
            get_market_data();
        });  
        
         function get_market_data()
         {
         	var dist_route_id=$("#dist_route_id").val();
         	var thana_id=0;
         	var location_type_id=0;
         	var territory_id=0;

         	$.ajax({
         		url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
         		data: {'dist_route_id': dist_route_id,'thana_id': thana_id,'location_type_id': location_type_id,'territory_id': territory_id},
         		type: 'POST',
         		success: function (data)
         		{
         			$("#market_id").html(data);
         		}
         	});
         }
         //get_route_data_from_dist_id();
         $("#distributor_id").change(function () {
         	get_route_data_from_dist_id();
         });  
        
      	function get_route_data_from_dist_id()
        {
            var distributor_id=$("#distributor_id").val();
            
             $.ajax({
                url: '<?= BASE_URL . 'distOrders/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
            
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        }
		
		
		/*if ($(".office_id").val())
		{
			get_dist_by_office_id($(".office_id").val());
		}*/
		$(".office_id").change(function () {
			get_dist_by_office_id($(this).val());
			$("#sr_id").html("<option value=''>Select SR</option>");
			
		});



		$("#distributor_id").change(function () {
			get_sr_list_by_distributor_id($(this).val());
		});

		function get_dist_by_office_id(office_id)
		{
			var date_from=$("#date_from").val();
			
			$.ajax({
				url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id' ?>',
				data: {'office_id': office_id,'order_date':date_from},
				type: 'POST',
				success: function (data)
				{
					//alert(data);
					$("#distributor_id").html(data);
				}
			});
		}
		
		function get_sr_list_by_distributor_id(distributor_id)
		{
			var date_from=$("#date_from").val();
			var date_to=$("#date_to").val();
			
			$.ajax({
				url: '<?= BASE_URL . 'DistOrders/get_sr_list_by_distributot_id_date_range' ?>',
				data: {'distributor_id': distributor_id,'order_date_from':date_from,'order_date_to':date_to},
				type: 'POST',
				success: function (data)
				{
					// console.log(data);
					$("#sr_id").html(data);
				}
			});
		}
	
		
	});
	
</script>