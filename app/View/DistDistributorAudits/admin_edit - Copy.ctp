<?php
$obj = new DistDistributorAuditsController();
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Distributor Audit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Audit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorAudit', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id','empty' => '------ Please Select ------', 'class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('id' => 'dist_distributor_id','empty' => '------ Please Select ------', 'class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_tso_id', array('id' => 'dist_tso_id','label' => 'TSO','empty' => '------ Please Select ------', 'class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('audit_date', array('label' => 'Audit Date', 'class' => 'form-control datepicker', 'type' => 'text')); ?>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Measurement Unit</th>
                            <th class="text-center">Quantity (Sale Unit)</th>
                            <th class="text-center">Expire Date</th>
                            <th class="text-center">Batch Number</th>
                            <th class="text-center">In Stock (Sale Unit)</th>
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_id', array('label' => false, 'id' => 'product_id', 'class' => 'full_width form-control product_id', 'empty' => '---- Select Product ----', 'required' => false)); ?>
                            </td>
                            <td width="19%" align="center">							
                                <?php echo $this->Form->input('measurement_unit_id', array('id' => 'measurement_unit_id', 'label' => false, 'class' => 'full_width form-control', 'required' => false)); ?>
                            </td>
                            <td width="12%" align="center">							
                                <?php echo $this->Form->input('qty', array('label' => false, 'class' => 'full_width form-control quantity')); ?><!--expire_date expireDatepicker  --->
                            </td>
                            <td width="12%" align="center">							
                                <?php echo $this->Form->input('expire_date', array('label' => false, 'class' => 'expire_date full_width form-control datepicker', 'type' => 'text')); ?>
                            </td>
                            <td width="12%" align="center">							
                                <?php echo $this->Form->input('batch_number', array('id' => 'batch_number', 'label' => false, 'class' => 'form-control full_width', 'type' => 'text')); ?>
                            </td>
                            <td width="12%" align="center">							
                                600
                            </td>
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
                        </tr>				
                    </tbody>
                </table>
                <table class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center" width="12%">Product Name</th>
                            <th class="text-center" width="8%">Unit</th>
                            <th class="text-center" width="5%">Quantity (Sale Unit)</th>
                            <th class="text-center" width="10%">Total Quantity (Sale Unit)</th>
                            <th class="text-center" width="12%">Expire Date</th>
                            <th class="text-center" width="12%">Batch Number</th>
                            <th class="text-center" width="15%">Remarks</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                    </thead>	
                    <tbody>
                        <?php
                        if (count($distDistributorAudits) > 0) {
                            $obj = new DistDistributorAuditsController();
                            //pr($distDistributorAudits);die();
                            $i = 1;
                            foreach ($distDistributorAudits[0]['DistDistributorAuditDetail'] as $key => $value) {
                                ?>
                                <tr class="table_row" id="rowCount<?= $i ?>">
                                    <td align="center"><?= $i ?></td>
                                    <td align="center">
                                        <input type="hidden" name="product_id[<?= $i ?>]" value="<?= $value['product_id'] ?>"/>
                                        <?= $products[$value['product_id']] ?>
                                    </td>
                                    <td align="center">
                                        <?php
                                        $measurement_unit_id = $obj->get_sales_measurement_unit($value['measurement_unit_id'], $value['product_id']);
                                        echo $measurementUnits[$measurement_unit_id];
                                        ?>
                                        <input type="hidden" name="measurement_unit[<?= $i ?>]" value="<?= $measurement_unit_id ?>">
                                    </td>
                                    <td align="center">                                        
                                        <input  name="quantity[<?= $i ?>]" class="<?= $value['product_id'] ?>_product_qty  p_quantity" value="<?= $value['qty'] ?>" required ></td>	
                                    <td align="center">
                                        <?= $value['qty'] ?>
                                    </td>
                                    <td align="center">
                                        <input  name="expire_date[<?= $i ?>]" class="p_expire_date datepicker" value="<?= $obj->get_date_format($value['expire_date']); ?>" type="text">
                                    </td>	
                                    <td align="center">
                                        <input  name="batch_number[<?= $i ?>]" class="full_width" value="<?= $value['batch_number'] ?>">
                                    </td>
                                    <td align="center">
                                        <input type="text" class="full_width form-control" name="remarks[<?= $i ?>]">
                                        <?= '' ?>
                                    </td>	
                                    <td align="center"><button class="btn btn-danger btn-xs remove" value="<?= $i ?>"><i class="fa fa-times"></i></button></td>	
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    <input type="hidden" name="last_row" id="last_row" value="<?= $i ?>">
                    </tbody>

                </table>
                </br>
                <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<script>
    $(document).ready(function () {
        $('body').on('change', '#office_id', function () {
            var office_id = $(this).val();
            var dist_distributor_id = $('#dist_distributor_id option:selected').val();
            get_distributors(office_id);
        });
        $('body').on('change', '#dist_distributor_id', function () {
            var office_id = $('#office_id').val();
            var dist_distributor_id = $(this).val();
            get_tsos(office_id, dist_distributor_id);
        });

        function get_distributors(office_id) {
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributors/get_dist_distributor_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        if (result.length != 0) {
                            var options = '';
                            for (var x in result) {
                                options += '<option value=' + '"' + result[x].id + '">' + result[x].name + '</option>'
                            }
                            $('#dist_distributor_id').html(options);
                        } else {
                            $('#dist_distributor_id').html('');
                        }
                    }
                });
            }
        }

        function get_tsos(office_id, dist_distributor_id) {
            if (office_id != '' && dist_distributor_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistTsos/get_tso_list',
                    type: 'POST',
                    data: {office_id: office_id, dist_distributor_id: dist_distributor_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        console.log(result);
                        if (result.length != 0) {
                            //var options = '<option >------ Please Select ------</option>'
                            var options = '';
                            for (var x in result) {
                                options += '<option value=' + '"' + result[x].DistTso.id + '">' + result[x].DistTso.name + '</option>'
                            }
                            $('#dist_tso_id').html(options);
                        } else {
                            $('#dist_tso_id').html('');
                        }
                    }
                });
            } else {
                $('#dist_tso_id').html('');
            }
        }
        $('body').on('change', '#product_id', function () {
            var product_id = $(this).val();
            if (product_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributorAudits/get_measurement_units_list',
                    type: 'POST',
                    data: {product_id: product_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        var options = '';// = '<option >------ Please Select ------</option>'
                        if (result.length != 0) {
                            for (var x in result) {
                                options += '<option value=' + '"' + result[x].id + '">' + result[x].name + '</option>'
                            }
                            $('#measurement_unit_id').html(options);
                        } else {
                            $('#measurement_unit_id').html('');
                        }
                    }
                });
            }

        });

        var is_maintain_batch = 1;
        var is_maintain_expire_date = 1;
        $(".chosen").change(function () {
            $('#myModal').modal('show');
            $('#loading').show();
            var product_id = $(this).val();
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL; ?>admin/products/product_details',
                data: 'product_id=' + product_id,
                cache: false,
                success: function (response) {
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

                    $('#myModal').modal('hide');
                    $('#loading').hide();
                }
            });

        });

//var last_row = $('#last_row').val();
        var rowCount = $('#last_row').val();
        var rowCount = rowCount - 1;
        console.log(rowCount);
        $(".add_more").click(function () {
            var product_id = $('.product_id').val();
            var product_name = $("#product_id option:selected").text();
            var quantity = $('.quantity').val();
            var expire_date = $('.expire_date').val();
            var batch_number = $('#batch_number').val();
            var measurement_unit = $('#measurement_unit_id').text();
            var measurement_unit_id = $('#measurement_unit_id').val();
            // console.log(measurement_unit);
            //var selected_stock_id = $("input[name=selected_stock_id]").val();
            var selected_stock_array = $(".selected_product_id").map(function () {
                return $(this).val();
            }).get();
            var product_check_id = product_id + expire_date;
            var stock_check = $.inArray(product_check_id, selected_stock_array) != -1;

            if (product_id == '')
            {
                alert('Please select any product.');
                return false;
            } else if (!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
            {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $('.quantity').val('');
                return false;
            } else if (parseFloat(quantity) >= 10000000)
            {
                alert('Quantity must be less then equal 10000000.');
                $('.quantity').val('');
                return false;
            } else if (is_maintain_expire_date == 1 && expire_date == '')
            {
                alert('Please enter valid expire date.');
                return false;
            } else if (stock_check == true) {
                alert('This product already added.');
                clear_field();
                return false;
            } else
            {
                rowCount++;
                console.log(rowCount);
                var recRow = '<tr class="table_row" id="rowCount' + rowCount + '">\n\
                            <td align="center">' + rowCount + '</td>\n\
                            <td align="center">' + product_name + '<input type="hidden" name="product_check[' + rowCount + ']" class="selected_product_id" value="' + product_id + expire_date + '"/><input type="hidden" name="product_id[' + rowCount + ']" value="' + product_id + '"/></td>\n\
                            <td align="center">' + measurement_unit + '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + measurement_unit_id + '"></td>\n\
                            <td align="center"><input  name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty  p_quantity" value="' + quantity + '" required ></td>\n\
                            <td align="center" class="' + product_id + '"></td>\n\
                            <td align="center"><input  name="expire_date[' + rowCount + ']" class="p_expire_date" value="' + expire_date + '"></td>\n\
                            <td align="center"><input  name="batch_number[' + rowCount + ']" class="full_width" value="' + batch_number + '">' + batch_number + '</td>\n\
                            <td align="center"><input type="text" class="full_width form-control" name="remarks[' + rowCount + ']"></td>\n\
                            <td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                $('.invoice_table').append(recRow);
                product_wise_total_quantity(product_id);
                clear_field();

                var total_quantity = set_total_quantity();
                if (total_quantity > 0)
                {
                    $('.save').prop('disabled', false);
                }

            }
        });

        function isDate(txtDate)
        {
            return txtDate.match(/^d\d?\/\d\d?\/\d\d\d\d$/);
        }

        $(document).on("keyup", ".p_quantity", function () {
            var quantity = $(this).val();
            if (!$.isNumeric(quantity) || parseFloat(quantity) <= 0.00)
            {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $(this).val('');
            }
            var p_id = $(this).attr('class').split('_');
            product_wise_total_quantity(p_id[0]);
        });

        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            var p_id = $("input[name~='product_id[" + removeNum + "]']").val();
            $('#rowCount' + removeNum).remove();
            product_wise_total_quantity(p_id);
            var total_quantity = set_total_quantity();
            if (total_quantity < 1)
            {
                $('.save').prop('disabled', true);
            }
        });


        $('.save').prop('disabled', true);

        function clear_field() {
            $('#product_type').val('');
            $('.product_id').val('');
            $('.quantity').val('');
            $('.expire_date').val('');
            $('.chosen').val('').trigger('chosen:updated');
            $('.add_more').val('');
            $('.expire_date').attr('disabled', false);
        }

        function set_total_quantity() {
            var sum = 0;
            var num = 1;
            $('.table_row').each(function () {
                var table_row = $(this);
                var total_quantity = table_row.closest('tr').find('.p_quantity').val();
                sum += parseInt(total_quantity);
                $(this).find("td:first").html(num++);
            });

            $('.total_quantity').html(sum);
            return sum;
        }

        function product_wise_total_quantity(product_id) {
            var sum = 0;

            $('.' + product_id + '_product_qty').each(function () {
                var qty = $(this).val();
                sum += parseFloat(qty);
            });

            $('.' + product_id + '').html(sum.toFixed(2));
            //return sum;
        }


        $("form").submit(function () {
            $('.save').prop('disabled', true);
        });

        var dates = $('.expireDatepicker').datepicker({
            'format': "M-yy",
            'startView': "year",
            'minViewMode': "months",
            'autoclose': true
        });
        $('.expireDatepicker').click(function () {
            dates.datepicker('setDate', null);
        });

    });
</script>