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

<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>

<?php //pr($stores);?><div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Opening inventory(Base Unit)'); ?></h3>
                <div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Current Inventory List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('DistCurrentInventory', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php
                    if ($office_parent_id == 0) {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                    } else {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','empty' => '---- Select Office ----'));
                    }
                    ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id', 'empty' => '--- Select Distributor ---', 'required' => 'required')); ?> 
                </div>     
                <div class="form-group">
                        <?php echo $this->Form->input('inventory_status_id', array('class' => 'form-control','empty'=>'-- Select ---','required')); ?>
                    </div>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Type</th>
                            <th>Product Name</th>
                            <th class="text-center">Stock Quantity</th>
                            <!-- <th class="text-center">Bonus Quantity</th> -->
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('product_type', array('label'=>false,'id'=>'product_type', 'class' => 'full_width form-control ','empty'=>'---- Select Product Type ----')); ?>
                            </td>
                            <td>
							<?php echo $this->Form->input('product_id', array('label'=>false, 'id'=>'product_id', 'class' => 'full_width form-control product_id chosen','empty'=>'---- Select Product ----')); ?>
                            </td>
                           
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
                            </td>

                            <!--  <td width="12%" align="center">                            
                            <?php //echo $this->Form->input('bonus_qty', array('label'=>false, 'class' => 'full_width form-control bonus_quantity')); ?>
                            </td> -->
                            
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
                            <th class="text-center" width="10%">Stock Quantity</th>
                            <!-- <th class="text-center" width="10%">Bonus Quantity</th> -->
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
        $("#office_id").prop("selectedIndex", 0);

        $(".office_id").change(function () {
            get_dist_by_office_id($(this).val());
        });
        function get_dist_by_office_id(office_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'DistCurrentInventories/get_dist_list_by_office_id_for_adjustment' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }
		$('#product_type').selectChain({
        target: $('#product_id'),
        value:'name',
        url: '<?= BASE_URL.'DistCurrentInventories/get_product'?>',
        type: 'post',
        data:{'product_type_id': 'product_type'  }
        });

        var is_maintain_batch = 1;
        var is_maintain_expire_date = 1;
        $(".chosen").change(function () {
            
            $('#myModal').modal('show');
            $('#loading').show();
            
            
            var product_id = $(this).val();           
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL;?>admin/products/product_details',
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
            var bonus_quantity = $('.bonus_quantity').val();
            var batch_no = $('.batch_no').val();
            var expire_date = $('.expire_date').val();
            //var selected_stock_id = $("input[name=selected_stock_id]").val();
           var selected_stock_array = $(".selected_product_id").map(function() {
               return $(this).val();
            }).get();
            var product_check_id = product_id + batch_no + expire_date; 
            var stock_check = $.inArray(product_check_id,selected_stock_array) != -1;

            if (product_id == '')
            {
                alert('Please select any product.');
                return false;
            } else if (is_maintain_batch == 1 && batch_no == '')
            {
                alert('Please enter batch number.');
                return false;
            } 
            /*else if (!quantity.match(/^\d+(\.\d{1,2})?$/) || parseFloat(quantity) <= 0.00)
            {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $('.quantity').val('');
                return false;
            }
             /*else if (!bonus_quantity.match(/^\d+(\.\d{1,2})?$/))
            {
                alert('Please enter valid bonus quantity. Ex. : 100 or 100.00');
                $('.bonus_quantity').val('');
                return false;
            }*/ 
            /*else if (parseFloat(quantity) >= 10000000)
            {
                alert('Quantity must be less then equal 10000000.');
                $('.quantity').val('');
                return false;
            } */
            else if (is_maintain_expire_date == 1 && expire_date == '')
            {
                alert('Please enter valid expire date.');
                return false;
            }
            else if(stock_check == true){
                alert('This product already added.');
                clear_field();
                return false;
            }
             else
            {
                rowCount++;
                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL;?>admin/products/product_details',
                    data: 'product_id=' + product_id,
                    cache: false,
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                       
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '">\
                            <td align="center">' + rowCount + '</td>\
                            <td>' + obj.Product.name+
                                '<input type="hidden" name="product_check[' + rowCount + ']" class="selected_product_id" value="' + obj.Product.id+batch_no+expire_date + '"/>\
                                <input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id+ '"/>\
                            </td>\
                            <td align="center">' + obj.BaseMeasurementUnit.name +
                                '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + obj.Product.base_measurement_unit_id + '">\
                            </td>\
                            <td align="center">\
                                <input  name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty  p_quantity" value="' + quantity + '" required >\
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
                        clear_field();

                        var total_quantity = set_total_quantity();
                        var total_bonus_qty = set_total_bonus_quantity();
                        if (total_quantity > 0)
                        {
                            $('.save').prop('disabled', false);
                        }else{
                            if(total_bonus_qty > 0){
                                $('.save').prop('disabled', false);
                            }
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
            /*if (!$.isNumeric(quantity) || parseFloat(quantity) <= 0.00)
            {
                alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $(this).val('');
            }*/
            if (!$.isNumeric(quantity))
            {
                //alert('Please enter valid quantity. Ex. : 100 or 100.00');
                $(this).val(0);
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
            $('.bonus_quantity').val('');
            $('.batch_no').val('');
            $('.expire_date').val('');
            $('.chosen').val('').trigger('chosen:updated');
            $('.add_more').val('');
            $('.batch_no').attr('readonly', false);
            $('.expire_date').attr('disabled', false);
        }

        function set_total_quantity() {
            var sum = 0;
            var num = 1;
            var bonus_sum = 0;
            $('.table_row').each(function () {
                var table_row = $(this);
                var total_quantity = table_row.closest('tr').find('.p_quantity').val();
                sum += parseInt(total_quantity);
                $(this).find("td:first").html(num++);
            });

            $('.total_quantity').html(sum);
            return sum;
        }
        function set_total_bonus_quantity() {
            var sum = 0;
            var num = 1;
            $('.table_row').each(function () {
                var table_row = $(this);
                var total_quantity = table_row.closest('tr').find('.p_bonus_quantity').val();
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

       var dates=$('.expireDatepicker').datepicker({
             'format': "M-yy",
             'startView': "year", 
             'minViewMode': "months",
             'autoclose': true
         }); 
        $('.expireDatepicker').click(function(){
            dates.datepicker('setDate', null);
        });

    });
</script>