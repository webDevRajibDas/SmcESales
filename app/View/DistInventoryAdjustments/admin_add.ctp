<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Add Distributor Inventory Adjustment (Base Unit)'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Inventory Adjustment List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('InventoryAdjustment', array('role' => 'form')); ?>

                <div class="form-group">
                    <?php
                    if ($office_parent_id == 0) {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                    } else {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                    }
                    ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id', 'empty' => '--- Select Distributor ---', 'required' => 'required')); ?>
                </div>


                <div class="form-group">
                    <?php echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'empty' => '---- Select Status ----', 'options' => $transaction_types)); ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('remarks', array('type' => 'textarea', 'class' => 'form-control')); ?>
                </div>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <!-- 
                            <th class="text-center">Inventory Status</th>
                            <th class="text-center">Batch No.</th>
                            <th class="text-center">Expire Date</th>
                            -->
                            <th class="text-center">Stock Quantity</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Bonus Quantity</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'type' => 'select',  'label' => false, 'class' => 'full_width form-control product_id chosen2', 'empty' => '---- Select Product ----')); ?>
                                <?php echo $this->Form->input('inventory_status_id', array('type' => 'hidden', 'id' => 'inventory_status_id', 'class' => 'inventory_status_id', 'label' => false, 'value' => 1)); ?>
                                <?php echo $this->Form->input('batch_no', array('type' => 'hidden', 'id' => 'batch_no', 'class' => 'batch_no', 'label' => false, 'value' => "")); ?>
                                <?php echo $this->Form->input('expire_date', array('type' => 'hidden', 'id' => 'expire_date', 'class' => 'expire_date', 'label' => false, 'value' => "")); ?>

                            </td>
                            <td width="15%" align="center">
                                <span class="product_qty"></span>
                                <?php echo $this->Form->input('qty', array('type' => 'hidden', 'class' => 'qty')); ?>
                            </td>
                            <td width="12%" align="center">
                                <?php echo $this->Form->input('challan_qty', array('label' => false, 'type' => 'number', 'class' => 'full_width form-control quantity')); ?>

                            </td>
                            <td width="12%" align="center">
                                <?php echo $this->Form->input('bonus_qty', array('label' => false, 'type' => 'number', 'class' => 'full_width form-control b_quantity')); ?>
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
                            <!-- 
                            <th class="text-center" width="12%">Inventory Status</th>
                            <th class="text-center" width="12%">Batch No.</th>
                            <th class="text-center" width="12%">Expire Date</th>
                            -->
                            <th class="text-center" width="12%">Unit</th>
                            <th class="text-center" width="12%">Quantity</th>
                            <th class="text-center" width="12%">Bonus Quantity</th>
                            <th class="text-center" width="10%">Action</th>
                        </tr>
                    </thead>
                    <!-- 
                    <tfoot>
                            <tr>
                                    <td colspan="7" align="right"><b>Total Quantity : </b></td>
                                    <td align="center"><b><span class="total_quantity"></span></b></td>
                                    <td></td>
                            </tr>																
                    </tfoot>
                    -->
                </table>
                </br>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var transaction_type_with_inout = <?php echo json_encode($transaction_typeid_with_inout) ?>;
        $("#office_id").prop("selectedIndex", 0);

        $(".office_id").change(function() {
            get_dist_by_office_id($(this).val());
        });

        $("#distributor_id").change(function() {
            get_product_list_from_dist_id($(this).val());
        });




        var is_maintain_batch = 0;
        var is_maintain_expire_date = 0;
        $(".chosen2").change(function() {
            var product_id = $(this).val();
            var db_id = $("#distributor_id").val();
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>admin/products/product_details',
                data: 'product_id=' + product_id,
                cache: false,
                success: function(response) {
                    var obj = jQuery.parseJSON(response);
                    var is_maintain_batch = 0;
                    var is_maintain_expire_date = 0;
                    if (is_maintain_batch == 0 && is_maintain_expire_date == 0) {
                        is_maintain_batch = 0;
                        is_maintain_expire_date = 0;

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
                        is_maintain_batch = 0;
                        is_maintain_expire_date = 0;
                        $('.batch_no').val('');
                        $('.batch_no').attr('disabled', false);
                        $('.expire_date').val('');
                        $('.expire_date').attr('disabled', false);
                        $('.qty').val('');
                        $('.product_qty').html('');
                    }
                }
            });
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>dist_inventory_adjustments/get_inventory_details',
                data: 'product_id=' + product_id + '&db_id=' + db_id,
                cache: false,
                success: function(response) {
                    $('.qty').val(response);
                    $('.product_qty').html(response);
                }
            });
        });


        var rowCount = 1;
        $(".add_more").click(function() {
            var inout = transaction_type_with_inout[$("#InventoryAdjustmentStatus").val()];
            var product_id = $('.product_id').val();
            var quantity = $('.quantity').val();
            var b_quantity = $('.b_quantity').val();
            var inventory_status_id = $('.inventory_status_id').val();
            var inventory_status_text = $('.inventory_status_id option:selected').text();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();

            var selected_stock_array = $(".selected_product_id").map(function() {
                return $(this).val();
            }).get();
            // var product_check_id = product_id + inventory_status_id + batch_no + expire_date;
            var product_check_id = product_id;
            var stock_check = $.inArray(product_check_id, selected_stock_array) != -1;

            //console.log("stock check " + (parseFloat(quantity) + parseFloat(b_quantity)) + "----" + parseFloat($('.qty').val()));

            if (product_id == '') {
                alert('Please select any product.');
                return false;
            } else if (is_maintain_batch == 1 && batch_no == '') {
                alert('Please enter valid batch number.');
                return false;
            } else if (is_maintain_expire_date == 1 && expire_date == '') {
                alert('Please enter expire date.');
                return false;
            } else if (!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) < 0.00) {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $('.quantity').val('');
                return false;
            } else if (!b_quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(b_quantity) < 0.00) {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $('.b_quantity').val('');
                return false;
            } else if (inout == 1 && (parseFloat(quantity) + parseFloat(b_quantity)) > parseFloat($('.qty').val())) {
                alert('Quantity should be less then equal Stock quantity.');
                $('.quantity').val('');
                $('.b_quantity').val('');
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
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td><input type="hidden" name="selected_product_id[]" class="selected_product_id" value="' + obj.Product.id + '"/>' + obj.Product.name + '<input type="hidden" name="product_id[]" value="' + obj.Product.id + '"/></td><td align="center">' + obj.BaseMeasurementUnit.name + '</td><td align="center"><input type="hidden" name="quantity[]" class="p_quantity" value="' + quantity + '">' + quantity + '</td><td align="center"><input type="hidden" name="bonus_quantity[]" class="p_b_quantity" value="' + b_quantity + '">' + b_quantity + '</td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('.invoice_table').append(recRow);
                        clear_field();

                        var total_quantity = set_total_quantity();
                        if (total_quantity > 0) {
                            $('.submit').prop('disabled', false);
                        }
                    }
                });

            }
        });


        $(document).on("click", ".remove", function() {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
            var total_quantity = set_total_quantity();
            if (total_quantity < 1) {
                $('.submit').prop('disabled', true);
            }
        });


        $('.submit').prop('disabled', true);

        function clear_field() {
            $('.product_id').val('');
            $('.quantity').val('');
            $('.b_quantity').val('');
            $('.batch_no').val('');
            $('.inventory_status_id').val('');
            $('.expire_date').val('');
            $('.product_qty').html('');
            $('.qty').val('');
            // $('.chosen').val('').trigger('chosen:updated');
            $('.chosen2').val('');
            $('.add_more').val('');
            $('.batch_no').attr('disabled', false);
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

            $('.total_quantity').html(sum.toFixed(2));
            return sum;
        }

        function get_dist_by_office_id(office_id) {

            $.ajax({
                url: '<?= BASE_URL . 'DistMemos/get_dist_list_by_office_id_for_adjustment' ?>',
                data: {
                    'office_id': office_id
                },
                type: 'POST',
                success: function(data) {
                    $("#distributor_id").html(data);
                }
            });
        }

        function get_product_list_from_dist_id(distributor_id) {

            $.ajax({
                url: '<?= BASE_URL . 'DistInventoryAdjustments/get_product_list_from_dist_id' ?>',
                data: {
                    'distributor_id': distributor_id
                },
                type: 'POST',
                success: function(data) {
                    $("#product_id").html(data);
                }
            });

        }

    });
</script>