<?php
//$this->Session->read('Office')['group_id'];
 $maintain_dealer_type=1;

 //pr($territories);die();
?>

<style>
    .width_100{
       width:100%;
   }
    .width_150{
       width:100%;
   }
   input[type=number]::-webkit-inner-spin-button, 
   input[type=number]::-webkit-outer-spin-button { 
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
/*#bonus_product
{
    position: relative;
    bottom: 181px;
    left: 320px;
    width: 626px;
}*/
</style>
      <div class="box-body">
                <div class="form-group">
                   <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id','empty' => '---- Select Office ----'));
                   }
                   else
                   {
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','required' => TRUE, 'empty' => '---- Select Office ----','options' => $offices));
                   }
                   ?>
               </div>
                
          <?php if($maintain_dealer_type==1){?>
               <?php if($office_parent_id==0){ ?>
                <div class="form-group"  id="distribut_id_so">
                    <?php echo $this->Form->input('distribut_outlet_id', array('label' => 'Distributor :', 'id' => 'distribut_outlet_id', 'onChange' => 'rowUpdate(1);','class' => 'form-control distribut_outlet_id', 'required' => TRUE, 'empty' => '---- Select Distributor ----')); ?>
                </div>
                 <?php } else {?>
                  <div class="form-group"  id="distribut_id_so">
                    <?php echo $this->Form->input('distribut_outlet_id', array('label' => 'Distributor :', 'id' => 'distribut_outlet_id','onChange' => 'rowUpdate(1);', 'class' => 'form-control distribut_outlet_id', 'required' => TRUE, 'empty' => '---- Select Distributor ----' , 'options' => $distributers)); ?>
                </div>
                  <?php } ?>

              <?php }?> 

               <?php if($territories != 0){?>   
              <?php echo $this->Form->input('territory_id', array('type'=>'hidden','id' => 'territory_id', 'class' => 'form-control territory_id', 'value'=> $territories)); ?>
              <?php }else{?>
                <?php echo $this->Form->input('territory_id', array('type'=>'hidden','id' => 'territory_id', 'class' => 'form-control territory_id')); ?>
              <?php }?>
                
                <div class="form-group">
                    <?php echo $this->Form->input('order_date', array('label'=>'Requisition Date :','class' => 'form-control datepicker', 'value'=>$current_date, 'type' => 'text', 'required'=>TRUE)); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('order_no', array('label'=>'Requisition No :','class' => 'form-control order_no', 'required'=>TRUE, 'type' => 'text', 'value'=>$generate_order_no, 'readonly')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('order_reference_no', array('label'=>'Remarks :','class' => 'form-control order_reference_no', 'maxlength' => '15', 'required'=>false,'type' => 'text')); ?>
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
                        <tbody class="product_row_box">
                            <tr id="1" class="new_row_number">
                                <th class="text-center sl_memo" width="5%">1</th>
                                <th class="text-center" id="memo_product_list">
                                    <?php
                                    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 product_id', 'empty' => '---- Select Product ----', 'label' => false, 'required' => true));
                                    ?>
                                    <input type="hidden" class="product_id_clone" />
                                    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly />
                                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly />
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty" readonly />
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" required />
                                    <input type="hidden" name="data[OrderDetail][combination_id][]" class="combination_id" value="" required/>
                                    <input type="hidden" class="combined_product"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly />
                                </th>
                                <th class="text-center" width="12%">
                                  <input type="text"   name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly value="" />
                                  <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" />
                                  <input type="hidden" value="" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>
                                  <input type="hidden" name="data[OrderDetail][policy_type][]" value="" class="form-control width_100 policy_type"/>
                                  <input type="hidden" name="data[OrderDetail][policy_id][]" value="" class="form-control width_100 policy_id"/>
                                  <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus"/>
                              </th>
                                <th class="text-center" width="10%">
                                    <input type="text" class="form-control width_100 bonus" disabled />
                                    <input type="hidden" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                                    <input type="hidden" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                                    <input type="hidden" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>
                                </th>
                                <th class="text-center" width="10%">
                                  <input type="text" name="data[OrderDetail][remarks][]" class="form-control remarks width_100"/>
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                    <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>
                                    <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                              <td colspan="7" align="right"><b>Total : </b></td>
                              <td align="center"><input name="data[Order][gross_value]" class="form-control width_100" type="text" id="gross_value" value="" readonly />
                              </td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                          </tr>
                          <tr>
                              <td colspan="7" align="right"><b>Total Discount: </b></td>
                              <td align="center"><input name="data[Order][total_discount]" value="" class="form-control width_100 total_discount" type="text" id="total_discount" readonly />
                              </td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                          </tr>
                          <tr>
                              <td colspan="7" align="right"><b>Net Payable: </b></td>
                              <td align="center"><input name="data[Order][net_payable]" class="form-control width_100 net_payable" type="text" id="net_payable" value="" readonly />
                              </td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                          </tr>
                            
                           <tr>
                                 <td colspan="4">  <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
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
                                                        <tr  class="bonus_row">
                                                            <th class="text-center" id="bonus_product_list">
                                                                <?php
                                                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','label' => false,'empty' => '---- Select Product ----','options' => $open_bonus_product_option));
                                
                                                                  
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
                                                                <input type="hidden" class="combined_product"/>
                                                                <input type="hidden" name="data[OrderDetail][discount_amount][]"/>
                                                                <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                                                                <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                                                                <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                                                                <input type="hidden" name="data[OrderDetail][is_bonus][]"/>
                                                            </th>
                                                            <th class="text-center" width="10%">
                                                                <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                                            </th>
                                                        </tr>
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
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td> 
                        </tr>

                    </tfoot>
                </table>
            </div>

            <div class="form-group" style="padding-top:20px;">
                <div class="pull-right">
                  <?php 
                  echo $this->Form->input('save', array('name' => 'save', 'class' => 'form-control','label' => false,'type'=>'hidden','value'=>'Save'));
                  echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div'=>false,)); ?>
                    <?php //echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
                </div>
            </div>
        </div>

<div id="order_product_list">
    <?php
    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false,));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>
<script>
var selected_bonus=[];
var selected_set=[];
var selected_policy_type=[];
$(document).ready(function () {
    $('#order_product_list').hide();
});
</script>
<script>
    $(document).ready(function(){
      $('.product_id').html('<option value="">---- Select Product ----');
       $("#office_id").prop("selectedIndex", 0);
       $("#territory_id").prop("selectedIndex", 0);
       $("#market_id").prop("selectedIndex", 0);
       $("#distribut_outlet_id").prop("selectedIndex", 0);
       $("#outlet_id").prop("selectedIndex", 0);
       $(".order_reference_no").val('');
     $(".show_bonus").click(function(){
        $('#bonus_product').toggle(400);
    });
    $('#bonus_product').hide();
});
</script>
<script>
$(document).ready(function () 
{
  $('.office_id').selectChain({
        target: $('.distribut_outlet_id'),
        value:'name',
        url: '<?= BASE_URL.'sales_people/get_outlet_list_with_distributor_name';?>',
        type: 'post',
        data:{'office_id': 'office_id',}
  });

  $("body").on("change",".office_id",function(){
    var office_id = $(".office_id").val();
     $.ajax({
      url: '<?= BASE_URL . 'orders/get_territory_id_info' ?>',
      'type': 'POST',
      data: {office_id: office_id},
      success: function (result){
          var obj = jQuery.parseJSON(result);
          console.log(obj);
          $('.territory_id').val(obj);
        }
      }); 
    });
});
</script>
<script>
function productList()
{
    var office_id = $('.office_id').val();  
    var outlet_id = $('.distribut_outlet_id').val();  
    
  if(office_id){
    $.ajax({
      type: "POST",
      url: '<?= BASE_URL . 'admin/orders/get_product'?>',
      data: {'office_id':office_id,'outlet_id':outlet_id},
      cache: false, 
      success: function(response){
            var json = $.parseJSON(response);
            $('.product_id option').remove(); 
            //$('.open_bonus_product_id option').remove(); 
            for (var i=0;i<json.length;++i) 
            {
              $('.product_id').append('<option value="'+json[i].id+'">'+json[i].name+'</option>');
              //$('.open_bonus_product_id').append('<option value="'+json[i].id+'">'+json[i].name+'</option>');
            }
        }
    });   
  }
};
function rowUpdate(productLit){

 sl = 1;
  var productLists =$('.open_bonus_product_id').html();
    console.log(productLists);
      product_list = '<div class="input select"><select id="OrderProductId" required="required" class="form-control width_100 product_id" name="data[OrderDetail][product_id][]"><option value="">---- Select Product ----'+productLists+'</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[OrderDetail][product_category_id][]">';
  
  
  
  var current_row = 
  '<th class="text-center sl_order" width="5%">1</th>\
  <th class="text-center">'+product_list+'\
    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/>\
    <input type="hidden" class="ajax_flag" value=0>\
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
    <input type="text" name="data[OrderDetail][sales_qty][]" class="form-control width_100 new_sales_qty" readonly />\
  </th>\
  <th>\
    <input type="number" min="0" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty deliverd_qty" required step="any"/>\
    <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
    <input type="hidden" class="combined_product"/>\
  </th>\
  <th class="text-center" width="12%">\
    <input type="text" class="form-control width_100 aso_stock_qty" readonly />\
    <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty"/>\
  </th>\
  <th class="text-center" width="12%">\
    <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/> \
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
  
  $('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');
  
  $('#gross_value').val(0);
  //$('.order_no').val('');
  
  if(productLit==1){
    productList();
  }else{
    $("#territory_id").prop("selectedIndex", 0);
  }

}
$('.office_id').change(function () {
  $('.market_id').html('<option value="">---- Select Market ----');
  $('.distribut_outlet_id').html('<option value="">---- Select Distributor ----');           
  $('.outlet_id').html('<option value="">---- Select Outlet ----');           
  $('.product_id').html('<option value="">---- Select Product ----');           
});
</script>