<?php
//pr(BASE_URL);die();
//print_r($product_name);die();

//pr($this->Session->read('Office.company_id'));die();
//pr($this->Session->read('Office')['group_id']);die();
//pr($this->Session->read('Office.group_id'));die();
//pr($this->App->menu_permission('manages','admin_view'));die();
?>

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

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Product Issue'); ?></h3>
				

			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('Order', array('role' => 'form','action'=>'filter')); ?>
                    <?php echo $this->Form->input('confirm_status', array('class' => 'form-control', 'value' => '1', 'type' => 'hidden')); ?>
					<table class="search">
						
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td width="50%"><?php echo $this->Form->input('order_no', array('class' => 'form-control','required'=>false,'value'=>(isset($this->request->data['Order']['order_no'])=='' ? '' : $this->request->data['Order']['order_no']))); ?></td>							
						</tr>					
						<!-- <tr>
							<td><?php //echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td><?php //echo $this->Form->input('order_reference_no', array('class' => 'form-control','required'=>false)); ?></td>							
						</tr> -->
						<tr>
							
							<td>
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker1','required'=>false,'value'=>(isset($this->request->data['Order']['date_from'])=='' ? date('Y-m-d') : $this->request->data['Order']['date_from']),)); ?>
							</td>
						</tr>					
						<tr>
							
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker1','required'=>false,'value'=>(isset($this->request->data['Order']['date_to'])=='' ? date('Y-m-d') : $this->request->data['Order']['date_to']))); ?>
							</td>
														
						</tr>	
						<tr>
							<td><?php echo $this->Form->input('outlet_id', array('label' => 'Distributor:','id' => 'distribut_outlet_id','class' => 'form-control distribut_outlet_id','required'=>false,'empty'=>'---- Select Distributers ----','options'=>$distributors,'default'=>$distribut_outlet_id)); ?></td>
							<td>
								<div class="operator_order_value"><?php echo $this->Form->input('order_value', array('class' => 'form-control')); ?></div>

								<?php echo $this->Form->input('confirmed', array('class' => 'form-control confirmed','value'=> 1, 'type'=>'hidden')); ?>
							</td>
							
						</tr>
						<tr>
							<td  class="text-left">
									<?php echo $this->Form->input('operator', array('class' => 'form-control operator','empty'=>'---Select---','options'=>array('1'=>'Less than (<)','2'=>'Gretter than (>)','3'=>'Between'))); ?>
							</td>
							
						</tr>
						<tr class="between_value">
							<td  class="text-left">
								<?php echo $this->Form->input('order_value_from', array('class' => 'form-control operator_between_order_value')); ?>
							</td>
							<td>
								<?php echo $this->Form->input('order_value_to', array('class' => 'form-control operator_between_order_value')); ?>
							</td>
						</tr>
						<?php /*?><tr>
							<td  class="text-left">
								<?php echo $this->Form->input('payment_status', array('class' => 'form-control','empty'=>'---Select---','options'=>array('1'=>'Due','2'=>'Paid'))); ?>
							</td>
							<td>
								
							</td>
						</tr><?php */?>
						<tr >
							<td  class="text-left">
								<?php echo $this->Form->input('confirm_status', array('class' => 'form-control','empty'=>'---Select---','options'=>$confirmation_status_optn)); ?>
							</td>
							<td></td>
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
				<table id="Order" class="table table-bordered">
					<thead>
						<tr>
							<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
							
                            <th class="text-center"><?php echo $this->Paginator->sort('order_reference_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name','Outlet'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Market.name','Market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Territory.name','Territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('gross_value','Order Total'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('order_date'); ?></th>
							
							
							<!-- <th class="text-center">Order Status</th> -->
                            <th class="text-center"><?php echo $this->Paginator->sort('confirm_status'); ?></th>
                            
							<th width="80" class="text-center"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$total_amount = 0;
					foreach ($orders as $order)
					{
						//echo "<pre>";print_r($order);exit();
					$date1=date_create(date('Y-m-d', strtotime($order['Order']['order_date'])));
					$date2=date_create(date('Y-m-d'));
					$diff=date_diff($date1,$date2);
					$dare_diff = $diff->format("%a");
					?>
					<tr style="background-color:<?php echo $order['Order']['from_app']==0 ? '#f5f5f5':'white'?>">
						<td align="center"><?php echo h($order['Order']['id']); ?></td>
						<td align="center"><?php echo h($order['Order']['order_no']); ?></td>
                        
						<td align="center"><?php echo h($order['Outlet']['name']); ?></td>
						<td align="center"><?php echo h($order['Market']['name']); ?></td>
						<td align="center"><?php echo h($order['Territory']['name']); ?></td>
						<td align="center"><?php echo sprintf('%.2f',$order['Order']['gross_value']); ?></td>
						<td align="center"><?php echo $this->App->datetimeformat($order['Order']['order_date']); ?></td>
						
						
                        <td align="center">
                        <?php
						if(@$order['Order']['confirm_status']==1)
						{
							echo '<span class="btn btn-primary btn-xs draft">Processing</span>';
						}
						elseif(@$order['Order']['confirm_status']==2)
						{
							echo '<span class="btn btn-info btn-xs draft">Deliverd</span>';
						}
						else
						{
							echo '<span class="btn btn-danger btn-xs draft">Pending</span>';	
						}
						?>
                        </td>
                        
                        
                        <td class="text-center" width="15%">
                        
                              <?php 
							    if($order['Order']['editable'] == 1){ 
                               if($this->App->menu_permission('manages','admin_edit_date'))
                               { 
									echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'),
									array('action' => 'edit_date', $order['Order']['id']), 
									array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Edit')); 
                                     
                                ?>
                            <?php }} ?>
                            
						</td>
                        
                        
					</tr>
					<?php 
					$total_amount = $total_amount + $order['Order']['gross_value'];
						
					}
									
					?>
					<tr>
						<td align="right" colspan="5"><b>Total Amount :</b></td>
						<td align="center"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
						<td class="text-center" colspan="4"></td>
					</tr>
					</tbody>
				</table>
                </div>
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

<!-- Report Print -->

	<div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
		<!-- <div style="text-align:right;width:100%;">Page No :1 of 1</div>
		<div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div> -->
		

		<div style="width:100%;text-align:center;float:left">
			<h2>SMC Enterprise Limited</h2>
			<h3>Top Sheet</h3>
			<h2><u>Sales Report</u></h2>
			<h5><?php if(!empty($requested_data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo $requested_data['Order']['date_from']; ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo $requested_data['Order']['date_to']; }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></h5>
			<h4>Area : <?php echo $offices[$requested_data['Order']['office_id']];?></h4>
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
	$('.company_id').selectChain({
		target: $('.office_id'),
		value:'name',
		url: '<?= BASE_URL.'admin/territories/get_office_list'?>',
		type: 'post',
		data:{'company_id': 'company_id' }
	});
	$('.office_id').selectChain({
        target: $('.distribut_outlet_id'),
        value:'name',
        url: '<?= BASE_URL.'sales_people/get_outlet_list_with_distributor_name';?>',
        type: 'post',
        data:{'office_id': 'office_id'}

  });
$('.office_id').selectChain({
	target: $('.territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
    target: $('.market_id'),
    value: 'name',
    url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
    type: 'post',
    data: {'territory_id': 'territory_id'}
});
function get_thana_list(territory_id)
{
	$.ajax
	({
		type: "POST",
		url: '<?=BASE_URL?>orders/get_thana_by_territory_id',
		data: 'territory_id='+territory_id,
		cache: false, 
		success: function(response)
		{          
			$('.thana_id').html(response); 
			<?php if(isset($this->request->data['Order']['thana_id'])){?> 
				$('.thana_id option[value="<?=$this->request->data['Order']['thana_id']?>"]').attr("selected",true);
				<?php }?>   
			}
		});
}
if($('.territory_id').val()!='')
{
	get_thana_list($('.territory_id').val());
}
$('body').on('change','.territory_id',function() {

	get_thana_list($(this).val());
});
$('.thana_id').selectChain({
	target: $('.market_id'),
	value:'name',
	url: '<?= BASE_URL.'orders/market_list';?>',
	type: 'post',
	data:{'thana_id': 'thana_id','territory_id':'territory_id' }
});
/*$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});*/
/*$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'admin/doctors/get_distribute';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});*/
$('.market_id').selectChain({
	target: $('.outlet_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_outlet_list';?>',
	type: 'post',
	data:{'market_id': 'market_id' }
});
$('.office_id').change(function(){
	$('.market_id').html('<option value="">---- Select Market ----');
	$('.outlet_id').html('<option value="">---- Select Distributers ----');
});

/*$('.territory_id').change(function(){
	$('.outlet_id').html('<option value="">---- Select Distributers ----');
});*/
$(".operator").change(function(){
	operator_value_set();
});
operator_value_set();
function operator_value_set()
{
	var operator_value=$(".operator").val();
	if(operator_value==3)
	{
		$('.between_value').show();
		$('.operator_order_value').hide();
	}
	else if(operator_value==1 || operator_value==2)
	{
		$('.operator_order_value').show();
		$('.between_value').hide();
	}
	else
	{
		$('.operator_order_value').hide();
		$('.between_value').hide();
	}
}
</script>
<script>
	function PrintElem(elem)
	{
		var mywindow = window.open('', 'PRINT', 'height=400,width=1000');
		

		//mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('</head><body >');
		//mywindow.document.write('<h1>' + document.title  + '</h1>');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</body></html>');

		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10*/

		mywindow.print();
		mywindow.close();

		return true;
	}
</script>

 <script>
  function focefullyclosed(order_id){
  	console.log(order_id);
  	$.ajax({
	url: '<?=BASE_URL?>/Manages/forcefullyclosed',
	type:"POST",
	//dataType: "json",
	data:{order_id:order_id},
	success: function(result){
	console.log(result);
	
	location.reload();

	}});
  }	
 </script>
 <script>
 	$(document).ready(function(){
 		$('.datepicker1').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true,
		});
 	});
 </script>





