<script>
var tso_list='<?php echo json_encode($tso_list);?>';
var ae_list='<?php echo json_encode($ae_list);?>';
</script>

<?php 

  //pr($existing_record['DistOrderDetail']);die();
   $selected_ae=$existing_record['DistOrder']['ae_id']; 
   $selected_tso=$existing_record['DistOrder']['tso_id'];
   
   $selected_ae_name="";
   $selected_tso_name="";
           
           
   if($selected_ae)
   $selected_ae_name=$ae_list[$selected_ae];
   
   if($selected_tso)
   $selected_tso_name=$tso_list[$selected_tso];
?>


<style>
  .form-control 
  {
    float: left;
    width: 50%;
    font-size: 13px;
    height: 28px;
    padding: 0px 4px;
  }
  .width_100_this
  {
    width:100%;
  }
  .display_none{display:none;}
  .width_100
  {
    width:100%;
  }
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button
  { 
    -webkit-appearance: none; 
    margin: 0; 
  }
  .bonus{
    width: 130px !important;
  }
  .product_unit_name{
    width: 80px !important;
  }
  .product_id{
    width: 150px !important;
  }
  .open_bonus_product_id{
    width: 150px !important;
  }
  #loading
  {
    position: absolute;
    width: auto;
    height: auto;
    text-align: center;
    top: 45%;
    left: 50%;
    display: none;
    z-index: 999;
  }
  #loading img
  {
    display: inline-block;
    height: 100px;
    width: auto;
  }

  .errorInput
  {
    border:1px solid #ff0000;
  }
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit SR Delivery'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>SR Delivery List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('DistOrder', array('role' => 'form','id'=>'frm')); ?>
             <div class='row'>
              <div class="col-lg-6">
                <div class="form-group">
                    <?php echo $this->Form->input('order_date', array('class' => 'form-control datepicker order_date','id'=>'order_date','type' => 'text', 'required'=>TRUE,  'value'=>$existing_record['order_date'])); ?>
                </div>
                            
                
                    <?php echo $this->Form->input('entry_date', array('type'=>'hidden','class' => 'form-control datepicker', 'value' => $existing_record['order_time'], 'required' => TRUE)); ?>
               
                            
                 <div class="form-group">
                    <?php echo $this->Form->input('order_reference_no', array('label'=>'Order Number :','class' => 'form-control order_reference_no','value'=>$existing_record['order_reference_no'], 'maxlength' => '15', 'required'=>TRUE,'type' => 'text' ,'readonly')); ?>
                </div>
               
                
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'selected'=>$existing_record['office_id'], 'disabled')); ?>
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['office_id'])); ?>
                </div>                     
                   
 <?php echo $this->Form->input('sale_type_id', array('class' => '', 'required'=>TRUE, 'id' => 'sale_type_id', 'type' => 'hidden', 'value'=> '10')); ?>                    
               
                 <div class="form-group">
                <?php echo $this->Form->input('distributor_id', array('id' => 'distributor_id', 'class' => 'form-control distributor_id', 'required' => TRUE, 'options'=>$distributors, 'selected'=>$existing_record['distributor_id'], 'disabled')); ?>
                 <?php echo $this->Form->input('distributor_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['distributor_id'])); ?>
                </div>

             <div class="form-group">
                <?php echo $this->Form->input('sr_id', array('label'=>'SR','id' => 'sr_id', 'class' => 'form-control sr_id', 'required' => TRUE, 'options'=>$srs, 'selected'=>$existing_record['sr_id'], 'disabled')); ?>
                 <?php echo $this->Form->input('sr_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['sr_id'])); ?>
              </div>
                <?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['territory_id'])); ?>
                <?php echo $this->Form->input('thana_id', array('id' => 'thana_id','class' => 'form-control thana_id','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['thana_id'])); ?>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                   <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','label'=>'Route/Beat', 'class' => 'form-control','required' => TRUE, 'options'=>$distRoutes, 'selected'=>$existing_record['dist_route_id'], 'disabled')); ?>
                   <?php echo $this->Form->input('dist_route_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['dist_route_id'])); ?>
                </div>

                <div class="form-group"  id="market_id_so">
                    <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'options' => $markets, 'selected'=>$existing_record['market_id'])); ?>
                </div>
                
                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => TRUE, 'options' => $outlets, 'selected'=>$existing_record['outlet_id'])); ?>
                </div>

                <div class="form-group">
                    <?php
                        echo $this->Form->input('ae_name', array('label'=>'Area Executive :','id'=>'ae_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"$selected_ae_name",'readonly'));
                   ?>
                 </div>
                 
                 <div class="form-group">
                    <?php
                        echo $this->Form->input('tso_name', array('label'=>'TSO :','id'=>'tso_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"$selected_tso_name",'readonly'));
                   ?>
                 </div>
              </div>
               
             </div>         
      <?php echo $this->Form->input('ae_id', array('type' => 'hidden','id'=>'ae_id','label' => false, 'class' => 'form-control', 'value' =>$selected_ae)); ?> 
      <?php echo $this->Form->input('tso_id', array('type' => 'hidden', 'id'=>'tso_id','label' => false, 'class' => 'form-control', 'value' =>$selected_tso)); ?>
      <?php echo $this->Form->input('dist_order_no', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['dist_order_no'], 'readonly')); ?>
      <div class="table-responsive">	
				<!--Set Product area-->
				<table class="table table-striped table-condensed table-bordered invoice_table">
				<thead>
					<tr>
						<th class="text-center" width="5%">ID</th>
						<th class="text-center">Product Name</th>
						<th class="text-center" width="12%">Unit</th>
						<th class="text-center" width="12%">Rate</th>
						<th class="text-center" width="12%">QTY</th>
						<th class="text-center" width="12%">Value</th>
            <th class="text-center" width="12%">Discount Value</th>
						<th class="text-center" width="10%">Bonus</th>
						<th class="text-center" width="10%">Action</th>
					</tr>
				</thead>
				<tbody class="product_row_box">
				<?php
					if(!empty($existing_record)) {
            $sl = 1;
						$total_price = 0;
						$gross_val = 0;
						foreach($existing_record['DistOrderDetail'] as $val){

              if($val['price']==0.000)
                continue;

							$total_price = $val['price'] * $val['sales_qty'];
							$gross_val = $gross_val + $total_price;
				?>
					<tr id="<?php echo $sl?>" class="new_row_number">
						<th class="text-center sl_Order" width="5%"><?php echo $sl?></th>
						<th class="text-center">
							<?php
								echo $this->Form->input('product_id',array('name'=>'data[OrderDetail][product_id][]','class'=>'form-control width_100 product_id','required'=>TRUE,'options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id'],'id'=>"OrderProductId"));
							?>
                            <input type="hidden" class="product_id_clone" value="<?php echo $val['product_id']; ?>" />
                            <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                            <input type="hidden" class="ajax_flag" value="1">
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="" class="form-control width_100 product_unit_name" value="<?=$val['measurement_unit_name']?>" disabled/>
							<input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" value="<?=$val['measurement_unit_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate <?='prate-'.$val['product_id']?>" value="<?=$val['price']?>" readonly />
							<input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id" value="<?=$val['product_price_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="number" step="any" min="0" name="data[OrderDetail][sales_qty][]" class="form-control width_100 min_qty" value="<?=$val['sales_qty']?>" data-prev_value="<?=$val['sales_qty']?>" required/>
              <input type="hidden" value="<?=$val['sales_qty']?>" class="prev_min_qty">
              <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>
							<input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>" readonly />
						</th>
            <th class="text-center" width="12%">
                <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />
                <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />
                <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>
                <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>
                <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>
                <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>
            </th>
						<th class="text-center" width="10%">
                <input type="text" class="form-control width_100 bonus" disabled  value="<?php 
                  if(!empty($val['bonus_product_id'])){
                    echo $val['bonus_qty'].' ('.$val['bonus_product_name'].')'; 
                  }else{
                    echo 'N.A';
                  }
                  
                ?>"/>
                <input type="hidden" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"  value="<?=$val['bonus_product_id']?>"/>
                <input type="hidden" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"  value="<?=$val['bonus_qty']?>"/>
                <input type="hidden" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"  value="<?=$val['measurement_unit_id']?>"/>
            </th>
						<th class="text-center" width="10%">
							<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                            <?php
                                if ($sl != 1) {
                                    echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                                }
                            ?>
							
							<?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_Order'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
						</th>
					</tr>
				<?php
					$sl++;
						}
					}
				?>
				</tbody>
				<tfoot>
					<tr>
              <td colspan="5" align="right"><b>Total : </b></td>
              <td align="center"><input name="data[DistOrder][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
              </td>
              <td></td>
             
          </tr>
          <tr>
              <td colspan="5" align="right"><b>Discount : </b></td>
              <td align="center">
                  <input name="data[DistOrder][total_discount]" class="form-control width_100 total_discount" type="text" id="total_discount" value="0" readonly />
                  <input name="data[DistOrder][discount_percent]" class="form-control width_100 discount_percent" type="hidden" id="discount_percent" readonly/>
                  <input name="data[DistOrder][discount_value]" class="form-control width_100 discount_value" type="hidden" id="discount_value" readonly />
                  <input name="data[DistOrder][discount_type]" class="form-control width_100 discount_value" type="hidden" id="discount_type_memo_total" readonly />
              </td>
              <td></td>
            
          </tr>
          <tr>
              <td colspan="5" align="right"><b>Net Payable: </b></td>
              <td align="center"><input name="data[Memo][gross_value]" class="form-control width_100 net_payable" type="text" id="net_payable" value="0" readonly />
              </td>
              <td></td>
              <td></td>
          </tr>
          <tr>
              <td colspan="5" align="right"><b>Cash Collection : </b></td>
              <td align="center">
                  <input name="data[DistOrder][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" />
                  <input name="data[DistOrder][credit_amount]" class="form-control width_100" type="hidden" id="credit_amount" readonly />
              </td>
              <td></td>
            
          </tr>
        <tr>
         
           <td colspan="4"> <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal"  data-backdrop="static" data-keyboard="false"   data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
              <div id="bonus_product" class="modal fade" role="dialog">
           
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Open Bonus</h4>
                    </div>
                    <div class="modal-body">
                      <table class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">Product Name</th>
                            <th class="text-center" width="12%">Unit</th>
                            <th class="text-center" width="12%">QTY</th>
                            <th class="text-center" width="10%">Action</th>
                          </tr>
                        </thead>
                        <tbody class="bonus_product">
                          <?php
                          if(!empty($existing_record)) {
                            foreach($existing_record['DistOrderDetail'] as $key=>$val){
                              $sl=1;
                              if($val['price'] > 0.00 || $val['is_bonus']==3)
                                continue;
                              ?>
                          <tr  class="bonus_row">
                            <th class="text-center" <?php if($sl==1)?> id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true,'default'=>$val['product_id']));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="<?=$val['measurement_unit_name']?>" disabled/>
                              <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?=$val['measurement_unit_id']?>"/>
           
                              <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value="<?=$val['sales_qty']?>"/>
                              <input type="hidden" class="combined_product"/>
                              <input type="hidden" name="data[OrderDetail][discount_amount][]" />
                              <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                              <input type="hidden" name="data[OrderDetail][is_bonus][]" value="1"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>
                            </th>
                          </tr>
                          <?php 
                          $sl++;
                        }
                      } 
                      ?>
                      <?php if($sl==1) {?>
                          <tr  class="bonus_row">
                            <th class="text-center" id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="" disabled/>
                              <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value=""/>
           
                              <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value=""/>
                              <input type="hidden" class="combined_product"/>
                              <input type="hidden" name="data[OrderDetail][discount_amount][]" />
                              <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                              <input type="hidden" name="data[OrderDetail][is_bonus][]" value="1"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              
                            </th>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" id="model_close_btn" class="btn btn-default" data-dismiss="modal">Ok</button>
                    </div>
                  </div>
                </div>        
              </div>
            </td> 
            <td align="right"></td>
            <td align="center">
            </td>
            <td></td>
          
        </tr>
				</tfoot>	
				</table>
                </div>
			
			<?php //echo $this->Form->submit('Submit', array('class' => 'submit btn btn-large btn-primary')); ?>
            
            <div class="form-group" style="padding-top:20px;">
                <div class="pull-right">
                	<?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); 
                  ?>
            		<?php // echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
                </div>
            </div>
            
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
	
</div>

<div id="order_product_list">
    <?php
        echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false, 'required'=>TRUE,'id'=>"OrderProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>


<div id="open_bonus_product_list">
    <?php
      echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true ,'id'=>"DistOrderProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
</div>


<style>
    .bonus{
        width: 130px !important;
    }
    .product_unit_name{
        width: 80px !important;
    }
    .product_id{
        width: 150px !important;
    }
    #loading{
        position: absolute;
        width: auto;
        height: auto;
        text-align: center;
        top: 45%;
        left: 50%;
        display: none;
        z-index: 999;
    }
    #loading img{
        display: inline-block;
        height: 100px;
        width: auto;
    }
</style>

<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
$('#frm').bind('submit', function (e) {
  var button = $('.save');
  button.prop('disabled', true);
  var valid = true; 
  if (!valid) { 
    e.preventDefault();
    button.prop('disabled', false);
  }
});
var special_groups=[];
var outlet_category_id;
$(document).ready(function () 
{
    get_special_group();
  function get_special_group()
  {
    var outlet_id=$('#outlet_id').val(); 
    var memo_date=$('#order_date').val();
    var office_id=$('#office_id').val();
    var territory_id=$('#territory_id').val();
    if(outlet_id){
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL . 'DistOrderDeliveries/get_spcial_group_and_outlet_category_id'?>',
            data: {
                'outlet_id':outlet_id,
                'memo_date':memo_date,
                'office_id':office_id,
            },
            cache: false, 
            success: function(response){
                var res=$.parseJSON(response);
                outlet_category_id=res.outlet_category_id;
                special_groups=res.special_group_id;
            }
        });     
    }
  }

  $('#order_product_list').hide();
  $('#open_bonus_product_list').hide();
  $('body').on('keydown', 'input, select, textarea', function(e) 
  {
    var self = $(this)
    , form = self.parents('form:eq(0)')
    , focusable
    , next
    ;
    if (e.keyCode == 13) 
    {
      focusable = form.find('input,select,textarea').filter(':visible');
      if(this.id == 'OrderProductId')
        next = focusable.eq(focusable.index(this)+3);
      else if(this.id=='open_bonus_min_qty')
        next = focusable.eq(focusable.index(this)+1);
      else if(this.name=='data[OrderDetail][sales_qty][]')
        next = focusable.eq(focusable.index(this)+4);
      else if(this.id=='DistOrderProductId')
        next = focusable.eq(focusable.index(this)+2);
      else
        next = focusable.eq(focusable.index(this)+1);
      if (next.length)
      {
        next.focus();
      } 
      else
      {
      }
      return false;
    }
  });
       
        /*For Adding Bonus Product list : START*/
    $(document).ready(function () {
      $('#open_bonus_product_list').hide();
      $('.bonus_add_more').hide();
      $(".bonus_row").last().find('.bonus_add_more').show();
        $("body").on("click", ".bonus_add_more", function () {
            var product_list = $('#open_bonus_product_list').html();
            var product_bonus_row =
                    '\
            <tr class="bonus_row">\
                <th class="text-center">' + product_list + '</th>\
                <th class="text-center" width="12%">\
                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>\
                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
                    <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
                </th>\
                <th class="text-center" width="12%">\
                    <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required  id="open_bonus_min_qty" />\
                    <input type="hidden" class="combined_product"/>\
                    <input type="hidden" name="data[OrderDetail][discount_amount][]"/>\
                    <input type="hidden" name="data[OrderDetail][disccount_type][]"/>\
                    <input type="hidden" name="data[OrderDetail][policy_type][]"/>\
                    <input type="hidden" name="data[OrderDetail][policy_id][]"/>\
                    <input type="hidden" name="data[OrderDetail][is_bonus][]" value="1"/>\
                </th>\
                <th class="text-center" width="10%">\
                    <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
                    <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
                </th>\
            </tr>\
            ';
            var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
            if (product_id)
            {
                $(this).hide();
                $(".bonus_product").append(product_bonus_row);
                 $("#DistOrderProductId").focus();
            } else
            {
                 $("#DistOrderProductId").focus();
            }
        });
        $("body").on("click", ".bonus_remove", function () {
            $(this).parent().parent().remove();
            // var total_tr= $(".bonus_row").length;
            $(".bonus_row").last().find('.bonus_add_more').show();
        });
        $("body").on("change", ".open_bonus_product_id", function () {
            var product_id = $(this).val();
            var product_box = $(this).parent().parent().parent();
            var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
            var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
            var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
            var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
            var distributor_id = $("#distributor_id").val();
           
            $.ajax({
                url: '<?= BASE_URL . 'dist_Orders/get_bonus_product_details' ?>',
                'type': 'POST',
                data: {product_id: product_id, distributor_id: distributor_id},
                success: function (result)
                {
                    var data = $.parseJSON(result);

                    product_category_id.val(data.category_id);
                    product_unit_name.val(data.measurement_unit_name);
                    product_unit_id.val(data.measurement_unit_id);
                    product_qty.val(1);
                    //product_qty.attr('min', 1);
                    //product_qty.attr('max', data.total_qty);
                },
                error: function (error)
                {
                    product_category_id.val();
                    product_unit_name.val();
                    product_unit_id.val();
                    product_qty.val(0);
                }
            });
        });

    });
    /*For Adding Bonus Product list : START*/
        
        /******  Submit data on CTRL+S event start ************/
            $(window).bind('keydown', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    switch (String.fromCharCode(event.which).toLowerCase()) {
                    case 's':
                        
                        var error_count=0;
                        error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                event.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            else 
                            {
                                event.preventDefault();
                                $(".save").trigger("click");
                            }
                       break;
                       case 'b':
                               event.preventDefault();
                                 $("#DistOrderProductId").focus();
                                $(".show_bonus").trigger("click");
                       break;
                       
                       case 'c':
                                event.preventDefault();
                                $("#model_close_btn").trigger("click");
                       break;
                       case 'p':
                                event.preventDefault();
                                $(".new_row_number").last().find('a.add_more').trigger("click");
                                $(".new_row_number").last().find('.product_id').focus();
                                //$("#DistOutletName").trigger("focus");
                       break;
                       case 'f':
                                 event.preventDefault();
                                 $(".bonus_row").last().find('a.bonus_add_more').trigger("click");
                       break;
                    }
                }
            });
            
          /******  Submit data on CTRL+S event End ************/  
          
          /******  Normal form submission start ************/
          
           $("form#DistOrderAdminEditForm").submit(function(e){
                       var error_count=0;
                       error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                e.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            
           });
          
          /******  Normal form submission end ************/
        
        
        
        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOrders/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

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
        
     
            $(".office_id").change(function () {
                get_dist_by_office_id($(this).val());
                $("#sr_id").html("<option value=''>Select SR</option>");
            });



            $("#distributor_id").change(function () {
                get_sr_list_by_distributor_id($(this).val());
            });

            function get_dist_by_office_id(office_id)
            {

                $.ajax({
                    url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#distributor_id").html(data);
                    }
                });
            }
            
            function get_sr_list_by_distributor_id(distributor_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrderDeliveries/get_sr_list_by_distributot_id' ?>',
                    data: {'distributor_id': distributor_id},
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
        
        
    });
</script>
<script>
    $(document).ready(function(){
        if ($('#sale_type_id').val() == 1) {
            $('.csa_name').hide();
            $('#market_id_csa').hide();
            $('#outlet_id_csa').hide();
            $('#market_name').attr('required',false);
            $('#outlet_name').attr('required',false);
        }
        $('#sale_type_id').change(function(){
            if ($('#sale_type_id').val() == 1) {
               
                $('#market_id_so').show();
                $('#outlet_id_so').show();
                $('#market_id_csa').hide();
                $('#outlet_id_csa').hide();
                $('.csa_name').hide();
                $('#market_name').attr('required',false);
                $('#outlet_name').attr('required',false);
            }
            if ($('#sale_type_id').val() == 2) {
                $('#market_id_so').hide();
                $('#outlet_id_so').hide();
                $('#market_id_csa').show();
                $('#outlet_id_csa').show();
                $('.csa_name').show();
                $('#market_id').attr('required',false);
                $('#outlet_id').attr('required',false);
            }
        });
    });
</script>
<script>
  $(document).ready(function()
  { 
    $("body").on("click", ".add_more", function ()
    {
      var sl = $('.invoice_table>tbody>tr').length + 1;

      var product_list = $('#order_product_list').html();
     
      var product_box = $(this).parent().parent().parent();
      
      var current_row_no = $(this).parent().parent().attr('id');
      //alert(current_row_no);

      var current_row = 
      '<th class="text-center sl_Order" width="5%"></th>\
      <th class="text-center">' + product_list + '</th>\
      <th class="text-center" width="12%">\
        <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
        <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
      </th>\
      <th class="text-center" width="12%">\
        <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/>\
        <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
      </th>\
      <th>\
        <input type="number" name="data[OrderDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty sales_qty_validation_check" required/>\
        <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
        <input type="hidden" class="combined_product"/>\
      </th>\
      <th class="text-center" width="12%">\
        <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
      </th>\
      <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
          <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
          <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
          <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>\
          <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>\
          <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>\
      </th>\
      <th class="text-center" width="10%">\
        <input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
        <input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
        <input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
        <input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
      </th>\
      <th class="text-center" width="10%">\
        <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> \
        <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>\
      </th>';


      var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
      if (valid_row != '') {
          product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
          $('#' + sl + '>.sl_Order').text(sl);
          $(this).hide();
      } else {
          alert('Please fill up this row!');
      }

    });
    $("body").on("change", ".product_id", function ()
    {
        $('#gross_value').val(0);
        var sl = $('.invoice_table>tbody>tr').length;
        var current_row_no = $(this).parent().parent().parent().attr('id');
        if ($('#'+current_row_no+'>th>.product_rate').val() == '') {
            $('#'+current_row_no+'>th>.bonus').val('N.A');
            $('#'+current_row_no+'>th>.bonus_product_id').val(0);
            $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
            $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
        }
      

        var product_id = $(this).val();
        var product_box = $(this).parent().parent().parent();
        var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
        var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
        var product_rate = product_box.find("th:nth-child(4) .product_rate");
        var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
        var product_qty = product_box.find("th:nth-child(5) .min_qty");
        var total_val = product_box.find("th:nth-child(6) .total_value");
        var combined_product = product_box.find("th:nth-child(5) .combined_product");
        var combined_product_change = combined_product.val();
        var outlet_id=$('#outlet_id').val();
        var rate_class = product_rate.attr('class').split(' ').pop();
        var value_class = total_val.attr('class').split(' ').pop();
        var distributor_id = $('#distributor_id').val();
        var territory_id = $('.territory_id').val();

        var order_date=$('#order_date').val();  
        $.ajax
        ({
            url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_unit' ?>',
            type: 'POST',
            data: {product_id: product_id, territory_id: territory_id, distributor_id: distributor_id,order_date:order_date},
            success: function (result) 
            {
                var obj = jQuery.parseJSON(result);
                product_unit.val(obj.product_unit.name);
                product_unit_id.val(obj.product_unit.id);
                var total_qty = obj.total_qty;
                product_box.find("th:nth-child(6) input").val('');
                product_box.find("th:nth-child(8) input").val('');
                $('#'+current_row_no+'>th>.min_qty').attr('max',total_qty);
                $('#'+current_row_no+'>th>.product_rate').val('0.00');
            }
        });
        

        if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1)
        {
            product_rate.removeClass(rate_class);
            total_val.removeClass(value_class);
            /*-----------*/
            product_rate.addClass('prate-' + product_id);
            total_val.addClass('tvalue-' + product_id);
        } 
        else 
        {
            product_rate.addClass('prate-' + product_id);
            total_val.addClass('tvalue-' + product_id);
        }
    });
  });
 
</script>

<script>
function total_values()
{
  var t = 0;
  $('.total_value').each(function () {
    if ($(this).val() != '') {
      t += parseFloat($(this).val());
    }
  });
  $('#gross_value').val(t);
  $('#cash_collection').val(t);
}
/*--------- check combined or individual product price --------*/
var selected_bonus=$.parseJSON('<?php echo json_encode($selected_bonus) ?>');
var selected_set=$.parseJSON('<?php echo json_encode($selected_set) ?>');
var selected_policy_type=$.parseJSON('<?php echo json_encode($selected_policy_type) ?>');
var selected_option_id=[];
$("body").on("keyup", ".min_qty", function (e)
{
    var min_qty = $(this).val();
    var current_row_no = $(this).parent().parent().attr('id');

    var keycode = (e.keyCode ? e.keyCode : e.which);
    if(keycode == '13')
    {
      e.preventDefault();
      var i = 0;
      if($(this).val() != "")
      {
        $('.product_id').each(function ()
        {   
          
          if($(this).val() == "")
          {
              $('.product_id').eq(i).focus();
              return false;
          }
          i++;
        }); 
      }   
    }
    
    var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();
    pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

    if ( pro_val) 
    {
        var sl = $('.invoice_table>tbody>tr').length;
        var product_box = $(this).parent().parent();
        var product_field = product_box.find("th:nth-child(2) .product_id");
        var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
        var product_rate = product_box.find("th:nth-child(4) .product_rate");
        var product_qty = product_box.find("th:nth-child(5) .min_qty");
        var total_value = product_box.find("th:nth-child(6) .total_value");
        var total_value_field = product_box.find("th:nth-child(6) .total_value");
        var combined_product = product_box.find("th:nth-child(5) .combined_product");
        var combined_product_field=combined_product;
        var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
        var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
        var min_qty = product_qty.val();
        var id = product_field.val();
        var product_id = id;
        var order_date=$('#order_date').val();
        
         /********************* new part start ******************/
        var new_product=0;
        if (min_qty != '' && min_qty != 0 && parseFloat(min_qty)>0)
        {
            var multiple_products=0;
            multiple_products=check_duplicate_products_all();
            if(multiple_products>0)
            {
                    alert("Duplicate product item has been selected");
                      $('#' + current_row_no + '>th>.min_qty').val('');
            }
            else 
            {
                new_product=1; 
            }
        }
            
        var distributor_id = $('#distributor_id').val();
        var territory_id = $('.territory_id').val();
        if (new_product == 1)
        {
            
            var product_wise_qty= {};
            $('.product_row_box .product_id').each(function(index,value){
            var producct_box_each=$(this).parent().parent().parent();
            if(producct_box_each.find("th:nth-child(5) .min_qty").val())
            {
              product_wise_qty[$(this).val()]=producct_box_each.find("th:nth-child(5) .min_qty").val();
            }
            });
            pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();
            var sl = $('.invoice_table>tbody>tr').length;
            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");

            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            var total_val = product_box.find("th:nth-child(6) .total_value");
            var combined_product_obj = product_box.find("th:nth-child(5) .combined_product");
            var combined_product_id_obj = product_box.find("th:nth-child(5) .combination_id");
            var combined_product = combined_product_obj.val();
            var min_qty = product_qty.val();
            var id = product_field.val();

            var order_date=$('#order_date').val(); 
            delay(function()
            { 
              $('#myModal').modal('show');
              $('#loading').show();

              if(min_qty == '' || min_qty == 0)
              {
                min_qty = 1;
                $('#'+current_row_no+'>th>.min_qty').val(1);
              }
              /*-----------------------------------*/
              $.ajax({
                  url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_price' ?>',
                  'type': 'POST',
                  data: {
                      combined_product: combined_product,
                      min_qty: min_qty, 
                      product_id: id,
                      memo_date:order_date ,
                      cart_product:product_wise_qty,
                      special_group:JSON.stringify(special_groups),
                      outlet_category_id:outlet_category_id
                  },
                  success: function (result) 
                  {
                    var obj = jQuery.parseJSON(result);

                    if (obj.price != '')
                    {
                        product_rate.val(obj.price);
                    }
                    if (obj.price_id != '')
                    {
                        product_price_id.val(obj.price_id);
                    }
                    if (obj.total_value)
                    {
                        total_val.val(obj.total_value);
                    }
                    combined_product_obj.val(obj.combine_product);
                    if (obj.combination != undefined)
                    {
                      combined_product_id_obj.val(obj.combination_id);
                      $.each(obj.combination, function (index, value)
                      {
                          var prate = $(".prate-" + value.product_id);
                          var tvalue = $(".tvalue-" + value.product_id);
                          prate.val(value.price);
                          tvalue.val(value.total_value);
                          prate.next('.product_price_id').val(value.price_id);
                          prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
                          prate.parent().parent().find("th:nth-child(5) .combination_id").val(obj.combination_id);
                      });
                    }

                    if (obj.recall_product_for_price != undefined)
                    {
                      $.each(obj.recall_product_for_price, function (index, value)
                      {
                          var prate = $(".prate-" + value);
                          var tvalue = $(".tvalue-" + value);
                          prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
                          prate.parent().parent().find("th:nth-child(5) .combination_id").val('');
                          prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
                      });
                    }

                    var gross_total = 0;
                    $('.total_value').each(function ()
                    {
                        if($(this).val() !='')
                        {
                          gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                        }
                    });
                    if($("#gross_value").val(gross_total.toFixed(2))){
                      $('.n_bonus_row').remove();
                      $('.discount_value').val(0.00);
                      $('.discount_amount').val(0.00);
                      get_policy_data();
                    }

                    if (obj.mother_product_quantity != undefined)
                    {
                      var mother_product_quantity = obj.mother_product_quantity;
                      var bonus_product_id = obj.bonus_product_id;
                      var bonus_product_name = obj.bonus_product_name;
                      var bonus_product_quantity = obj.bonus_product_quantity;
                      var sales_measurement_unit_id = obj.sales_measurement_unit_id;
                      var no_of_bonus_slap = mother_product_quantity.length;
                      var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;
                      for (var i = 0; i < no_of_bonus_slap; i++) 
                      {
                        if (parseFloat($('#'+current_row_no+'>th>.min_qty').val())>=parseFloat(mother_product_quantity[i].min) && parseFloat($('#'+current_row_no+'>th>.min_qty').val())<=parseFloat(mother_product_quantity[i].max))
                        
                        {
                          if (i == 0)
                          {
                             $('#'+current_row_no+'>th>.bonus').val('N.A');
                             $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                             $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                             $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                          }
                          else
                          {
                             $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i+(-1)]+'('+bonus_product_name[i+(-1)]+')');
                             $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i+(-1)]);
                             $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i+(-1)]);
                             $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i+(-1)]);
                          }
                          break;
                        }
                        else
                        {
                          var current_qty = parseFloat($('#'+current_row_no+'>th>.min_qty').val());
                      
                          var bonus_qty = Math.floor(current_qty/parseFloat(mother_product_quantity_bonus)) * bonus_product_quantity[i];
                          $('#'+current_row_no+'>th>.bonus').val(bonus_qty+' ('+bonus_product_name[i]+')');
                          $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
                          $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_qty);
                          $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
                        }
                      }  
                    }
                    else
                    {
                      $('#'+current_row_no+'>th>.bonus').val('N.A');
                      $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                      $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                      $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                    }
                    $('#cash_collection').val('');
                    $('#loading').hide();
                    $('#myModal').modal('hide');
                    $('.add_more').removeClass('disabled');
                  }
              });

              function get_policy_data()
              {
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_policy' ?>',
                    'type': 'POST',
                    data: {
                              min_qty: min_qty,
                              product_id: id,
                              order_date:order_date ,
                              cart_product:product_wise_qty,
                              memo_total:$("#gross_value").val(),
                              special_group:JSON.stringify(special_groups),
                              outlet_category_id:outlet_category_id,
                              outlet_id:$('#outlet_id').val(),
                              office_id:$('#office_id').val(),
                              distributor_id: distributor_id,
                              selected_bonus: JSON.stringify(selected_bonus),
                              selected_set: JSON.stringify(selected_set),
                              selected_policy_type: JSON.stringify(selected_policy_type),
                              selected_option_id: JSON.stringify(selected_option_id),
                          },
                    success: function (result) 
                    {
                      var response=$.parseJSON(result);
                      if(response.discount)
                      {
                        var discount=response.discount;
                        var total_discount=response.total_discount;
                        $.each(discount,function(ind,val){
                          $.each(val,function(ind1,val1){
                            var prate = $(".prate-" + val1.product_id);
                            var tvalue = $(".tvalue-" + val1.product_id);
                            prate.val(val1.price);
                            tvalue.val(val1.total_value);
                            prate.next('.product_price_id').val(val1.price_id);
                            prate.parent().parent().find("th:nth-child(7) .discount_value").val(val1.total_discount_value);
                            prate.parent().parent().find("th:nth-child(7) .discount_amount").val(val1.discount_amount);
                            prate.parent().parent().find("th:nth-child(7) .disccount_type").val(val1.discount_type);
                            prate.parent().parent().find("th:nth-child(7) .policy_type").val(val1.policy_type);
                            prate.parent().parent().find("th:nth-child(7) .policy_id").val(val1.policy_id);
                            prate.parent().parent().find("th:nth-child(7) .is_bonus").val('0');
                          });
                        });
                        $('.total_discount').val(total_discount.toFixed(2));
                        gross_value = $('#gross_value').val();
                        net_payable = (gross_value)-(total_discount);
                        $('.net_payable').val(net_payable.toFixed(2));
                      }
                      if(response.bonus_html)
                      {
                        var b_html = response.bonus_html;
                        selected_bonus=response.selected_bonus;
                        selected_set=response.selected_set;
                        selected_policy_type=response.selected_policy_type;
                        $('.bonus_product').append(b_html);
                      }
                    selected_option_id=response.selected_option_id;
                    }
                });
              }

            }, 1000 );
        }
    }
});
$("body").on("click",".is_bonus_checked",function(){
    if($(this).prop('checked'))
    {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly',false);
      $(this).parent().prev().find('.policy_min_qty').prop('required',true);
      $(this).parent().prev().find('.policy_min_qty').attr('min',1);
    }
    else
    {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly',true);
      $(this).parent().prev().find('.policy_min_qty').prop('required',false);
      $(this).parent().prev().find('.policy_min_qty').attr('min',0);
      $(this).parent().prev().find('.policy_min_qty').val(0.00);
    }
});


$("body").on("keyup",".policy_min_qty",function(){
    var class_list=$(this).attr('class');
    class_list=class_list.split(" ");
    var policy_set_class=class_list[2];
    var max_qty=parseFloat($(this).attr('max'));
    var stock_qty=parseFloat($(this).data('stock'));
    var total_provide_qty=0.00;
    $("."+policy_set_class).not(this).each(function(ind,val){
      total_provide_qty+=parseFloat($(this).val());
    });
    var given_qty=parseFloat($(this).val());
    var max_provide_qty=max_qty-total_provide_qty;
    if(stock_qty < given_qty && stock_qty<=max_provide_qty)
    {
        $(this).val(stock_qty);
    }
    else if(given_qty > max_provide_qty)
    {
      $(this).val(max_provide_qty);
    }
    
    var set=$(this).data('set');
    var policy_id=$(this).parent().prev().find('.policy_id').val();
        // selected_bonus[policy_id]=0;
    $("."+policy_set_class).each(function(ind,val){
      var product_id=$(this).parent().prev().prev().find('.policy_bonus_product_id').val();
      selected_bonus[policy_id][set][product_id]=$(this).val();
    });
});
$("body").on("click",".btn_set",function(e){
    e.preventDefault();
    var set=$(this).data('set');
    var policy_id=$(this).data('policy_id');
    var prev_selected=selected_set[policy_id];
    if(set!=prev_selected)
    {
      $(".btn_set[data-set='"+set+"'][data-policy_id='"+policy_id+"']").addClass('btn-success');
      $(".btn_set[data-set='"+set+"'][data-policy_id='"+policy_id+"']").removeClass('btn-default');

      $(".btn_set[data-set='"+prev_selected+"'][data-policy_id='"+policy_id+"']").addClass('btn-default');
      $(".btn_set[data-set='"+prev_selected+"'][data-policy_id='"+policy_id+"']").removeClass('btn-success');

      $(".bonus_policy_id_"+policy_id+".set_"+set).removeClass('display_none');
      $(".bonus_policy_id_"+policy_id+".set_"+set+" :input:not(:checkbox)").prop('disabled',false);
      $(".bonus_policy_id_"+policy_id+".set_"+prev_selected).addClass('display_none');
      $(".bonus_policy_id_"+policy_id+".set_"+prev_selected+" :input:not(:checkbox)").prop('disabled',true);
      selected_set[policy_id]=set;
    }
});
$("body").on("click",".btn_type",function(e){
    e.preventDefault();
    var type=$(this).data('type');
    var policy_id=$(this).data('policy_id');
    var prev_selected=selected_policy_type[policy_id];
    if(type!=prev_selected)
    {
      $(".btn_type[data-type='"+type+"'][data-policy_id='"+policy_id+"']").addClass('btn-primary');
      $(".btn_type[data-type='"+type+"'][data-policy_id='"+policy_id+"']").removeClass('btn-basic');

      $(".btn_type[data-type='"+prev_selected+"'][data-policy_id='"+policy_id+"']").addClass('btn-basic');
      $(".btn_type[data-type='"+prev_selected+"'][data-policy_id='"+policy_id+"']").removeClass('btn-primary');
      selected_policy_type[policy_id]=type;
      $(".min_qty:last").trigger('keyup');
    }
});
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();
</script>
<script>
$(document).ready(function ()
{
        
  $("input[type='submit']").on("click", function(){
			$("div#divLoading_default").addClass('hide');
  });
  $('body').on('click', '.delete_item', function ()
  {
    var product_box = $(this).parent().parent();
    var product_field = product_box.find("th:nth-child(2) .product_id");
    var product_rate = product_box.find("th:nth-child(4) .product_rate");
    var combined_product = product_box.find("th:nth-child(5) .combined_product");
    var product_qty = product_box.find("th:nth-child(6) .min_qty");
    combined_product = combined_product.val();
    var id = product_field.val();

    var total_value = $('.tvalue-'+id).val();
    var gross_total = $('#gross_value').val();
    var new_gross_value = parseFloat(gross_total-total_value);
    $('#gross_value').val(new_gross_value);
    $('#cash_collection').val('');
    alert('Removed this row -------');
    var min_qty = product_qty.val();
    if (product_field.val() == '')
    {
        product_box.remove();

        var last_row = $('.invoice_table>tbody tr:last').attr('id');
        $('#'+last_row+'>th>.add_more').show();
        
        total_values();
    } 
    else
    {
      product_box.remove();
      if(combined_product)
      {
        $.each(combined_product.split(','),function(index , value){
          if(value!=product_field.val())
          {
            var prate = $(".prate-" + value);
            prate.parent().parent().find("th:nth-child(5) .combined_product").val('');
            prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
          }
        });
      }
      var last_row = $('.invoice_table>tbody tr:last').attr('id');
      $('#'+last_row+'>th>.add_more').show();
      total_values();
    }
  });
  /*--------------------------------*/
  $("body").on("keyup", "#cash_collection", function () {
      var gross_value = parseFloat($("#gross_value").val());
      var collect_cash = parseFloat($(this).val());
      var credit_amount = gross_value - collect_cash;
      if(credit_amount >= 0){
          $("#credit_amount").val(credit_amount.toFixed(2));
      }else{
          $("#credit_amount").val(0);
      }
  });
});
    
function check_duplicate_products()
{

  var error_count=0;
  var product_ids = [];

  $('.product_id').removeClass("errorInput");  

  $('.product_id').each(function ()
  {
    var cur_val=this.value;
    if(product_ids.indexOf(cur_val) === -1)
    {
      product_ids.push(cur_val);
    } 
    else 
    {
      error_count++;
      $(this).addClass('errorInput');
    }
  });
  return error_count;
}

function check_duplicate_products_all()
{

  var error_count=0;
  var product_ids = [];

  $('.product_id').removeClass("errorInput");  

  $('.product_id').each(function ()
  {
    var cur_val=this.value;
    if(cur_val)
    {
      if(product_ids.indexOf(cur_val) === -1)
      {
        product_ids.push(cur_val);
      } 
      else 
      {
        error_count++;
        $(this).addClass('errorInput');
      }
    }
  });
  return error_count;
}
function check_duplicate_open_bonus_products()
{
  var error_count=0;
  var count=0;

  $('.open_bonus_product_id').removeClass("errorInput");  

  $('.open_bonus_product_id').each(function ()
  {
    count++;
    var cur_val=this.value;
    if(cur_val>0)
    {
    }
    else 
    {
      error_count++;
      $(this).addClass('errorInput'); 
    }
  });

  if(count==1)
  {
    error_count=0;
  }
  return error_count;
}
$(window).on('load', function() {
  $(".min_qty:last").trigger('keyup');
});
</script>
