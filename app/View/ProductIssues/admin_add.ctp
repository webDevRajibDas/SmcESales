<style>
    .col-md-7 label {
        width: 40%;
    }

    .col-md-7 .form-control {
        width: 55%;
    }

    #challan_meg {
        display: none;
    }

    #challan_meg {
        background: #4043a0;
        padding: 10px 20px;
        color: #fff;
        font-size: 15px;
        width: 70%;
        border-radius: 5px;
    }

    #challan_meg:hover {
        background: #0073b7;
        cursor: pointer;
        text-decoration: underline;
    }

    #challan_meg a {
        color: #fff;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary" style="float:left; width:100%; position:relative; padding-bottom:10px;">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('New Product Issue'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Issue List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>


            <div class="box-body">
                <?php echo $this->Form->create('Challan', array('role' => 'form')); ?>

                <div class="col-md-7">

                    <div class="form-group">
                        <?php echo $this->Form->input('receiver_store_id', array('id' => 'receiver_store_id', 'class' => 'form-control', 'empty' => '---- Select Receiver ----', 'onChange' => 'getPreviousChallan()', 'options' => $receiverStore, 'required' => true)); ?>
                        <?php echo $this->Form->input('receiver_store_id', array('id' => 'receiver_store_id_final', 'class' => 'form-control', 'required' => TRUE, 'type' => 'hidden')); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('challan_date', array('id' => 'challan_date', 'label' => 'Product Issue Date', 'onChange' => 'getPreviousChallan()', 'type' => 'text', 'class' => 'form-control challan-datepicker', 'required' => true, 'readonly' => true)); ?>
                        <span style="color:red">*</span>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('carried_by', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('truck_no', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('driver_name', array('class' => 'form-control')); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('inventory_status_id', array('id' => 'inventory_status_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => 1)); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('transaction_type_id', array('id' => 'transaction_type_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => 2)); ?>
                    </div>

                </div>


                <div class="col-md-5">
                    <div id="challan_meg">

                    </div>
                </div>

                <div class="clear"></div>

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
                        <!-- <td>
                                <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'full_width form-control product_id ', 'empty' => '---- Select Product ----')); ?>
                            </td> -->
                        <td width="15%" align="center">
                            <?php echo $this->Form->input('batch_no', array('id' => 'batch_no', 'label' => false, 'type' => 'select', 'class' => 'full_width form-control batch_no', 'empty' => '---- Select Batch ----')); ?>
                        </td>
                        <td width="15%" align="center">
                            <?php echo $this->Form->input('expire_date', array('id' => 'expire_date', 'label' => false, 'type' => 'select', 'class' => 'full_width form-control expire_date', 'empty' => '---- Select Expire Date ----')); ?>
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
                        <td width="10%" align="center"><span
                                    class="btn btn-xs btn-primary add_more"> Add Product </span></td>
                    </tr>
                    </tbody>
                </table>
                <table id="invoice_table" class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                    <tr>
                        <th class="text-center" width="5%">SL.</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center" width="12%">Batch No.</th>
                        <th class="text-center" width="12%">Expire Date</th>
                        <th class="text-center" width="12%">Unit</th>
                        <th class="text-center" width="12%">Quantity</th>
                        <th class="text-center" width="10%">Total Quantity</th>
                        <th class="text-center">Source</th>
                        <th class="text-center" width="15%">Remarks</th>
                        <th class="text-center" width="10%">Action</th>
                    </tr>
                    </thead>
                </table>


                </br>

                <div class="pull-right">
                    <?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div' => false, 'name' => 'save', 'disabled')); ?>
                    <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
                </div>
                
                <?php echo $this->Form->end(); ?>

            </div>


        </div>
    </div>
</div>
<style>
    .datepicker table tr td.disabled, .datepicker table tr td.disabled:hover {
        color: #c7c7c7;
    }
</style>
<?php
$startDate = date('d-m-Y', strtotime('-1 day'));
?>
<script>
    /*Challan Datepicker : Start*/
    $(document).ready(function () {
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
    $(document).ready(function () {

        //Disable Receiver Store if Add Product

        function disableReceiverStore() {
            var invoice_row = document.getElementById("invoice_table").rows.length;

            if (invoice_row > 1) {

                var receiver_store_id = $('#receiver_store_id').val();
                $('#receiver_store_id_final').val(receiver_store_id);

                $("#receiver_store_id").prop('disabled', 'disabled');


            } else {
                $("#receiver_store_id").removeAttr("disabled");

            }

        }


        $('#receiver_store_id').selectChain({
            target: $('#product_id'),
            value: 'name',
            url: '<?= BASE_URL . 'product_issues/get_territory_wise_group'?>',
            type: 'post',
            data: {'store_id': 'receiver_store_id'},
            afterSuccess: function () {
                $(".chosen").val('').trigger("chosen:updated");
            }
        });

        var is_maintain_batch = 1;
        var is_maintain_expire_date = 1;
        $(".chosen").chosen().change(function () {
            var product_id = $(this).val();
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL;?>admin/products/product_details',
                data: 'product_id=' + product_id,
                cache: false,
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    var source = obj.Product.source;
                    $('.source').val(source);
                    if (obj.Product.maintain_batch == 0 && obj.Product.is_maintain_expire_date == 0) {
                        is_maintain_batch = 0;
                        is_maintain_expire_date = 0;
                        $.ajax({
                            type: "POST",
                            url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
                            data: 'product_id=' + product_id + '&batch_no=' + '' + '&expire_date=' + '',
                            cache: false,
                            success: function (response1) {
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
            url: '<?= BASE_URL . 'current_inventories/get_batch_list';?>',
            type: 'post',
            data: {'product_id': 'product_id', 'inventory_status_id': 'inventory_status_id', 'with_stock': true}
        });

        $('#batch_no').change(function () {
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
                        url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
                        data: 'product_id=' + product_id + '&batch_no=' + batch_no + '&expire_date=' + expire_date,
                        cache: false,
                        success: function (response) {
                            $('.qty').val(response);
                            $('.product_qty').html(response);
                        }
                    });
                }
            }
        });

        $('#batch_no').selectChain({
            target: $('#expire_date'),
            value: 'title',
            url: '<?= BASE_URL . 'current_inventories/get_expire_date_list';?>',
            type: 'post',
            data: {
                'product_id': 'product_id',
                'batch_no': 'batch_no',
                'inventory_status_id': 'inventory_status_id',
                'with_stock': true
            }
        });

        $('#expire_date').change(function () {
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
                    url: '<?php echo BASE_URL;?>current_inventories/get_inventory_details',
                    data: {
                        'product_id': product_id,
                        'batch_no': batch_no,
                        'expire_date': expire_date,
                        'inventory_status_id': inventory_status_id
                    },
                    cache: false,
                    success: function (response) {
                        $('.qty').val(response);
                        $('.product_qty').html(response);
                    }
                });

            }
        });


        var rowCount = 1;
        $(".add_more").click(function () {

            var product_qty = $('.qty').val();
            var product_id = $('.product_id').val();
            var pname = $(".product_id").find("option:selected").text();
            // alert(pname);
            var quantity = $('.quantity').val();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();
            var display_exp_date = expire_date_format(expire_date);
            var source = $('.source').val();
            console.log(source);
            //var selected_stock_id = $("input[name=selected_stock_id]").val();
            var selected_stock_array = $(".selected_product_id").map(function () {
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
                    url: '<?php echo BASE_URL;?>admin/products/product_details',
                    data: 'product_id=' + product_id,
                    cache: false,
                    success: function (response) {

                        var obj = jQuery.parseJSON(response);
                        console.log(obj);
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td> <input type="hidden" name="product_qty[' + rowCount + ']" class="product_qty" value="' + product_qty + '"/> <input type="hidden" name="selected_product_id[' + rowCount + ']" class="selected_product_id" value="' + obj.Product.id + batch_no + expire_date + '"/>' + pname + '<input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id + '"/></td><td align="center"><input type="hidden" name="batch_no[' + rowCount + ']" value="' + batch_no + '">' + batch_no + '</td><td align="center"><input type="hidden" name="expire_date[' + rowCount + ']" value="' + expire_date + '">' + display_exp_date + '</td><td align="center">' + obj.ChallanMeasurementUnit.name + '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + obj.Product.challan_measurement_unit_id + '"></td><td align="center"><input name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty p_quantity" value="' + quantity + '" required ></td><td align="center" class="' + product_id + '"></td><td  align="center"><input type="text" name="source[' + rowCount + ']" value="' + source + '" readonly></td><td align="center"><input type="text" class="full_width form-control" name="remarks[' + rowCount + ']"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('.invoice_table').append(recRow);
                        product_wise_total_quantity(product_id);
                        clear_field();

                        var total_quantity = set_total_quantity();
                        if (total_quantity > 0) {
                            $('.draft').prop('disabled', false);
                        }
                        //call Disavle Store
                        disableReceiverStore();

                    }
                });

            }
        });


        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            var p_id = $("input[name~='product_id[" + removeNum + "]']").val();
            $('#rowCount' + removeNum).remove();
            product_wise_total_quantity(p_id);
            var total_quantity = set_total_quantity();
            if (total_quantity <= 0) {
                $('.draft').prop('disabled', true);
            }
            //call Disavle Store
            disableReceiverStore();
        });

        $(document).on("keyup", ".p_quantity", function () {
            var quantity = $(this).val();
            if (!$.isNumeric(quantity) || parseFloat(quantity) <= 0.00) {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $(this).val('');
            }
            var p_id = $(this).attr('class').split('_');
            product_wise_total_quantity(p_id[0]);
        });

        $('.draft').prop('disabled', true);

        function clear_field() {
            $('.source').val('');
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
        }

        function set_total_quantity() {
            var sum = 0;
            var num = 1;
            $('.table_row').each(function () {
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

            $('.' + product_id + '_product_qty').each(function () {
                var qty = $(this).val();
                sum += parseFloat(qty);
            });

            $('.' + product_id + '').html(sum.toFixed(2));
            //return sum;
        }

        $("form").submit(function () {
            //$('.draft').prop('disabled', true);
        });


    });
</script>


<script type="text/javascript">
    function getPreviousChallan() {
        receiver_store_id = $('#receiver_store_id').val();
        challan_date = $('#challan_date').val();

        if (receiver_store_id && challan_date) {
            $('#challan_meg').show();


            var dataString = 'receiver_store_id=' + receiver_store_id + '&challan_date=' + challan_date;

            $.ajax
            ({
                url: '<?= BASE_URL . 'admin/product_issues/challan_validation'?>',
                type: "POST",
                data: dataString,
                //beforeSend: function() {$("#message").html("<img id='checkmark' src='images/loading.gif' />")},
                //complete: function() {$("#confirmWait_"+pro_id).hide()},
                success: function (result) {
                    var obj = jQuery.parseJSON(result);

                    //alert(obj.challan_no);

                    if (obj.challan_no) {

                        html_text = ' <a data-fancybox data-type="ajax" data-src="<?=BASE_URL?>admin/challans/view/' + obj.id + '/?lightbox=1" href="javascript:;">Last Challan issue date : ' + obj.challan_date + '<br>Challan Number is : ' + obj.challan_no + '<br><b>Click Here</b> for details.</a>';

                        $('#challan_meg').html(html_text);

                    } else {

                        html_text = 'Last Challan Not Found!';

                        $('#challan_meg').text(html_text);
                    }


                    //obj.product_unit.name
                    //$('#syncTableList').html(msg);

                }
            });

        } else {
            $('#challan_meg').hide();
        }

    }
</script>


<?php echo $this->Html->css("jquery.fancybox.min"); ?>
<?php echo $this->Html->script("jquery.fancybox.min"); ?>


