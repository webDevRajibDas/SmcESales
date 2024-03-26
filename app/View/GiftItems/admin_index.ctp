

<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Gift Sample Issue'); ?></h3>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('GiftItem', array('role' => 'form','action'=>'index')); ?>
					
					<table class="search">

						<tr>
							<td class="required" width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','id'=>'date_from', 'required'=>true)); ?>
							</td>
							<td class="required">
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','id'=>'date_to', 'required'=>true)); ?>
							</td>						
						</tr>
						<tr>
							<?php if(isset($region_offices)){?>
							<td class="required" width="50%">
								<?php 
								if(count($region_offices)>1)
								{
									echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id','empty'=>'---- Head Office ----', 'options' => $region_offices,)); 
								}
								else
								{
									echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id','options' => $region_offices)); 
								}
								?>

							</td>
							<?php }?>					
							<td class="required" width="50%">
								<?php
								if(count($offices)>1)
								{
									echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id',  'empty'=>'---- Select Office ----'));
								} 
								else
								{
									echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id'));
								} 
								?>

							</td>
						</tr>	
						<tr>

							<td class="territory_list">
								<?php 
								if(isset($territory_list))
									echo $this->Form->input('territory_id', array('class' => 'form-control territory_id','empty'=>'--- Select---','options' => $territory_list,'label'=>'Territory'));
								?>

							</td>
							<td class="thana_list">
								<?php 
								if(isset($thana_list))
									echo $this->Form->input('thana_id', array('class' => 'form-control thana_id','empty'=>'--- Select---','options' => $thana_list,'label'=>'Thana'));
								?>

							</td>
						</tr>
						<tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('product_type', array('legend' => 'Product Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '0', 'options' => $product_type_list, 'required' => true));  ?>
                            </td>
                        </tr>
						<tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Products : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                                    <div style="margin:auto; width:90%; float:left;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                        <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                        <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
								<?php if($gift_items){ ?>
									<a class="btn btn-success" id="download_xl">Download XL</a>
								<?php } ?>
									
							</td>						
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
				<table id="GiftItem" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="60" class="text-center"><?php //echo $this->Paginator->sort('id'); ?>Serial</th>
							<th class="text-center"><?php echo $this->Paginator->sort('territory'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('SalesPerson.name','SO Name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('thana'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('market'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('Outlet.name'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('memo_no'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('updated_at','Push Date'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('product'); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('quantity'); ?></th>
							<!-- <th width="120" class="text-center"><?php echo __('Actions'); ?></th> -->
						</tr>
					</thead>
					<tbody>
					<?php $serial=1;foreach ($gift_items as $item): ?>
					<tr>
						<td class="text-center"><?php /*echo h($item['GiftItem']['id']);*/ echo $serial++;?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['territory']); ?></td>
						<td class="text-center"><?php echo h($item['SalesPerson']['name']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['thana']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['market']); ?></td>
						<td class="text-center"><?php echo h($item['Outlet']['name']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['memo_no']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($item['GiftItem']['date']); ?></td>
						<td class="text-center"><?php echo date("d-M-Y h:i:sa",strtotime($item['GiftItem']['updated_at'])); ?></td>
						
						
						<td class="text-center"><?php echo h($item['GiftItem']['product']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['quantity']); ?></td>
						<!-- <td class="text-center">
							<?php //if($this->App->menu_permission('gift_items','admin_view')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $item['GiftItem']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>
						</td> -->
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
<!-- <script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.so_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_so_list';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.so_id').html('<option value="">---- Select SO ----');
});
</script> -->

<script>
	//$(input[type='checkbox']).iCheck(false); 
	$(document).ready(function() {
		$("input[type='checkbox']").iCheck('destroy');
		$("input[type='radio']").iCheck('destroy');
		$('#checkall2').click(function() {
			var checked = $(this).prop('checked');
			$('.selection2').find('input:checkbox').prop('checked', checked);
		});
		$('#checkall').click(function() {
			var checked = $(this).prop('checked');
			$('.selection').find('input:checkbox').prop('checked', checked);
		});
	});
</script>

<script type="text/javascript">

	$(document).ready(function(){
		if($('#office_id').val()!='')
		{
			get_territory_list($('#office_id').val());
		}
		$('#office_id').change(function() {

			get_territory_list($(this).val());
		});
		function get_territory_list(office_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>gift_items/get_territory_list',
				data: 'office_id='+office_id,
				cache: false, 
				success: function(response)
				{          
					$('.territory_list').html(response);
					<?php if(isset($this->request->data['GiftItem']['territory_id'])){?> 
						$('.territory_id option[value="<?=$this->request->data['GiftItem']['territory_id']?>"]').attr("selected",true);
						<?php }?>       
					}
				});
		}
		$('.region_office_id').selectChain({
			target: $('.office_id'),
			value:'name',
			url: '<?= BASE_URL.'gift_items/get_office_list';?>',
			type: 'post',
			data:{'region_office_id': 'region_office_id' }
		});

		function get_thana_list(territory_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>gift_items/get_thana_list',
				data: 'territory_id='+territory_id,
				cache: false, 
				success: function(response)
				{          
					$('.thana_list').html(response); 
					<?php if(isset($this->request->data['GiftItem']['thana_id'])){?> 
						$('.thana_id option[value="<?=$this->request->data['GiftItem']['thana_id']?>"]').attr("selected",true);
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


		$("#download_xl").click(function(e){
			e.preventDefault();
			var territory_id=$('.territory_id').val();
			var thana_id=$('.thana_id').val();
			var office_id=$('.office_id').val();
			var region_office_id=$('.region_office_id').val();
			var date_from=$('#date_from').val();
			var date_to=$('#date_to').val();
			var product_type = $("input[name='data[GiftItem][product_type]']:checked").val();
			var product_id = [];
			$(':checkbox:checked').each(function(i){
				product_id[i] = $(this).val();
			});

			var html = '';
			$.ajax({
				url:'<?= BASE_URL.'gift_items/download_xl';?>',
				type:'POST',
				data:{
					'office_id':office_id,
					'region_office_id':region_office_id,
					'thana_id':thana_id,
					'date_from':date_from,
					'date_to':date_to,
					'territory_id':territory_id,
					'product_type':product_type,
					'product_id':product_id,
				},
				cache: false, 
				success: function(response)
				{   
					// console.log(response);
					html+=response;
					var blob = new Blob([html], { type: 'data:application/vnd.ms-excel' }); 
					var downloadUrl = URL.createObjectURL(blob);
					var a = document.createElement("a");
					a.href = downloadUrl;
					a.download = "downloadFile.xls";
					document.body.appendChild(a);
					a.click();
				}

			});
			
		});

		get_product_list($(".product_type:checked").serializeArray());
        $(".product_type").change(function() {
            product_type = $(".product_type:checked").serializeArray();
            console.log(product_type);
            get_product_list(product_type);
        });
        var product_check = <?php echo @json_encode($this->request->data['GiftItem']['product_id']); ?>;
        console.log(product_check);

        function get_product_list(product_type) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>gift_items/get_product_list',
                data: product_type,
                cache: false,
                success: function(response) {
                    $(".product").html(response);
                    if (product_check) {
                        $.each(product_check, function(i, val) {

                            $(".product_id>input[value='" + val + "']").prop('checked', true);

                        });
                    }
                }
            });
        }




	})
</script>