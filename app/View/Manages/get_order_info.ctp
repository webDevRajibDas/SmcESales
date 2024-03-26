<?php 

?>
<div class="form-group">
    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['office_id'],'id'=>'office_id')); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('distribut_outlet_id', array('class' => 'form-control distribut_outlet_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['outlet_id'],'id'=>'distribut_outlet_id')); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('w_store_id', array('class' => 'form-control w_store_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['w_store_id'],'id'=>'w_store_id')); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['territory_id'],'id'=>'territory_id')); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('order_date', array('class' => 'form-control order_date', 'type' => 'hidden', 'value'=>$existing_record['Order']['order_date'])); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('order_no', array('class' => 'form-control order_no', 'type' => 'hidden', 'value'=>$existing_record['Order']['order_no'])); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('sales_person_id', array('class' => 'form-control sales_person_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['sales_person_id'])); ?>
</div>
<div class="table-responsive">
        <!--Set Product area-->
        <table class="table table-striped table-condensed table-bordered invoice_table">
          <thead>
            <tr>
              <th class="text-center" width="5%">ID</th>
              <th class="text-left">Product Name</th>
              <th class="text-left" width="12%">Unit</th>
              <th class="text-left" width="12%">Rate</th>
              <th class="text-left" width="12%">Order QTY</th>
              <th class="text-left" width="12%">Deliverd QTY</th>
              <th class="text-left" width="12%">ASO Stock QTY</th>
              <th class="text-left" width="12%">Value</th>
              <th class="text-left" width="12%">Discount Value</th>
              <th class="text-left" width="10%">Bonus</th>
              <th class="text-left" width="10%">Remarks</th>
              <th class="text-center" width="10%">Action</th>
            </tr>
          </thead>
        <tbody class="product_row_box product_table">
          <?php
          if(!empty($existing_record)){
            $sl = 1;
            $total_price = 0;
            $gross_val = 0;

            foreach($existing_record['OrderDetail'] as $val){
              if($val['price'] == 0.0 && $val['product_type_id'] == 1)
                continue;
              $total_price = $val['price'] * $val['sales_qty'];
              $gross_val = $gross_val + $total_price;
              ?>
              <tr id="<?php echo $sl?>" class="new_row_number">
                <th class="text-center sl_order" width="5%"><?php echo $sl?></th>
                <th class="text-center selected_product">
                  <?php
                  echo $this->Form->input('product_id',array('name'=>'data[OrderDetail][product_id][]','class'=>'form-control width_100 product_id','options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id']));
                  ?>
                  <input type="hidden" class="product_id_clone" value="<?php echo $val['product_id']; ?>" />
                  <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                  <input type="hidden" class="ajax_flag" value=1>
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
                  <input type="number" step="any" name="data[OrderDetail][sales_qty][]" class="form-control width_100 new_sales_qty" value="<?=$val['sales_qty']?>" readonly/>
                </th>
                <th class="text-center" width="12%">
                <input type="number" step="any" min="0" max="<?=$val['aso_stock_qty']?>" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty sales_qty" value="<?=(!isset($val['deliverd_qty']))?$val['sales_qty']:$val['deliverd_qty']?>" required/>
                <input type="hidden" name="data[OrderDetail][combination_id][]" class="combination_id" value="<?=$val['product_combination_id']?>" required/>
                  <input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?=$val['aso_stock_qty']?>" readonly />
                  <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?=$val['aso_stock_qty']?>"/>
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>" readonly />
                </th>
                <th class="text-center" width="12%">
                    <input type="text"   name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly value="<?=sprintf('%0.2f',$val['discount_amount']*$val['sales_qty'])?>" />
                    <input type="hidden" name="data[OrderDetail][discount_amount][]" value="<?=$val['discount_amount']?>" class="form-control width_100 discount_amount" />
                    <input type="hidden" value="<?=$val['discount_type']?>" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>
                    <input type="hidden" name="data[OrderDetail][policy_type][]" value="<?=$val['policy_type']?>" class="form-control width_100 policy_type"/>
                    <input type="hidden" name="data[OrderDetail][policy_id][]" value="<?=$val['policy_id']?>" class="form-control width_100 policy_id"/>
                    <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus"/>
                </th>
                <!-- <th class="text-center" width="10%">
                  <input type="text" id="bonus<?php echo $sl;?>" value="<?php echo ($val['bonus_qty']!=0 && $val['product_type_id']==1)?$val['bonus_qty'].'('.$product_list[$val['bonus_product_id']].')':'N.A';?>" class="form-control width_100 bonus" disabled />
                  <input type="hidden" id="bonus_product_id<?php echo $sl;?>" value="<?php echo $val['bonus_product_id'];?>" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                  <input type="hidden" id="bonus_product_qty<?php echo $sl;?>" value="<?php echo $val['bonus_qty'];?>" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                  <input type="hidden" id="bonus_measurement_unit_id<?php echo $sl;?>" name="data[OrderDetail][bonus_measurement_unit_id][]" value="<?php if(!empty($product_measurement_units[$val['bonus_product_id']])) echo $product_measurement_units[$val['bonus_product_id']];?>" class="form-control width_100 bonus_measurement_unit_id"/>
                </th> -->
                <th class="text-center" width="10%">
                  <input type="text" id="bonus<?php echo $sl;?>" value="<?php echo ($val['bonus_qty']!=0 && $val['product_type_id']==1)?$val['bonus_qty'].'('.$product_list[$val['bonus_product_id']].')':'N.A';?>" class="form-control width_100 bonus" disabled />
                  <input type="hidden" id="bonus_product_id<?php echo $sl;?>" value="<?php echo ($val['bonus_qty']!=0 && $val['product_type_id']==1)?$val['bonus_product_id']:'';?>" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                  <input type="hidden" id="bonus_product_qty<?php echo $sl;?>" value="<?php echo ($val['bonus_qty']!=0 && $val['product_type_id']==1)?$val['bonus_qty']:'';?>" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                  <input type="hidden" id="bonus_measurement_unit_id<?php echo $sl;?>" name="data[OrderDetail][bonus_measurement_unit_id][]" value="<?php if(!empty($product_measurement_units[$val['bonus_product_id']]) && ($val['bonus_qty']!=0 && $val['product_type_id']==1)) echo $product_measurement_units[$val['bonus_product_id']];?>" class="form-control width_100 bonus_measurement_unit_id"/>
                </th>
                <th class="text-center" width="10%">
                  <input type="text" name="data[OrderDetail][remarks][]" class="form-control remarks width_100"/>
                </th>
                <th class="text-center" width="10%">
                  <?php
                  echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                  echo '<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>';

                  ?>
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
              <td colspan="7" align="right"><b>Total : </b></td>
              <td align="center"><input name="data[Order][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?php echo $existing_record['Order']['gross_value']+$existing_record['Order']['total_discount']; ?>" readonly />
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
          </tr>
          <tr>
              <td colspan="7" align="right"><b>Total Discount: </b></td>
              <td align="center"><input name="data[Order][total_discount]" value="<?php echo $existing_record['Order']['total_discount']; ?>" class="form-control width_100 total_discount" type="text" id="total_discount" readonly />
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
          </tr>
          <tr>
              <td colspan="7" align="right"><b>Net Payable: </b></td>
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
                          <th class="text-center">Product Name</th>
                          <th class="text-center" width="12%">Unit</th>
                          <th class="text-center" width="12%">QTY</th>
                          <th class="text-center" width="10%">Action</th>
                        </tr>
                      </thead>
                      <tbody class="bonus_product">
                        <?php $bon=0;  foreach($existing_record['OrderDetail'] as $val){
                         // if($val['price'] > 0.0 || $val['is_bonus']==1)
                          if(($val['price'] > 0.0 && $val['is_bonus'] !=1) || $val['product_type_id'] !=1 ||$val['is_bonus'] ==3)
                            continue;
                  // echo $val['product_id'];  $open_bonus_product_option
                          ?>
                          <tr  class="bonus_row">
                            <th class="text-center" id="bonus_product_list">
                              <?php 
                              echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----','options'=>$open_bonus_product_option, 'label' => false,'default'=>$val['product_id']));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="<?=$val['measurement_unit_name']?>" disabled/>
                              <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?=$val['measurement_unit_id']?>"/>

                              <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" max="<?=$val['aso_stock_qty']?>" name="data[OrderDetail][sales_qty][]" value="<?=$val['sales_qty']?>"  step="any" class="form-control width_100 open_bonus_min_qty" />
                              <input type="hidden" class="combined_product"/>
                              <input type="hidden" name="data[OrderDetail][discount_amount][]"/>
                              <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                              <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                              <input type="hidden" name="data[OrderDetail][is_bonus][]"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              <?php if($bon!=0){?>
                                <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>
                              <?php } ?>
                            </th>
                          </tr>
                          <?php $bon++; }?>
                          <?php if($bon==0){ ?>
                            <tr  class="bonus_row">
                              <th class="text-center" id="bonus_product_list">
                                <?php 
                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----','options'=>$open_bonus_product_option, 'label' => false));
                                ?>
                                <input type="hidden" class="product_id_clone" />
                                <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
                              </th>
                              <th class="text-center" width="12%">
                                <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>
                                <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>

                                <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                              </th>
                              <th class="text-center" width="12%">
                                <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" />
                                <input type="hidden" class="combined_product"/>
                                <input type="hidden" name="data[OrderDetail][discount_amount][]"/>
                                <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                                <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                                <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                                <input type="hidden" name="data[OrderDetail][is_bonus][]"/>
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
            <td ></td>
            <td colspan="4"></td>
            <td ></td>
            <!-- <td colspan="4" align="right"><b>Credit : </b></td>
            <td align="center"><input name="data[Order][credit_amount]" class="form-control width_100" type="text" id="credit_amount" value="<?php //echo $existing_record['Order']['credit_amount']; ?>" readonly />
            </td> -->
            <td></td>
            <td></td>
            
          </tr>
        </tfoot>  
      </table>
    </div>
    <br>
    <div class="pull-right">
        <?php 
        echo $this->Form->input('save', array('name' => 'save', 'class' => 'form-control','label' => false,'type'=>'hidden','value'=>'Save'));
        echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false,)); ?>
          
        <?php  //echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
    </div>

<!-- Last Memo info: END  -->




<div id="open_bonus_product_list">
    <?php
      //echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true ,'id'=>"DistMemoProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
    <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
</div>

<div id="order_product_list">
    <?php
    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false,));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>
<script>
var selected_bonus=$.parseJSON('<?php echo json_encode($selected_bonus) ?>');
var selected_set=$.parseJSON('<?php echo json_encode($selected_set) ?>');
var selected_policy_type=$.parseJSON('<?php echo json_encode($selected_policy_type) ?>');
$(document).ready(function () {
    $('#order_product_list').hide();
    //ConfirmDialog('Are you sure');

    /*function ConfirmDialog(message) {
      $('<div></div>').appendTo('body')
        .html('<div><h6>' + message + '?</h6></div>')
        .dialog({
          modal: true,
          title: 'Delete message',
          zIndex: 10000,
          autoOpen: true,
          width: 'auto',
          resizable: false,
          buttons: {
            Yes: function() {
              // $(obj).removeAttr('onclick');                                
              // $(obj).parents('.Parent').remove();

              $('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

              $(this).dialog("close");
            },
            No: function() {
              $('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');

              $(this).dialog("close");
            }
          },
          close: function(event, ui) {
            $(this).remove();
          }
        });
    }*/
});
</script>
