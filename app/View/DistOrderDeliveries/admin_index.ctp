<?php //pr($dist_Orders);exit;?>
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
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('SR Delivery List'); ?></h3>
				<div class="box-tools pull-right">
					<?php 
					if($this->App->menu_permission('DistOrderDeliveries','admin_create_order')){?>
						<a class="btn btn-primary" href="<?=BASE_URL?>admin/DistOrderDeliveries/create_order"><i class="glyphicon glyphicon-plus"></i> New SR Delivery</a>
					<?php } ?>
					
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistOrder', array('role' => 'form', 'action'=>'filter')); ?>
					<table class="search">
                                            
                        <tr>
							<td width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistOrder']['date_from'])=='' ? $current_date : $this->request->data['DistOrder']['date_from']),'required'=>TRUE)); ?>
							</td>
                                                        
                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistOrder']['date_to'])=='' ? $current_date : $this->request->data['DistOrder']['date_to']),'required'=>TRUE)); ?>
                            </td>								
						</tr>	
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td><?php echo $this->Form->input('order_reference_no', array('label'=>'Order Number :','class' => 'form-control','required'=>false)); ?></td>								
						</tr>
						<tr>
							<td width="50%">
								<?php echo $this->Form->input('tso_id', array('id' => 'tso_id','class' => 'form-control tso_id','required'=>false,'empty'=>'---- Select TSO ----','options'=>$tsos,'default'=>$tso_id)); ?>
							</td>
							<td></td>
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id','empty'=>'--- Select Distributor ---','default'=>$distributor_id)); ?> 
							</td>

                            <td>
                                 <?php echo $this->Form->input('sr_id', array('label'=>'SR','class' => 'form-control ', 'id' => 'sr_id','empty'=>'--- Select SR ---','default'=>$sr_id)); ?> 
                            </td>						
						</tr>					
							
						
						<tr>
                                                    
                            <td>
                                                           
								<?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','class' => 'form-control dist_route_id','required'=>false,'empty'=>'---- Select Route/Beat ----','options'=>$routes)); ?>
							</td>
							<td>
                                                           
								<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?>
							</td>
                                                        
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?>

							</td>
							<td>
								<?php echo $this->Form->input('status', array('id' => 'status','class' => 'form-control status','required'=>false,'empty'=>'---- Select status ----','options'=>$status_list)); ?>
								<?php echo $this->Form->input('from_app', array('id' => 'from_app','class' => 'form-control from_app','required'=>false,'value'=>"1",'type'=>'hidden')); ?>
							</td>
							<!-- <td>
								<?php //echo $this->Form->input('sr_wise_sales_bonus_report', array('type' => 'checkbox','label'=>'SR Wise Sales Bonus Report', 'class'=>'checkbox-inline', 'required'=>false )); ?>
								<?php //echo $this->Form->input('p_wise_sales_bonus_report', array('type' => 'checkbox','label'=>'Product Wise Sales Report', 'class'=>'checkbox-inline', 'required'=>false )); ?>
							
							</td> -->

						</tr>

						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>	
								
                             		<a class="btn btn-success" id="download_xl">Download XL</a>
                            	
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
                <div class="table-responsive">	
                    <?php if(!empty($sr_wise_sales_bonus_report) || !empty($p_wise_sales_bonus_report))
                    {
                    	

                    	$dist_Orders = new DistOrdersController();
                    ?>
                    
                   
					<div id="rpt_content">
						<div style="float:left; width:100%; padding-bottom:20px;">
							<div style="width:25%;text-align:left;float:left">
								&nbsp;&nbsp;&nbsp;&nbsp;
							</div>
							<div style="width:50%;text-align:center;float:left">
								<font id="heading_name"><b>SMC Enterprise Limited</b></font><br>
								<span id="heading_add">SMC Tower, 33 Banani C/A, Dhaka- 1213</span><br>
								<font><b>Product Wise Sales Detail</b></font><br>
								<font><b>Issueing Office : <?php echo h($offices[$this->request->data['DistOrder']['office_id']]); ?></b></font><br>
								<font><?php if(!empty($this->request->data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo date('d-F-Y',strtotime($this->request->data['DistOrder']['date_from'])); ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo date('d-F-Y',strtotime($this->request->data['DistOrder']['date_to'])); }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></font>
							</div>
							<div style="width:25%;text-align:right;float:left">
								&nbsp;&nbsp;&nbsp;&nbsp;
							</div>        
						</div>  
						<div style="float:left; width:100%; height:450px; overflow:scroll;">
							<?php 
							if(!empty($sr_wise_sales_bonus_report))
							{ 
								$last_sr_id='';
								$sr_no_outlet_sub_total=0;
								$sr_no_order_sub_total=0;
								$sr_qty_sub_total=0;
								$sr_rev_sub_total=0;
								$sr_bonus_sub_total=0;

								$no_outlet_total=0;
								$no_order_total=0;
								$qty_total=0;
								$rev_total=0;
								$bonus_total=0;


							?>
								<table  style="width:100%;text-align:center; margin-bottom: 50px;" border="1px solid black" cellpadding="0px" cellspacing="0" align="center" class="table table-bordered table-responsive">
									<thead>
										<tr>
											<th>SR Name</th>
											<th>Product</th>
											<th>No. Of Outlet</th>
											<th>No. Of Order</th>
											<th>Quantity</th>
											<th>Revenue</th>
											<th>Bonus Quantity</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($sr_wise_sales_bonus_report as $data){ ?>
											<?php 
											if($last_sr_id && $last_sr_id != $data['DistOrder']['sr_id'])
											{
												?>
											<tr>
												<td style="border: 0px;"></td>
												<td style="text-align: right; border: 0px;"><b>Sub Total</b></td>
												<td style="text-align: center;border: 0px;"><b><?=$sr_no_outlet_sub_total?></b></td>
												<td style="text-align: center;border: 0px;"><b><?=$sr_no_order_sub_total?></b></td>
												<td style="text-align: center;border: 0px;"><b><?=$sr_qty_sub_total?></b></td>
												<td style="text-align: center;border: 0px;"><b><?=$sr_rev_sub_total?></b></td>
												<td style="text-align: center;border: 0px;"><b><?=$sr_bonus_sub_total?></b></td>

											</tr>
												<?php
												$sr_no_outlet_sub_total=0;
												$sr_no_order_sub_total=0;
												$sr_qty_sub_total=0;
												$sr_rev_sub_total=0;
												$sr_bonus_sub_total=0;
											} 
											?>
										<tr>

											<td>
												<?php 
												if($last_sr_id!=$data['DistOrder']['sr_id'])
												{
													$last_sr_id=$data['DistOrder']['sr_id'];
													echo $dist_Orders->get_sr_name_by_sr_id($data['DistOrder']['sr_id']);
												}

												?>
											</td>
											<td><?=$data['Product']['name']?></td>
											<td style="text-align: center;"><?=$data['0']['no_outlet']?></td>
											<td style="text-align: center;"><?=$data['0']['no_Order']?></td>
											<td style="text-align: center;"><?=$data['0']['sales_qty']?></td>
											<td style="text-align: center;"><?=$data['0']['revenue']?></td>
											<td style="text-align: center;"><?=$data['0']['bonus_qty']?></td>
										</tr>
										<?php
											$sr_no_outlet_sub_total+=$data['0']['no_outlet'];
											$sr_no_order_sub_total+=$data['0']['no_Order'];
											$sr_qty_sub_total+=$data['0']['sales_qty'];
											$sr_rev_sub_total+=$data['0']['revenue'];
											$sr_bonus_sub_total+=$data['0']['bonus_qty']; 

											$no_outlet_total+=$data['0']['no_outlet'];
											$no_order_total+=$data['0']['no_Order'];
											$qty_total+=$data['0']['sales_qty'];
											$rev_total+=$data['0']['revenue'];
											$bonus_total+=$data['0']['bonus_qty']; 
											} 
										?>
									<tr>
										<td style="border: 0px;"></td>
										<td style="text-align: right; border: 0px;"><b>Sub Total</b></td>
										<td style="text-align: center;border: 0px;"><b><?=$sr_no_outlet_sub_total?></b></td>
										<td style="text-align: center;border: 0px;"><b><?=$sr_no_order_sub_total?></b></td>
										<td style="text-align: center;border: 0px;"><b><?=$sr_qty_sub_total?></b></td>
										<td style="text-align: center;border: 0px;"><b><?=$sr_rev_sub_total?></b></td>
										<td style="text-align: center;border: 0px;"><b><?=$sr_bonus_sub_total?></b></td>
									</tr>
									<tr>
										<td style="border-right: 0px;"></td>
										<td style="text-align: right; border-right: 0px;border-left: 0px;"><b>Total</b></td>
										<td style="text-align: center;border-right: 0px;border-left: 0px"><b><?=$no_outlet_total?></b></td>
										<td style="text-align: center;border-right: 0px;border-left: 0px"><b><?=$no_order_total?></b></td>
										<td style="text-align: center;border-right: 0px;border-left: 0px"><b><?=$qty_total?></b></td>
										<td style="text-align: center;border-right: 0px;border-left: 0px"><b><?=$rev_total?></b></td>
										<td style="text-align: center;border-right: 0px;border-left: 0px"><b><?=$bonus_total?></b></td>
									</tr>
									</tbody>
								</table>
							<?php 
							} 
							elseif(!empty($p_wise_sales_bonus_report))
							{
								$last_dist_id=0;
								$last_sr_id=0;
								$last_product_id=0;

								$dist_qty_total=0;
								$dist_rev_total=0;
								$dist_bonus_total=0;

								$p_qty_total=0;
								$p_rev_total=0;
								$p_bonus_total=0;

								$qty_total=0;
								$rev_total=0;
								$bonus_total=0;

								/*$last_product_class='';
								$last_dist_class='';*/
							?>
							<table style="width:100%;text-align:center; margin-bottom: 50px;" border="1px solid black" cellpadding="0px" cellspacing="0" align="center" class="table table-bordered p_wise_report">
								<thead>
									<tr>
										<th>Product</th>
										<th>Distributor</th>
										<th>SR</th>
										<th>Customer</th>
										<th>Order Date</th>
										<th>Order no</th>
										<th>Quantity</th>
										<th>Revenue</th>
										<th>Bonus</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($p_wise_sales_bonus_report as $key=>$data){ ?>
										<?php 
										if($last_dist_id && ($last_dist_id != $data['DistOrder']['distributor_id'] || $last_product_id != $data['DistOrderDetail']['product_id']))
										{
											?>
											<tr>
												<td class="p_<?=$last_product_id;?>"></td>
												<td class="dist_<?=$last_dist_id?>_<?=$last_product_id;?>"></td>
												<td colspan="4" style="text-align:right;"><b>Sub Total</b></td>
												<td><?=$dist_qty_total?></td>
												<td><?=$dist_rev_total?></td>
												<td><?=$dist_bonus_total?></td>
											</tr>

											<?php
											$dist_qty_total=0;
											$dist_rev_total=0;
											$dist_bonus_total=0;
										} 
										?>
										<?php 
										if($last_product_id && $last_product_id != $data['DistOrderDetail']['product_id'])
										{
											?>
											<tr>
												<td class="p_<?=$last_product_id;?>"></td>
												<td colspan="5" style="text-align:right;"><b>Product Total</b></td>
												<td><?=$p_qty_total?></td>
												<td><?=$p_rev_total?></td>
												<td><?=$p_bonus_total?></td>
											</tr>

											<?php
											$p_qty_total=0;
											$p_rev_total=0;
											$p_bonus_total=0;
										} 
										?>
										<td class="p_<?=$data['DistOrderDetail']['product_id'];?>"><?=$data['Product']['name'];?></td>
										<td class="dist_<?=$data['DistOrder']['distributor_id']?>_<?=$data['DistOrderDetail']['product_id'];?>"><?=$dist_Orders->get_dist_name_by_dist_id($data['DistOrder']['distributor_id']);?></td>
										<td class="sr_<?=$data['DistOrder']['sr_id']?>_<?=$data['DistOrder']['distributor_id']?>_<?=$data['DistOrderDetail']['product_id'];?>" style="text-align:center;"><?=$dist_Orders->get_sr_name_by_sr_id($data['DistOrder']['sr_id']);?></td>
										<td><?=$dist_Orders->get_outlet_market_thana_name_by_outlet_market_thana_id($data['DistOrder']['outlet_id'],$data['DistOrder']['market_id'],$data['DistOrder']['thana_id']);?></td>
										<td><?=date('d-M-y',strtotime($data['DistOrder']['order_date']));?></td>
										<td style="mso-number-format:\@;"><?=$data['DistOrder']['order_reference_no'];?></td>
										<td><?=$data['0']['sales_qty']?$data['0']['sales_qty']:0?></td>
										<td><?=$data['0']['revenue']?$data['0']['revenue']:0?></td>
										<td><?=$data['0']['bonus_qty']?$data['0']['bonus_qty']:0?></td>
										
									</tr>
									<?php
										if($last_dist_id!=$data['DistOrder']['distributor_id'])
											$last_dist_id=$data['DistOrder']['distributor_id'];

										if($last_product_id!=$data['DistOrderDetail']['product_id'])
											$last_product_id=$data['DistOrderDetail']['product_id'];

										$dist_qty_total+=$data['0']['sales_qty'];
										$dist_rev_total+=$data['0']['revenue'];
										$dist_bonus_total+=$data['0']['bonus_qty'];

										$p_qty_total+=$data['0']['sales_qty'];
										$p_rev_total+=$data['0']['revenue'];
										$p_bonus_total+=$data['0']['bonus_qty'];

										$qty_total+=$data['0']['sales_qty'];
										$rev_total+=$data['0']['revenue'];
										$bonus_total+=$data['0']['bonus_qty'];

										/*$last_product_class='p_'.$data['DistOrderDetail']['product_id'];
										$last_dist_class='dist_'.$data['DistOrder']['distributor_id'].'_'.$data['DistOrderDetail']['product_id'];
										*/
									} 
									?>
									<tr>
										<td class="p_<?=$last_product_id;?>"></td>
										<td class="dist_<?=$last_dist_id?>_<?=$last_product_id;?>"></td>
										<td colspan="4" style="text-align:right;"><b>Sub Total</b></td>
										<td><?=$dist_qty_total?></td>
										<td><?=$dist_rev_total?></td>
										<td><?=$dist_bonus_total?></td>
									</tr>
									<tr>
										<td class="p_<?=$last_product_id;?>"></td>
										<td colspan="5" style="text-align:right;"><b>Product Total</b></td>
										<td><?=$p_qty_total?></td>
										<td><?=$p_rev_total?></td>
										<td><?=$p_bonus_total?></td>
									</tr>
									<tr>
										<td colspan="6" style="text-align:right;"><b>Grand Total</b></td>
										<td><?=$qty_total?></td>
										<td><?=$rev_total?></td>
										<td><?=$bonus_total?></td>
									</tr>
									</tr>
								</tbody>
							</table>
							<?php } ?>
					</div>
				</div>
                    
                    <?php                        
                    }
                    else 
                    {
                        ?>
						<table id="DistOrder" class="table table-bordered">
							<thead>
								<tr>
									<th width="50" class="text-center"><?php echo $this->Paginator->sort('id'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('dist_order_no','Order No'); ?></th>
									<th class="text-center">Area Office</th>
									<th class="text-center">Area Executive</th>
									<th class="text-center">TSO</th>
									<th class="text-center"><?php echo $this->Paginator->sort('distributor_id'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('sr_id','SR Name'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('outlet_id'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('market_id'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('dist_route_id','Route'); ?></th>				
									<th class="text-center"><?php echo $this->Paginator->sort('gross_value','Amount'); ?></th>
									<th class="text-center"><?php echo $this->Paginator->sort('order_date'); ?></th>
									<th class="text-center">Status</th>
									<!-- <th class="text-center"><?php //echo $this->Paginator->sort('processing_status'); ?></th> -->
									<th width="80" class="text-center"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							$total_amount = 0;
							foreach ($dist_Orders as $Order): 
							$Order['DistOrder']['from_app']=0;
						    ?>
							<tr style="background-color:<?php echo $Order['DistOrder']['from_app']==0 ? '#f5f5f5':'white'?>">
								<td align="center"><?php echo h($Order['DistOrder']['id']); ?></td>
								<td align="center"><?php echo h($Order['DistOrder']['dist_order_no']); ?></td>
								<td align="center"><?php echo h($Order['Office']['office_name']); ?></td>
								<td align="center"><?php echo h($Order['DistAE']['name']); ?></td>
								<td align="center"><?php echo h($Order['DistTso']['name']); ?></td>
								<td align="center"><?php echo h($Order['DistDistributor']['name']); ?></td>
								<td align="center"><?php if($sr_names){
									echo h($sr_names[$Order['DistOrder']['sr_id']]);
								} ?></td>
								<td align="center"><?php echo h($Order['DistOutlet']['name']); ?></td>
								<td align="center"><?php echo h($Order['DistMarket']['name']); ?></td>
								<td align="center"><?php echo h($Order['DistRoute']['name']); ?></td>
								<td align="center"><?php echo sprintf('%.2f',$Order['DistOrder']['gross_value']); ?></td>
								<td align="center"><?php echo date("d-m-Y",strtotime($Order['DistOrder']['order_date'])); ?></td>
								
								<td align="center">
								<?php
								if($Order['DistOrder']['status']==0){
									echo '<span class="btn btn-warning btn-xs draft">Draft</span>';	
								}elseif($Order['DistOrder']['status']==1){
									echo '<span class="btn btn-primary btn-xs draft">Pending</span>';
								}elseif($Order['DistOrder']['processing_status']==1){
									echo '<span class="btn btn-info btn-xs draft">Invoice Created</span>';	
								}elseif($Order['DistOrder']['status']==3){
									echo '<span class="btn btn-danger btn-xs draft">Cancel</span>';	
								}elseif($Order['DistOrder']['processing_status']==2){
									echo '<span class="btn btn-success btn-xs draft">Delivered</span>';
								}
								?>
								</td>
								<!-- <td align="center">
								<?php
								/*if($Order['DistOrder']['processing_status']==0){
									if($Order['DistOrder']['status']==0 || $Order['DistOrder']['status']==3){
										echo '<span class="btn btn-danger btn-xs draft">Cancel</span>';	
									}else{
										echo '<span class="btn btn-primary btn-xs draft">Pending</span>';
									}
								}elseif($Order['DistOrder']['processing_status']==1){
									echo '<span class="btn btn-info btn-xs draft">Processin Complete</span>';	
								}elseif($Order['DistOrder']['processing_status']==2){
									echo '<span class="btn btn-success btn-xs draft">Memo Complete</span>';	
								}*/
								?>
								</td> -->
								
								<td class="text-center">
									<?php if($this->App->menu_permission('dist_order_deliveries', 'admin_view'))
														   { 
										echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $Order['DistOrder']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
									
									<?php if($Order['DistOrder']['action']!=0){ ?>
										
										<?php if($Order['DistOrder']['status']==0 ){?>
											<?php if($this->App->menu_permission('dist_order_deliveries','admin_edit')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $Order['DistOrder']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>
										<?php //} ?>
									
										<?php if($this->App->menu_permission('dist_order_deliveries', 'admin_delete')){ echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $Order['DistOrder']['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $Order['DistOrder']['id'])); } ?>
									
									<?php }}?>
								</td>
								
								
							</tr>
							<?php 
							$total_amount = $total_amount + $Order['DistOrder']['gross_value'];
							endforeach; 					
							?>
							<tr>
								<td align="right" colspan="10"><b>This Page Total Amount :</b></td>
								<td align="center"><b><?php echo sprintf('%.2f',$total_amount); ?></b></td>
								<td class="text-center" colspan="3"></td>
							</tr>
							<tr>
								<td align="right" colspan="10"><b>Total Amount :</b></td>
								<td align="center"><b><?php echo sprintf('%.2f',$total_value_of_order); ?></b></td>
								<td class="text-center" colspan="3"></td>
							</tr>
							</tbody>
						</table>
                    <?php 
                    }
                    ?>
                </div>
				<?php if(empty($sr_wise_sales_bonus_report) && empty($p_wise_sales_bonus_report))
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
			<h5><?php if(!empty($requested_data)){ ?><strong>Between:</strong>&nbsp;&nbsp;<u><?php  echo $requested_data['DistOrder']['date_from']; ?>&nbsp;&nbsp;to&nbsp;&nbsp;<?php echo $requested_data['CsaOrder']['date_to']; }?></u>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Reporting Date:</strong>&nbsp;&nbsp;<?php echo date('d-F-Y');?></h5>
			<h4>Area : <?php echo $offices[$requested_data['DistOrder']['office_id']];?></h4>
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
            url: '<?= BASE_URL . 'DistOrders/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });
        
</script>

<script>
	$(document).ready(function(){

		$('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
            $('.tso_id').html('<option value="">---- Select TSO ----');
        });
        //get_route_by_office_id($("#office_id").val());
        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
            //get_tso_by_office_id($(this).val());
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
                    <?php if(isset($this->request->data['DistOrder']['dist_route_id'])){ ?>
                    	if($("#dist_route_id").val(<?=$this->request->data['DistOrder']['dist_route_id']?>))
                    	{
                    		get_market_data();
                    	}
                    <?php } ?>
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
                url: '<?= BASE_URL . 'distOrders/get_territory_thana_info' ?>',
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
		    //get_tso_by_office_id($(this).val());
		    
		}
		$(".office_id").change(function () {
		    get_dist_by_office_id($(this).val());
		    $("#sr_id").html("<option value=''>Select SR</option>");
		});
		$(".office_id").change(function () {
		    get_tso_by_office_id($(this).val());
		});
		function get_tso_by_office_id(office_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_tso_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#tso_id").html(data);
                }
            });
        }
        $("#tso_id").change(function () {
		    get_dist_by_tso_id($(this).val());
		});
		function get_dist_by_tso_id(tso_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_tso_id_and_date_range' ?>',
                data: {'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'tso_id':tso_id,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }
		function get_dist_by_office_id(office_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id_and_date_range' ?>',
                data: {'office_id': office_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }

        $("#distributor_id").change(function () {
            get_sr_list_by_distributor_id($(this).val());
        });


        function get_dist_by_office_id(office_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id_and_date_range' ?>',
                data: {'office_id': office_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }
        
        function get_sr_list_by_distributor_id(distributor_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            
            $.ajax({
                url: '<?= BASE_URL . 'DistOrderDeliveries/get_sr_list_by_distributot_id_date_range' ?>',
                data: {'distributor_id': distributor_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo},
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
                url: '<?= BASE_URL . 'DistOrders/get_thana_by_territory_id' ?>',
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
                url: '<?= BASE_URL . 'DistOrders/get_market_by_thana_id' ?>',
                data: {'thana_id': thana_id},
                type: 'POST',
                success: function (data)
                {
                    // console.log(data);
                    $("#market_id").html(data);
                }
            });
        }

        $('input[type="checkbox"]').on('change', function() {
        	$(this).siblings('input[type="checkbox"]').prop('checked', false);
        });
        function getallclasses() {
        	var classes = [];

        	$('.p_wise_report td').each(function (index) {
        		
        		if($(this).attr('class') && $.inArray($(this).attr('class'),classes)==-1)
        		{
        			classes.push($(this).attr('class'));
        		}
        	});

        	return classes;
        }
        var classes=getallclasses();
        $.each(classes,function(index,value){
        	/*var p_13=$("p_13").length;
        	console.log(p_13);*/
        	var total_class=$("."+value).length;
        	$("."+value).not(':first').remove();
        	
        	$("."+value+":first").attr('rowspan',total_class);
        });

	});
$(document).ready(function(){
	$("#download_xl").click(function(e){
		e.preventDefault();
		var formData = $(this).closest('form').serialize();
            // var arrStr = encodeURIComponent(formData);
            // console.log(formData);return;
            // console.log(arrStr);
            window.open("<?= BASE_URL; ?>DistOrderDeliveries/download_xl?" + formData);
	});
});
</script>