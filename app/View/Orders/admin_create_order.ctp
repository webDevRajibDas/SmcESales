<?php
//$this->Session->read('Office')['group_id'];
$maintain_dealer_type = 1;

//pr($territories);die();
?>

<style>
  .width_100_this {
    width: 100%;
  }

  .display_none {
    display: none;
  }

  .width_150 {
    width: 100%;
  }

  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .bonus {
    width: 130px !important;
  }

  .remarks {
    width: 130px !important;
  }

  .product_unit_name {
    width: 80px !important;
  }

  .product_id {
    width: 150px !important;
  }

  .open_bonus_product_id {
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

  /*#bonus_product
{
    position: relative;
    bottom: 181px;
    left: 320px;
    width: 626px;
}*/
</style>

<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Create Requisition'); ?></h3>
        <div class="box-tools pull-right">
          <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Requisition List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
        </div>
      </div>
      <div class="box-body">
        <?php echo $this->Form->create('Order', array('role' => 'form')); ?>

        <div class="form-group">
          <?php
          if ($office_parent_id == 0) {
            echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
          } else {
            echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => TRUE, 'empty' => '---- Select Office ----', 'options' => $offices));
          }
          ?>
        </div>

        <?php if ($maintain_dealer_type == 1) { ?>
          <?php if ($office_parent_id == 0) { ?>
            <div class="form-group" id="distribut_id_so">
              <?php echo $this->Form->input('distribut_outlet_id', array('label' => 'Distributor :', 'id' => 'distribut_outlet_id', 'onChange' => 'rowUpdate(1);', 'class' => 'form-control distribut_outlet_id', 'required' => TRUE, 'empty' => '---- Select Distributor ----')); ?>
            </div>
          <?php } else { ?>
            <div class="form-group" id="distribut_id_so">
              <?php echo $this->Form->input('distribut_outlet_id', array('label' => 'Distributor :', 'id' => 'distribut_outlet_id', 'onChange' => 'rowUpdate(1);', 'class' => 'form-control distribut_outlet_id', 'required' => TRUE, 'empty' => '---- Select Distributor ----', 'options' => $distributers)); ?>
            </div>
          <?php } ?>

        <?php } ?>

        <?php if ($territories != 0) { ?>
          <?php echo $this->Form->input('territory_id', array('type' => 'hidden', 'id' => 'territory_id', 'class' => 'form-control territory_id', 'value' => $territories)); ?>
        <?php } else { ?>
          <?php echo $this->Form->input('territory_id', array('type' => 'hidden', 'id' => 'territory_id', 'class' => 'form-control territory_id')); ?>
        <?php } ?>

        <div class="form-group">
          <?php echo $this->Form->input('order_date', array('label' => 'Requisition Date :', 'class' => 'form-control datepicker', 'value' => $current_date, 'type' => 'text', 'required' => TRUE)); ?>
        </div>

        <div class="form-group">
          <?php echo $this->Form->input('order_no', array('label' => 'Requisition No :', 'class' => 'form-control order_no', 'required' => TRUE, 'type' => 'text', 'value' => $generate_order_no, 'readonly')); ?>
        </div>

        <div class="form-group">
          <?php echo $this->Form->input('order_reference_no', array('label' => 'Remarks :', 'class' => 'form-control order_reference_no', 'maxlength' => '15', 'required' => false, 'type' => 'text')); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('db_balance', array('id' => 'db_balance', 'label' => 'DB Balance :', 'class' => 'form-control db_balance', 'required' => false, 'type' => 'text', 'readonly')); ?>
        </div>
        <div class="table-responsive">
          <!--Set Product area-->
          <table class="table table-striped table-condensed table-bordered invoice_table">
            <thead>
              <tr>
                <th class="text-center" width="5%">ID</th>
                <th class="text-center">Product Name</th>
                <th class="text-center" width="12%">Unit</th>
                <th class="text-center" width="12%">Price</th>
                <th class="text-center" width="12%">ASO Stock Qty</th>
                <th class="text-center" width="12%">Distributor Stock Qty</th>
                <th class="text-center" width="12%">Order QTY</th>
                <th class="text-center" width="12%">Value</th>
                <th class="text-center" width="12%">Discount Value</th>
                <th class="text-center" width="10%">Bonus</th>
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
                  <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id" />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="" class="form-control width_100 product_unit_name" disabled />
                  <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly />
                  <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id" />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly />
                  <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty" readonly />
                  <input type="hidden" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty" />
                </th>
                <th class="text-center" width="12%">
                  <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" required />
                  <input type="hidden" class="combined_product" />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly />
                </th>
                <th class="text-center" width="12%">
                  <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />
                  <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" />
                  <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" />
                  <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type" />
                  <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id" />
                  <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" />
                </th>
                <th class="text-center" width="10%">
                  <input type="text" class="form-control width_100 bonus" disabled />
                  <input type="hidden" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id" />
                  <input type="hidden" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty" />
                  <input type="hidden" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id" />
                </th>
                <th class="text-center" width="10%">
                  <input type="text" name="data[OrderDetail][remarks][]" class="form-control remarks width_100" />
                </th>
                <th class="text-center" width="10%">
                  <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                </th>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="7" align="right"><b>Total : </b></td>
                <td align="center"><input name="data[Order][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" align="right"><b>Total Discount: </b></td>
                <td align="center"><input name="data[Order][total_discount]" class="form-control width_100 total_discount" type="text" id="total_discount" value="0" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" align="right"><b>Net Payable: </b></td>
                <td align="center"><input name="data[Order][net_payable]" class="form-control width_100 net_payable" type="text" id="net_payable" value="0" readonly />
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>

              <tr>
                <td></td>
                <td>
                  <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
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
                              <tr class="bonus_row">
                                <th class="text-center" id="bonus_product_list">
                                  <?php
                                  echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'label' => false));
                                  ?>
                                  <input type="hidden" class="product_id_clone" />
                                  <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" />
                                </th>
                                <th class="text-center" width="12%">
                                  <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled />
                                  <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" />

                                  <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                  <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value="" />
                                  <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
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
            <?php //echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); 
            ?>
            <?php echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
          </div>
        </div>

        <?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>


</div>




<div class="modal" id="myModal" data-backdrop="static" data-keyboard="false"></div>
<div id="loading">
  <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
  $(document).ready(function() {
    $('.product_id').html('<option value="">---- Select Product ----');
    $("#office_id").prop("selectedIndex", 0);
    $("#territory_id").prop("selectedIndex", 0);
    $("#market_id").prop("selectedIndex", 0);
    $("#distribut_outlet_id").prop("selectedIndex", 0);
    $("#outlet_id").prop("selectedIndex", 0);
    $(".order_reference_no").val('');
    $(".show_bonus").click(function() {
      $('#bonus_product').toggle(400);
    });
    $('#bonus_product').hide();
  });
</script>




<script>
  /*function productList()
{
    var territory_id = $('#territory_id').val();	
		
	if(territory_id){
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL . 'admin/orders/get_product' ?>',
			data: {'territory_id':territory_id},
			cache: false, 
			success: function(response){
				var json = $.parseJSON(response);
				$('.product_id option').remove();
				for (var i=0;i<json.length;++i)
				{
					$('.product_id').append('<option value="'+json[i].id+'">'+json[i].name+'</option>');
                }
            }
        });		
	}
};*/
  function productList() {
    var office_id = $('.office_id').val();
    var outlet_id = $('.distribut_outlet_id').val();

    if (office_id) {
      $.ajax({
        type: "POST",
        url: '<?= BASE_URL . 'admin/orders/get_product' ?>',
        data: {
          'office_id': office_id,
          'outlet_id': outlet_id
        },
        cache: false,
        success: function(response) {
          var json = $.parseJSON(response);
          $('.product_id option').remove();

          for (var i = 0; i < json.length; ++i) {
            $('.product_id').append('<option value="' + json[i].id + '">' + json[i].name + '</option>');
          }
        }
      });
    }
  };

  function rowUpdate(productLit) {

    sl = 1;
    var productLists = $('.open_bonus_product_id').html();
    product_list = '<div class="input select"><select id="OrderProductId" required="required" class="form-control width_100 product_id" name="data[OrderDetail][product_id][]"><option value="">---- Select Product ----' + productLists + '</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[OrderDetail][product_category_id][]">';



    var current_row =
      '<th class="text-center sl_memo" width="5%">1</th>\
  <th id="memo_product_list" class="text-center">' + product_list + '</th>\
  <th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
  </th>\
  <th class="text-center" width="12%">\
    <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
  </th>\
  <th class="text-center" width="12%">\
    <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly />\
    <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty"/>\
  </th>\
  <th class="text-center" width="12%">\
    <input type="text" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty" readonly />\
    <input type="hidden" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty"/>\
  </th>\
  <th>\
    <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" value="" required/>\
    <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
    <input type="hidden" class="combined_product"/>\
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
  </th>';

    $('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');

    $('#gross_value').val(0);
    //$('.order_no').val('');

    if (productLit == 1) {
      productList();
    } else {
      $("#territory_id").prop("selectedIndex", 0);
    }

  }

  $(document).ready(function() {


    $('.territory_id').selectChain({
      target: $('.market_id'),
      value: 'name',
      url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
      type: 'post',
      data: {
        'territory_id': 'territory_id'
      }
    });

    $('.market_id').selectChain({
      target: $('.outlet_id'),
      value: 'name',
      url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
      type: 'post',
      data: {
        'market_id': 'market_id'
      }
    });

    $('.office_id').selectChain({

      target: $('.distribut_outlet_id'),
      value: 'name',
      url: '<?= BASE_URL . 'sales_people/get_outlet_list_with_distributor_name'; ?>',
      type: 'post',
      data: {
        'office_id': 'office_id',
      }

    });

    $('.office_id').change(function() {
      $('.market_id').html('<option value="">---- Select Market ----');
      $('.distribut_outlet_id').html('<option value="">---- Select Distributor ----');
      $('.outlet_id').html('<option value="">---- Select Outlet ----');
      $('.product_id').html('<option value="">---- Select Product ----');
    });



  });
</script>
<script>
  $(document).ready(function() {
    $("body").on("change", ".office_id", function() {
      var office_id = $(".office_id").val();
      $.ajax({
        url: '<?= BASE_URL . 'orders/get_territory_id_info' ?>',
        'type': 'POST',
        data: {
          office_id: office_id
        },
        success: function(result) {
          var obj = jQuery.parseJSON(result);
          $('.territory_id').val(obj);
        }
      });

      $.ajax({
        type: "POST",
        url: '<?= BASE_URL . 'orders/get_bonus_product' ?>',
        data: 'office_id=' + office_id,
        cache: false,
        success: function(response) {
          var json = $.parseJSON(response);
          $('.open_bonus_product_id option').remove();
          $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
          for (var i = 0; i < json.length; ++i) {
            $('.open_bonus_product_id').append('<option value="' + json[i].Product.id + '">' + json[i].Product.name + '</option>');
          }
        }
      });
    });

    $("body").on("change", ".distribut_outlet_id", function() {
      get_db_balance($(this).val());
    });

    function get_db_balance(db_id) {
      $.ajax({
        url: '<?= BASE_URL . 'Orders/get_db_balance' ?>',
        data: {
          'distributor_id': db_id
        },
        type: 'POST',
        success: function(data) {
          $('#db_balance').val(data);
        }
      });
    }
  });
</script>

<script>
  function total_values() {
    var t = 0;
    //alert(t);
    $('.total_value').each(function() {
      if ($(this).val() != '') {
        t += parseFloat($(this).val());
      }
    });

    $('#gross_value').val(t);
  }

  $(document).ready(function() {
    /*$("body").on("change",".company_id",function(){
        var company_id = $(".company_id").val();
         $.ajax({
            url: '<?= BASE_URL . 'orders/company_confirmation_type' ?>',
            'type': 'POST',
            data: {company_id: company_id},
            success: function (result){
              //alert(result);
              }
            }); 
          });*/

    $("body").on("click", ".add_more", function() {
      var sl = parseInt($('.invoice_table>tbody>tr:last').attr('id')) + 1;

      var product_list = $('#memo_product_list').html();
      var product_box = $(this).parent().parent().parent();
      var current_row_no = $(this).parent().parent().attr('id');
      //alert(current_row_no);

      var current_row =
        '<th class="text-center sl_memo" width="5%"></th>\
        <th class="text-center">' + product_list + '</th>\
        <th class="text-center" width="12%">\
          <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
          <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/>\
          <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly />\
          <input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty"/>\
        </th>\
        <th class="text-center" width="12%">\
          <input type="text" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty" readonly />\
          <input type="hidden" name="data[OrderDetail][dist_stock_qty][]" class="form-control width_100 dist_stock_qty"/>\
        </th>\
        <th>\
          <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/>\
          <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
          <input type="hidden" class="combined_product"/>\
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
          <a class="btn btn-primary btn-xs add_more">\
            <i class="glyphicon glyphicon-plus"></i>\
          </a>\
          <a class="btn btn-danger btn-xs delete_item">\
            <i class="glyphicon glyphicon-remove"></i>\
          </a>\
        </th>';


      var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
      if (valid_row != '') {
        product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
        $('#' + sl + '>.sl_memo').text(sl);
        $(this).hide();
      } else {
        alert('Please fill up this row!');
      }

    });

    $("body").on("change", ".product_id", function() {
      var ajax_img = 1;
      var new_product = 1;
      $('#myModal').modal('show');
      $('#loading').show();
      $('#gross_value').val(0);

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
      $('.product_id').each(function() {
        console.log($(this).val());
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
            //$(this).val('').attr('selected', true);

            new_product = 0;
            $('#myModal').modal('hide');
            $('#loading').hide();
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
      var product_qty = product_box.find("th:nth-child(7) .min_qty");
      var total_val = product_box.find("th:nth-child(8) .total_value");

      var rate_class = product_rate.attr('class').split(' ').pop();
      var value_class = total_val.attr('class').split(' ').pop();

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
      if ($('#sale_type_id').val() == 3) {
        var territory_id = $('.spo_territory_id').val();
      } else {
        var territory_id = $('.territory_id').val();
      }
      var outlet_id = $('.distribut_outlet_id').val();


      $.ajax({
        url: '<?= BASE_URL . 'orders/get_product_unit' ?>',
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
          /*var general_price = obj.product_price.general_price;
          var min_qty = obj.product_combination.min_qty;
          $('#'+current_row_no+'>th>.total_value').val(total_value);*/
          $('#' + current_row_no + '>th>.aso_stock_qty').val(total_qty);
          $('#' + current_row_no + '>th>.dist_stock_qty').val(total_dist_qty);
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
  /*--------- check combined or individual product price --------*/
  var selected_bonus = [];
  var selected_set = [];
  var selected_policy_type = [];
  var other_policy_info = [];
  $("body").on("keyup", ".min_qty", function() {
    var current_row_no = $(this).parent().parent().attr('id');
    var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

    var product_wise_qty = {};
    $('.product_row_box .product_id').each(function(index, value) {
      var producct_box_each = $(this).parent().parent().parent();
      if (producct_box_each.find("th:nth-child(7) .min_qty").val()) {
        product_wise_qty[$(this).val()] = producct_box_each.find("th:nth-child(7) .min_qty").val();
      }
    });
    pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();
    var sl = $('.invoice_table>tbody>tr').length;
    var product_box = $(this).parent().parent();
    var product_field = product_box.find("th:nth-child(2) .product_id");

    var product_rate = product_box.find("th:nth-child(4) .product_rate");
    var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
    var product_qty = product_box.find("th:nth-child(7) .min_qty");
    var total_val = product_box.find("th:nth-child(8) .total_value");
    var combined_product_obj = product_box.find("th:nth-child(7) .combined_product");
    var combined_product_id_obj = product_box.find("th:nth-child(7) .combination_id");
    var combined_product = combined_product_obj.val();
    var min_qty = product_qty.val();
    var id = product_field.val();

    var order_date = $("#OrderOrderDate").val();
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
      $.ajax({
        url: '<?= BASE_URL . 'orders/get_product_price' ?>',
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
              prate.parent().parent().find("th:nth-child(7) .combined_product").val(obj.combine_product);
              prate.parent().parent().find("th:nth-child(7) .combination_id").val(obj.combination_id);
            });
          }

          if (obj.recall_product_for_price != undefined) {
            $.each(obj.recall_product_for_price, function(index, value) {
              var prate = $(".prate-" + value);
              var tvalue = $(".tvalue-" + value);
              prate.parent().parent().find("th:nth-child(7) .combined_product").val(obj.combine_product);
              prate.parent().parent().find("th:nth-child(7) .combination_id").val('');
              prate.parent().parent().find("th:nth-child(7) .min_qty").trigger('keyup');
            });
          }

          var gross_total = 0;
          $('.total_value').each(function() {
            if ($(this).val() != '') {
              gross_total = parseFloat(gross_total) + parseFloat($(this).val());
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
          url: '<?= BASE_URL . 'orders/get_product_policy' ?>',
          'type': 'POST',
          data: {
            office_id: $("#office_id").val(),
            min_qty: min_qty,
            product_id: id,
            order_date: order_date,
            cart_product: product_wise_qty,
            memo_total: $("#gross_value").val(),
            selected_bonus: JSON.stringify(selected_bonus),
            selected_set: JSON.stringify(selected_set),
            selected_policy_type: JSON.stringify(selected_policy_type),
            other_policy_info: JSON.stringify(other_policy_info),
          },
          async: false,
          success: function(result) {
            var response = $.parseJSON(result);
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
                  prate.parent().parent().find("th:nth-child(9) .discount_value").val(val1.total_discount_value);
                  prate.parent().parent().find("th:nth-child(9) .discount_amount").val(val1.discount_amount);
                  prate.parent().parent().find("th:nth-child(9) .disccount_type").val(val1.discount_type);
                  prate.parent().parent().find("th:nth-child(9) .policy_type").val(val1.policy_type);
                  prate.parent().parent().find("th:nth-child(9) .policy_id").val(val1.policy_id);
                  prate.parent().parent().find("th:nth-child(9) .is_bonus").val('0');
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
    } else {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly', true);
      $(this).parent().prev().find('.policy_min_qty').prop('required', false);
      $(this).parent().prev().find('.policy_min_qty').attr('min', 0);
      $(this).parent().prev().find('.policy_min_qty').val(0.00);
    }
  });


  $("body").on("keyup", ".policy_min_qty", function() {

    var class_list = $(this).attr('class');
    class_list = class_list.split(" ");
    var policy_set_class = class_list[2];
    var max_qty = parseFloat($(this).attr('max'));
    var total_provide_qty = 0.00;
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
    }
  });
  $("body").on("click", ".btn_type", function(e) {
    e.preventDefault();
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
</script>

<script>
  $(document).ready(function() {
    $('body').on('click', '.delete_item', function() {
      var product_box = $(this).parent().parent();
      var product_field = product_box.find("th:nth-child(2) .product_id");
      var product_rate = product_box.find("th:nth-child(4) .product_rate");
      var combined_product = product_box.find("th:nth-child(7) .combined_product");
      var product_qty = product_box.find("th:nth-child(7) .min_qty");
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
              prate.parent().parent().find("th:nth-child(7) .combined_product").val('');
              prate.parent().parent().find("th:nth-child(7) .min_qty").trigger('keyup');
            }
          });
        }
        for (key in policy_product) {
          var prate = $(".prate-" + key);
          prate.parent().parent().find("th:nth-child(7) .min_qty").trigger('keyup');
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

      // alert(credit_amount);
      if (credit_amount >= 0) {
        $("#credit_amount").val(credit_amount.toFixed(2));
      } else {
        $("#credit_amount").val(0);
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('body').on("keyup", ".order_no", function() {
      var order_no = $('.order_no').val();
      var sale_type = $('#sale_type_id').val();

      delay(function() {
        $.ajax({
          url: '<?php echo BASE_URL . 'admin/orders/order_no_validation' ?>',
          'type': 'POST',
          data: {
            order_no: order_no,
            sale_type: sale_type
          },
          success: function(result) {
            obj = jQuery.parseJSON(result);
            /*if(obj == 1){
                alert('Memo Number Already Exist');
                $('.submit').prop('disabled', true);
            }*/
            if (obj == 0) {
              $('.submit_btn').prop('disabled', false);
            } else {
              alert('Memo Number Already Exist');
              $('.submit_btn').prop('disabled', true);
            }
          }
        });
      }, 1000);

    });
  });

  var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();

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
                    <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">\
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

      var sale_type_id = $("#sale_type_id option:selected").val();

      if (sale_type_id == 3) {
        territory_id = $("#spo_territory_id option:selected").val();
      }

      //alert(territory_id);

      /*if(territory_id){
          $.ajax({
              type: "POST",
              url: '<?= BASE_URL . 'orders/get_bonus_product' ?>',
              data: 'territory_id='+territory_id,
              cache: false, 
              success: function(response){
                  var json = $.parseJSON(response);
                  console.log(json);
                  $('.open_bonus_product_id option').remove();
                  $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
                  for (var i=0;i<json.length;++i)
                  {
                      $('.open_bonus_product_id').append('<option value="'+json[i].Product.id+'">'+json[i].Product.name+'</option>');
                  }
              }
          });     
      }*/
    })
  });
</script>