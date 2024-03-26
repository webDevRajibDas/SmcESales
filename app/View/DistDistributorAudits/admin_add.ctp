<?php //echo 'hi';?><style>
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

<?php //pr($stores);?><div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Distributors Audit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributors Audits List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorAudit', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('audit_date', array('label' => 'Audit Date','id'=>'audit_date', 'class' => 'form-control datepicker', 'type' => 'text')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '------ Please Select ------', 'required')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributors', 'id' => 'dist_distributor_id', 'class' => 'form-control', 'empty' => '------ Please Select ------', 'required')); ?>
                </div>
               
                
                <div class="form-group">
                    <?php
                        echo $this->Form->input('ae_name', array('label'=>'Area Executive :','id'=>'ae_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"",'readonly'));
                   ?>
                 </div>
                 
                 <div class="form-group">
                    <?php
                        echo $this->Form->input('tso_name', array('label'=>'TSO :','id'=>'tso_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"",'readonly'));
                   ?>
                 </div>
                
                 <div class="form-group">
                    <?php
                        echo $this->Form->input('audit_by', array('label' => 'Audit By :', 'id' => 'audit_by', 'class' => 'form-control','options'=>$audit_by, 'required'));
                   ?>
                 </div>
               
                
                <?php echo $this->Form->input('dist_tso_id', array('type'=>'hidden','id' => 'dist_tso_id', 'class' => 'form-control','value'=>0)); ?>
                <?php echo $this->Form->input('dist_ae_id', array('type'=>'hidden','id' => 'dist_ae_id', 'class' => 'form-control','value'=>0)); ?>
                
                
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
                                <div id="storage_product"></div>
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
                            <th class="text-center" width="12%">Unit</th>
                            <th class="text-center" width="10%">Quantity (Sale Unit)</th>
                            <th class="text-center" width="10%">Total Quantity (Sale Unit)</th>
                            <th class="text-center" width="12%">Expire Date</th>
                            <th class="text-center" width="12%">Batch Number</th>
                            <th class="text-center" width="15%">Remarks</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                    </thead>					
                </table>
                </br>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary save')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
   $('#product_id').html('<option value="">---- Select ----');
    $("input[type='checkbox']").iCheck('destroy');
  $("input[type='radio']").iCheck('destroy');
        
        $('body').on('change', '#office_id', function () {
            var office_id = $(this).val();
            var dist_distributor_id = $('#dist_distributor_id option:selected').val();
            get_distributors(office_id);
        });
        $('body').on('change', '#dist_distributor_id', function () {
            var office_id = $('#office_id').val();
            var dist_distributor_id = $(this).val();
            get_tso_ae_data(office_id, dist_distributor_id);
            get_product_list_from_dist_id(office_id, dist_distributor_id);

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
        
        
        
        function get_tso_ae_data(office_id, dist_distributor_id) {
           
           var audit_date = $("#audit_date").val();
            if (office_id != '' && dist_distributor_id != '' && audit_date != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributorAudits/get_tso_ae_list',
                    type: 'POST',
                    data: {office_id: office_id, dist_distributor_id: dist_distributor_id,'audit_date':audit_date},
                    success: function (data) {
                       var info = data.split("||");
                        if (info[0] !== "")
                        {
                            $('#dist_ae_id').val(info[0]);
                        }

                        if (info[1] !== "")
                        {
                            $('#ae_name').val(info[1]);
                        }
                        
                        if (info[2] !== "")
                        {
                            $('#dist_tso_id').val(info[2]);
                        }
                        
                        if (info[3] !== "")
                        {
                            $('#tso_name').val(info[3]);
                        }
                        
                        if(info[0]== "" && info[2]== "")
                        {
                            alert("Area Executive and TSO are not mapped properly. Please map first");
                        }
                        else if(info[0]== "")
                        {
                             alert("Area Executive is not mapped properly. Please map first");
                        }
                        else if(info[2]== "")
                        {
                             alert("TSO is not mapped properly. Please map first");
                        }
                    }
                });
            } else {
                $('#dist_ae_id').val('');
                $('#dist_tso_id').val(''); 
                $('#ae_name').val('');
                $('#tso_name').val('');
                 
            }
        }
        
        
		
		function get_storage_product(office_id, dist_distributor_id,product_id) {
                var measurement_unit_id=$("#measurement_unit_id").val();
            if (office_id != '' && dist_distributor_id != '' && product_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributorAudits/get_storage_product',
                    type: 'POST',
                    data: {office_id: office_id, dist_distributor_id: dist_distributor_id,product_id:product_id,measurement_unit_id:measurement_unit_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        console.log(result);
						$('#storage_product').html(result);
                    }
                });
            } else {
                $('#storage_product').html('');
            }
        }
        /*$('#dist_distributor_id').selectChain({
            target: $('#product_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributorAudits/get_product_list_from_dist_id' ?>',
            type: 'post',
            data: {'dist_distributor_id': 'dist_distributor_id','office_id':'office_id'}
        });*/
        function get_product_list_from_dist_id(office_id, dist_distributor_id) {
                
            if (office_id != '' && dist_distributor_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistDistributorAudits/get_product_list_from_dist_id',
                    type: 'POST',
                    data: {office_id: office_id, dist_distributor_id: dist_distributor_id},
                    success: function (result) {
                       // result = $.parseJSON(result);
                        console.log(result);
                        $('#product_id').html(result);
                    }
                });
            } else {
                $('#product_id').html('');
            }
        }
		
        $('body').on('change', '#office_id', function () {
			var office_id = $(this).val();
			var dist_distributor_id = $('#dist_distributor_id').val();
			var product_id = $('#product_id').val();
			get_storage_product(office_id, dist_distributor_id,product_id);
		});
		$('body').on('change', '#dist_distributor_id', function () {
			var office_id = $('#office_id').val();
			var dist_distributor_id = $(this).val();
			var product_id = $('#product_id').val();
			get_storage_product(office_id, dist_distributor_id,product_id);
		});
        $('body').on('change', '#product_id', function () {
            var product_id = $(this).val();
			var office_id = $('#office_id').val();
			var dist_distributor_id = $('#dist_distributor_id').val();
			get_storage_product(office_id, dist_distributor_id,product_id);
			
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


        var rowCount = 0;
        $(".add_more").click(function () {
            var product_id = $('.product_id').val();
            var quantity = $('.quantity').val();
            var expire_date = $('.expire_date').val();
            var batch_number = $('#batch_number').val();
            var measurement_unit = $('#measurement_unit_id').text();
            var measurement_unit_id = $('#measurement_unit_id').val();
            console.log(measurement_unit);
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
                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL; ?>admin/products/product_details',
                    data: 'product_id=' + product_id,
                    cache: false,
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '">\n\
                            <td align="center">' + rowCount + '</td>\n\
                            <td>' + obj.Product.name + '<input type="hidden" name="product_check[' + rowCount + ']" class="selected_product_id" value="' + obj.Product.id + expire_date + '"/><input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id + '"/></td>\n\
                            <td align="center">' + measurement_unit + '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + measurement_unit_id + '"></td>\n\
                            <td align="center"><input  name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty  p_quantity" value="' + quantity + '" required ></td>\n\
                            <td align="center" class="' + product_id + '"></td>\n\
                            <td align="center"><input type="hidden" name="expire_date[' + rowCount + ']" class="p_expire_date" value="' + expire_date + '">' + expire_date + '</td>\n\
                            <td align="center"><input type="hidden" name="batch_number[' + rowCount + ']" class="full_width" value="' + batch_number + '">' + batch_number + '</td>\n\
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