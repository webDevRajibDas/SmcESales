<?php
//pr($challan_info);die();
// $rs = $challan_info[0]['PrimaryMemo']['sender_store_id'];
?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit PrimaryMemo'); ?></h3>
        <div class="box-tools pull-right">
          <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> PrimaryMemo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
        </div>
      </div>
      <div class="box-body">
        <?php echo $this->Form->create('PrimaryMemo', array('role' => 'form')); ?>
        <div class="form-group">
          <?php echo $this->Form->input('sender_store_id', array('class' => 'form-control', 'label' => 'Sender :', 'options' => $sender, 'required' => true, 'default' => $challan_info[0]['PrimaryMemo']['sender_store_id'])); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control', 'label' => 'Receiver :', 'options' => $receiver, 'required' => true, 'default' => $challan_info[0]['PrimaryMemo']['receiver_store_id'])); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('challan_referance_no', array('class' => 'form-control challan_referance_no', 'value' => $challan_info[0]['PrimaryMemo']['challan_referance_no'])); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('challan_date', array('type' => 'text', 'value' => date('d-m-Y', strtotime($challan_info[0]['PrimaryMemo']['challan_date'])), 'class' => 'form-control challan-datepicker', 'required' => true, 'readonly' => true)); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('delivery_point', array('value' => $challan_info[0]['PrimaryMemo']['delivery_point'], 'class' => 'form-control', 'rows' => 1)); ?>
        </div>
        <div class="form-group">
          <?php echo $this->Form->input('remarks', array('value' => $challan_info[0]['PrimaryMemo']['remarks'], 'class' => 'form-control')); ?>
        </div>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <!--  <th class="text-center">Product Type</th> -->
              <th class="text-center">Product Name</th>
              <th class="text-center hidden">Batch No.</th>
              <th class="text-center hidden">Expire Date</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Price</th>
              <th class="text-center">Vat(%)</th>
              <th class="text-center">Total Price</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <!-- <td align="center">
                      <?php //echo $this->Form->input('product_type', array('label'=>false,'id'=>'product_type', 'class' => 'full_width form-control product_type','empty'=>'--- Product Type ---')); 
                      ?>
                    </td> -->
              <td>
                <?php echo $this->Form->input('product_id', array('label' => false, 'class' => 'full_width form-control product_id chosen', 'id' => 'product_id', 'empty' => '--- Select Product ---', 'options' => $products)); ?>
              </td>
              <td align="center" class="text-center hidden">
                <?php echo $this->Form->input('batch_no', array('label' => false, 'class' => 'full_width form-control batch_no')); ?>
              </td>
              <td align="center" class="text-center hidden">
                <?php echo $this->Form->input('expire_date', array('label' => false, 'placeholder' => '---Select Date---', 'class' => 'full_width form-control expire_date expireDatepicker')); ?>
              </td>
              <td align="center">
                <?php echo $this->Form->input('challan_qty', array('label' => false, 'class' => 'full_width form-control quantity')); ?>
              </td>
              <td align="center">
                <?php echo $this->Form->input('product_price', array('label' => false, 'class' => 'full_width form-control product_price')); ?>
              </td>
              <td align="center">
                <?php echo $this->Form->input('vat', array('label' => false, 'class' => 'full_width form-control vat')); ?>
              </td>
              <td align="center" class="main_total_price">
              </td>
              <td align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>
            </tr>
          </tbody>
        </table>
        <div class="table-responsive">
          <table class="table table-striped table-condensed table-bordered invoice_table">
            <thead>
              <tr>
                <th class="text-center">SL.</th>
                <th class="text-center" width="12%">Product Name</th>
                <th class="text-center">Unit</th>
                <th class="text-center hidden">Batch No.</th>
                <th class="text-center hidden" width="12%">Expire Date</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Total Quantity</th>
                <th class="text-center" width="7%">Price</th>
                <th class="text-center" width="7%">Vat(%)</th>
                <th class="text-center">Total Price</th>
                <th class="text-center">Remarks</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sl = 1;
              $total_price = 0;
              $display_serial_number = 1;
              $row_count_for_remove = 0;
              foreach ($challan_info[0]['PrimaryMemoDetail'] as $value) {
                $row_count_for_remove++;
              ?>
                <tr class="table_row" id="rowCount<?php echo $row_count_for_remove; ?>">
                  <td align="center">
                    <?php echo $display_serial_number;
                    $display_serial_number++ ?>
                  </td>
                  <td>
                    <input type="hidden" name="product_id_batch_date[<?php echo $row_count_for_remove; ?>]" class="selected_product_id" value="<?php echo $value['product_id'];
                                                                                                                                                echo $value['batch_no'];
                                                                                                                                                echo $this->App->expire_dateformat($value['expire_date']);
                                                                                                                                                echo $value['product_price']; ?>" />
                    <?php echo $value['Product']['name']; ?>
                    <input type="hidden" name="product_id[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['product_id'];  ?>" />
                  </td>
                  <td align="center">
                    <?php echo $value['MeasurementUnit']['name']; ?>
                    <input type="hidden" name="measurement_unit[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['measurement_unit_id']; ?>">
                  </td>
                  <td align="center" class="text-center hidden">
                    <input type="hidden" name="batch_no[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['batch_no']; ?>">
                    <?php echo $value['batch_no']; ?>
                  </td>
                  <td align="center" class="text-center hidden">
                    <input type="hidden" name="expire_date[<?php echo $row_count_for_remove; ?>]" value="<?php if ($value['expire_date'] != ' ') {
                                                                                                            echo $this->App->expire_dateformat($value['expire_date']);
                                                                                                          } ?>">
                    <?php if ($value['expire_date'] != ' ') {
                      echo $this->App->expire_dateformat($value['expire_date']);
                    } ?>
                  </td>
                  <td align="center">
                    <input type='text' name="quantity[<?php echo $row_count_for_remove; ?>]" class="<?php echo $value['product_id'];  ?>_product_qty p_quantity" value="<?php echo $value['challan_qty']; ?>" required onchange="count_total_value(<?php echo $value['product_id']; ?>,this)">
                  </td>
                  <td align="center" id="quantity" class="<?php echo $value['product_id']; ?>">
                    <?php echo $value['challan_qty']; ?>
                  </td>
                  <td align="center">
                    <input type="text" name="price[<?php echo $row_count_for_remove; ?>]" class="<?php echo $value['product_id']; ?>product_price" value="<?php echo $value['product_price']; ?>" required onchange="count_total_value(<?php echo $value['product_id'];  ?>,this)">
                  </td>
                  <td align="center">
                    <input type="text" name="vat[<?php echo $row_count_for_remove; ?>]" class="<?php echo $value['product_id']; ?>vat" value="<?php echo $value['vat']; ?>" required>
                  </td>

                  <td align="center">
                    <p class="ttprice total_price_<?php echo $value['product_id']; ?>"><?php echo $value['challan_qty'] * $value['product_price']; ?> </p>
                    <!-- <?php $total = $value['challan_qty'] * $value['product_price']; ?> -->
                  </td>
                  <td align="center">
                    <input type="text" class="full_width form-control" name="remarks[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['remarks']; ?>">
                  </td>
                  <td align="center">
                    <button class="btn btn-danger btn-xs remove" value="<?php echo $row_count_for_remove ?>"><i class="fa fa-times"></i></button>
                  </td>
                </tr>
              <?php
                $total_price =  $total_price + $total;
                $sl++;
              } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="7" align="right"><b>Total : </b></td>
                <td align="center">
                  <p class="total_amount"><?php echo $total_price;  ?></p>
                </td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
        </br>
        <div class="pull-right">
          <?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
          <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft'));
          ?>
        </div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>
</div>
<style>
  .datepicker table tr td.disabled,
  .datepicker table tr td.disabled:hover {
    color: #c7c7c7;
  }
</style>
<?php
$startDate = date('d-m-Y', strtotime('day'));
?>
<script>
  /*PrimaryMemo Datepicker : Start*/
  $(document).ready(function() {
    var today = new Date(new Date().setDate(new Date().getDate()));
    $('.challan-datepicker').datepicker({
      startDate: '<?php echo $startDate; ?>',
      format: "dd-mm-yyyy",
      autoclose: true,
    });
  });
  /*PrimaryMemo Datepicker : End*/
  $(document).ready(function() {
    $('#product_type').selectChain({
      target: $('#product_id'),
      value: 'name',
      url: '<?= BASE_URL . 'primarymemos/get_product' ?>',
      type: 'post',
      data: {
        'product_type_id': 'product_type'
      }
    });
    var is_maintain_batch = 1;
    var is_maintain_expire_date = 1;
    $(".chosen").change(function() {
      var product_id = $(this).val();
      $.ajax({
        type: "POST",
        url: '<?php echo BASE_URL; ?>admin/products/product_details',
        data: 'product_id=' + product_id,
        cache: false,
        success: function(response) {
          var obj = jQuery.parseJSON(response);
          if (obj.Product.maintain_batch == 0) {
            is_maintain_batch = 0;
            $('.batch_no').val('');
            $('.batch_no').attr('readonly', true);
          } else {
            is_maintain_batch = 1;
            $('.batch_no').val('');
            $('.batch_no').attr('readonly', false);
          }
          if (obj.Product.is_maintain_expire_date == 0) {
            is_maintain_expire_date = 0;
            $('.expire_date').val('');
            $('.expire_date').attr('disabled', true);
          } else {
            is_maintain_expire_date = 1;
            $('.expire_date').val('');
            $('.expire_date').attr('disabled', false);
          }
        }
      });
    });
    var rowCount = 0;
    $('.table_row').each(function() {
      rowCount++;
    });
    $(".add_more").click(function() {
      var product_id = $('.product_id').val();
      var quantity = $('.quantity').val();
      var batch_no = $('.batch_no').val();
      var product_price = $('.product_price').val();
      var vat = $('.vat').val();
      var expire_date = $('.expire_date').val();
      var price = $('.main_total_price').text();
      //console.log(price) ;          
      var selected_stock_array = $(".selected_product_id").map(function() {
        return $(this).val();
      }).get();
      var product_check_id = product_id + batch_no + expire_date + product_price;
      var stock_check = $.inArray(product_check_id, selected_stock_array) != -1;
      if (product_id == '') {
        alert('Please select any product.');
        return false;
      }
      /*else if (is_maintain_batch == 1 && (batch_no == '' || batch_no ==0))
      {
          alert('Please enter batch number.');
          return false;
      }*/
      else if (!quantity.match(/^\d+(\.\d{1,4})?$/) || parseFloat(quantity) <= 0.0000) {
        alert('Please  Enter Product Quantity. Ex. : 100 or 100.0000');
        $('.quantity').val('');
        return false;
      } else if (!vat.match(/^\d+(\.\d{1,4})?$/) || parseFloat(vat) >= 100.0000) {
        alert('Please  Enter vat. Ex. : 0 or 100.0000');
        $('.vat').val('');
        return false;
      } else if (!product_price.match(/^\d+(\.\d{1,4})?$/) || parseFloat(product_price) <= -1) {
        alert('Please Enter Product Price.');
        $('.product_price').val('');
        return false;
      } else if (parseFloat(quantity) >= 10000000) {
        alert('Quantity must be less then equal 10000000.');
        $('.quantity').val('');
        return false;
      }
      /*else if (is_maintain_expire_date == 1 && expire_date == '')
      {
          alert('Please enter valid expire date.');
          return false;
      }*/
      else if (stock_check == true) {
        alert('This product already added.');
        clear_field();
        return false;
      } else {
        rowCount++;
        $.ajax({
          type: "POST",
          url: '<?php echo BASE_URL; ?>admin/products/product_details',
          data: 'product_id=' + product_id,
          cache: false,
          success: function(response) {
            var obj = jQuery.parseJSON(response);
            var recRow = '<tr class="table_row" id="rowCount' + rowCount + '">\
                        <td align="center">' + rowCount + '</td>\
                        <td>' + obj.Product.name + '\
                          <input type="hidden" name="product_check[' + rowCount + ']" class="selected_product_id" value="' + obj.Product.id + batch_no + expire_date + product_price + '"/>\
                          <input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id + '"/>\
                        </td>\
                        <td align="center">' + obj.BaseMeasurementUnit.name + '\
                          <input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + obj.Product.base_measurement_unit_id + '">\
                        </td>\
                        <td align="center" class="text-center hidden">\
                          <input type="hidden" name="batch_no[' + rowCount + ']" value="' + batch_no + '">' + batch_no + '\
                        </td>\
                        <td align="center" class="text-center hidden">\
                          <input type="hidden" name="expire_date[' + rowCount + ']" class="p_expire_date" value="' + expire_date + '">' + expire_date + '\
                        </td>\
                        <td align="center">\
                          <input  name="quantity[' + rowCount + ']" onchange="count_total_value(' + obj.Product.id + ',this)" class="' + product_id + '_product_qty  p_quantity " value="' + quantity + '" required >\
                        </td>\
                        <td align="center" class="' + product_id + '"></td>\
                        <td  align="center">\
                          <input name="product_price[' + rowCount + ']" onchange="count_total_value(' + obj.Product.id + ',this)" class="' + product_id + 'product_price" value="' + product_price + '" required >\
                        </td>\
                        <td  align="center">\
                          <input name="vat[' + rowCount + ']" class="' + product_id + 'vat" value="' + vat + '" required >\
                        </td>\
                        <td align="center" >\
                          <input type="hidden" name="price[' + rowCount + ']" class="total_product_price" value="' + price + '"><p class="ttprice total_price_' + product_id + '">' + price + ' </p>\
                        </td>\
                        <td align="center">\
                          <input type="text" class="full_width form-control" name="remarks[' + rowCount + ']">\
                        </td>\
                        <td align="center">\
                          <button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button>\
                        </td>\
                      </tr>';
            $('.invoice_table').append(recRow);
            product_wise_total_quantity(product_id);
            product_total_price();
            clear_field();
            var total_quantity = set_total_quantity();
            if (total_quantity > 0) {
              $('.draft').prop('disabled', false);
              $('.save').prop('disabled', false);
            }
          }
        });
      }
    });

    function isDate(txtDate) {
      return txtDate.match(/^d\d?\/\d\d?\/\d\d\d\d$/);
    }

    $(document).on("keyup", ".p_quantity", function() {
      var quantity = $(this).val();
      if (!$.isNumeric(quantity) || parseFloat(quantity) <= 0.00) {
        alert('Please enter valid quantity. Ex. : 100 or 100.00');
        $(this).val('');
      }
      var p_id = $(this).attr('class').split('_');
      product_wise_total_quantity(p_id[0]);
    });

    $(document).on("click", ".remove", function() {
      var removeNum = $(this).val();
      var p_id = $("input[name~='product_id[" + removeNum + "]']").val();
      $('#rowCount' + removeNum).remove();
      product_wise_total_quantity(p_id);
      var total_quantity = set_total_quantity();
      product_total_price();
      if (total_quantity <= 0) {
        $('.draft').prop('disabled', true);
        $('.save').prop('disabled', true);
      }
    });

    $('.draft').prop('disabled', false);
    $('.save').prop('disabled', false);

    function clear_field() {
      $('.product_type').val('');
      $('.product_id').val('');
      $('.quantity').val('');
      $('.batch_no').val('');
      $('.price').val('');
      $('.vat').val('');
      $('.expire_date').val('');
      $('.product_price').val('');
      $(".main_total_price").text("0");
      $('.chosen').val('').trigger('chosen:updated');
      $('.add_more').val('');
      $('.batch_no').attr('readonly', false);
      $('.expire_date').attr('disabled', false);
    }

    function set_total_quantity() {
      var sum = 0;
      var num = 1;
      $('.table_row').each(function() {
        var table_row = $(this);
        var total_quantity = table_row.closest('tr').find('.p_quantity').val();
        sum += parseFloat(total_quantity);
        $(this).find("td:first").html(num++);
      });

      $('.total_quantity').html(sum);
      return sum;
    }

    function product_wise_total_quantity(product_id) {
      var sum = 0;
      $('.' + product_id + '_product_qty').each(function() {
        var qty = $(this).val();
        sum += parseFloat(qty);
      });
      $('.' + product_id + '').html(sum.toFixed(2));
    }
    $(document).on("change", 'input[name="data[PrimaryMemo][challan_qty]"],input[name="data[PrimaryMemo][product_price]"]', function() {
      var product_qty = $('input[name="data[PrimaryMemo][challan_qty]"]').val();
      var product_price = $('input[name="data[PrimaryMemo][product_price]"]').val();
      var total = parseFloat(product_qty) * parseFloat(product_price);
      $(".main_total_price").text(total);
    });
    $("form").submit(function() {
      $('.draft').hide();
      $('.save').hide();
    });
    var dates = $('.expireDatepicker').datepicker({
      'format': "M-yy",
      'startView': "year",
      'minViewMode': "months",
      'autoclose': true
    });
    $('.expireDatepicker').click(function() {
      dates.datepicker('setDate', null);
    });
  });
</script>
<script>
  function count_total_value(id, __this) {
    var price = $(__this).parent().parent().find('.' + id + 'product_price').val();
    var qty = $(__this).parent().parent().find('.' + id + '_product_qty').val();
    var totalprice = qty * price;
    $(__this).parent().parent().find('td p.total_price_' + id).text(totalprice);
    product_total_price();
  }

  function product_total_price() {
    var total = 0;
    $(".ttprice").each(function(index) {
      console.log(index + ": " + $(this).text());
      total += parseFloat($(this).text());
    });
    $('.total_amount').text(total);
  }
</script>