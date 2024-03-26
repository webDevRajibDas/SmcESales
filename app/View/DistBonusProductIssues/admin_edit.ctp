<?php //pr($challan_info);die();
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary" style="float:left; width:100%; position:relative; padding-bottom:10px;">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Product Issue'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistChallan', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control', 'options' => $receiverStore, 'required' => true, 'default' => $challan_info['DistChallan']['receiver_dist_store_id'])); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('challan_date', array('label' => 'Product Issue Date', 'type' => 'text', 'class' => 'form-control challan-datepicker', 'required' => true, 'value' => $challan_info['DistChallan']['challan_date'], 'readonly' => true)); ?><span style="color:red">*</span>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('remarks', array('class' => 'form-control', 'value' => $challan_info['DistChallan']['remarks'])); ?>
                </div>
                <div class="form-group">
                    <!-- <div class="form-group">
                                <?php //echo $this->Form->input('carried_by', array('class' => 'form-control','value'=>$challan_info['DistChallan']['carried_by'])); 
                                ?>
                                </div>
                                <div class="form-group">
                                    <?php //echo $this->Form->input('truck_no', array('class' => 'form-control','value'=>$challan_info['DistChallan']['truck_no'])); 
                                    ?>
                                </div>
                                 <div class="form-group">
                                            <?php //echo $this->Form->input('driver_name', array('class' => 'form-control','value'=>$challan_info['DistChallan']['driver_name'])); 
                                            ?>
                                </div> -->
                    <?php echo $this->Form->input('inventory_status_id', array('id' => 'inventory_status_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => 1)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('transaction_type_id', array('id' => 'transaction_type_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => 2)); ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Batch No.</th>
                                <th class="text-center">Expire Date</th>
                                <th class="text-center">In Stock</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Source</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'full_width form-control product_id chosen', 'empty' => '---- Select Product ----')); ?>
                                </td>
                                <td width="15%" align="center">
                                    <?php echo $this->Form->input('batch_no', array('id' => 'batch_no', 'label' => false, 'type' => 'select', 'class' => 'full_width form-control batch_no', 'empty' => '---- Select Batch ----')); ?>
                                </td>
                                <td width="15%" align="center">
                                    <?php echo $this->Form->input('expire_date', array('id' => 'expire_date_BP', 'label' => false, 'type' => 'select', 'class' => 'full_width form-control expire_date', 'empty' => '---- Select Expire Date ----')); ?>
                                </td>
                                <td width="15%" align="center">
                                    <span class="product_qty"></span>
                                    <?php echo $this->Form->input('qty', array('type' => 'hidden', 'class' => 'qty')); ?>
                                </td>
                                <td width="12%" align="center">
                                    <?php echo $this->Form->input('challan_qty', array('label' => false, 'type' => 'text', 'class' => 'full_width form-control quantity')); ?>
                                </td>
                                <td width="12%" align="center">
                                    <?php echo $this->Form->input('source', array('label' => false, 'type' => 'text', 'class' => 'full_width form-control source', 'readonly')); ?>
                                </td>
                                <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-striped table-condensed table-bordered invoice_table">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">SL.</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center" width="12%">Batch No.</th>
                                <th class="text-center" width="12%">Expire Date</th>
                                <th class="text-center" width="12%">Unit</th>
                                <th class="text-center" width="12%">Quantity</th>
                                <th class="text-center" width="10%">In Stock</th>
                                <th class="text-center">Source</th>
                                <th class="text-center" width="15%">Remarks</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $display_serial_number = 1;
                            $row_count_for_remove = 1;
                            
                            foreach ($challan_info['DistChallanDetail'] as $value) {
                                $row_count_for_remove++;

                                if($value['virtual_product_id'] > 0){
                                    $value['product_id'] = $value['virtual_product_id'];
                                  }

                            ?>

                                <tr class="table_row" id="rowCount<?php echo $row_count_for_remove; ?>">
                                    <td align="center">
                                        <?php //echo $value['id']; 
                                        ?>
                                        <?php echo $display_serial_number; ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="product_id_batch_date[<?php echo $row_count_for_remove; ?>]" class="selected_product_id" value="<?php echo $value['product_id'];
                                                                                                                                                                    echo $value['batch_no'];
                                                                                                                                                                    echo $value['expire_date']; ?>" />
                                        <?php echo $value['Product']['name']; ?>
                                        <input type="hidden" name="product_id[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['product_id']; ?>" />
                                    </td>
                                    <td align="center">
                                        <input type="hidden" name="batch_no[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['batch_no']; ?>">
                                        <?php echo $value['batch_no']; ?>
                                    </td>
                                    <td align="center">
                                        <input type="hidden" name="expire_date[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['expire_date']; ?>">
                                        <?php if ($value['expire_date']) {
                                            echo $this->App->expire_dateformat($value['expire_date']);
                                        } else echo ''; ?>
                                    </td>
                                    <td align="center">
                                        <?php echo $value['MeasurementUnit']['name']; ?>
                                        <input type="hidden" name="measurement_unit[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['measurement_unit_id']; ?>">
                                    </td>
                                    <td align="center" id="<?php echo $row_count_for_remove; ?>">
                                        <input type="number" step="any" min="0" name="quantity[<?php echo $row_count_for_remove; ?>]" class="product_quantity" id="product_quantity<?php echo $row_count_for_remove; ?>" value="<?php echo $value['challan_qty']; ?>" required>
                                    </td>
                                    <td align="center" class="stock_qty" id="stock_qty<?php echo $row_count_for_remove; ?>">
                                        <?php echo $value['stock_qty']; ?>
                                    </td>
                                    <input type="hidden" name="product_qty[<?php echo $row_count_for_remove; ?>]" class="product_qty" value="<?php echo $value['stock_qty']; ?>" />
                                    <td align="center">
                                        <input type="text" class="product_source" readonly="readonly" class="full_width form-control" name="source[<?php echo $row_count_for_remove; ?>]" value="<?php echo $product_source[$value['product_id']]; ?>">
                                    </td>
                                    <td align="center">
                                        <input type="text" class="full_width form-control" name="remarks[<?php echo $row_count_for_remove; ?>]" value="<?php echo $value['remarks']; ?>">
                                    </td>
                                    <td align="center">
                                        <button class="btn btn-danger btn-xs remove" value="<?php echo $row_count_for_remove ?>"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>

                            <?php
                                $display_serial_number++;
                            }
                            ?>
                        </tbody>

                    </table>
                </div>

                </br>
                <div class="pull-right">
                    <?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
                    <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
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
$startDate = date('d-m-Y', strtotime('-1 day'));
?>
<script>
    /*Challan Datepicker : Start*/
    $(document).ready(function() {
        var today = new Date(new Date().setDate(new Date().getDate()));
        $('.challan-datepicker').datepicker({
            startDate: '<?php echo $startDate; ?>',
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true,
            endDate: today
        });
    });
    /*Challan Datepicker : End*/
    $(document).ready(function() {


        var is_maintain_batch = 1;
        var is_maintain_expire_date = 1;
        $(".chosen").chosen().change(function() {
            var product_id = $(this).val();
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>admin/products/product_details',
                data: 'product_id=' + product_id,
                cache: false,
                success: function(response) {
                    var obj = jQuery.parseJSON(response);
                    var source = obj.Product.source;
                    $('.source').val(source);

                    if (obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 0) {
                        is_maintain_batch = 0;
                        is_maintain_expire_date = 0;
                        $.ajax({
                            type: "POST",
                            url: '<?php echo BASE_URL; ?>current_inventories/get_inventory_details',
                            data: 'product_id=' + product_id + '&batch_no=' + '' + '&expire_date=' + '',
                            cache: false,
                            success: function(response1) {

                                $('.qty').val(response1);
                                $('.product_qty').html(response1);
                            }
                        });
                        $('.batch_no').val('');
                        $('.batch_no').attr('disabled', true);
                        $('.expire_date').val('');
                        $('.expire_date').attr('disabled', true);
                    } else if (obj.Product.maintain_batch == 1 && obj.Product.is_maintain_expire_date == 0) {
                        maintain_batch = 1;
                        is_maintain_expire_date = 0;
                        $('.batch_no').val('');
                        $('.batch_no').attr('disabled', false);
                        $('.expire_date').val('');
                        $('.expire_date').attr('disabled', true);
                        $('.qty').val('');
                        $('.product_qty').html('');
                    } else if (obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 1) {
                        is_maintain_batch = 0;
                        is_maintain_expire_date = 1;
                        $('.batch_no').val('');
                        $('.batch_no').attr('disabled', true);
                        $('.expire_date').val('');
                        $('.expire_date').attr('disabled', false);
                        $('.qty').val('');
                        $('.product_qty').html('');
                    } else {
                        is_maintain_batch = 1;
                        is_maintain_expire_date = 1;
                        $('.batch_no').val('');
                        $('.batch_no').attr('disabled', false);
                        $('.expire_date').val('');
                        $('.expire_date').attr('disabled', false);
                        $('.qty').val('');
                        $('.product_qty').html('');
                    }
                }
            });
        });

        $('#product_id').selectChain({
            target: $('#batch_no'),
            value: 'title',
            url: '<?= BASE_URL . 'current_inventories/get_batch_list'; ?>',
            type: 'post',
            data: {
                'product_id': 'product_id',
                'inventory_status_id': 'inventory_status_id',
                'with_stock': true
            }
        });

        $('#batch_no').change(function() {
            var product_id = $('.product_id').val();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();

            if (product_id == '') {
                alert('Please select any product.');
                return false;
            } else {
                if (is_maintain_expire_date == 0) {
                    $.ajax({
                        type: "POST",
                        url: '<?php echo BASE_URL; ?>current_inventories/get_inventory_details',
                        data: 'product_id=' + product_id + '&batch_no=' + batch_no + '&expire_date=' + expire_date,
                        cache: false,
                        success: function(response) {
                            $('.qty').val(response);
                            $('.product_qty').html(response);
                        }
                    });
                }
            }
        });

        $('#batch_no').selectChain({
            target: $('#expire_date_BP'),
            value: 'title',
            url: '<?= BASE_URL . 'current_inventories/get_expire_date_list'; ?>',
            type: 'post',
            data: {
                'product_id': 'product_id',
                'batch_no': 'batch_no',
                'inventory_status_id': 'inventory_status_id',
                'with_stock': true
            }
        });

        $('#expire_date_BP').change(function() {
            var product_id = $('.product_id').val();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();
            var inventory_status_id = $('#inventory_status_id').val();
            if (product_id == '') {
                alert('Please select any product.');
                return false;
            } else if (is_maintain_batch == 1 && batch_no == '') {
                alert('Please select any Batch.');
                return false;
            } else {

                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL; ?>current_inventories/get_inventory_details',
                    /*data: 'product_id=' + product_id + '&batch_no=' + batch_no + '&expire_date=' + expire_date + '&inventory_status_id=' + inventory_status_id,*/
                    data: {
                        'product_id': product_id,
                        'batch_no': batch_no,
                        'expire_date': expire_date,
                        'inventory_status_id': inventory_status_id
                    },
                    cache: false,
                    success: function(response) {
                        $('.qty').val(response);
                        $('.product_qty').html(response);
                    }
                });

            }
        });



        var rowCount = <?php echo $row_count_for_remove; ?>;
        //alert(rowCount);
        $(".add_more").click(function() {

            var product_qty = $('.qty').val();
            var product_id = $('.product_id').val();
            var quantity = $('.quantity').val();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();
            var stock_qty = $('.product_qty').text();
            var source = $('.source').val();

            var display_exp_date = expire_date_format(expire_date);
            //var selected_stock_id = $("input[name=selected_stock_id]").val();
            var selected_stock_array = $(".selected_product_id").map(function() {
                return $(this).val();
            }).get();
            var product_check_id = product_id + batch_no + expire_date;
            var stock_check = $.inArray(product_check_id, selected_stock_array) != -1;

            if (product_id == '') {
                alert('Please select any product.');
                return false;
            } else if (is_maintain_batch == 1 && batch_no == '') {
                alert('Please enter valid batch number.');
                return false;
            } else if (is_maintain_expire_date == 1 && expire_date == '') {
                alert('Please enter expire date.');
                return false;
            } else if (!quantity.match(/^(\d+\.?\d{1,2}|\d|\.\d{1,2})$/) || parseFloat(quantity) <= 0.00) {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $('.quantity').val('');
                return false;
            } else if (parseFloat(quantity) >= 10000000) {
                alert('Quantity must be less then equal 10000000.');
                $('.quantity').val('');
                return false;
            } else if (parseFloat(quantity) > parseFloat($('.qty').val())) {
                alert('Quantity should be less then equal Stock quantity.');
                $('.quantity').val('');
                return false;
            } else if (stock_check == true) {
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
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td>   <input type="hidden" name="selected_product_id[' + rowCount + ']" class="selected_product_id" value="' + obj.Product.id + batch_no + expire_date + '"/>' + obj.Product.name + '<input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id + '"/></td><td align="center"><input type="hidden" name="batch_no[' + rowCount + ']" value="' + batch_no + '">' + batch_no + '</td><td align="center"><input type="hidden" name="expire_date[' + rowCount + ']" value="' + expire_date + '">' + display_exp_date + '</td><td align="center">' + obj.ChallanMeasurementUnit.name + '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + obj.Product.challan_measurement_unit_id + '"></td><td align="center" id="' + rowCount + '"><input type="number" step="any" min="0" name="quantity[' + rowCount + ']" id="product_quantity' + rowCount + '" class="' + product_id + '_product_qty p_quantity product_quantity" value="' + quantity + '" required ></td><td align="center" id="stock_qty' + rowCount + '" class="stock_qty">' + product_qty + ' <input type="hidden" name="product_qty[' + rowCount + ']" class="product_qty" value="' + product_qty + '"/> </td><td  align="center"><input type="text" name="source[' + rowCount + ']" value="' + source + '" readonly></td><td align="center"><input type="text" class="full_width form-control" name="remarks[' + rowCount + ']"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('.invoice_table').append(recRow);
                        product_wise_total_quantity(product_id);
                        clear_field();

                        var total_quantity = set_total_quantity();
                        if (total_quantity > 0) {
                            //$('.draft').prop('disabled', false);
                            //$('.save').prop('disabled', false);
                            $('.draft').show();
                            $('.save').show();
                        }
                    }
                });

            }
        });


        $(document).on("click", ".remove", function() {
            var removeNum = $(this).val();


            var p_id = $("input[name~='product_id[" + removeNum + "]']").val();

            $('#rowCount' + removeNum).remove();

            product_wise_total_quantity(p_id);
            var total_quantity = set_total_quantity();
            if (total_quantity <= 0) {
                //$('.draft').prop('disabled', true);
                //$('.save').prop('disabled', true);
                $('.draft').hide();
                $('.save').hide();
            }
        });

        $(document).on("keyup", ".p_quantity", function() {
            var quantity = $(this).val();
            if (!$.isNumeric(quantity) || parseFloat(quantity) <= 0.00) {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $(this).val('');
            }
            var p_id = $(this).attr('class').split('_');
            product_wise_total_quantity(p_id[0]);
        });

        //$('.draft').prop('disabled', false);
        //$('.save').prop('disabled', false);

        function clear_field() {
            $('.product_id').val('');
            $('.quantity').val('');
            $('.batch_no').val('');
            $('.expire_date').val('');
            $('.product_qty').html('');
            $('.qty').val('');
            $('.chosen').val('').trigger('chosen:updated');
            $('.add_more').val('');
            $('.batch_no').attr('disabled', false);
            $('.expire_date').attr('disabled', false);
            $('.source').val('');
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
            var sum = 0.00;

            $('.' + product_id + '_product_qty').each(function() {
                var qty = $(this).val();
                sum += parseFloat(qty);
            });

            $('.' + product_id + '').html(sum.toFixed(2));
            //return sum;
        }

        $("form").submit(function() {
            //$('.draft').prop('disabled', true);
            //$('.save').prop('disabled', true);
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('.save').click(function() {
            var row_count = $('.invoice_table tbody tr:last').find('td:eq(5)').attr('id');
            for (i = 2; i <= row_count; i++) {
                var stock_qty = parseFloat($('#stock_qty' + i).text());
                var current_qty = parseFloat($('#product_quantity' + i).val());
                if (stock_qty < current_qty) {
                    alert('Quantity must be less than or equal to In Stock quantity');
                    $('.save').prop('disabled', true);
                    $("div#divLoading_default").removeClass('show');
                    return false;
                }
            }

        });
        $('body').on('keyup', '.product_quantity', function() {
            $('.save').prop('disabled', false);
        });
    });
</script>