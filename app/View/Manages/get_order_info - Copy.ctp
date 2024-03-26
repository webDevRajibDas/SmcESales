
<div class="form-group">
    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['office_id'],'id'=>'office_id')); ?>

    <?php echo $this->Form->input('distribut_outlet_id', array('class' => 'form-control distribut_outlet_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['outlet_id'],'id'=>'distribut_outlet_id')); ?>

    <?php echo $this->Form->input('w_store_id', array('class' => 'form-control w_store_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['w_store_id'],'id'=>'w_store_id')); ?>

    <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id', 'type' => 'hidden', 'value'=>$existing_record['Order']['territory_id'],'id'=>'territory_id')); ?>

    <?php echo $this->Form->input('order_date', array('class' => 'form-control order_date', 'type' => 'hidden', 'value'=>$existing_record['Order']['order_date'])); ?>
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
            <th class="text-left" width="10%">Bonus</th>
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
              if($val['price'] == 0.0)
                continue;
              $total_price = $val['price'] * $val['sales_qty'];
              $gross_val = $gross_val + $total_price;
              ?>
              <tr id="<?php echo $sl?>" class="new_row_number">
                <th class="text-center sl_order" width="5%"><?php echo $sl?></th>
                <th class="text-center selected_product">
                 <?php
                 echo $this->Form->input('product_id',array('name'=>'data[OrderDetail][product_id][]','class'=>'form-control width_100 product_id','required'=>TRUE,'options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id']));
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
                 <input type="number" step="any" name="data[OrderDetail][sales_qty][]" class="form-control width_100" value="<?=$val['sales_qty']?>" readonly/>
               </th>
               <th class="text-center" width="12%">
                <input type="number" step="any" min="0" max="<?=$val['aso_stock_qty']?>" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty sales_qty" value="<?=$val['deliverd_qty']?>" required/>
               <input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
              </th>
                     <!-- <th class="text-center" width="12%">
                       <input type="number" step="any" min="0" name="data[OrderDetail][remaining_qty][]" class="form-control width_100 remaining_qty" <?='rqty-'.$val['product_id']?>" value="<?=$val['remaining_qty']?>" readonly/>
                     </th> -->
                     <th class="text-center" width="12%">
                        <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?=$val['aso_stock_qty']?>" readonly />
                        <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" value="<?=$val['aso_stock_qty']?>"/>
                    </th>
                     <th class="text-center" width="12%">
                       <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>" readonly />
                     </th>
                     <th class="text-center" width="10%">
                      <input type="text" id="bonus<?php echo $sl;?>" value="<?php echo $val['bonus_product_id']!=0?$val['bonus_qty'].'('.$product_list[$val['bonus_product_id']].')':'N.A';?>" class="form-control width_100 bonus" disabled />
                      <input type="hidden" id="bonus_product_id<?php echo $sl;?>" value="<?php echo $val['bonus_product_id'];?>" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                      <input type="hidden" id="bonus_product_qty<?php echo $sl;?>" value="<?php echo $val['bonus_qty'];?>" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                      <input type="hidden" id="bonus_measurement_unit_id<?php echo $sl;?>" name="data[OrderDetail][bonus_measurement_unit_id][]" value="<?php if(!empty($product_measurement_units[$val['bonus_product_id']])) echo $product_measurement_units[$val['bonus_product_id']];?>" class="form-control width_100 bonus_measurement_unit_id"/>
                    </th>
                   <th class="text-center" width="10%">
                     <!-- <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> -->
                     <?php
                   if ($sl != 1) {
                     echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                    echo '<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>';
                  }
                     
                     ?>

                  <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_order'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); 

                  ?>
               <!--  </th> -->
              </tr>
              <?php
              $sl++;
            }
          }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <td></td>
            <td></td>
            <td colspan="5" align="right"><b>Total : </b></td>
            <td align="center"><input name="data[Order][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?php echo $existing_record['Order']['gross_value']; ?>" readonly />
            </td>
            <td></td>
            <td></td>
            
          </tr>
          <!-- <tr>
            <td></td>
            <td></td>
            <td colspan="5" align="right"><b>Collection : </b></td>
            <td align="center"><input name="data[Order][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" value="<?php //echo $existing_record['Order']['cash_recieved']; ?>" required />
            </td>
            <td></td>
            
          </tr>
          <tr>
            <td></td>
            <td></td> 
            <td colspan="5" align="right"><b>Instrument Type : </b></td> 
            <td align="center" width="10%"><?php
            //echo $this->Form->input('instrument_type', array('id' => 'instrument_type', 'class' => 'form-control width_150 instrument_type', 'required' => TRUE, 'label' => false,'empty' => '--- Select ---', 'options' => $instrumentType, 'selected'=>$existing_record['Order']['instrument_type'])); 
            ?></td>
            <td></td>
            
          
          </tr>
          <tr>
            <td></td> 
            <td></td> 
            <td colspan="5" align="right"><b>Instrument Reference Number :</b></td>
            <td align="center" width="10%">
              <?php
            //echo $this->Form->input('instrument_reference_no', array('id' => 'instrument_reference_no', 'class' => 'form-control width_150 instrument_reference_no', 'label' => false,'value'=>$existing_record['Order']['instrument_reference_no'])); 
            ?>
              <input name="data[Order][instrument_reference_no]" class="form-control width_150" type="text" id="instrument_reference_no" value="<?php //echo $existing_record['Order']['instrument_reference_no']; ?>"/>
                          </td>
            <td></td>
            
          </tr> -->
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
                          if($val['price'] > 0.0 && $val['is_bonus'] !=1)
                            continue;
                  // echo $val['product_id'];  $open_bonus_product_option
                          ?>
                          <tr  class="bonus_row">
                            <th class="text-center" id="bonus_product_list">
                              <?php 
                              echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----','options'=>$product_list, 'label' => false,'default'=>$val['product_id']));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>
                              <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?=$val['measurement_unit_id']?>"/>

                              <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[OrderDetail][sales_qty][]" value="<?=$val['sales_qty']?>"  step="any" class="form-control width_100 open_bonus_min_qty" />
                              <input type="hidden" class="combined_product"/>
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
                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----','options'=>$product_list, 'label' => false));
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
            <td align="center"><input name="data[Order][credit_amount]" class="form-control width_100" type="text" id="credit_amount" value="<?php echo $existing_record['Order']['credit_amount']; ?>" readonly />
            </td> -->
            <td></td>
            <td></td>
            
          </tr>
        </tfoot>  
      </table>
    </div>
    <br>
    <div class="pull-right">
        <?php //echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
        <?php  echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
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
    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false, 'required'=>TRUE));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>
<script>
$(document).ready(function () {
    $('#order_product_list').hide();
});
</script>
