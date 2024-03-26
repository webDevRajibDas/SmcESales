<?php
$maintain_dealer_type = 1;
//pr($products);die();
/* if(array_key_exists(179, $not_in_stock_product)){
    pr($not_in_stock_product);die();
 }
*/

//echo '<pre>';print_r($selected_bonus);
//echo '<pre>';print_r($selected_set);
//echo '<pre>';print_r($selected_policy_type);
//echo '<pre>';print_r($existing_record);exit;


?>

<style>

  
  .display_none {
    display: none;
  }

  .width_150 {
    width: 100%;
  }

  .form-control {
    float: left;
    width: 50%;
    font-size: 13px;
    height: 28px;
    padding: 0px 4px;
  }

  .width_100_this {
    width: 100%;
  }

  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .open_bonus_product_id {
    width: 150px !important;
  }

  .chosen-container .chosen-results li {
    font-weight: 600 !important;
    text-align: left !important;
  }

</style>

<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Product Issue'); ?></h3>
        <div class="box-tools pull-right">
          <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
        </div>
      </div>
      <div class="box-body">

        <?php echo $this->Form->create('OrderProces', array('role' => 'form')); ?>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => TRUE, 'options' => $offices, 'selected' => $existing_record['office_id'])); ?>
              <?php //echo $this->Form->input('office_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['office_id'])); 
              ?>
            </div>
            <div class="form-group">
              <?php echo $this->Form->input('store_id', array('label' => 'Store :', 'class' => 'form-control store_id', 'value' => $existing_record['Store']['name'], 'readonly' => 'readonly', 'type' => 'text')); ?>
              <?php echo $this->Form->input('w_store_id', array('type' => 'hidden', 'class' => 'form-control w_store_id', 'value' => $existing_record['Order']['w_store_id'])); ?>
            </div>

            <div class="form-group" id="distribut_outlet_id_so">
              <?php echo $this->Form->input('distribut_outlet_id', array('label' => 'Distributor :', 'id' => 'distribut_outlet_id', 'class' => 'form-control distribut_outlet_id', 'required' => TRUE, 'options' => $distributers, 'selected' => $existing_record['outlet_id'])); ?>
              <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id', 'required' => TRUE, 'type' => 'hidden', 'id' => 'territory_id', 'value' => $existing_record['territory_id'])); ?>
            </div>
            <div class="form-group">
              <?php echo $this->Form->input('dist_balance', array('label' => 'Balance :', 'class' => 'form-control', 'required' => TRUE, 'type' => 'text', 'value' => $existing_record['current_balance'], 'readonly')); ?>
              <?php //echo $this->From->input('order_id',array('class'=>'form-control order_id','id'=>'order_id','value'=>$existing_record['Order']['id'],'type'=>'hidden'));
              ?>
            </div>
            <!-- <div class="form-group" id="territory_id_div">
            <?php //echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => TRUE, 'options'=>$territories, 'selected'=>$existing_record['territory_id'])); 
            ?>
            <?php //echo $this->Form->input('territory_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['territory_id'])); 
            ?>
            </div> -->
            <!-- <div class="form-group">
            <?php //echo $this->Form->input('entry_date', array('class' => 'form-control datepicker', 'value' => $existing_record['order_time'], 'required' => TRUE)); 
            ?>
            </div> -->
			
			
			
			 <div class="form-group">
				<?php echo $this->Form->input('driver_name', array('class' => 'form-control', 'value' => $existing_record['driver_name'])); ?>
			</div>
			

          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <?php echo $this->Form->input('order_date', array('label' => 'Requisition Date :', 'class' => 'form-control datepicker', 'type' => 'text', 'required' => TRUE,  'value' => $existing_record['order_date'], 'disabled')); ?>
              <?php echo $this->Form->input('order_date', array('class' => 'form-control datepicker', 'type' => 'text', 'required' => TRUE,  'value' => $existing_record['order_date'], 'type' => "hidden")); ?>
            </div>
            <div class="form-group">
              <?php echo $this->Form->input('order_no', array('class' => 'form-control', 'required' => TRUE, 'type' => 'hidden', 'value' => $existing_record['order_no'], 'readonly')); ?>
              <?php //echo $this->From->input('order_id',array('class'=>'form-control order_id','id'=>'order_id','value'=>$existing_record['Order']['id'],'type'=>'hidden'));
              ?>
              
              <input type="hidden" autocomplete="off" id="last_row_flag" value="0">
              
            </div>


            <div class="form-group">
              <?php echo $this->Form->input('order_reference_no', array('label' => 'Remarks :', 'class' => 'form-control order_reference_no', 'value' => $existing_record['order_reference_no'], 'maxlength' => '15', 'required' => false, 'type' => 'text')); ?>
            </div>
			
			<div class="form-group">
				<?php echo $this->Form->input('truck_no', array('class' => 'form-control', 'value' => $existing_record['truck_no'])); ?>
			</div>
			
          </div>
		
		  
        </div>

        <div class="table-responsive">
          <!--Set Product area-->
          <table class="table table-striped table-condensed table-bordered invoice_table">
            <thead>
              <tr>
                <th class="text-center" width="5%">ID</th>
                <th class="text-left">Product Name</th>
                <th class="text-left" width="12%">Unit</th>
                <th class="text-left" width="10%">Rate</th>
                <th class="text-left" width="12%">Order QTY</th>
                <th class="text-left" width="12%">Deliverd QTY</th>
                <th class="text-left" width="12%">Batch</th>
                <th class="text-left" width="12%">ASO Stock QTY</th>
                <th class="text-left" width="12%">Value</th>
                <th class="text-left" width="12%">Discount Value</th>
                <th class="text-left" width="8%">Bonus</th>
                <th class="text-left" width="10%">Remarks</th>
                <th class="text-center" width="10%">Action</th>
              </tr>
            </thead>
            <tbody class="product_row_box product_table">
              <?php
              if (!empty($existing_record)) {
                $sl = 1;
                $total_price = 0;
                $gross_val = 0;
                
                foreach ($existing_record['OrderDetail'] as $val) {
                  if ($val['price'] == 0.0 && $val['product_type_id'] == 1)
                    continue;
                  $total_price = $val['price'] * $val['sales_qty'];
                  $gross_val = $gross_val + $total_price;


                  $orderqty = (!isset($val['deliverd_qty'])) ? $val['sales_qty'] : $val['deliverd_qty'];
				  
				  if($val['virtual_product_id']){
						$val['product_id'] = $val['virtual_product_id'];
					}
                 
              ?>
                  <tr id="<?php echo $sl ?>" class="new_row_number">
                    <th class="text-center sl_order" width="5%"><?php echo $sl ?></th>
                    <th class="text-center selected_product">
                      <?php
                      echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 product_id chosen', 'required' => TRUE, 'options' => $products, 'empty' => '---- Select Product ----', 'label' => false, 'default' => ($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id'])));
                      ?>
                      <input type="hidden" class="product_id_clone" value="<?php echo ($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id']); ?>" />
                      <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id" value="<?php echo $product_category_id_list[($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id'])]; ?>" />
                      <input type="hidden" class="ajax_flag" value=1>
                      
                    </th>
                    <th class="text-center" width="12%">
                      <input type="text" name="" class="form-control width_100 product_unit_name" value="<?= $val['measurement_unit_name'] ?>" disabled />
                      <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" value="<?= $val['measurement_unit_id'] ?>" />
                    </th>
                    <th class="text-center" width="12%">
                      <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate <?= 'prate-' . ($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id']) ?>" value="<?= $val['price'] ?>" readonly />
                      <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id" value="<?= $val['product_price_id'] ?>" />
                    </th>
                    <th class="text-center" width="12%">
                      <input type="number" step="any" name="data[OrderDetail][sales_qty][]" class="form-control width_100" value="<?= $val['sales_qty'] ?>" readonly />
                    </th>
                    <th class="text-center" width="12%">
                      <input type="number" step="any" min="0" max="<?= $val['aso_stock_qty'] ?>" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty sales_qty" step="any" value="<?= (!isset($val['deliverd_qty'])) ? $val['sales_qty'] : $val['deliverd_qty'] ?>" required />
                      <input type="hidden" name="data[OrderDetail][combination_id][]" class="combination_id" value="<?= $val['product_combination_id'] ?>" required />
                      <input type="hidden" class="combined_product" value="<?php if (isset($val['combined_product'])) {
                                                                              echo $val['combined_product'];
                                                                            } ?>" />
                    </th>

                    <th>
						<input type="hidden" class="order_details_p_id" value="<?= $val['id'] ?>">
                        <!-- product batch selectiong -->
                          
                        <button type="button" id="button_disable_id_<?php echo $sl ?>" data-backdrop="static" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal<?php echo $sl ?>">Select</button>
                        
                        <div class="modal fade" id="myModal<?php echo $sl ?>" role="dialog">
                            <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close modalclose<?php echo $sl ?>" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"> Product Issue Batch Assign  (<b class="batchproductname"></b>) </h4>
                              </div>
                              <div class="modal-body">
                                <style>
                                  .batch-table{
                                    width: 100%;
                                    border-collapse: collapse;
                                  }
                                  .batch-table tr th,td{
                                    padding: 10px 7px;
                                  }
                                </style>
                                <p>Given Qty and Deliverd when match then close option show . Deliverd Stcok : <b class="deliverdstock"></b></p>
                                <input type="hidden"  class="givenstock" value="0">
                                
                                
                                
                                  <table class="batch-table" border="1">
                                      <tr>
                                        <th>#</th>
                                        <th>Batch</th>
                                        <th>Expire Date</th>
                                        <th>Aval. Stock</th>
                                        <th>Given Stock</th>
                                      </tr>
                                      <tfoot class="productBatch"></tfoot>
                                  </table>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default modalclose<?php echo $sl ?>" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                                  
                        <script>
                            $(document).ready(function() {
                              get_product_batch_list("<?= $val['id'] ?>","<?= $orderqty ?>", "<?= $val['product_id'];?>", "<?=$existing_record['Order']['office_id'];?>", "<?php echo $sl ?>");
                            });
                        </script>
                      

                        <!--- end --->
                    </th>

                    <th class="text-center" width="12%">
                      <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?= $val['aso_stock_qty'] ?>" readonly />
                      <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?= $val['aso_stock_qty'] ?>" />
                    </th>
                    <th class="text-center" width="12%">
                      <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value <?= 'tvalue-' . ($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id']) ?>" value="<?= $total_price ?>" readonly />
                    </th>
                    <th class="text-center" width="12%">
                      <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly value="<?= sprintf('%0.2f', $val['discount_amount'] * $val['sales_qty']) ?>" />
                      <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="<?= $val['discount_amount'] ?>" />
                      <input type="hidden" value="<?= $val['discount_type'] ?>" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" />
                      <input type="hidden" name="data[OrderDetail][policy_type][]" value="<?= $val['policy_type'] ?>" class="form-control width_100 policy_type" />
                      <input type="hidden" name="data[OrderDetail][policy_id][]" value="<?= $val['policy_id'] ?>" class="form-control width_100 policy_id" />
                      <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" />
                    </th>
                    <th class="text-center" width="10%">
                      <input type="text" id="bonus<?php echo $sl; ?>" value="<?php echo ($val['bonus_qty'] != 0 && $val['product_type_id'] == 1) ? $val['bonus_qty'] . '(' . $product_list[$val['bonus_product_id']] . ')' : 'N.A'; ?>" class="form-control width_100 bonus" disabled />
                      <input type="hidden" id="bonus_product_id<?php echo $sl; ?>" value="<?php echo ($val['bonus_qty'] != 0 && $val['product_type_id'] == 1) ? $val['bonus_product_id'] : ''; ?>" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id" />
                      <input type="hidden" id="bonus_product_qty<?php echo $sl; ?>" value="<?php echo ($val['bonus_qty'] != 0 && $val['product_type_id'] == 1) ? $val['bonus_qty'] : ''; ?>" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty" />
                      <input type="hidden" id="bonus_measurement_unit_id<?php echo $sl; ?>" name="data[OrderDetail][bonus_measurement_unit_id][]" value="<?php if (!empty($product_measurement_units[$val['bonus_product_id']]) && ($val['bonus_qty'] != 0 && $val['product_type_id'] == 1)) echo $product_measurement_units[$val['bonus_product_id']]; ?>" class="form-control width_100 bonus_measurement_unit_id" />
                    </th>
                    <th class="text-center" width="10%">
                      <input type="text" name="data[OrderDetail][remarks][]" class="form-control remarks width_100" value="<?= $val['challan_remarks'] ?>" />
                    </th>
                    <th class="text-center" width="10%">
                      <!-- <a class="btn btn-1primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> -->
                      <?php
                      echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                      echo '<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>';
                      ?>
                  </tr>
              <?php
                  $sl++;
                }
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="8" align="right"><b>Total : </b></td>
                <td align="center"><input name="data[Order][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?php echo $existing_record['Order']['gross_value'] + $existing_record['Order']['total_discount']; ?>" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="8" align="right"><b>Total Discount: </b></td>
                <td align="center"><input name="data[Order][total_discount]" value="<?php echo $existing_record['Order']['total_discount']; ?>" class="form-control width_100 total_discount" type="text" id="total_discount" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="8" align="right"><b>Net Payable: </b></td>
                <td align="center"><input name="data[Order][net_payable]" class="form-control width_100 net_payable" type="text" id="net_payable" value="<?php echo $existing_record['Order']['gross_value']; ?>" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td> <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
                  <div id="bonus_product" class="modal fade" role="dialog">>

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
                                <th class="text-center" width="25%">Product Name</th>
                                <th class="text-center" width="12%">Unit</th>
                                <th class="text-center" width="22%">QTY</th>
                                <th class="text-center" width="10%">Action</th>
                              </tr>
                            </thead>
                            <tbody class="bonus_product">
                              <?php $bon = 0;
                             
                              foreach ($existing_record['OrderDetail'] as $val) {
                                // if($val['price'] > 0.0 || $val['is_bonus']==1)
                                if (($val['price'] > 0.0 && $val['is_bonus'] != 1) || $val['product_type_id'] != 1 || $val['is_bonus'] == 3)
                                  continue;
                                // echo $val['product_id'];  $open_bonus_product_option
                              ?>
                                <tr class="bonus_row">
                                  <th class="text-center" id="bonus_product_list">
                                    <?php
                                    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'options' => $open_bonus_product_option, 'label' => false, 'default' => ($val['virtual_product_id'] ? $val['virtual_product_id'] : $val['product_id'])));
                                    ?>
                                    <input type="hidden" class="product_id_clone" />
                                    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" />
                                  </th>
                                  <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="<?= $val['measurement_unit_name'] ?>" disabled />
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?= $val['measurement_unit_id'] ?>" />

                                    <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value="" />
                                  </th>
                                  <th class="text-center" width="12%">
                                    <input type="number" min="0" name="data[OrderDetail][sales_qty][]" value="<?= $val['sales_qty'] ?>" step="any" class="form-control width_100 open_bonus_min_qty" />
                                    <input type="hidden" class="combined_product" />
                                    <input type="hidden" name="data[OrderDetail][discount_amount][]" />
                                    <input type="hidden" name="data[OrderDetail][disccount_type][]" />
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" />
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" />
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" />
                                  </th>
                                  <th class="text-center" width="10%">
                                    <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                    <?php if ($bon != 0) { ?>
                                      <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>
                                    <?php } ?>
                                  </th>
                                </tr>
                              <?php $bon++;
                              } ?>
                              <?php if ($bon == 0) { ?>
                                <tr class="bonus_row">
                                  <th class="text-center" id="bonus_product_list">
                                    <?php
                                    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'options' => $open_bonus_product_option, 'label' => false));
                                    ?>
                                    <input type="hidden" class="product_id_clone" />
                                    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" />
                                  </th>
                                  <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled />
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" />

                                    <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value="" />
                                  </th>
                                  <th class="text-center" width="12%">
                                    <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" />
                                    <input type="hidden" class="combined_product" />
                                    <input type="hidden" name="data[OrderDetail][discount_amount][]" />
                                    <input type="hidden" name="data[OrderDetail][disccount_type][]" />
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" />
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" />
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" />
                                  </th>
                                  <th class="text-center" width="10%">
                                    <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>
                                  </th>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
                <td></td>
                <td colspan="4"></td>
                <td></td>
                <td></td>
                <td></td>

              </tr>
            </tfoot>
          </table>
        </div>
        

        <div id="bonusproductinforamiton">
          <input type="hidden" id="product_order_id" value="<?=$existing_record['Order']['id'];?>">
          <div id="bonus_product_assing_modal"></div>
        </div>

        <?php //echo $this->Form->submit('Submit', array('class' => 'submit btn btn-large btn-primary')); 
        // if($canPermitted==1){
        ?>

        <div class="form-group can_permit" style="padding-top:20px;">
          <div class="pull-right">
            <?php if ($existing_record['Order']['confirm_status'] == 1) { ?>
              <?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save stock_check_draft', 'div' => false, 'name' => 'save')); ?>
            <?php } ?>
            <?php echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft stock_check_draft',  'div' => false, 'name' => 'draft')); ?>
          </div>
        </div>
        <?php //} 
        ?>
        <?php echo $this->Form->end(); ?>

      </div>
    </div>
  </div>

</div>

<div id="order_product_list">
  <?php
  echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 product_id chosen', 'options' => $products, 'empty' => '---- Select ----', 'label' => false, 'required' => TRUE));
  ?>
  <input type="hidden" class="product_id_clone" />
</div>


<style>
  .bonus {
    width: 130px !important;
  }

  .product_unit_name {
    width: 80px !important;
  }

  .product_id {
    width: 150px !important;
  }

  #loading {
    position: absolute;
    width: auto;
    height: auto;
    text-align: center;
    top: 45%;
    left: 50%;
    display: none;
    z-index: 999;
  }

  #loading img {
    display: inline-block;
    height: 100px;
    width: auto;
  }
</style>

<div class="modal" id="myModal" data-backdrop="static" data-keyboard="false"></div>
<div id="loading">
  <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
  $(document).ready(function() {

    $('.product_id.chosen').chosen();

    $('#myModal').modal('show');
    $('#loading').show();
    $('.company_id').selectChain({
      target: $('.office_id'),
      value: 'name',
      url: '<?= BASE_URL . 'admin/territories/get_office_list' ?>',
      type: 'post',
      data: {
        'company_id': 'company_id'
      }
    });
    $('.office_id').selectChain({
      target: $('.territory_id'),
      value: 'name',
      url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
      type: 'post',
      data: {
        'office_id': 'office_id'
      }
    });

    $('.territory_id').selectChain({
      target: $('.market_id'),
      value: 'name',
      url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
      type: 'post',
      data: {
        'territory_id': 'territory_id'
      }
    });

    /*$('.distribut_outlet_id').selectChain({
        target: $('.product_id'),
        value: 'name',
        url: '<?= BASE_URL . 'admin/Orders/get_product'; ?>',
        type: 'post',
        data: {'office_id': 'office_id','outlet_id': 'distribut_outlet_id'}
    });*/
    $('.office_id').selectChain({

      target: $('.distribut_outlet_id'),
      value: 'name',
      url: '<?= BASE_URL . 'sales_people/get_outlet_list_with_distributor_name'; ?>',
      type: 'post',
      data: {
        'office_id': 'office_id'
      }



    });

    /*  $('.market_id').selectChain({
          target: $('.outlet_id'),
          value: 'name',
          url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
          type: 'post',
          data: {'market_id': 'market_id'}
      });*/

    $('.market_id').selectChain({
      target: $('.outlet_id'),
      value: 'name',
      url: '<?= BASE_URL . 'sales_people/get_outlet_list'; ?>',
      type: 'post',
      data: {
        'market_id': 'market_id'
      }
    });
    $('.distribut_outlet_id').selectChain({
      target: $('.territory_id'),
      value: 'name',
      url: '<?= BASE_URL . 'sales_people/get_territory_list_distributor' ?>',
      type: 'post',
      data: {
        'distribut_outlet_id': 'distribut_outlet_id'
      }
    });

    $('.office_id').change(function() {
      $('.market_id').html('<option value="">---- Select Market ----');
      $('.distribut_outlet_id').html('<option value="">---- Select Dealer ----');
      $('.outlet_id').html('<option value="">---- Select Outlet ----');
    });

    /* $('.territory_id').change(function () {
            $('.distribut_outlet_id').html('<option value="">---- Select Outlet ----');
        });
*/
    /* temporary commented */
    /*
      $("body").on("change", "#sales_person_id", function () {
          var sales_person_id = $(this).val();
          $.ajax({
              url: '<?= BASE_URL . 'Orders/get_territory_id' ?>',
              type: 'POST',
              data: {sales_person_id: sales_person_id},
              success: function (response) {
                  var obj = jQuery.parseJSON(response);
                  if (obj.territory_id != null) {
                      $("#territory_id").val(obj.territory_id);
                  } else {
                      alert('Territory Id not be Null');
                      return false;
                  }

              }

          });

      });
      */

      $(".stock_check_draft").click(function(){

        var validation = 0;
        $('.sales_qty').each(function(key, value) {

          var deli_qty = $(this).val();
        
          deli_qty = parseFloat(deli_qty).toFixed(2);
		  
          var seril_number =  $(this).parent().parent().attr('id');
		  
            var batch_qty = 0;
            $('#myModal'+seril_number+ ' .given_stock_qty').each(function(key, value) {
                var batch_val =$(this).val();
				batch_qty +=  parseFloat(batch_val);
			 
            });
			
			 var total_batch_qty = parseFloat(batch_qty).toFixed(2);
		
            if( total_batch_qty < deli_qty || total_batch_qty > deli_qty ){
                alert('--Please Select Batch---');
                
                validation = 1;
				        return false;
            }
			
			

        });
	

        var max_qty_bonus = 0;
        var bonus_deli_qty = 0;
		
		
		
        $('.policy_min_qty:not(:disabled)').each(function(key, value) {

          max_qty_bonus = $(this).attr('max'); 

          var deli_qty = $(this).val();
        
          deli_qty = parseFloat(deli_qty).toFixed(2);
		  
          
          if(deli_qty > 0.0){

            var seril_number_key =  $(this).attr('id').split('_');

            var serial = seril_number_key[2];
            var key = seril_number_key[3];
            
            var batch_qty = 0;
              $('#bonusbatchModal'+serial+'_'+key+ ' .given_stock_qty').each(function(key, value) {
                  //batch_qty =  parseInt(batch_qty) + parseInt($(this).val());
                  //batch_qty +=  parseFloat($(this).val());
                  var batch_val =$(this).val();
                  batch_qty +=  parseFloat(batch_val);

              });
			  
              var total_batch_qty = parseFloat(batch_qty).toFixed(2);

              if( total_batch_qty < deli_qty || total_batch_qty > deli_qty ){
                  alert('--Please Bonus Product Select Batch---');
                  validation = 1;
				        return false;
                  
              }

          }
		  

        });

     

        if(validation == 1){
		      $("#divLoading_default").removeClass('show');
          return false;
        }
        
        
      });
      

  });
</script>

<script>
  function total_values() {
    var t = 0;
    $('.total_value').each(function() {
      if ($(this).val() != '') {
        t += parseFloat($(this).val());
      }
    });
    $('#gross_value').val(t);
  }

  $(document).ready(function() {
    $('#order_product_list').hide();
    //$('.add_more').hide();
    var last_row_number = $('.invoice_table tbody tr:last').attr('id');
    $('#' + last_row_number + '>th>.add_more').show();

    $("body").on("click", ".add_more", function() {

      $("#last_row_flag").val(1);
	  dist_bonus_call_flag = 0;
      var row_value = $("#last_row_flag").val();
      //alert(row_value);

      var sl = parseInt($('.invoice_table>tbody>tr:last').attr('id')) + 1;

      var all_product_list = $('#order_product_list .product_id').html();
      //var product_list = $('#order_product_list').html();
      var product_box = $(this).parent().parent().parent();
      var current_row_no = $(this).parent().parent().attr('id');

      var product_list = '<div class="input select"><select id="product_id" required="required" class="form-control width_100 product_id chosen" name="data[OrderDetail][product_id][]">'+ all_product_list +'</select></div><input type="hidden" class="product_id_clone">';

      var current_row =
        '<th class="text-center sl_order" width="5%"></th>\
        <th class="text-center">' + product_list + '\
          <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/><input type="hidden" class="ajax_flag" value=0>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
          <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
          <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" class="form-control width_100 new_sales_qty" readonly />\
          <input type="hidden" name="data[OrderDetail][sales_qty][]" class="form-control width_100 new_sales_qty"/>\
        </th>\
        <th>\
          <input type="number" min="0" step="any" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty deliverd_qty sales_qty" required/>\
          <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
          <input type="hidden" class="combined_product"/>\
        </th>\
        <th>\
			<input type="hidden" class="order_details_p_id" value="0">\
          <button type="button" disabled id="button_disable_id_'+sl+'" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal'+sl+'">Select</button>\
          <div class="modal fade" id="myModal'+sl+'" role="dialog"><div class="modal-dialog">\
                  <div class="modal-content">\
                    <div class="modal-header">\
                      <button type="button" class="close modalclose'+sl+'" data-dismiss="modal">&times;</button>\
                      <h4 class="modal-title"> Product Issue Batch Assign (<b class="batchproductname"></b>) </h4>\
                    </div>\
                    <div class="modal-body">\
                      <style>\
                        .batch-table{width: 100%;border-collapse: collapse;}\
                        .batch-table tr th,td{padding: 10px 7px;}\
                      </style>\
                      <p>Given Qty and Deliverd when match then close option show . Deliverd Stcok : <b class="deliverdstock"></b></p>\
                      <input type="hidden"  class="givenstock" value="0">\
                        <table class="batch-table" border="1">\
                            <tr><th>#</th><th>Batch</th><th>Expire Date</th><th>Aval. Stock</th><th>Given Stock</th></tr>\
                            <!--tr><th colspan="2">Product </th><th>Panther Dotted</th><th>Total Delivary</th><th>21</th></tr-->\
                            <tfoot class="productBatch"></tfoot>\
                        </table>\
                    </div>\
                    <div class="modal-footer">\
                      <button type="button" class="btn btn-default modalclose'+sl+'" data-dismiss="modal">Close</button>\
                    </div>\
                  </div>\
                </div>\
              </div>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly />\
          <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
        </th>\
        <th class="text-center" width="12%">\
            <input type="text"   name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
            <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
            <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
            <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>\
            <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>\
            <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus"/>\
        </th>\
        <th class="text-center" width="10%">\
          <input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
          <input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
          <input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
          <input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
        </th>\
        <th class="text-center" width="10%">\
          <input type="text" name="data[OrderDetail][remarks][]" class="form-control remarks width_100"/>\
        </th>\
        <th class="text-center" width="10%">\
          <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>\
          <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>\
        </th>';

      var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
      if (valid_row != '') {
        product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
        $('#' + sl + '>.sl_order').text(sl);

        $('#'+sl+ ' select.product_id.chosen').chosen('destroy');
        $('#'+sl+ ' select.product_id.chosen').chosen();
        $('#'+sl+ ' .product_id.chosen').trigger("chosen:updated");

        $('#cash_collection').val('');
        $(this).hide();
      } else {
        alert('Please fill up this row!');
      }

    });

    $("body").on("change", ".product_id", function() {

      var new_product = 1;
      $('#myModal').modal('show');
      $('#loading').show();
      //console.log($('#'+sl+'>th>.bonus').val());
      $('#gross_value').val(0);
      /*----- make array with product list -------*/
      var sl = $('.invoice_table>tbody>tr').length;

      var current_row_no = $(this).parent().parent().parent().attr('id');

      if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
        $('#' + current_row_no + '>th>.bonus').val('N.A');
        $('#' + current_row_no + '>th>.bonus_product_id').val(0);
        $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
        $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
      }

      var product_change_flag = 1;
      var product_id_list_array = new Array();
      var product_id_list = '';
      $('.product_row_box .product_id').each(function() {

        //alert($(this).val());

        if ($(this).val() != '') {
          //product_id_list = $(this).val()+','+product_id_list;
          if (product_id_list_array.indexOf($(this).val()) == -1) {
            product_id_list_array.push($(this).val());
            product_id_list = $(this).val() + ',' + product_id_list;
          } else {
            alert("This poduct already exists");
            product_change_flag = 0;
            $('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
            if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
              $(this).val('').attr('selected', true);
              $('#' + current_row_no + '>th>.bonus').val('');

            }
            total_values();
			
			console.log(current_row_no);
			
			$('#' + current_row_no +' .product_id').val('');
			$('#'+current_row_no+ ' .product_id.chosen').trigger("chosen:updated");

            new_product = 0;
            $('#myModal').modal('hide');
            $('#loading').hide();
            // checkInventory(); /*edited by Ibrahim 01-12-2019*/
            return false;
          }

        } else {
          pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

          if (pro_val) {
            alert("Please select any product from last row or remove it!");
          } else {
            alert("Please select any product!");
          }

          product_change_flag = 0;
          $('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
          if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
            $(this).val('').attr('selected', true);
            $('#' + current_row_no + '>th>.bonus').val('');
          }
          total_values();

          new_product = 0;
          $('#myModal').modal('hide');
          $('#loading').hide();
          return false;
        }
      });

      var product_id = $(this).val();
      var product_box = $(this).parent().parent().parent();
      var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
      var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
      var product_rate = product_box.find("th:nth-child(4) .product_rate");
      var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
      var product_qty = product_box.find("th:nth-child(6) .min_qty");
      var remaining_qty = product_box.find("th:nth-child(7) .remaining_qty");
      var total_val = product_box.find("th:nth-child(9) .total_value");
      var combined_product = product_box.find("th:nth-child(6) .combined_product");
      var combined_product_change = combined_product.val();


      var rate_class = product_rate.attr('class').split(' ').pop();
      var value_class = total_val.attr('class').split(' ').pop();

      //console.log(rate_class);

      if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1) {
        product_rate.removeClass(rate_class);
        total_val.removeClass(value_class);
        /*-----------*/
        product_rate.addClass('prate-' + product_id);
        total_val.addClass('tvalue-' + product_id);
      } else {
        product_rate.addClass('prate-' + product_id);
        total_val.addClass('tvalue-' + product_id);
      }
      var outlet_id = $('.distribut_outlet_id').val();
      var territory_id = $('.territory_id').val();
      $.ajax({
        url: '<?= BASE_URL . 'manages/get_product_unit' ?>',
        'type': 'POST',
        data: {
          product_id: product_id,
          territory_id: territory_id,
          outlet_id: outlet_id
        },
        success: function(result) {
          var obj = jQuery.parseJSON(result);
          product_unit.val(obj.product_unit.name);
          product_unit_id.val(obj.product_unit.id);
          var total_qty = obj.total_qty;
          var total_dist_qty = obj.total_dist_qty;

          product_qty.val('');
          product_box.find("th:nth-child(10) input").val('');
          product_box.find("th:nth-child(8) input").val('');
          $('#' + current_row_no + '>th>.aso_stock_qty').val(total_qty);
          $('#' + current_row_no + '>th>.dist_stock_qty').val(total_dist_qty);
          $('#' + current_row_no + '>th>.min_qty').attr('max', total_qty);
          $('#' + current_row_no + '>th>.product_rate').val('0.00');
          $('.add_more').removeClass('disabled');
          $('#loading').hide();
          $('#myModal').modal('hide');
        }
      });
    });

    /*------- unset session -------*/
  });
</script>
<script>
  function checkInventory() {
    var arrData = [];
    var w_store_id = $('.w_store_id').val();
    $(".product_table tr").each(function() {
      var currentRow = $(this);
      var product_id_value = currentRow.find(".product_id option:selected").val();
      var sales_qty = currentRow.find(".sales_qty").val();
      var obj = {};
      obj.product_id = product_id_value;
      obj.sales_qty = sales_qty;
      arrData.push(obj);
    });

    //console.log(arrData);
    $.ajax({
      url: '<?= BASE_URL . 'manages/get_inventory_product_list' ?>',
      'type': 'POST',
      data: {
        products: arrData,
        store_id: w_store_id
      },
      success: function(result) {
        var obj = jQuery.parseJSON(result);
        var canPermitted = obj['canPermitted'];
        var msg = obj['msg'];
        //console.log(obj);
        if (msg != "") {
          alert(msg);
        }
        //obj.Product.forEach(){}
        //for(var pro in obj){
        // console.log(obj.pro);
        /*$(".product_table tr").each(function(){
          var currentRow=$(this);
          var produc_id=currentRow.find(".product_id option:selected").val();

          if(produc_id==obj['Product']['id']){
              currentRow.find(".min_qty").val(0);
              currentRow.find(".min_qty").attr('readonly', true);
              currentRow.find(".min_qty").css('outline', '2px solid red');
          }
        });*/
        // }
        /*if(canPermitted==0)
        {
          $(".can_permit").hide();
        }
        else
        {
          $(".can_permit").show(); 
        }*/
      }
    });
  }
</script>
<script>
  /*--------- check combined or individual product price --------*/
  var selected_bonus = $.parseJSON('<?php echo json_encode($selected_bonus) ?>');
  var selected_set = $.parseJSON('<?php echo json_encode($selected_set) ?>');
  var selected_policy_type = $.parseJSON('<?php echo json_encode($selected_policy_type) ?>');
  var select_pre_batch = {};
  var other_policy_info = [];
  
  var dist_bonus_call_flag = 0;
  
  $("body").on("keyup", ".min_qty", function() {
    var current_row_no = $(this).parent().parent().attr('id');
    var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();
	
    var product_wise_qty = {};
	var product_wise_qty_value = {};
	
    $('.product_row_box .product_id').each(function(index, value) {
      var producct_box_each = $(this).parent().parent().parent();
      if (producct_box_each.find("th:nth-child(6) .min_qty").val()) {
        product_wise_qty[$(this).val()] = producct_box_each.find("th:nth-child(6) .min_qty").val();
      }
    });
    pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();
    var sl = $('.invoice_table>tbody>tr').length;
    var product_box = $(this).parent().parent();
    var product_field = product_box.find("th:nth-child(2) .product_id");

    var product_rate = product_box.find("th:nth-child(4) .product_rate");
    var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
    var product_qty = product_box.find("th:nth-child(6) .min_qty");
    var total_val = product_box.find("th:nth-child(9) .total_value");
    var combined_product_obj = product_box.find("th:nth-child(6) .combined_product");
    var combined_product_id_obj = product_box.find("th:nth-child(6) .combination_id");
    var combined_product = combined_product_obj.val();
    var min_qty = product_qty.val();
    var id = product_field.val();

    var product_rate_discount = {};
		var product_price_id_discount = {};
	
	var order_details_p_ids = $('#' + current_row_no + '>th>.order_details_p_id').val();

    var order_date = $("#OrderProcesOrderDate").val();
    delay(function() {
      $('#myModal').modal('show');
      $('#loading').show();

      if (min_qty == '' || min_qty == 0) {
        min_qty = 1;
        $('#' + current_row_no + '>th>.min_qty').val(1);
      }
      /*-----------------------------------*/

      /*-----------------------------------*/
      $.ajax({
        url: '<?= BASE_URL . 'manages/get_remarks' ?>',
        'type': 'POST',
        data: {
          min_qty: min_qty,
          product_id: id
        },
        success: function(result) {
          var obj = jQuery.parseJSON(result);
          $('#' + current_row_no + '>th>.remarks').val(obj.remarks);
        }
      });
	  
	  //--------------product batch selection ------\\
	  
	  
      var check_last_row = $("#last_row_flag").val();
      var office_id = $("#office_id").val();
	  
       
      if(check_last_row > 0){
		  
        get_product_batch_list(order_details_p_ids, min_qty, id, office_id, current_row_no);
      }
      

     

      //------------end-------------\\
	  

      $.ajax({
        url: '<?= BASE_URL . 'manages/get_product_price' ?>',
        'type': 'POST',
        data: {
          combined_product: combined_product,
          min_qty: min_qty,
          product_id: id,
          order_date: order_date,
          cart_product: product_wise_qty
        },
        async: false,
        success: function(result) {
          var obj = jQuery.parseJSON(result);

          if (obj.price != '') {
            product_rate.val(obj.price);
          }
          if (obj.price_id != '') {
            product_price_id.val(obj.price_id);
          }
          if (obj.total_value) {
            total_val.val(obj.total_value);
          }
          combined_product_obj.val(obj.combine_product);
          if (obj.combination != undefined) {
            combined_product_id_obj.val(obj.combination_id);
            $.each(obj.combination, function(index, value) {
              var prate = $(".prate-" + value.product_id);
              var tvalue = $(".tvalue-" + value.product_id);
              prate.val(value.price);
              tvalue.val(value.total_value);
              prate.next('.product_price_id').val(value.price_id);
              prate.parent().parent().find("th:nth-child(6) .combined_product").val(obj.combine_product);
              prate.parent().parent().find("th:nth-child(6) .combination_id").val(obj.combination_id);
            });
          }

          if (obj.recall_product_for_price != undefined) {
			 
            $.each(obj.recall_product_for_price, function(index, value) {
				
				dist_bonus_call_flag = 1;
				
			  var prate = $(".prate-" + value);
              var tvalue = $(".tvalue-" + value);
              prate.parent().parent().find("th:nth-child(6) .combined_product").val(obj.combine_product);
              prate.parent().parent().find("th:nth-child(6) .combination_id").val('');
              prate.parent().parent().find("th:nth-child(6) .min_qty").trigger('keyup');
            });
          }

          var gross_total = 0;
          $('.total_value').each(function() {
            if ($(this).val() != '') {

              var producct_box_each = $(this).parent().parent();
							var product_id = producct_box_each.find("th:nth-child(2) div .product_id").val();
              product_wise_qty_value[product_id] = $(this).val();
			        gross_total = parseFloat(gross_total) + parseFloat($(this).val());

              product_rate_discount[product_id] = producct_box_each.find("th:nth-child(4)  .product_rate").val();
							product_price_id_discount[product_id] = producct_box_each.find("th:nth-child(4)  .product_price_id").val();
						
			  
            }
          });
          if ($("#gross_value").val(gross_total.toFixed(2))) {
            $('.n_bonus_row').remove();
            $('.discount_value').val(0.00);
            $('.discount_amount').val(0.00);
            get_policy_data();
          }

          if (obj.mother_product_quantity != undefined) {
            var mother_product_quantity = obj.mother_product_quantity;
            var bonus_product_id = obj.bonus_product_id;
            var bonus_product_name = obj.bonus_product_name;
            var bonus_product_quantity = obj.bonus_product_quantity;
            var sales_measurement_unit_id = obj.sales_measurement_unit_id;
            var no_of_bonus_slap = mother_product_quantity.length;
            var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;
            console.log("no_of_bonus_slap :" + no_of_bonus_slap);
            for (var i = 0; i < no_of_bonus_slap; i++) {
              if (parseFloat($('#' + current_row_no + '>th>.min_qty').val()) >= parseFloat(mother_product_quantity[i].min) && parseFloat($('#' + current_row_no + '>th>.min_qty').val()) <= parseFloat(mother_product_quantity[i].max))

              {
                if (i == 0) {
                  $('#' + current_row_no + '>th>.bonus').val('N.A');
                  $('#' + current_row_no + '>th>.bonus_product_id').val(0);
                  $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
                  $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
                } else {
                  $('#' + current_row_no + '>th>.bonus').val(bonus_product_quantity[i + (-1)] + '(' + bonus_product_name[i + (-1)] + ')');
                  $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i + (-1)]);
                  $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_product_quantity[i + (-1)]);
                  $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i + (-1)]);
                }
                break;
              } else {
                var current_qty = parseFloat($('#' + current_row_no + '>th>.min_qty').val());

                var bonus_qty = Math.floor(current_qty / parseFloat(mother_product_quantity_bonus)) * bonus_product_quantity[i];
                $('#' + current_row_no + '>th>.bonus').val(bonus_qty + ' (' + bonus_product_name[i] + ')');
                $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i]);
                $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_qty);
                $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
              }
            }
          } else {
            $('#' + current_row_no + '>th>.bonus').val('N.A');
            $('#' + current_row_no + '>th>.bonus_product_id').val(0);
            $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
            $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
          }
          $('#cash_collection').val('');
          $('#loading').hide();
          $('#myModal').modal('hide');
          $('.add_more').removeClass('disabled');
        }
      });
	  
	  
      function get_policy_data() {
        $.ajax({
          url: '<?= BASE_URL . 'manages/get_product_policy' ?>',
          'type': 'POST',
          data: {
			office_id: $("#office_id").val(),
            min_qty: min_qty,
            product_id: id,
            product_rate_discount: product_rate_discount,
			product_price_id_discount: product_price_id_discount,
            order_date: order_date,
            cart_product: product_wise_qty,
			cart_product_value: product_wise_qty_value,
            memo_total: $("#gross_value").val(),
            selected_bonus: JSON.stringify(selected_bonus),
            selected_set: JSON.stringify(selected_set),
            selected_policy_type: JSON.stringify(selected_policy_type),
            other_policy_info: JSON.stringify(other_policy_info),
          },
          async: false,
          success: function(result) {
            var response = $.parseJSON(result);
			
			
			$("#last_row_flag").val(1);
			
			//console.log($("#last_row_flag").val() + '---call----fff' + dist_bonus_call_flag);
			
            if (response.discount) {
              var discount = response.discount;
              var total_discount = response.total_discount;
              $.each(discount, function(ind, val) {
                $.each(val, function(ind1, val1) {
                  var prate = $(".prate-" + val1.product_id);
                  var tvalue = $(".tvalue-" + val1.product_id);
                  prate.val(val1.price);
                  tvalue.val(val1.total_value);
                  prate.next('.product_price_id').val(val1.price_id);
                  prate.parent().parent().find("th:nth-child(10) .discount_value").val(val1.total_discount_value);
                  prate.parent().parent().find("th:nth-child(10) .discount_amount").val(val1.discount_amount);
                  prate.parent().parent().find("th:nth-child(10) .disccount_type").val(val1.discount_type);
                  prate.parent().parent().find("th:nth-child(10) .policy_type").val(val1.policy_type);
                  prate.parent().parent().find("th:nth-child(10) .policy_id").val(val1.policy_id);
                  prate.parent().parent().find("th:nth-child(10) .is_bonus").val('0');
				  
                });
              });
              $('.total_discount').val(total_discount.toFixed(2));
              gross_value = $('#gross_value').val();
              net_payable = (gross_value) - (total_discount);
              $('.net_payable').val(net_payable.toFixed(2));
            }
            if (response.bonus_html) {
              var b_html = response.bonus_html;
              selected_bonus = response.selected_bonus;
              selected_set = response.selected_set;
              selected_policy_type = response.selected_policy_type;
              other_policy_info = response.other_policy_info;
              $('.bonus_product').append(b_html);
				
				dist_bonus_call_flag = 0
              var bonus_office_is =  $("#office_id").val();
              bonus_product_batch_selection(bonus_office_is);
			  
			  
              
			  //console.log(response.bonus_html);


            }
          }
        });
      }

    }, 1000);
  });
  $("body").on("click", ".is_bonus_checked", function() {
    if ($(this).prop('checked')) {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly', false);
      $(this).parent().prev().find('.policy_min_qty').prop('required', true);
      $(this).parent().prev().find('.policy_min_qty').attr('min', 1);
      $(this).parent().prev().find('.remove_select_batch').removeAttr('disabled');
    } else {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly', true);
      $(this).parent().prev().find('.policy_min_qty').prop('required', false);
      $(this).parent().prev().find('.policy_min_qty').attr('min', 0);
      $(this).parent().prev().find('.policy_min_qty').val(0.00);
      $(this).parent().prev().find('.remove_select_batch').attr('disabled', 'disabled');
    }
  });


  $("body").on("keyup", ".policy_min_qty", function() {
    var class_list = $(this).attr('class');
    class_list = class_list.split(" ");
    var policy_set_class = class_list[2];
    var max_qty = parseFloat($(this).attr('max'));
    var total_provide_qty = 0;
    $("." + policy_set_class).not(this).each(function(ind, val) {
      total_provide_qty += parseFloat($(this).val());
    });
    var given_qty = parseFloat($(this).val());
    var max_provide_qty = max_qty - total_provide_qty;
    if (given_qty > max_provide_qty) {
      $(this).val(max_provide_qty);
    }
    var set = $(this).data('set');
    var policy_id = $(this).parent().prev().find('.policy_id').val();
    // selected_bonus[policy_id]=0;
    $("." + policy_set_class).each(function(ind, val) {
      var product_id = $(this).parent().prev().prev().find('.policy_bonus_product_id').val();
      selected_bonus[policy_id][set][product_id] = $(this).val();
    });
  });
  $("body").on("click", ".btn_set", function(e) {
    
	e.preventDefault();
    var set = $(this).data('set');
    var policy_id = $(this).data('policy_id');
    var prev_selected = selected_set[policy_id];
    
    if (set != prev_selected) {
      $(".btn_set[data-set='" + set + "'][data-policy_id='" + policy_id + "']").addClass('btn-success');
      $(".btn_set[data-set='" + set + "'][data-policy_id='" + policy_id + "']").removeClass('btn-default');

      $(".btn_set[data-set='" + prev_selected + "'][data-policy_id='" + policy_id + "']").addClass('btn-default');
      $(".btn_set[data-set='" + prev_selected + "'][data-policy_id='" + policy_id + "']").removeClass('btn-success');

      $(".bonus_policy_id_" + policy_id + ".set_" + set).removeClass('display_none');
      $(".bonus_policy_id_" + policy_id + ".set_" + set + " :input:not(:checkbox)").prop('disabled', false);
      $(".bonus_policy_id_" + policy_id + ".set_" + prev_selected).addClass('display_none');
      $(".bonus_policy_id_" + policy_id + ".set_" + prev_selected + " :input:not(:checkbox)").prop('disabled', true);
      selected_set[policy_id] = set;
	  
	  /* ------ batch modal input field disabled false ---- */
	  $(".bonus_policy_id_" + policy_id + ".set_" + set).each(function(index,val){
			var prod_id_for_modal=$(this).find('.policy_bonus_product_id').val();
			$('.b_modal-'+policy_id+'-'+prod_id_for_modal+' :input').prop('disabled', false);
	   });
	   /* ------ batch modal input field disabled ---- */
	   $(".bonus_policy_id_" + policy_id + ".set_" + prev_selected).each(function(index,val){
		   var prod_id_for_modal=$(this).find('.policy_bonus_product_id').val();
		   $('.b_modal-'+policy_id+'-'+prod_id_for_modal+' :input').prop('disabled', true);
	   });
	   
    }

  });
  $("body").on("click", ".btn_type", function(e) {
    e.preventDefault();
	
	$('#last_row_flag').val(0);
	
    var type = $(this).data('type');
    var policy_id = $(this).data('policy_id');
    var prev_selected = selected_policy_type[policy_id];
    if (type != prev_selected) {
      $(".btn_type[data-type='" + type + "'][data-policy_id='" + policy_id + "']").addClass('btn-primary');
      $(".btn_type[data-type='" + type + "'][data-policy_id='" + policy_id + "']").removeClass('btn-basic');

      $(".btn_type[data-type='" + prev_selected + "'][data-policy_id='" + policy_id + "']").addClass('btn-basic');
      $(".btn_type[data-type='" + prev_selected + "'][data-policy_id='" + policy_id + "']").removeClass('btn-primary');
      selected_policy_type[policy_id] = type;
      $(".min_qty:last").trigger('keyup');
    }
  });

  var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();
</script>

<script>
  $(document).ready(function() {
    $('body').on('click', '.delete_item', function() {
      
      $("#last_row_flag").val(0);
	   dist_bonus_call_flag = 1;

      var product_box = $(this).parent().parent();
      var product_field = product_box.find("th:nth-child(2) .product_id");
      var product_rate = product_box.find("th:nth-child(4) .product_rate");
      var combined_product = product_box.find("th:nth-child(6) .combined_product");
      var product_qty = product_box.find("th:nth-child(6) .min_qty");
      combined_product = combined_product.val();
      var id = product_field.val();

      var total_value = $('.tvalue-' + id).val();
      var gross_total = $('#gross_value').val();
      var new_gross_value = parseFloat(gross_total - total_value);
      $('#gross_value').val(new_gross_value);
      $('#cash_collection').val('');
      alert('Removed this row -------');
      var min_qty = product_qty.val();
      if (product_field.val() == '') {
        product_box.remove();

        var last_row = $('.invoice_table>tbody tr:last').attr('id');
        $('#' + last_row + '>th>.add_more').show();
        
        total_values();
      } else {
        product_box.remove();
        var policy_product = other_policy_info.policy_product;
        if (combined_product) {
          $.each(combined_product.split(','), function(index, value) {
            if (value != product_field.val()) {
              delete policy_product[value];
              var prate = $(".prate-" + value);
              prate.parent().parent().find("th:nth-child(6) .combined_product").val('');
              prate.parent().parent().find("th:nth-child(6) .min_qty").trigger('keyup');
            }
          });
        }
        for (key in policy_product) {
          var prate = $(".prate-" + key);
          prate.parent().parent().find("th:nth-child(6) .min_qty").trigger('keyup');
        }
        var last_row = $('.invoice_table>tbody tr:last').attr('id');
        $('#' + last_row + '>th>.add_more').show();
       
        total_values();
      }

    });
    /*--------------------------------*/
    $("body").on("keyup", "#cash_collection", function() {
      var gross_value = parseFloat($("#gross_value").val());
      var collect_cash = parseFloat($(this).val());
      var credit_amount = gross_value - collect_cash;
      if (credit_amount >= 0) {
        $("#credit_amount").val(credit_amount.toFixed(2));
      } else {
        $("#credit_amount").val(0);
      }
    });
  });

  /*For Adding Bonus Product list : START*/
  $(document).ready(function() {
    $("body").on("click", ".bonus_add_more", function() {
      var product_list = $('#bonus_product_list').html();
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
                <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required />\
                <input type="hidden" class="combined_product"/>\
                <input type="hidden" name="data[OrderDetail][discount_amount][]"/>\
                <input type="hidden" name="data[OrderDetail][disccount_type][]"/>\
                <input type="hidden" name="data[OrderDetail][policy_type][]"/>\
                <input type="hidden" name="data[OrderDetail][policy_id][]"/>\
                <input type="hidden" name="data[OrderDetail][is_bonus][]"/>\
            </th>\
            <th class="text-center" width="10%">\
                <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
                <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
            </th>\
        </tr>\
        ';
      var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
      if (product_id) {
        $(this).hide();
        $(".bonus_product").append(product_bonus_row);
        $(this).parent().parent().next().find('.open_bonus_product_id').val('');
      } else {
        alert('plese select product first');
      }
    });
    $("body").on("click", ".bonus_remove", function() {
      $(this).parent().parent().remove();
      // var total_tr= $(".bonus_row").length;
      $(".bonus_row").last().find('.bonus_add_more').show();
    });
    $("body").on("change", ".open_bonus_product_id", function() {
      var product_id = $(this).val();
      var product_box = $(this).parent().parent().parent();
      var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
      var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
      var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
      var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
      var territory_id = $('.territory_id').val();
      var office_id = $('#office_id').val();
      $.ajax({
        url: '<?= BASE_URL . 'manages/get_bonus_product_details' ?>',
        'type': 'POST',
        data: {
          product_id: product_id,
          territory_id: territory_id,
          office_id: office_id
        },
        success: function(result) {
          var data = $.parseJSON(result);

          product_category_id.val(data.category_id);
          product_unit_name.val(data.measurement_unit_name);
          product_unit_id.val(data.measurement_unit_id);
          product_qty.val(1);
          product_qty.attr('min', 0.1);
          product_qty.attr('max', data.total_qty);
        },
        error: function(error) {
          product_category_id.val();
          product_unit_name.val();
          product_unit_id.val();
          product_qty.val(0);
        }
      });
    });
    $("body").on("change", "#territory_id", function() {
      var territory_id = $('#territory_id').val();

      //   var sale_type_id = $("#sale_type_id option:selected").val(); 

      //    if(sale_type_id==3){
      //    territory_id = $("#spo_territory_id option:selected").val(); 
      //   }

      //alert(territory_id);

      if (territory_id) {
        $.ajax({
          type: "POST",
          url: '<?= BASE_URL . 'manages/get_bonus_product' ?>',
          data: 'territory_id=' + territory_id,
          cache: false,
          success: function(response) {
            var json = $.parseJSON(response);
            //console.log(json);
            $('.open_bonus_product_id option').remove();
            $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
            for (var i = 0; i < json.length; ++i) {
              $('.open_bonus_product_id').append('<option value="' + json[i].Product.id + '">' + json[i].Product.name + '</option>');
            }
          }
        });
      }
    })
  });
  /*For Adding Bonus Product list : START*/
</script>
<script>
  $("body").on("keyup", ".deliverd_qty", function() {
    var product_box = $(this).parent().parent();
    var product_qty = product_box.find("th:nth-child(6) .min_qty");
    var qty = product_qty.val();
    var remaining_qty_obj = product_box.find("th:nth-child(5) .new_sales_qty");

    remaining_qty_obj.val(qty);

  });
  /*$("body").on("keyup", ".min_qty", function () {
       var product_box = $(this).parent().parent();
       var product_field = product_box.find("th:nth-child(2) .product_id");
       var product_qty = product_box.find("th:nth-child(6) .min_qty");
       var remaining_qty_obj = product_box.find("th:nth-child(7) .remaining_qty");
       var min_qty = product_qty.val();
       var remaining_qty = remaining_qty_obj.val();
       var id = product_field.val();
       var order_id= <?php //echo $existing_record['Order']['id'];
                      ?>;
       var qty = remaining_qty;
       console.log(order_id);
        $.ajax({
           type: "POST",
           url: '<?= BASE_URL . 'manages/get_remaining_quantity' ?>',
            data: {product_id: id, order_id: order_id},
           cache: false, 
           success: function(response){
               var json = $.parseJSON(response);
              // console.log(json);
              if(json['remaining_qty']==null){
                 
                 remaining_qty = json['sales_qty'] - min_qty;
               }
               else{
                 qty = remaining_qty;
                 remaining_qty = json['remaining_qty'] - min_qty;
               }
              if(remaining_qty < 0){
               alert("Please Give Remaining Quantity");
               product_qty.val(0);
               remaining_qty_obj.val(qty);
              }
              else{
                remaining_qty_obj.val(remaining_qty);
              }
               
           }
       }); 

  });*/
</script>
<script>
  function checkInventoryByProduct(id) {
    var w_store_id = $('.w_store_id').val();
    $.ajax({
      url: '<?= BASE_URL . 'manages/get_inventory_by_product_id' ?>',
      'type': 'POST',
      data: {
        id: id,
        store_id: w_store_id
      },
      success: function(result) {
        var obj = jQuery.parseJSON(result);
        var canPermitted = obj['canPermitted'];
        var msg = obj['msg'];
        //console.log(obj);
        if (msg != "") {
          alert(msg);
        }

      }
    });
  }



function stock_validataion(id){

  $('.modalclose'+id).hide();
  
  delay(function() {

	  var total_given_stock = $('#'+id+ ' .givenstock').val();
	  var total = 0;

	  $('#'+id+ ' .given_stock_qty').each(function(i, obj) {
		var max_value = $(this).attr('max');
		//var givenstock = parseInt($(this).val());
		var givenstock = parseFloat($(this).val());
	   
		if(givenstock > max_value){
			//givenstock = parseInt(max_value);
			givenstock = parseFloat(max_value);
			$(this).val(max_value);
		}

		  total += givenstock;    
	  });
	  
	  total = parseFloat(total).toFixed(2);
	  
	  total_given_stock = parseFloat(total_given_stock).toFixed(2);
	  
	  //console.log( total +'---------'+ total_given_stock);

	  if(total > total_given_stock){
		  alert( ' Delivired qty not match. ' );
	  }

	  if(total == total_given_stock || total==0){
		$('.modalclose'+id).show();
	  }
  
  }, 1000);

}

function checkbox_check(ciid, slid){

  if ( $('#ciid_'+ ciid).is(":checked") ) {
    $("#given_stock_id_"+ciid).removeAttr("disabled");
  }
  else {
    $("#given_stock_id_"+ciid).val(0);
    $("#given_stock_id_"+ciid).attr("disabled","disabled");
    stock_validataion(slid);
  }

}

var product_serial_number = 0;

function get_product_batch_list(order_details_id, min_qty, product_id, office_id, current_row_no){
    

    product_serial_number = current_row_no;
	
	var product_order_id =  $("#product_order_id").val();
    
    //console.log('---start row -----'+product_serial_number);

    $('#' + current_row_no + ' .deliverdstock').text(min_qty);
    var product_unit_id = $('#' + current_row_no + ' .product_unit_id').val();
   
    $.ajax({
      url: '<?= BASE_URL . 'Manages/get_product_batch_list' ?>',
      'type': 'POST',
      data: {
        order_details_id: order_details_id,
        min_qty: min_qty,
        product_id: product_id,
        office_id: office_id,
        current_row_no: current_row_no,
        product_unit_id: product_unit_id,
        product_order_id: product_order_id,
        
      },
      success: function(result) {
        var obj = jQuery.parseJSON(result);
        $('#button_disable_id_' + current_row_no).removeAttr("disabled");
        $('#button_disable_id_' + current_row_no).removeAttr("style");
        if(obj.disable == 1){
          $('#button_disable_id_' + current_row_no).attr("disabled","disabled");
          $('#button_disable_id_' + current_row_no).attr("style","display:none;");
        }

        var productname = $('#' + current_row_no + ' .product_id option:selected').text(); 
        $('#' + current_row_no + ' .batchproductname').text(productname);

        $('#' + current_row_no + ' .givenstock').val(min_qty);
        $('#' + current_row_no + ' .productBatch').empty();
        $('#' + current_row_no + ' .productBatch').append(obj.html);
      }

    });

  }

  


  function bonus_product_batch_selection(office_id){
	  
	var product_serial_number = $('.invoice_table .new_row_number:last').attr('id');
	
	//console.log('-------sal last row----'+product_serial_number);
	
    var product_order_id =  $("#product_order_id").val();
	
    //---------------bonus modal------------\\

    //$('#bonusproductinforamiton #bonus_product_assing_modal').empty();

    var m=1;
    var html = '';
    $(".b-modal-for-rm").addClass('remove');
    $('.multi_batch_modal').each(function(key, value) {

        var batch_product_id = $(this).attr('value');

        var b_min_qty =  $(this).prev().val();
        var policy_id = $(this).attr('data-policy');

        //console.log(b_min_qty);

        var id_serial_number = parseInt(product_serial_number)+m;
        // console.log(id_serial_number+'--------');
        var onclic_fun = "assign_bonus_product_batch("+batch_product_id+","+key+","+id_serial_number+")";
        var href_modal = "#bonusbatchModal"+batch_product_id+'_'+key;
        var inpur_id = "bproductid_qty_"+batch_product_id+'_'+key;
        var id_add_button = "button_disable_id_"+id_serial_number;
        $(this).attr('onclick', onclic_fun);
        $(this).attr('id', id_add_button);
       // $(this).attr('href', href_modal);
        $(this).prev().attr('id', inpur_id);

        m++;
        if(typeof select_pre_batch[policy_id] =='undefined'){
            select_pre_batch[policy_id]=new Array();
          }

          if(typeof select_pre_batch[policy_id][batch_product_id] =='undefined'){
              
              select_pre_batch[policy_id][batch_product_id] = 0;
          }
          //console.log(select_pre_batch[policy_id][batch_product_id]+'before modal creation if');
          if(select_pre_batch[policy_id][batch_product_id]==0 || select_pre_batch[policy_id][batch_product_id]!=b_min_qty){
			  
			  
          
            //console.log(select_pre_batch[policy_id][batch_product_id]+'after modal createion if');
            $('.b_modal-'+policy_id+'-'+batch_product_id).remove();
            html+= 
            '\
            <div class="modal fade b-modal-for-rm b_modal-'+policy_id+'-'+batch_product_id+'" data-backdrop="static" id="bonusbatchModal'+batch_product_id+'_'+key+'" role="dialog">\
                <div class="modal-dialog">\
                    <div class="modal-content">\
                        <div class="modal-header">\
                        <!--button type="button" class="close" data-dismiss="modal">&times;</button-->\
                        <h4 class="modal-title"> Bonus Product Issue Batch Assign (<b class="bonusbatchproductname"></b>) </h4>\
                        </div>\
                        <div class="modal-body">\
                        <style>.batch-table{width: 100%;border-collapse: collapse;}.batch-table tr th,td{padding: 10px 7px;}</style>\
                        <p>Given Qty and Deliverd when match then close option show . Deliverd Stcok : <b class="deliverdstock"></b></p>\
                        <input type="hidden"  class="givenstock" value="0">\
                            <table id="bonusbatchtable'+batch_product_id+'" class="batch-table" border="1">\
                                <tr><th>#</th><th>Batch</th><th>Expire Date</th><th>Aval. Stock</th><th>Given Stock</th></tr>\
                                <tfoot class="bonusproductBatch"></tfoot>\
                            </table>\
                        </div>\
                        <div class="modal-footer">\
                            <button type="button" class="btn btn-default bonusmodalclose" data-dismiss="modal">Close</button>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            ';
      }
      else{
        $('.b_modal-'+policy_id+'-'+batch_product_id).attr('id','bonusbatchModal'+batch_product_id+'_'+key);
        $('.b_modal-'+policy_id+'-'+batch_product_id).removeClass('remove');
        //console.log(select_pre_batch[policy_id][batch_product_id]+'after modal createion else');
      }
    });
    $('#bonusproductinforamiton #bonus_product_assing_modal').append(html);
    $(".b-modal-for-rm.remove").remove();
  
    //---------------bonus modal end------------\\

    //$('#bonusproductinforamiton .bonus_product_id_batch').each(function(key, value) {
      var n=1;
    $('.policy_bonus_product_id').each(function(key, value) {

        var product_id = $(this).val();

        var productname = $(this).text(); 
		
		

        //var b_min_qty = $(this).parent().parent().next().find('.policy_min_qty').val();

        var b_min_qty =  parseFloat($("#bproductid_qty_"+product_id+ '_'+key).val());

        //console.log('-----'+b_min_qty);

        //if(b_min_qty > 0){
			//console.log(' serial --------' +product_serial_number);
          var p_serial_number = parseInt(product_serial_number)+n;

          var policy_id = $("#bproductid_qty_"+product_id+ '_'+key).next().attr('data-policy');

          var bonus_product_unit_id = $("#bonus_product_unit_id_"+policy_id+ '_'+product_id).val();
          
          //--------------array gerate-------------\\

        
          /* if(typeof select_pre_batch[policy_id] =='undefined'){
            select_pre_batch[policy_id]=new Array();
          }

          if(typeof select_pre_batch[policy_id][product_id] =='undefined'){
              
              select_pre_batch[policy_id][product_id] = 0;
          } */
          //console.log(select_pre_batch[policy_id][product_id]+'-----before 2 if');
		  
          //console.log('-----------' + b_min_qty);
		  
          if(select_pre_batch[policy_id][product_id]==0 || select_pre_batch[policy_id][product_id]!=b_min_qty){
            select_pre_batch[policy_id][product_id] = b_min_qty;
          
          //console.log(select_pre_batch[policy_id][product_id]+'after  2 if');
          //console.log('----------'+ policy_id + '-----' + product_id);
          
          //----------------end---------------\\
          

          var min_qty = b_min_qty;
          var current_row_no = key;
          
          $.ajax({
              url: '<?= BASE_URL . 'Manages/get_bonus_product_batch_list' ?>',
              'type': 'POST',
              data: {
                policy_id: policy_id,
                min_qty: min_qty,
                product_id: product_id,
                office_id: office_id,
                current_row_no: current_row_no,
                product_order_id: product_order_id,
                row_number: p_serial_number,
                bonus_product_unit_id: bonus_product_unit_id,
               
              },
              success: function(result) {
                var obj = jQuery.parseJSON(result);

                if(obj.disable == 1){
                  //console.log(p_serial_number);
                 // $('#button_disable_id_' + p_serial_number).removeAttr("data-toggle");
                  $('#button_disable_id_' + p_serial_number).attr("disabled","disabled");
                }

                
                $('#bonusbatchModal' + product_id + '_'+key+ ' .bonusbatchproductname').text(productname);
                
                //$('#' + current_row_no + ' .givenstock').val(min_qty);
                $('#bonusbatchModal' + product_id + '_'+key+ ' .bonusproductBatch').empty();
                $('#bonusbatchModal' + product_id + '_'+key+ ' .bonusproductBatch').append(obj.html);

              }
          }); 
        }else{
          $("#bonusbatchModal"+product_id+ '_'+key).find('.given_stock_qty').attr('name','data[OrderDetail][product_given_stock]['+p_serial_number+'][]');
          $("#bonusbatchModal"+product_id+ '_'+key).find("[type='checkbox']").attr('name','data[OrderDetail][product_current_inventory_id]['+p_serial_number+'][]');;
        }
          n++;

        //}

      
    });
    
	
	
	btn_set_disable();
	
	
    
  }

  function assign_bonus_product_batch(product_id, serial, p_serial_number){

    var product_order_id =  $("#product_order_id").val();
    var office_id =  $("#office_id").val();
    var min_qty = $("#bproductid_qty_"+product_id+ '_'+serial).val();

    $('#bonusbatchModal' + product_id + '_'+serial+ ' .deliverdstock').text(min_qty);


    if(min_qty > 0){

      $('#bonusbatchModal' + product_id + '_'+serial).modal();

      //-------------------exitng data check-----------------\\

      var total = 0;
      $('#bonusbatchModal' + product_id + '_'+serial+ ' .given_stock_qty').each(function(i, obj) {
          //var givenstock = parseInt($(this).val());
          var givenstock = parseFloat($(this).val());
          total += givenstock;    
      });

      //console.log(min_qty + '------bonus qty----'+total);
      

      if(min_qty == total){
        $('#bonusbatchModal' + product_id + '_'+serial+ ' .givenstock').val(min_qty);
        return false;
      }


      //---------------------end-----------------------------\\

      var current_row_no = serial;
      var policy_id = $("#bproductid_qty_"+product_id+ '_'+serial).next().attr('data-policy');

      var bonus_product_unit_id = $("#bonus_product_unit_id_"+policy_id+ '_'+product_id).val();
      select_pre_batch[policy_id][product_id] = min_qty;
      $.ajax({
          url: '<?= BASE_URL . 'Manages/get_bonus_product_batch_list' ?>',
          'type': 'POST',
          data: {
            policy_id: policy_id,
            min_qty: min_qty,
            product_id: product_id,
            office_id: office_id,
            current_row_no: current_row_no,
            product_order_id: product_order_id,
            row_number: p_serial_number,
            bonus_product_unit_id: bonus_product_unit_id,
          },
          success: function(result) {

            var obj = jQuery.parseJSON(result);
            
            $('#bonusbatchModal' + product_id + '_'+serial+ ' .givenstock').val(min_qty);
            $('#bonusbatchModal' + product_id + '_'+serial+ ' .bonusproductBatch').empty();
            $('#bonusbatchModal' + product_id + '_'+serial+ ' .bonusproductBatch').append(obj.html);

          }
      });


    }else{
      alert('Pleease qty can not empty!.');
      //$('#bonusbatchModal' + product_id + '_'+serial).hide();
      //$('#bonusbatchModal' + product_id + '_'+serial).modal('toggle');
    }
    
    
  }

  function bonus_checkbox_check(ciid, pid, serial){

    if ( $( '#bonusbatchModal' + pid + '_'+serial+ ' #bonus_ciid_'+ ciid).is(":checked") ) {
        $('#bonusbatchModal' + pid + '_'+serial+ " #bonus_given_stock_id_"+ciid).removeAttr("disabled");
    }
    else {
      $('#bonusbatchModal' + pid + '_'+serial+ " #bonus_given_stock_id_"+ciid).val(0);
      $('#bonusbatchModal' + pid + '_'+serial+ " #bonus_given_stock_id_"+ciid).attr("disabled","disabled");
       bonus_stock_validataion(pid, serial);
    }

  }

  function bonus_stock_validataion(pid, serial){

    $( '#bonusbatchModal' + pid + '_'+serial+ " .bonusmodalclose").hide();
	
	delay(function() {

		var total_given_stock = $('#bonusbatchModal' + pid + '_'+serial+  ' .givenstock').val();
		var total = 0;

		$('#bonusbatchModal' + pid + '_'+serial+ ' .given_stock_qty').each(function(i, obj) {
		  //console.log('------');

			var max_value = $(this).attr('max');

			//var givenstock = parseInt($(this).val());

			var givenstock = parseFloat($(this).val());
		  
			if(givenstock > max_value){
			   // givenstock = parseInt(max_value);
				givenstock = parseFloat(max_value);
				$(this).val(max_value);
			}
			total += givenstock;    
		});
		
		total = parseFloat(total).toFixed(2);
		
		total_given_stock = parseFloat(total_given_stock).toFixed(2);
		
		//console.log( total + ' --------'+ total_given_stock);
		
		if(total > total_given_stock){
			alert( ' Delivired qty not match. ' );
		}
		

		if(total == total_given_stock || total == 0){
		  $( '#bonusbatchModal' + pid + '_'+serial+ " .bonusmodalclose").show();
		}
	
	}, 1000);

  }
  
  function btn_set_disable(){
	 // console.log('button--');
	setTimeout(function() { 
			
			$(".btn.btn-default.btn_set").each(function(index,val){
				
				var set = $(this).attr('data-set');
				var policy_id = $(this).attr('data-policy_id');
				
				var set = $('.btn.btn-default.btn_set').attr('data-set');
				var policy_id = $('.btn.btn-default.btn_set').attr('data-policy_id');
				$(".bonus_policy_id_" + policy_id + ".set_" + set).each(function(index,val){
					var prod_id_for_modal=$(this).find('.policy_bonus_product_id').val();
						$('.b_modal-'+policy_id+'-'+prod_id_for_modal+ ' .bonusproductBatch').find('.disable_class').removeAttr('checked');
					    $('.b_modal-'+policy_id+'-'+prod_id_for_modal+' :input').prop('disabled', true);
						
				});
				
			});
			
			/*
		  var set = $('.btn.btn-default.btn_set').attr('data-set');
		  var policy_id = $('.btn.btn-default.btn_set').attr('data-policy_id');
		   $(".bonus_policy_id_" + policy_id + ".set_" + set).each(function(index,val){
			   var prod_id_for_modal=$(this).find('.policy_bonus_product_id').val();
			   $('.b_modal-'+policy_id+'-'+prod_id_for_modal+ ' .bonusproductBatch').find('.disable_class').removeAttr('checked');
			   $('.b_modal-'+policy_id+'-'+prod_id_for_modal+' :input').prop('disabled', true);
				
		   });
		   */
		
		
		
    }, 2000);
	 
	 
  }

  $("body").on('click',"button[data-dismiss-modal=modal2]",function () {
    $('.modal222').modal('hide');
  });

  //$('.modal222').modal({backdrop: 'static', keyboard: false})

  $(window).on('load', function() {
    $(".min_qty:last").trigger('keyup');
    
  });
  $("form").submit(function() {
    $('.submit_btn').hide();
  });

  $('form').on('focus', 'input[type=number]', function (e) {
    $(this).on('wheel.disableScroll', function (e) {
      e.preventDefault()
    })
  });
  $('form').on('blur', 'input[type=number]', function (e) {
    $(this).off('wheel.disableScroll')
  });


</script>