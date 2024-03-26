<style>
  .width_100{
        width:100%;
    }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
      -webkit-appearance: none; 
      margin: 0; 
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Create Memo'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('Memo', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php
                    if($office_parent_id==0){
                         echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','empty' => '---- Select Office ----'));
                    }
                    else
                    {
                         echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id'));
                    }
                    ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('sale_type_id', array('class' => 'form-control', 'id' => 'sale_type_id', 'options' => $sale_type_list)); ?>
                </div>
                <div class="form-group" id="territory_id_div">
                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => TRUE, 'empty' => '---- Select Territory ----', 'options' => $territories)); ?>
                </div>
                <div class="form-group"  id="market_id_so">
                    <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'empty' => '---- Select Market ----')); ?>
                </div>

                <div class="form-group"  id="market_id_csa">
                    <?php echo $this->Form->input('market', array('type'=>'text', 'id' => 'market_name', 'class' => 'form-control market_name', 'required'=>TRUE)); ?>
                </div>
                
                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => TRUE, 'empty' => '---- Select Outlet ----', 'options' => $outlets)); ?>
                </div>

                <div class="form-group"  id="outlet_id_csa">
                    <?php echo $this->Form->input('outlet', array('type'=>'text', 'id' => 'outlet_name', 'class' => 'form-control outlet_name', 'required'=>TRUE)); ?>
                </div>

                <div class="form-group csa_name">
                    <?php echo $this->Form->input('csa_name', array('label'=>'CSA Name :', 'id' => 'csa_name', 'class' => 'form-control', 'required'=>TRUE)); ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('entry_date', array('class' => 'form-control datepicker', 'value' => (isset($this->request->data['Memo']['date_from']) == '' ? $current_date : $this->request->data['Memo']['date_from']), 'required' => TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker', 'value'=>$current_date, 'type' => 'text', 'required'=>TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no','required'=>TRUE,'type' => 'text')); ?>
                </div>
                <!--Set Product area-->
                <table class="table table-striped table-condensed table-bordered invoice_table">
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
                        <tr id="1" class="new_row_number">
                            <th class="text-center sl_memo" width="5%">1</th>
                            <th class="text-center">
                                <?php
                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 product_id', 'empty' => '---- Select Product ----', 'label' => false, 'required' => true));
                                ?>
                                <input type="hidden" id="product_category_id" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id"/>
                            </th>
                            <th class="text-center" width="12%">
                                <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>
                                <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>
                            </th>
                            <th class="text-center" width="12%">
                                <input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly />
                                <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/>
                            </th>
                            <th class="text-center" width="12%">
                                <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty"/>
                                <input type="hidden" class="combined_product"/>
                            </th>
                            <th class="text-center" width="12%">
                                <input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly />
                            </th>
                            <th class="text-center" width="10%">
                                <input type="text" class="form-control width_100 bonus" disabled />
                                <input type="hidden" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                                <input type="hidden" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                                <input type="hidden" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>
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
                            <td colspan="5" align="right"><b>Total : </b></td>
                            <td align="center"><input name="data[Memo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><b>Cash Collection : </b></td>
                            <td align="center"><input name="data[Memo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" required />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><b>Credit : </b></td>
                            <td align="center"><input name="data[Memo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" readonly />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <?php echo $this->Form->submit('Submit', array('class' => 'submit btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>

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
    $(document).ready(function () {
        $('.office_id').selectChain({
            target: $('.territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

        $('.territory_id').selectChain({
            target: $('.market_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });

		$('.territory_id').selectChain({
            target: $('.product_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/Memos/get_product'; ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });
        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

        $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');           
        });

        $('.territory_id').change(function () {
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });

      /* temporary commented */
      /*
        $("body").on("change", "#sales_person_id", function () {
            var sales_person_id = $(this).val();
            $.ajax({
                url: '<?= BASE_URL . 'memos/get_territory_id' ?>',
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
            $('#csa_name').attr('required',false);
        }
        $('#sale_type_id').change(function(){
            if ($('#sale_type_id').val() == 1) {
                $('#territory_id_div').show();
                $('#market_id_so').show();
                $('#outlet_id_so').show();
                $('#market_id_csa').hide();
                $('#outlet_id_csa').hide();
                $('.csa_name').hide();
                $('#market_name').attr('required',false);
                $('#outlet_name').attr('required',false);
                $('#csa_name').attr('required',false);
            }
            if ($('#sale_type_id').val() == 2) {
                $('#territory_id_div').hide();
                $('#market_id_so').hide();
                $('#outlet_id_so').hide();
                $('#market_id_csa').show();
                $('#outlet_id_csa').show();
                $('.csa_name').show();
                $('#territory_id').attr('required',false);
                $('#market_id').attr('required',false);
                $('#outlet_id').attr('required',false);
            }
        });
    });
</script>
<script>

    function total_values(){
       var t = 0;
       $('.total_value').each(function(){
           t += parseFloat($(this).val());
         });
       $('#gross_value').val(t);
    } 
 
    $(document).ready(function () {
        $("body").on("click", ".add_more", function () {
            var sl = $('.invoice_table>tbody>tr').length+1;

            var product_box = $(this).parent().parent().parent();
            var current_row = $(this).parent().parent().html();
            product_box.append('<tr id='+sl+' class=new_row_number>' + current_row + '</tr>');
            $('#'+sl+'>.sl_memo').text(sl);
        });
        $("body").on("change", ".product_id", function () {
            var new_product = 1;
            $('#myModal').modal('show');
            $('#loading').show();
            //console.log($('#'+sl+'>th>.bonus').val());
            $('#gross_value').val(0);
            /*----- make array with product list -------*/
            var sl = $('.invoice_table>tbody>tr').length;

            var current_row_no = $(this).parent().parent().parent().attr('id');

            $('#'+current_row_no+'>th>.bonus').val('N.A');
            $('#'+current_row_no+'>th>.bonus_product_id').val(0);
            $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
            $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
            
            //console.log(a);
            var product_id_list = '';
            $('.product_id').each(function () {
                if ($(this).val() != '') {
                    //product_id_list = $(this).val()+','+product_id_list;
                    if (product_id_list.search($(this).val()) == -1) {
                        product_id_list = $(this).val() + ',' + product_id_list;
                    } else {
                        alert("This poduct already exists");
                        $(this).val('').attr('selected', true);
                        new_product = 0;
                        $('#myModal').modal('hide');
                        $('#loading').hide();
                    }

                } else {
                    alert("Please select a product");
                    return false;                
                }
            });
            var product_id = $(this).val();
            var product_box = $(this).parent().parent().parent();
            var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
            var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            var total_val = product_box.find("th:nth-child(6) .total_value");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");

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

            var territory_id = $('.territory_id').val();
            if (new_product == 1) {
            $.ajax({
                url: '<?= BASE_URL . 'memos/get_product_unit' ?>',
                'type': 'POST',
                data: {product_id: product_id, territory_id: territory_id, product_id_list: product_id_list},
                success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    product_unit.val(obj.product_unit.name);
                    product_unit_id.val(obj.product_unit.id);
                    product_rate.val(obj.product_price.general_price);
                    product_price_id.val(obj.product_price.id);
                    product_qty.val(obj.product_combination.min_qty);
                    combined_product.val(obj.combined_product);

                    //$('#product_category_id').val(obj.product_category_id);
					$('#'+current_row_no+'>th>.product_category_id').val(obj.product_category_id);

                    var total_qty = obj.total_qty;
                    var general_price = obj.product_price.general_price;
                    var min_qty = obj.product_combination.min_qty;                 
                    var total_value = parseFloat(general_price*min_qty);

                    //var product_id = $('#'+sl+'>th>div>#MemoProductId').val();
                    //var a = '#'+sl+'>.tvalue-'+product_id;
                    //$('#'+current_row_no+'>th>.tvalue-'+product_id).val(total_value);
                    $('#'+current_row_no+'>th>.total_value').val(total_value);
                    //console.log(current_row_no);
                    $('#'+current_row_no+'>th>.min_qty').attr('max',total_qty);
					$('#'+current_row_no+'>th>.min_qty').trigger('keyup');

                    total_values();

                    if (obj.bonus_product_qty != undefined) {
                        $('#'+current_row_no+'>th>.bonus').val(obj.bonus_product_qty+'('+obj.bonus_product_name+')');
                        $('#'+current_row_no+'>th>.bonus_product_id').val(obj.bonus_product_id);
                        $('#'+current_row_no+'>th>.bonus_product_qty').val(obj.bonus_product_qty);
                        $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(obj.bonus_measurement_unit_id);
                    }

                    $('#loading').hide();
                    $('#myModal').modal('hide');
                }
            });
        }
   
        });
        
        /*------- unset session -------*/
    });
</script>
<script>
    /*--------- check combined or individual product price --------*/
    $("body").on("keyup", ".min_qty", function () {

        var current_row_no = $(this).parent().parent().attr('id');
        //var product_category_id = $('#product_category_id').val();
        var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();
    if (product_category_id != 32) {
        var sl = $('.invoice_table>tbody>tr').length;

        var product_box = $(this).parent().parent();
        var product_field = product_box.find("th:nth-child(2) .product_id");
        var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
        var product_rate = product_box.find("th:nth-child(4) .product_rate");
        var product_qty = product_box.find("th:nth-child(5) .min_qty");
        var total_value = product_box.find("th:nth-child(6) .total_value");
        var combined_product = product_box.find("th:nth-child(5) .combined_product");
        var combined_product = combined_product.val();
        var min_qty = product_qty.val();
        var id = product_field.val();
        
        /*-----------------------------------*/
        var product_id_list = '';
        $('.product_id').each(function () {

            product_id_list = $(this).val() + ',' + product_id_list;
        });
        /*-----------------------------------*/
        $.ajax({
            url: '<?= BASE_URL . 'memos/get_combine_or_individual_price' ?>',
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
                    gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                });
                $("#gross_value").val(gross_total.toFixed(2));

                if (obj.mother_product_quantity != undefined) {
                    var mother_product_quantity = obj.mother_product_quantity;
                    var bonus_product_id = obj.bonus_product_id;
                    var bonus_product_name = obj.bonus_product_name;
                    var bonus_product_quantity = obj.bonus_product_quantity;
                    var sales_measurement_unit_id = obj.sales_measurement_unit_id;
                    var no_of_bonus_slap = mother_product_quantity.length;

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
                            $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i]+'('+bonus_product_name[i]+')');
                            $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
                            $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i]);
                            $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]); 
                        }
                    }  
                }
                $('#cash_collection').val('');
            }
        });
    }
    });
</script>
<script>

    $(document).ready(function () {
        $('body').on('click', '.delete_item', function () {
            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            combined_product = combined_product.val();
            var id = product_field.val();

            var total_value = $('.tvalue-'+id).val();
            var gross_total = $('#gross_value').val();
            var new_gross_value = parseFloat(gross_total-total_value);
            $('#gross_value').val(new_gross_value);
            $('#cash_collection').val('');

            var min_qty = product_qty.val();
            if (product_field.val() == '') {
                console.log('if');
                product_box.remove();
                alert('Removed this row -------');
                total_values();
				var product_row_count = $('.invoice_table tbody tr').length;
                if(product_row_count == 0){
                    $('.submit').prop('disabled',true);
                }
            } else {
                console.log('else');
                $.ajax({
                    url: '<?= BASE_URL . 'memos/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, product_id: id},
                    success: function (result) {
                        if (result == 'yes') {

                            product_box.remove();

                            /*-----------------------------------*/
                            var product_id_list = '';
                            $('.product_id').each(function () {

                                product_id_list = $(this).val() + ',' + product_id_list;
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

                            if (no_of_new_combined_product > 0) {
                                $.ajax({
                                url: '<?= BASE_URL . 'memos/get_combine_or_individual_price' ?>',
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
                                        gross_total = parseFloat(gross_total) + parseFloat($(this).val());
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
                            $('#'+i+'>.sl_memo').text(i++);
                        });
                    }
                });
            }

            var i = 1;
            $('.new_row_number').each(function(){
                $(this).attr('id',i++);
                $('#'+i+'>.sl_memo').text(i++);
            });
			var product_row_count = $('.invoice_table tbody tr').length;
            if(product_row_count == 1){
                $('.submit').prop('disabled',true);
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
</script>
<script>
    $(document).ready(function(){
        $('body').on("keyup", ".memo_no", function(){
            var memo_no = $('.memo_no').val();
            
            $.ajax({
                url: '<?php echo BASE_URL.'admin/memos/memo_no_validation' ?>',
                'type': 'POST',
                data: {memo_no: memo_no},
                success: function(result){
                    obj = jQuery.parseJSON(result);
                    if(obj == 1){
                        alert('Memo Number Already Exist');
                        $('.submit').prop('disabled', true);
                    }
                    if(obj == 0){
                        $('.submit').prop('disabled', false);
                    }
                }
            });
        });
    });
</script>
