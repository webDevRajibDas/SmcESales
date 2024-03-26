<?php // echo $distributor_id; exit;?>
<style>
	table, th, td {
		/*border: 1px solid black;*/
		border-collapse: collapse;
	}
	#content { display: none; }
	@media print
		{
			#non-printable { display: none; }
			#content { display: block; }
			table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
	}
		}
</style>

<style>
.checkbox label{
	font-weight:700;
}
</style>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Memo List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('DistMemos','admin_create_memo')){ ?>
						<a class="btn btn-primary" href="<?=BASE_URL?>admin/dist_memos/create_memo"><i class="glyphicon glyphicon-plus"></i> New Distributor Memo</a>
					<?php } ?>
					
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistMemo', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td><?php echo $this->Form->input('memo_reference_no', array('label'=>'Memo Number :','class' => 'form-control','required'=>false)); ?></td>								
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id','empty'=>'--- Select Distributor ---','default'=>$distributor_id)); ?> </td>
							<td>
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistMemo']['date_from'])=='' ? $current_date : $this->request->data['DistMemo']['date_from']),'required'=>TRUE)); ?>
							</td>
													
						</tr>					
						<tr>
							<td>
                                                            <?php echo $this->Form->input('sr_id', array('label'=>'SR','class' => 'form-control ', 'id' => 'sr_id','empty'=>'--- Select SR ---','default'=>$sr_id)); ?> 
                                                            </td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistMemo']['date_to'])=='' ? $current_date : $this->request->data['DistMemo']['date_to']),'required'=>TRUE)); ?>
							</td>
														
						</tr>	
						
						<tr>
                                                    
                                                         <td>
                                                           
								<?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','class' => 'form-control dist_route_id','required'=>false,'empty'=>'---- Select Route/Beat ----')); ?>
							</td>
							<td>
                                                           
								<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----')); ?>
							</td>
                                                        
						</tr>
                                                <tr>
                                                    <td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?></td>
                                                    <td>
                                                        <?php echo $this->Form->input('p_wise_sales_bonus_report', array('type' => 'checkbox','label'=>'Product Wise Sales Bonus Report', 'class'=>'checkbox-inline', 'required'=>false )); ?></td>
                                                    
                                                 </tr>

						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>	
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
                <div class="table-responsive">	
                    <?php if(!empty($product_wise_sales_bonus_report))
                    {
                        ?>
                    
                    <table id="DistMemo" class="table table-bordered">
					<thead>
						<tr>
							<th width="50" class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">EC</th>
							<th class="text-center">Bonus Qty</th>										
				
						</tr>
					</thead>
					<tbody>
					<?php 
                                        $r=1;
					foreach ($product_wise_sales_bonus_report as $k=>$each_data)
                                        {
                                         $each_report_data=$each_data[0];
                                         $product_id=$each_report_data['product_id'];
                                         $product_name=$product_list[$product_id];
                                         ?>
                                            <tr style="background-color:#f5f5f5;">
                                                    <td align="center"><?php echo h($r); ?></td>
                                                    <td align="center"><?php echo h($product_name); ?></td>
                                                    <td align="center"><?php echo h($each_report_data['ec']); ?></td>
                                                    <td align="center"><?php echo h($each_report_data['bonus_amount']); ?></td>	
                                            </tr>
                                            <?php
                                            $r++;
                                        }
                                            ?>
					</tbody>
				</table>
                    
                    
                    
                      <?php                        
                    }
                    else 
                    {
                        ?>
				<table id="DistMemo" class="table table-bordered">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_reference_no','Dist. Memo No'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>			
							<th class="text-center"><?php echo $this->Paginator->sort('gross_value','Dist. Memo Total'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_date'); ?></th>
							
							<th width="80" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					foreach ($dist_memos as $memo): 

                                        $memo['DistMemo']['from_app']=0;
                                       ?>
					<tr style="background-color:<?php echo $memo['DistMemo']['from_app']==0 ? '#f5f5f5':'white'?>">
						<td align="center"><?php echo h($memo['DistMemo']['id']); ?></td>
						<td align="center"><?php echo h($memo['DistMemo']['memo_reference_no']); ?></td>
						<td align="center"><?php echo h($memo['Outlet']['name']); ?></td>
						<td align="center"><?php echo h($memo['Market']['name']); ?></td>
					
						<td align="center"><?php echo sprintf('%.2f',$memo['DistMemo']['gross_value']); ?></td>
						<td align="center"><?php echo date("d-m-Y",strtotime($memo['DistMemo']['memo_date'])); ?></td>
						
						
                        
                        <td class="text-center">
							<?php if($this->App->menu_permission('dist_memos', 'admin_view'))
                                                   { 
echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $memo['DistMemo']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
							
							<?php if($memo['DistMemo']['action']!=0){?>
							
                            	
                                <?php if($memo['DistMemo']['status']==0){?>
									<?php if($this->App->menu_permission('dist_memos','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $memo['DistMemo']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
                                <?php } ?>
                            
								<?php if($this->App->menu_permission('dist_memos', 'admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $memo['DistMemo']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $memo['DistMemo']['id'])); } ?>
                            
							<?php }?>
						</td>
                        
                        
					</tr>
					<?php 
					$total_amount = $total_amount + $memo['DistMemo']['gross_value'];
					endforeach; 					
					?>
					<tr>
						<td align="right" colspan="5"><b>Total Amount :</b></td>
						<td align="center"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
						<td class="text-center" colspan="3"></td>
					</tr>
					</tbody>
				</table>
                       <?php 
                    }
                       ?>
                </div>
                            <?php if(empty($product_wise_sales_bonus_report))
                            {
                             ?>     
                           
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
                            
                               <?php
                             }
                               ?>
			</div>			
		</div>
	</div>
</div>

<!-- Report Print -->

	<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
		<!-- <div style="text-align:right;width:100%;">Page No :1 of 1</div>
		<div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div> -->
		

		<div style="width:100%;text-align:center;float:left">
			<h2>SMC Enterprise Limited</h2>
			<h3>Top Sheet</h3>
			<h2><u>Sales Report</u></h2>
			<h5><?php if(!empty($requested_data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo $requested_data['DistMemo']['date_from']; ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo $requested_data['CsaMemo']['date_to']; }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></h5>
			<h4>Area : <?php echo $offices[$requested_data['DistMemo']['office_id']];?></h4>
		</div>	  
		
		<!-- product quantity get-->
		<?php
		$product_qnty=array();

		foreach ($product_quantity as $data) {
			

			$product_qnty[$data['0']['sales_person_id']][$data['0']['product_id']]=$data['0']['pro_quantity'];
			
		}
		?>
		<table style="width:100%" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
			  <tr>
			  		<th>Sales Officer</th>
				  <?php
				  	foreach ($product_name as $value) {
				  ?>
				  	<th <?php if($value['Product']['product_category_id']==20){echo 'class="condom"';}else if($value['Product']['product_category_id']==21){echo 'class="pill"';}?>><?php echo $value['Product']['name'].'<br>['.$value['0']['mes_name'].']';?></th>
				  <?php 
				  	} 
				  ?>
				  <script>
				
				  	$('.condom:last').after("<th>Total Condom</th>");
				  	$('.pill:last').after("<th>Total Pill</th>")
				  </script>
			  </tr>
			  <?php
			  	foreach ($sales_people as $data_s) {
			  ?>
			  <tr>
				<td><?=$data_s['SalesPerson']['name']?></td>
				 <?php
				 
				  	foreach ($product_name as $data_q) {
				  ?>
				  	<td <?php if($data_q['Product']['product_category_id']==20){echo 'class="condom_'.$data_s['0']['sales_person_id'].'"';}else if ($data_q['Product']['product_category_id']==21) {
				  		echo 'class="pill_'.$data_s['0']['sales_person_id'].'"';
				  	}?>>
				  	<?php 
				  		if(array_key_exists($data_q['Product']['id'], $product_qnty[$data_s['0']['sales_person_id']])){
				  			echo $product_qnty[$data_s['0']['sales_person_id']][$data_q['Product']['id']];
				  		}
				  		else echo '0.00';
				  	?>
				  		
				  	</td>
				  <?php 
				  	} 
				  ?>
				  <script>
				  /**
				   * [total_condom description]
				   * @type {Number}
				   */
				  	var total_condom=0.0;
				  	$('.condom_<?php echo $data_s['0']['sales_person_id']?>').each(function(){
				  		total_condom+=parseFloat($(this).text());
				  	});
				  	$('.condom_<?php echo $data_s['0']['sales_person_id']?>:last').after('<td>'+total_condom+'</td>')
				  	/**
				  	 * [total_pill description]
				  	 * @type {Number}
				  	 */
				  	var total_pill=0.0;
				  	$('.pill_<?php echo $data_s['0']['sales_person_id']?>').each(function(){
				  		total_pill+=parseFloat($(this).text());
				  	});
				  		$('.pill_<?php echo $data_s['0']['sales_person_id']?>:last').after('<td>'+total_pill+'</td>')
				  </script>
			  </tr>
			  <?php }?>
      </table>
	  
		<div style="width:100%;padding-top:100px;">
			<div style="width:33%;text-align:left;float:left">
				Prepared by:______________ 
			</div>
			<div style="width:33%;text-align:center;float:left">
				Checked by:______________ 
			</div>
			<div style="width:33%;text-align:right;float:left">
				Signed by:______________
			</div>		  
		</div>
		
	</div>

    
<script>
$('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistMemos/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });
        
</script>

<script>
	$(document).ready(function(){
		 $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });
        
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
        
     $("#distributor_id").change(function () {
            get_route_data_from_dist_id();
        });  
        
      function get_route_data_from_dist_id()
        {
            var distributor_id=$("#distributor_id").val();
            
             $.ajax({
                url: '<?= BASE_URL . 'distMemos/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
            
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        }
        
        
      $("#market_id").change(function () {
            get_territory_thana_info();
        });  
        
      function get_territory_thana_info()
        {
            var market_id=$("#market_id").val();
            
            if(market_id)
            {
             $.ajax({
                url: '<?= BASE_URL . 'distMemos/get_territory_thana_info' ?>',
                data: {'market_id': market_id},
                type: 'POST',
                success: function (data)
                {
                   var info=data.split("||");
                   if(info[0]!=="")
                    {
                        $('#territory_id').val(info[0]);
                    }
                   
                   if(info[1]!=="")
                    {
                        $('#thana_id').val(info[1]);
                    }
                   
                }
            });
            }
        }
        
         if ($(".office_id").val())
            {
                get_dist_by_office_id($(".office_id").val());
            }
            $(".office_id").change(function () {
                get_dist_by_office_id($(this).val());
                $("#sr_id").html("<option value=''>Select SR</option>");
            });



            $("#distributor_id").change(function () {
                get_sr_list_by_distributor_id($(this).val());
            });

            function get_dist_by_office_id(office_id)
            {
                var DistMemoDateFrom=$("#DistMemoDateFrom").val();
                var DistMemoDateTo=$("#DistMemoDateTo").val();
                var distributor_id=$("#distributor_id").val();

                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_dist_list_by_office_id_and_date_range' ?>',
                    data: {'office_id': office_id,'memo_date_from':DistMemoDateFrom,'memo_date_to':DistMemoDateTo,'distributor_id':distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#distributor_id").html(data);
                    }
                });
            }
            
            function get_sr_list_by_distributor_id(distributor_id)
            {
                var DistMemoDateFrom=$("#DistMemoDateFrom").val();
                var DistMemoDateTo=$("#DistMemoDateTo").val();
                
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_sr_list_by_distributot_id_date_range' ?>',
                    data: {'distributor_id': distributor_id,'memo_date_from':DistMemoDateFrom,'memo_date_to':DistMemoDateTo},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#sr_id").html(data);
                    }
                });
            }

            function get_thana_by_territory_id(territory_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_thana_by_territory_id' ?>',
                    data: {'territory_id': territory_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#thana_id").html(data);
                    }
                });
            }
            function get_market_by_thana_id(thana_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_market_by_thana_id' ?>',
                    data: {'thana_id': thana_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#market_id").html(data);
                    }
                });
            }
            
	});
</script>