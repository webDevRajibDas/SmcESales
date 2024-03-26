

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
.form-control {
    float: left;
    width: 50%;
    font-size: 13px;
    height: 28px;
    padding: 0px 4px;
}
label {
    float: left;
    width: 30%;
    text-align: right;
    margin: 5px 20px 0 0;
} 
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Distributer Product Issue'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('OrderProces', array('role' => 'form')); ?>
                <div class="row">
                    <!-- First Part : Start -->
                    <div class="col-xs-6" id = "requisition_type_div">
                        <div class="form-group">
                            <?php echo $this->Form->input('requisition_type_id', array('class' => 'form-control requisition_type_id','id' => 'requisition_type_id', 'required' => 'required','options'=>$requisition_type_list,'empty' => '--- Select Requisition ---')); ?> 
                        </div>

                    </div>
                    <div class="col-xs-6" id = "order_div">
                        <div class="form-group">
                            <?php echo $this->Form->input('order_id', array('class' => 'form-control order_id','id' => 'order_id', 'required' => 'required','options'=>$order_list,'empty' => '--- Select Order ---')); ?> 
                        </div>

                    </div>
                    
                 <!-- Second Pard : Start -->   
                </div>
               
                <div id="show_data"></div>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>

</div>



<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
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


<script>
    $(document).ready(function () {
        $("#order_div").hide();
        $("body").on("change", "#requisition_type_id", function () {
            var requisition_type = $(this).val();
            if(requisition_type == 1){
                $("#order_div").show();
            }else{
                $("#order_div").hide();
            }
        });
        $("body").on("change", ".order_id", function () {
           
            var order_id = $(this).val();
            if(order_id){
                $.ajax({
                url: '<?= BASE_URL . 'Manages/get_order_info' ?>',
                data: {'order_id': order_id},
                type: 'POST',
                success: function (response)
                {
                    console.log(response);
                    $('#show_data').show();
                    $('#show_data').html(response);
                    catch_content = response;
                }
            });
            }
            else{
                alert('here');
            }
        });
});
</script>
<script>

    function total_values(){
     var t = 0;
     $('.total_value').each(function(){
       if($(this).val()!=''){
        t += parseFloat($(this).val());
    }
});
     $('#gross_value').val(t);
 } 
 
 $(document).ready(function () {      
    $('#order_product_list').hide();
    //$('.add_more').hide();
    var last_row_number = $('.invoice_table tbody tr:last').attr('id');
    $('#'+last_row_number+'>th>.add_more').show();

    $("body").on("click", ".add_more", function () {
        var sl = $('.invoice_table>tbody>tr').length+1;

        var product_list = $('#order_product_list').html();
        var product_box = $(this).parent().parent().parent();
        var current_row_no = $(this).parent().parent().attr('id');

        /*var current_row = '<th class="text-center sl_order" width="5%"></th><th class="text-center">'+product_list+'<input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/><input type="hidden" class="ajax_flag" value=0></th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/><input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[OrderDetail][sales_qty][]" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th></th><th></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';*/

        var current_row = '<th class="text-center sl_order" width="5%"></th><th class="text-center">'+product_list+'<input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/><input type="hidden" class="ajax_flag" value=0></th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/><input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty new_sales_qty" readonly /><input type="hidden" name="data[OrderDetail][sales_qty][]" class="form-control width_100 aso_stock_qty"/></th><th><input type="number" min="0" name="data[OrderDetail][deliverd_qty][]" class="form-control width_100 min_qty deliverd_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty" readonly /><input type="hidden" name="data[OrderDetail][aso_stock_qty][]" class="form-control width_100 aso_stock_qty"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';

        var valid_row = $('#'+current_row_no+'>th>.product_rate').val();
        if (valid_row != '') {
            product_box.append('<tr id='+sl+' class=new_row_number>' + current_row + '</tr>');
            $('#'+sl+'>.sl_order').text(sl);
            $('#cash_collection').val('');
            $(this).hide();
        }else{
            alert('Please fill up this row!');
        }

    });
 
    //var canPermitted= <?php //echo $canPermitted;?>;
    hide_save_button();
    function hide_save_button()
    {
      /*if(canPermitted==0)
      {
        $(".can_permit").hide();
      }
      else
      {
        $(".can_permit").show(); 
      }*/
    }


  $("body").on("click", ".add_more", function () { 
      //checkInventory(); /*edited by Ibrahim 01-12-2019*/
  });
 // $("body").on("click", ".delete_item", function () {
    //  checkInventory();
  //});

  $("body").on("change", ".product_id", function () {
          //checkInventory_byProduct();
          
          var new_product = 1;
          $('#myModal').modal('show');
          $('#loading').show();
              //console.log($('#'+sl+'>th>.bonus').val());
              $('#gross_value').val(0);
              /*----- make array with product list -------*/
              var sl = $('.invoice_table>tbody>tr').length;

              var current_row_no = $(this).parent().parent().parent().attr('id');

              if ($('#'+current_row_no+'>th>.product_rate').val() == '') {
                  $('#'+current_row_no+'>th>.bonus').val('N.A');
                  $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                  $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                  $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
              }
              
              var product_change_flag = 1;
              var product_id_list = '';
              $('.product_row_box .product_id').each(function () {

                //alert($(this).val());
                
                  if ($(this).val() != '') {
                      //product_id_list = $(this).val()+','+product_id_list;
                      if (product_id_list.search($(this).val()) == -1) 
                      {
                          product_id_list = $(this).val() + ',' + product_id_list;
                         // checkInventory(); /*edited by Ibrahim 01-12-2019*/
                      } 
                      else 
                      {
                          alert("This poduct already exists");
                          product_change_flag = 0;
                          $('#'+current_row_no+'>th>div>select').val($('#'+current_row_no+'>th>.product_id_clone').val());
                          if($('#'+current_row_no+'>th>.product_rate').val() == ''){
                              $(this).val('').attr('selected', true);
                              $('#'+current_row_no+'>th>.bonus').val('');

                          }
                          total_values();

                          new_product = 0;
                          $('#myModal').modal('hide');
                          $('#loading').hide();
                          // checkInventory(); /*edited by Ibrahim 01-12-2019*/
                          return false;
                      }

                  } else {
                      pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();
                      
                      if(pro_val){
                          alert("Please select any product from last row or remove it!");
                      }else{
                          alert("Please select any product!");
                      }

                      product_change_flag = 0;
                      $('#'+current_row_no+'>th>div>select').val($('#'+current_row_no+'>th>.product_id_clone').val());
                      if($('#'+current_row_no+'>th>.product_rate').val() == ''){
                          $(this).val('').attr('selected', true);
                          $('#'+current_row_no+'>th>.bonus').val('');
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
              var total_val = product_box.find("th:nth-child(8) .total_value");
              var combined_product = product_box.find("th:nth-child(6) .combined_product");
              var combined_product_change = combined_product.val();

              
              if ($('#'+current_row_no+'>th>.product_id_clone').val() != '' && product_change_flag == 1) {

                  var product_id_for_change = $('#'+current_row_no+'>th>.product_id_clone').val();
                  
                  $.ajax({
                      url: '<?= BASE_URL . 'manages/delete_order' ?>',
                      'type': 'POST',
                      data: {combined_product: combined_product_change, product_id: product_id_for_change},
                      success: function (result) {
                          if (result == 'yes') {


                              /*-----------------------------------*/
                              var product_id_list_change = '';
                              $('.product_id').each(function () {
                                  if ($(this).val() != '') {
                                      product_id_list_change = $(this).val() + ',' + product_id_list_change;
                                  }
                              });
                              /*-----------------------------------*/

                              var combined_product_array = combined_product_change.split(',');
                              var product_id_list_pre_array = product_id_list_change.slice(0,-1);
                              var product_id_list_array = product_id_list_pre_array.split(',');

                              $.arrayIntersect = function(a, b)
                              {
                                  return $.grep(a, function(i)
                                  {
                                      return $.inArray(i, b) > -1;
                                  });
                              };
                              var arr_intersect = $.arrayIntersect(combined_product_array, product_id_list_array);
                              var product_id_new = arr_intersect[0];
                              var no_of_new_combined_product = arr_intersect.length;
                              var prev_combined_row_no = $('.prate-'+product_id_new).parent().parent().attr('id');
                              var min_qty_new = $('#'+prev_combined_row_no+'>th>.min_qty').val();
                              //console.log(no_of_new_combined_product);
                              //console.log(product_id_new);
                             // console.log(min_qty_new);
                              //var territory_id = $('.territory_id').val();
                              
                              /*$('.product_id').each(function(){
                                  $(this).trigger('change');
                              });*/
                              
                              if (no_of_new_combined_product > 0) {

                                  $.ajax({
                                      url: '<?= BASE_URL . 'manages/get_combine_or_individual_price' ?>',
                                      'type': 'POST',
                                      data: {combined_product: combined_product_change, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list_change},
                                      success: function (result) {

                                          var obj = jQuery.parseJSON(result);

                                          if (obj.unit_rate != '') {
                                              product_rate.val(obj.unit_rate);
                                          }
                                          if (obj.total_value) {
                                              total_value.val(obj.total_value);
                                          }

                                          $.each(obj, function (index, value) {
                                              var prate = $(".prate-" + index);
                                              var tvalue = $(".tvalue-" + index);
                                              prate.val(value.unit_rate);
                                              tvalue.val(value.total_value);
                                          });

                                          var gross_total = 0;
                                          $('.total_value').each(function () {
                                              if($(this).val() !=''){
                                                 gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                             }
                                         });
                                          $("#gross_value").val(gross_total.toFixed(2));

                                          $('#cash_collection').val('');
                                          var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

                                          if(product_category_id==32)
                                          {
                                            $('#'+current_row_no+'>th>.product_rate').val('0.00');
                                            $('.add_more').removeClass('disabled');
                                        }
                                    }
                                });
                              }
                              
                          }
                          
                      }
                  });
  }


              //product_rate.addClass('p_rate_'+product_id);
              //total_val.addClass('t_value_'+product_id);

              var rate_class = product_rate.attr('class').split(' ').pop();
              var value_class = total_val.attr('class').split(' ').pop();

              //console.log(rate_class);

              if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1)
              {
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
             if (new_product == 1) {
                  $.ajax({
                      url: '<?= BASE_URL . 'manages/get_product_unit' ?>',
                      'type': 'POST',
                      data: {product_id: product_id, territory_id: territory_id, product_id_list: product_id_list,outlet_id:outlet_id},
                      success: function (result) {
                          var obj = jQuery.parseJSON(result);
                          console.log(obj);
                          product_unit.val(obj.product_unit.name);
                          product_unit_id.val(obj.product_unit.id);
                          product_rate.val(obj.product_price.general_price);
                          product_price_id.val(obj.product_price.id);
                          product_qty.val(obj.product_combination.min_qty);
                          combined_product.val(obj.combined_product);

                          $('#'+current_row_no+'>th>.product_category_id').val(obj.product_category_id);
                          $('#'+current_row_no+'>th>.product_id_clone').val(product_id);

                          var total_qty = obj.total_qty;
                          var general_price = obj.product_price.general_price;
                          var min_qty = obj.product_combination.min_qty;                 
                          var total_value = parseFloat(general_price*min_qty);


                      //var product_id = $('#'+sl+'>th>div>#orderProductId').val();
                      //var a = '#'+sl+'>.tvalue-'+product_id;
                      //$('#'+current_row_no+'>th>.tvalue-'+product_id).val(total_value);
                      $('#'+current_row_no+'>th>.total_value').val(total_value);
                      $('#'+current_row_no+'>th>.aso_stock_qty').val(total_qty);
                      //console.log(current_row_no);
                      //$('#'+current_row_no+'>th>.min_qty').attr('max',total_qty);

                      $('.add_more').addClass('disabled');

                      var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

                      if(product_category_id==32)
                      {
                        $('#'+current_row_no+'>th>.product_rate').val('0.00');
                        $('.add_more').removeClass('disabled');
                        $('#loading').hide();
                        $('#myModal').modal('hide');
                    }
                    else
                    {
                        $('#'+current_row_no+'>th>.min_qty').trigger('keyup');
                    }

                    total_values();
                    console.log('hello Hunny bunny');
                    console.log(obj.bonus_product_qty);
                    if (obj.bonus_product_qty != undefined) {
                      $('#'+current_row_no+'>th>.bonus').val(obj.bonus_product_qty+'('+obj.bonus_product_name+')');
                      $('#'+current_row_no+'>th>.bonus_product_id').val(obj.bonus_product_id);
                      $('#'+current_row_no+'>th>.bonus_product_qty').val(obj.bonus_product_qty);
                      $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(obj.bonus_measurement_unit_id);
                  }

                  $('#cash_collection').val('');
                      //$('#loading').hide();
                      //$('#myModal').modal('hide');
                      //var get_product_unit_ajax_flag = 1;
                  }
              });
              }
  });

/*------- unset session -------*/
});
</script>
<script>
  
 function checkInventory(){
      var arrData=[];
      var w_store_id = $('.w_store_id').val();
       $(".product_table tr").each(function(){
            var currentRow=$(this);
            var product_id_value=currentRow.find(".product_id option:selected").val();
            var sales_qty=currentRow.find(".sales_qty").val();
            var obj={};
            obj.product_id=product_id_value;
            obj.sales_qty=sales_qty;
            arrData.push(obj);
       });
       
     //console.log(arrData);
      $.ajax({
          url: '<?= BASE_URL . 'manages/get_inventory_product_list' ?>',
          'type': 'POST',
          data: {products: arrData, store_id:w_store_id},
          success: function (result) {
              var obj = jQuery.parseJSON(result);
              var canPermitted = obj['canPermitted'];
              var msg = obj['msg'];
              //console.log(obj);
              if(msg != ""){
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

/*function checkInventory_byProduct(){

     
      var w_store_id = $('.w_store_id').val();
      var arrData=[];
      $('.product_row_box ').each(function () {
          arrData =$(this).find(".product_id option:selected").val();
      });
      console.log(arrData);
      
      $.ajax({
          url: '<?= BASE_URL . 'manages/get_inventory_product_list' ?>',
          'type': 'POST',
          data: {products: arrData, store_id:w_store_id},
          success: function (result) {
              var obj = jQuery.parseJSON(result);
              var canPermitted = obj['canPermitted'];
              if(canPermitted==0)
              {
                $(".can_permit").hide();
              }
              else
              {
                $(".can_permit").show(); 
              }
            }
    });
}*/

</script>
<script>

    /*--------- check combined or individual product price --------*/
    $("body").on("keyup", ".min_qty", function () {

       var current_row_no = $(this).parent().parent().attr('id');
       var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

       pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();

       if (product_category_id != 32 && pro_val) {

        var sl = $('.invoice_table>tbody>tr').length;
        //var current_row_no = $(this).parent().parent().attr('id');

        var product_box = $(this).parent().parent();
        var product_field = product_box.find("th:nth-child(2) .product_id");
        var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
        var product_rate = product_box.find("th:nth-child(4) .product_rate");
        var product_qty = product_box.find("th:nth-child(6) .min_qty");
        var total_value = product_box.find("th:nth-child(8) .total_value");
        var combined_product = product_box.find("th:nth-child(6) .combined_product");
        var combined_product = combined_product.val();
        var min_qty = product_qty.val();
        var id = product_field.val();
        var territory_id = $('.territory_id').val();      

        
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
            var product_id_list = '';
            $('.product_id').each(function () {
                if($(this).val() != ''){
                    product_id_list = $(this).val() + ',' + product_id_list;
                }
            });

            /*------------------------- Ajax Call----------------------*/

            var outlet_id = $('.distribut_outlet_id').val();
            var ajax_flag = $('#'+current_row_no+'>th>.ajax_flag').val();
            if(min_qty>0)
            {
                if(ajax_flag == 1)
                {
                  $.ajax({
                        url: '<?= BASE_URL . 'orders/get_product_unit' ?>',
                        'type': 'POST',
                        data: {product_id: id, territory_id: territory_id, product_id_list: product_id_list,outlet_id:outlet_id},
                        success: function (result) {
                            var obj = jQuery.parseJSON(result);

                        //var total_qty = obj.total_qty;
                        //var previous_qty = $('#'+current_row_no+'>th>.prev_min_qty').val();

                        //$('#'+current_row_no+'>th>.min_qty').attr('max',parseFloat(total_qty)+parseFloat(previous_qty));
                        //$('#'+current_row_no+'>th>.min_qty').trigger('keyup');
                        
                        $.ajax({
                            url: '<?= BASE_URL . 'orders/get_combine_or_individual_price' ?>',
                            'type': 'POST',
                            data: {combined_product: combined_product, min_qty: min_qty, product_id: id, product_id_list: product_id_list},
                            success: function (result) {
                                var obj = jQuery.parseJSON(result);
                                if (obj.unit_rate != '') {
                                    product_rate.val(obj.unit_rate);
                                }
                                if (obj.total_value) {
                                    total_value.val(obj.total_value);
                                }
                                $.each(obj, function (index, value) {
                                    var prate = $(".prate-" + index);
                                    var tvalue = $(".tvalue-" + index);
                                    prate.val(value.unit_rate);
                                    tvalue.val(value.total_value);
                                });

                                var gross_total = 0;
                                $('.total_value').each(function () {
                                    if($(this).val() !=''){
                                       gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                   }
                               });
                                $("#gross_value").val(gross_total.toFixed(2));
                                $('#cash_collection').val('');

                                $('.add_more').removeClass('disabled');

                                if (obj.mother_product_quantity != undefined) {
                                    var mother_product_quantity = obj.mother_product_quantity;
                                    var bonus_product_id = obj.bonus_product_id;
                                    var bonus_product_name = obj.bonus_product_name;
                                    var bonus_product_quantity = obj.bonus_product_quantity;
                                    var sales_measurement_unit_id = obj.sales_measurement_unit_id;
                                    var no_of_bonus_slap = mother_product_quantity.length;
                                    var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;

                                    for (var i = 0; i < no_of_bonus_slap; i++) {
                                        if (parseFloat($('#'+current_row_no+'>th>.min_qty').val())>=parseFloat(mother_product_quantity[i].min) && parseFloat($('#'+current_row_no+'>th>.min_qty').val())<=parseFloat(mother_product_quantity[i].max)) {
                                            if (i == 0) {
                                                $('#'+current_row_no+'>th>.bonus').val('N.A');
                                                $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                                                $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                                                $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                                            }else{
                                                $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i+(-1)]+'('+bonus_product_name[i+(-1)]+')');
                                                $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i+(-1)]);
                                                $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i+(-1)]);
                                                $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i+(-1)]);
                                            }
                                            break;
                                        }
                                        else{
                                            var current_qty = parseFloat($('#'+current_row_no+'>th>.min_qty').val());
                                            var bonus_qty = Math.floor(current_qty/parseFloat(mother_product_quantity_bonus));

                                            $('#'+current_row_no+'>th>.bonus').val(bonus_qty+' ('+bonus_product_name[i]+')');
                                            $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
                                            $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_qty);
                                            $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]); 
                                        }
                                    }  
                                }

                                $('#myModal').modal('hide');
                                $('#loading').hide();
                            }
                        });
}
});

}
else
{
    $.ajax({
      url: '<?= BASE_URL . 'orders/get_combine_or_individual_price' ?>',
      'type': 'POST',
      data: {combined_product: combined_product, min_qty: min_qty, product_id: id, product_id_list: product_id_list},
      success: function (result) 
      {
         var obj = jQuery.parseJSON(result);
         if (obj.unit_rate != '') {
            product_rate.val(obj.unit_rate);
        }
        if (obj.total_value) {
            total_value.val(obj.total_value);
        }
        $.each(obj, function (index, value) {
            var prate = $(".prate-" + index);
            var tvalue = $(".tvalue-" + index);
            prate.val(value.unit_rate);
            tvalue.val(value.total_value);
        });

        var gross_total = 0;
        $('.total_value').each(function () {
            if($(this).val() !=''){
               gross_total = parseFloat(gross_total) + parseFloat($(this).val());
           }
       });
        $("#gross_value").val(gross_total.toFixed(2));
        $('#cash_collection').val('');

        $('.add_more').removeClass('disabled');

        if (obj.mother_product_quantity != undefined) {
            var mother_product_quantity = obj.mother_product_quantity;
            var bonus_product_id = obj.bonus_product_id;
            var bonus_product_name = obj.bonus_product_name;
            var bonus_product_quantity = obj.bonus_product_quantity;
            var sales_measurement_unit_id = obj.sales_measurement_unit_id;
            var no_of_bonus_slap = mother_product_quantity.length;
            var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;

            for (var i = 0; i < no_of_bonus_slap; i++) {
               if (parseFloat($('#'+current_row_no+'>th>.min_qty').val())>=parseFloat(mother_product_quantity[i].min) && parseFloat($('#'+current_row_no+'>th>.min_qty').val())<=parseFloat(mother_product_quantity[i].max)) {
                  if (i == 0) {
                     $('#'+current_row_no+'>th>.bonus').val('N.A');
                     $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                     $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                     $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                 }else{
                     $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i+(-1)]+'('+bonus_product_name[i+(-1)]+')');
                     $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i+(-1)]);
                     $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i+(-1)]);
                     $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i+(-1)]);
                 }
                 break;
             }
             else{
              var current_qty = parseFloat($('#'+current_row_no+'>th>.min_qty').val());
              var bonus_qty = Math.floor(current_qty/parseFloat(mother_product_quantity_bonus));

              $('#'+current_row_no+'>th>.bonus').val(bonus_qty+' ('+bonus_product_name[i]+')');
              $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
              $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_qty);
              $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]); 
          }
      }  
  }

  $('#myModal').modal('hide');
  $('#loading').hide();
}
});
}
}
else
{
    alert('Please Enter Valid Quantity');
    $(product_qty).val($('#'+current_row_no+'>th>.prev_min_qty').val());
}

}, 1000 );

/*-----------------------------------*/


}else{
  $('.add_more').removeClass('disabled'); 
}


});

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();
</script>

<script>
    $(document).ready(function () {
        $('body').on('click', '.delete_item', function () {
          //checkInventory(); 
            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var combined_product = product_box.find("th:nth-child(6) .combined_product");
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
            if (product_field.val() == '') {
                product_box.remove();

                var last_row = $('.invoice_table tbody tr:last').attr('id');
                $('#'+last_row+'>th>.add_more').show();
                //console.log('if'+last_row);
                
                total_values();
            } else {
                $.ajax({
                    url: '<?= BASE_URL . 'manages/delete_order' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, product_id: id},
                    success: function (result) {
                        if (result == 'yes') {

                            product_box.remove();
                            //checkInventory();/*edited by Ibrahim 01-12-2019*/
                            //$('#dlt_product_id_class').removeClass('product_id');
                            var last_row = $('.invoice_table tbody tr:last').attr('id');
                            $('#'+last_row+'>th>.add_more').show();
                            //console.log('else'+last_row);

                            /*-----------------------------------*/
                            var product_id_list = '';
                            $('.product_id').each(function () {
                                if ($(this).val() != '') {
                                    product_id_list = $(this).val() + ',' + product_id_list;
                                }
                            });
                            /*-----------------------------------*/

                            var combined_product_array = combined_product.split(',');
                            var product_id_list_pre_array = product_id_list.slice(0,-1);
                            var product_id_list_array = product_id_list_pre_array.split(',');

                            $.arrayIntersect = function(a, b)
                            {
                                return $.grep(a, function(i)
                                {
                                    return $.inArray(i, b) > -1;
                                });
                            };
                            var arr_intersect = $.arrayIntersect(combined_product_array, product_id_list_array);
                            var product_id_new = arr_intersect[0];
                            var no_of_new_combined_product = arr_intersect.length;
                            var prev_combined_row_no = $('.prate-'+product_id_new).parent().parent().attr('id');
                            var min_qty_new = $('#'+prev_combined_row_no+'>th>.min_qty').val();
                            //console.log(no_of_new_combined_product);
                            //console.log(product_id_new);
                            //console.log(min_qty_new);
                            //var territory_id = $('.territory_id').val();
                            
                            /*$('.product_id').each(function(){
                                $(this).trigger('change');
                            });*/
                            
                            if (no_of_new_combined_product > 0) {

                                $.ajax({
                                    url: '<?= BASE_URL . 'manages/get_combine_or_individual_price' ?>',
                                    'type': 'POST',
                                    data: {combined_product: combined_product, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list},
                                    success: function (result) {

                                        var obj = jQuery.parseJSON(result);

                                        if (obj.unit_rate != '') {
                                            product_rate.val(obj.unit_rate);
                                        }
                                        if (obj.total_value) {
                                            total_value.val(obj.total_value);
                                        }

                                        $.each(obj, function (index, value) {
                                            var prate = $(".prate-" + index);
                                            var tvalue = $(".tvalue-" + index);
                                            prate.val(value.unit_rate);
                                            tvalue.val(value.total_value);
                                        });

                                        var gross_total = 0;
                                        $('.total_value').each(function () {
                                            if($(this).val() !=''){
                                               gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                           }
                                       });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val('');

                                    }
                                });
                            }
                            
                        }
                        var i = 1;
                        $('.new_row_number').each(function(){
                            $(this).attr('id',i);
                            $('#'+i+'>.sl_order').text(i++);
                        });
                    }
                });
                
}

           //checkInventory();

            /*var i = 1;
            $('.new_row_number').each(function(){
                $(this).attr('id',i++);
                $('#'+i+'>.sl_order').text(i++);
            });*/
            
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

/*For Adding Bonus Product list : START*/
$(document).ready(function(){
    $("body").on("click", ".bonus_add_more", function () {
        var product_list = $('#bonus_product_list').html();
        var product_bonus_row =
        '\
        <tr class="bonus_row">\
            <th class="text-center">'+product_list+'</th>\
            <th class="text-center" width="12%">\
                <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>\
                <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
                <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
                <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="number" min="0" name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required />\
                <input type="hidden" class="combined_product"/>\
            </th>\
            <th class="text-center" width="10%">\
                <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
                <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
            </th>\
        </tr>\
        ';
        var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
        if(product_id)
        {
            $(this).hide();
            $(".bonus_product").append(product_bonus_row);
            $(this).parent().parent().next().find('.open_bonus_product_id').val('');
        }
        else
        {
            alert('plese select product first');
        }
    });
    $("body").on("click", ".bonus_remove", function () {
        $(this).parent().parent().remove();
            // var total_tr= $(".bonus_row").length;
            $(".bonus_row").last().find('.bonus_add_more').show();
        });
    $("body").on("change",".open_bonus_product_id",function(){
        var product_id=$(this).val();
        var product_box=$(this).parent().parent().parent();  
        var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
        var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
        var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
        var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
        var territory_id = $('.territory_id').val();
        $.ajax({
            url: '<?= BASE_URL . 'manages/get_bonus_product_details' ?>',
            'type': 'POST',
             data: {product_id: product_id,territory_id:territory_id},
            success: function (result) 
            {
                var data = $.parseJSON(result);                  

                product_category_id.val(data.category_id);
                product_unit_name.val(data.measurement_unit_name);
                product_unit_id.val(data.measurement_unit_id);
                product_qty.val(1);
                product_qty.attr('min',1);
                product_qty.attr('max',data.total_qty);
            },
            error:function(error)
            {
                product_category_id.val();
                product_unit_name.val();
                product_unit_id.val();
                product_qty.val(0);
            }
        });
    });
    $("body").on("change","#territory_id",function(){
        var territory_id = $('#territory_id').val();    

     //   var sale_type_id = $("#sale_type_id option:selected").val(); 

    //    if(sale_type_id==3){
      //    territory_id = $("#spo_territory_id option:selected").val(); 
   //   }

    //alert(territory_id);
    
    if(territory_id){
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL . 'manages/get_bonus_product'?>',
            data: 'territory_id='+territory_id,
            cache: false, 
            success: function(response){
                var json = $.parseJSON(response);
                //console.log(json);
                $('.open_bonus_product_id option').remove();
                $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
                for (var i=0;i<json.length;++i)
                {
                    $('.open_bonus_product_id').append('<option value="'+json[i].Product.id+'">'+json[i].Product.name+'</option>');
                }
            }
        });     
    }
})
});
/*For Adding Bonus Product list : START*/
</script>
 <script>

    $("body").on("keyup", ".deliverd_qty", function () {
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
        var order_id= <?php echo $existing_record['Order']['id'];?>;
        var qty = remaining_qty;
        console.log(order_id);
         $.ajax({
            type: "POST",
            url: '<?= BASE_URL . 'manages/get_remaining_quantity'?>',
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
  function checkInventoryByProduct(id){
 var w_store_id = $('.w_store_id').val();
 $.ajax({
         url: '<?= BASE_URL . 'manages/get_inventory_by_product_id' ?>',
         'type': 'POST',
         data: {id: id, store_id:w_store_id},
         success: function (result) {
             var obj = jQuery.parseJSON(result);
             var canPermitted = obj['canPermitted'];
             var msg = obj['msg'];
             //console.log(obj);
             if(msg != ""){
               alert(msg);
             }
            
          }
   });
 } 
</script>
