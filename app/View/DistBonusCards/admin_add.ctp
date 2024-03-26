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
                 <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Incentive Party Setup'); ?></h3> 
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('DistBonusCard', array('role' => 'form')); ?>
                <div class="form-group required">
                    <?php echo $this->Form->input('name', array('class' => 'form-control name', 'maxlength' => '15', 'required'=>true,'type' => 'text')); ?>
                </div>
                <div class="form-group required">
                    <?php echo $this->Form->input('bonus_card_type_id', array('label'=>'Incentive Party Type :','class' => 'form-control','empty'=>'---- Select ----','options'=>$bonusCardTypes)); ?>
                </div>
                <div class="form-group required">
                    <?php echo $this->Form->input('date_from', array('label'=>'Date From:','class' => 'form-control datepicker_range date_from ','id'=>'date_from', 'type' => 'text', 'required'=>TRUE)); ?>
                </div>
                <div class="form-group required">
                    <?php echo $this->Form->input('date_to', array('label'=>'Date TO:','class' => 'form-control datepicker_range date_to ','id'=>'date_to', 'type' => 'text', 'required'=>TRUE)); ?>
                </div>
				<div class="form-group required">
                    <?php echo $this->Form->input('min_qty', array('class' => 'form-control min_qty', 'required'=>true,'type' => 'text')); ?>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td  align="center">
							<?php echo $this->Form->input('product_id', array('label'=>false, 'id'=>'product_id', 'class' => 'full_width form-control product_id chosen','empty'=>'---- Select Product ----','options'=>$products)); ?>
                            </td>
                            
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
                            </td>
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
                        </tr>				
                    </tbody>
                </table>
                <br>		
                <table class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center" width="10%">Quantity</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                    </thead>					
                </table>
                </br>
                <br>
                <br>
                <br>
                <br>
                <table class="table table-striped table-condensed table-bordered product_bonus_table">
                    <thead>
                        <tr>
                            
                            <th class="text-center">Product Name</th>
                            <th class="text-center" >Date From</th>
                            <th class="text-center" >Date To</th>
                            <th class="text-center" width="10%">Quantity</th>
                            <th class="text-center" width="10%">Action</th>                 
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td  align="center">
                            <?php echo $this->Form->input('period_product_id', array('label'=>false, 'id'=>'period_product_id', 'class' => 'full_width form-control period_product_id chosen','empty'=>'---- Select Product ----')); ?>
                            </td>
                            <td align="center">                         
                            <?php echo $this->Form->input('product_date_from', array('label'=>false,'class' => 'form-control datepicker1 product_date_from','id'=>'product_date_from', 'type' => 'text','autocomplete'=>"off")); ?>
                            </td>
                            <td align="center">                         
                            <?php echo $this->Form->input('product_date_to', array('label'=>false,'class' => 'form-control datepicker1 product_date_to','id'=>'product_date_to', 'type' => 'text','autocomplete'=>"off")); ?>
                            </td>
                            <td width="12%" align="center">                         
                            <?php echo $this->Form->input('qty', array('label'=>false, 'class' => 'full_width form-control period_product_qty')); ?>
                            </td>
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more_period_product"> Add Product </span></td>                  
                        </tr>               
                    </tbody>                    
                </table>
                <br>
                <br>        
                <table class="table table-striped table-condensed table-bordered period_bonus_table" id= "period_bonus_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center" width="10%">Date From</th>
                            <th class="text-center" width="10%">Date To</th>
                            <th class="text-center" width="10%">Quantity</th>
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
    //$(document).ready(function () {
        $('.datepicker_range').datepicker({
            format: 'yyyy/mm/dd',
            //format: 'yyyy',
            startDate: '0d',
            'autoclose': true
        });
    //});
</script>
<script>
    $(document).ready(function () {
        $('.period_bonus_table').hide();
        $('.product_bonus_table').hide();
        //$('.datepicker2').datepicker({ dateFormat: 'yyyy-mm-dd' });
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
        var p_rowCount = 0;

        $(".add_more").click(function () {
            var product_id = $('.product_id').val();
            var quantity = $('.quantity').val();
            //var batch_no = $('.batch_no').val();
            //var expire_date = $('.expire_date').val();
            var date_from = $('.date_from').val();
            var date_to = $('.date_to').val();
            //var selected_stock_id = $("input[name=selected_stock_id]").val();
           /*var selected_stock_array = $(".selected_product_id").map(function() {
               return $(this).val();
            }).get();
            var product_check_id = product_id + batch_no + expire_date; 
            var stock_check = $.inArray(product_check_id,selected_stock_array) != -1;*/

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
            }
            else if (date_from == '')
            {
               alert('Please Enter Date From');
                $('.date_from').val('');
                return false;
            }
            else if (date_to == '')
            {
                alert('Please Enter Date To');
                $('.date_to').val('');
                return false;
            }
             else
            {
                rowCount++;
                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL;?>admin/DistBonusCards/product_details',
                    data:  {product_id: product_id, date_from: date_from, date_to: date_to},
                    cache: false,
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                        console.log(obj);
                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td>' + obj.Product.name+ '<input type="hidden" name="product_id[' + rowCount + ']" value="' + obj.Product.id+ '"/></td><td align="center"><input  name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty  p_quantity" value="' + quantity + '" required ></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('.invoice_table').append(recRow);
                        //product_wise_total_quantity(product_id);
                        clear_field();

                       /* var total_quantity = set_total_quantity();
                        if (total_quantity > 0)
                        {*/
                        $('.save').prop('disabled', false);
                       // }
                        $('.period_bonus_table').show();
                        $('.product_bonus_table').show();
                        var total_month =  obj.DateCalculation.total_month;
                        var number_of_days =  obj.DateCalculation.number_of_days;
                        var num_days = number_of_days / total_month;
                        var pro_qty = quantity / (total_month).toFixed(2);
                        pro_qty = parseFloat(pro_qty).toFixed(2);
                        console.log(num_days);
                        var number_of_days =  obj.DateCalculation.number_of_days;
                        var i;
                        var from_date  = date_from;
                        date_from = new Date(date_from).toLocaleDateString('en-GB');
                        p_rowCount++;
                        for (i = 0; i < total_month; i++) 
                        {
                            
                            //var options = { weekday: 'long', year: 'numeric', month: 'numeric', day: 'numeric' };
                            var to_date = addDays(from_date,num_days);
                            date_to = to_date.toLocaleDateString('en-GB');
                            var product_name = obj.Product.name;
                            var product_id =obj.Product.id;
                            add_more_period_product_in_table(product_name,product_id,date_from,date_to,p_rowCount,pro_qty);
                            p_rowCount++;
                            from_date = addDays(to_date,0);
                            date_from = from_date.toLocaleDateString('en-GB');
                        }
                        //$('#period_bonus_table').find('.datepicker1').datepicker({ dateFormat: 'dd-mm-yy' });
                        $('#period_bonus_table').find('.datepicker1').datepicker();
                        $('.period_product_id').append(`<option value="${product_id}">${obj.Product.name}</option>`);
                    }
                });
            }
        });
        function addDays(date, days) {
            var result = new Date(date);
            console.log(result);
            result.setDate(result.getDate() + days);
            return result;
        }
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

       /*var dates=$('.expireDatepicker').datepicker({
             'format': "M-yy",
             'startView': "year", 
             'minViewMode': "months",
             'autoclose': true
         }); 
        $('.expireDatepicker').click(function(){
            dates.datepicker('setDate', null);
        });

*/
    //var date_from = $('.date_from').val();
    //var date_to = $('.date_to').val();
    //console.log(date_from);
    //console.log(date_to);

        /*$('.datepicker1').datepicker({
            startDate: new Date(),
            format: "yyyy/mm/dd",
            startDate: $('.date_from').val(),
            endDate: $('.date_to').val(),
            autoclose: true,
            todayHighlight: true
        });*/
        /*$('.datepicker1').click(function(){
            dates.datepicker('setDate', null);
        });*/

    });
</script>
<script>
   $(".add_more_period_product").click(function () {
        var product_id = $('.period_product_id').val();
        var quantity = $('.period_product_qty').val();
        var date_from = $('.product_date_from').val();
        var date_to = $('.product_date_to').val();
        var rowCount = parseInt($('#period_bonus_table tr:last').attr('id')) + 1;
        //alert(rowCount);
        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>admin/DistBonusCards/product_details',
            data:  {product_id: product_id, date_from: date_from, date_to: date_to},
            cache: false,
            success: function (response) {
                var obj = jQuery.parseJSON(response);
                console.log(obj);
                var product_name = obj.Product.name;
                var product_id =obj.Product.id;
                add_more_period_product_in_table(product_name,product_id,date_from,date_to,rowCount,quantity);
                clear_period_bonus_table();
            }
        });
        
        function clear_period_bonus_table() {
            
            $('.period_product_id').val('');
            $('.period_product_qty').val('');
            $('.product_date_from').val('');
            $('.product_date_to').val('');
        }

   });


   $(document).on("click", ".period_product_remove", function () {
            var removeNum = $(this).val();
            var p_id = $("input[name~='period_product_id[" + removeNum + "]']").val();
            var p_qty = $("input[class~='" + p_id + "_period_product_qty").val();
            console.log(p_qty);
            $('#' + removeNum).remove();
        });
</script>

<script>
    function add_more_period_product_in_table(product_name,product_id,date_from,date_to,rowCount,pro_qty) {
        var recRow = '<tr class="table_row" id="' + rowCount + '"><td align="center">' + rowCount + '</td><td>' +product_name+ '<input type="hidden" name="period_product_id['+product_id+'][' + rowCount + ']" value="' +product_id+ '"/></td><td align="center"><input  name="period_date_from['+product_id+'][' + rowCount + ']" class="' + product_id + '_period_date_from datepicker1 " value="' + date_from + '" required ></td><td align="center"><input  name="period_date_to['+product_id+'][' + rowCount + ']" class="' + product_id + '_period_date_to datepicker1 " value="' + date_to + '" required ></td><td align="center"><input  name="period_product_quantity['+product_id+'][' + rowCount + ']" class="' + product_id + '_period_product_qty  p_quantity" value="' + pro_qty + '" required ></td><td align="center"><button class="btn btn-danger btn-xs period_product_remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
        $('.period_bonus_table').append(recRow);
    }
</script>

<script>
    /*$('#date_from,#date_to').change(function(){
        var date_from $('.date_from').val();
        var date_to = $('.date_to').val();

        $('.datepicker1').datepicker('option', 'minDate', new Date(date_from));
        $('.datepicker1').datepicker('option', 'maxDate', new Date(date_to));
    });*/
    $('.datepicker1').datepicker({
            startDate: new Date(),
            format: "yyyy/mm/dd",
            //startDate: ,
            //endDate: ,
            autoclose: true,
            todayHighlight: true
        });
</script> 