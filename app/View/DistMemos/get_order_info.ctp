<?php 

$existing_record = $orders;
?>
<div class="form-group">
    <?php echo $this->Form->input('territory_id', array('class' => 'form-control territory_id', 'type' => 'hidden', 'value'=>$existing_record['DistOrder']['territory_id'])); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'type' => 'hidden', 'value'=>$existing_record['DistOrder']['distributor_id'])); ?>
</div>
<div class="form-group">
    <?php echo $this->Form->input('order_date', array('class' => 'form-control order_date', 'type' => 'hidden', 'value'=>$existing_record['DistOrder']['order_date'])); ?>
</div>


<div class="table-responsive">
    <table class="table table-striped table-condensed table-bordered invoice_table" id="main_invoice_data">
        <thead>
            <tr>
                <th class="text-center" width="5%">ID</th>
                <th class="text-center">Product Name</th>
                <th class="text-center" width="12%">Unit</th>
                <th class="text-center" width="12%">Price</th>
                <th class="text-center" width="12%">QTY</th>
                <th class="text-center" width="12%">Value</th>
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
                <th class="text-center sl_memo" width="5%"><?php echo $sl?></th>
                <th class="text-center">
                    <?php
                        echo $this->Form->input('product_id',array('name'=>'data[MemoDetail][product_id][]','class'=>'form-control width_100 product_id','required'=>TRUE,'options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id'],'id'=>"MemoProductId"));
                    ?>
                    <input type="hidden" class="product_id_clone" value="<?php echo $val['product_id']; ?>" />
                    <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                    <input type="hidden" class="ajax_flag" value="1">
                </th>
                <th class="text-center" width="12%">
                    <input type="text" name="" class="form-control width_100 product_unit_name" value="<?=$measurement_units[$val['measurement_unit_id']]?>" disabled/>
                    <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" value="<?=$val['measurement_unit_id']?>"/>
                </th>
                <th class="text-center" width="12%">
                    <input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate <?='prate-'.$val['product_id']?>" value="<?=$val['price']?>" readonly />
                    <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id" value="<?=$val['product_price_id']?>"/>
                </th>
                <th class="text-center" width="12%">
                    <input type="number" step="any" min="0" name="data[MemoDetail][sales_qty][]" class="form-control width_100 min_qty" value="<?=$val['sales_qty']?>" data-prev_value="<?=$val['sales_qty']?>" required/>
                    <input type="hidden" value="<?=$val['sales_qty']?>" class="prev_min_qty">
                    <input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
                </th>
                <th class="text-center" width="12%">
                    <input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>" readonly />
                </th>
                <th class="text-center" width="10%">
                  <input type="text" class="form-control width_100 bonus" disabled  value="<?=$val['bonus_qty']?>"/>
                  <input type="hidden" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"  value="<?=$val['bonus_product_id']?>"/>
                  <input type="hidden" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"  value="<?=$val['bonus_qty']?>"/>
                  <input type="hidden" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"  value="<?=$val['measurement_unit_id']?>"/>
              </th>
                <th class="text-center" width="10%">
                    <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                    <?php
                        if ($sl != 1) {
                            echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                        }
                    ?>
                    
                    <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
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
                <td align="center"><input name="data[DistMemo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?php echo $existing_record['DistOrder']['gross_value']; ?>" onChange='check_discount()' readonly />
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><b>Cash Collection : </b></td>
                <td align="center"><input name="data[DistMemo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" value="<?php echo $existing_record['DistOrder']['cash_recieved']; ?>" />
                </td>
                <td></td>
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

                              if($val['price'] == 0)
                                {
                              ?>
                          <tr  class="bonus_row">
                            <th class="text-center" <?php if($sl==1)?> id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true,'default'=>$val['product_id']));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value="<?php echo $product_category_id_list[$val['product_id']];?>"/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="<?=$measurement_units[$val['measurement_unit_id']]?>" disabled/>
                              <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?=$val['measurement_unit_id']?>"/>

                              <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value="<?=$val['sales_qty']?>"/>
                              <input type="hidden" class="combined_product"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>
                            </th>
                          </tr>
                          <?php 
                          $sl++;
                          }
                          else{ continue;}
                        }
                      } 
                      ?>
                      <?php if($sl==1) {?>
                          <tr  class="bonus_row">
                            <th class="text-center" id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$product_list,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="" disabled/>
                              <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value=""/>

                              <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value=""/>
                              <input type="hidden" class="combined_product"/>
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
            <td align="right"><b>Credit : </b></td>
            <td align="center"><input name="data[DistMemo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" value="<?php echo $existing_record['DistOrder']['credit_amount']; ?>" readonly />
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5" align="right"><b>Discount : </b></td>
            <td align="center"><input name="data[DistMemo][discount_percent]" class="form-control width_100 discount_percent" type="text" id="discount_percent" value="<?php echo $existing_record['DistOrder']['discount_percent']; ?>" readonly/>
            </td>
            <td></td>
          
        </tr>
        <tr>
            <td colspan="5" align="right"><b>After Discount : </b></td>
            <td align="center"><input name="data[DistMemo][discount_value]" class="form-control width_100 discount_value" type="text" id="discount_value" value="<?php echo $existing_record['DistOrder']['discount_value']; ?>" readonly />
            </td>
            <td></td>
          
        </tr>
    </tfoot>
    </table>
</div>
<div class="form-group" style="padding-top:20px;">
    <div>
        <p>
            <b>CTRL+S</b> = Memo/Market(If Panel Open)/Outlet(If Panel Open) Save, 
            <b>CTRL+L</b> = Last Memo Info Open/Close, 
            <b>CTRL+B</b> = Bonus Panel Open, 
            <b>CTRL+C</b> = Bonus Panel Close,  
        </p>
        <p>
            <b>CTRL+F</b> = Bonus Product Row Add,  
            <b>CTRL+M</b> = New Market Panel Open/Close,   
            <b>CTRL+O</b> = New Outlet Panel Open/Close,  
            <b>CTRL+P</b> = Add New Product Row,  
        </p>
    </div>
    <div class="pull-right">
        <?php //echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
        <?php  echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
    </div>
</div>
<!-- Last Memo info: END  -->
<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>



<div id="open_bonus_product_list">
    <?php
      //echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true ,'id'=>"DistMemoProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
    <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
</div>

<div id="memo_product_list">
    <?php
        echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false, 'id'=>"OrderProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>
<script>
$(document).ready(function () {
    $('#memo_product_list').hide();
    productList();
});
function productList()
    {
        var csa_id = 0;
        var distributor_id = $('#distributor_id').val();
        var order_date=$('#DistOrderOrderDate').val();

        /*if (distributor_id) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'admin/DistOrders/get_product' ?>',
                data: {'distributor_id': distributor_id, 'csa_id': csa_id},
                cache: false,
                success: function (response) {
                    var json = $.parseJSON(response);
                    $('.product_id option').remove();
                    for (var i = 0; i < json.length; ++i)
                    {
                        $('.product_id').append('<option value="' + json[i].id + '">' + json[i].name + '</option>');
                        
                    }
                }
            });
        }*/



        if (distributor_id) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'DistOrders/get_bonus_product' ?>',
                data: {'distributor_id': distributor_id,'order_date':order_date},
                cache: false,
                success: function (response) {
                    var json = $.parseJSON(response);
                    //console.log(json);
                    $('.open_bonus_product_id option').remove();
                    $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
                    for (var i = 0; i < json.length; ++i)
                    {
                        $('.open_bonus_product_id').append('<option value="' + json[i].Product.id + '">' + json[i].Product.name + '</option>');
                    }
                }
            });
        }
    }
</script>

