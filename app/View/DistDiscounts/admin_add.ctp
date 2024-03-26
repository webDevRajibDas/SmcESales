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
.form-control {
    float: left;
    width: 50%;
    font-size: 13px;
    height: 28px;
    padding: 0px 4px;
    }
.datepicker_range table tr td.disabled, .datepicker table tr td.disabled {
    color: #c7c7c7;
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
                     <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('SR Sales Discount Setup'); ?></h3> 
                    <div class="box-tools pull-right">
    					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Sales Discount List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                    </div>
                    </div>
                <div class="box-body">		
    			<?php echo $this->Form->create('DistDiscount', array('role' => 'form')); ?>
                <div class="row">
                    <div class='col-lg-6'>
                        <div class="form-group">
                        <?php

                            if ($office_parent_id == 0) {
                            echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'empty' => '---- Select Office ----', 'options'=>$offices));
                            } else {
                            echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----','options'=>$offices));
                            }
                        ?>
                        </div>
                        <div class="form-group">
                        <?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'id' => 'distributor_id', 'options'=>$distributors,'empty' => '--- Select Distributor ---')); ?> 
                        </div>

                        <div class="form-group">
                        <?php echo $this->Form->input('description', array('class' => 'form-control description', 'required'=>false,'type' => 'textArea')); ?>
                        </div>

                    </div>
                    <div class='col-lg-6'>
                        <div class="form-group">
                        <?php echo $this->Form->input('date_from', array('label'=>'Date From:','class' => 'form-control datepicker_range effective_date datepicker1','id'=>'date_from', 'type' => 'text', 'required'=>TRUE,'autocomplete'=>"off")); ?>
                        </div>
                        <div class="form-group">
                        <?php echo $this->Form->input('date_to', array('label'=>'Date TO:','class' => 'form-control datepicker_range effective_date datepicker1','id'=>'date_to', 'type' => 'text', 'required'=>TRUE,'autocomplete'=>"off")); ?>
                        </div>
                        <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>','default'=>1)); ?>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Memo Value</th>
                            <th class="text-center">Discount</th>
                            <th class="text-center">Discount Type</th>
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td  align="center">
							<?php echo $this->Form->input('amount', array('label'=>false, 'id'=>'amount', 'class' => 'full_width form-control amount', 'type'=> 'number')); ?>
                            </td>
                            
                            <td width="12%" align="center">							
							<?php echo $this->Form->input('discount_percent', array('label'=>false, 'class' => 'full_width form-control discount','type'=> 'number')); ?>
                            </td>
                            <td width="12%" align="center">                         
                            <?php echo $this->Form->input('discount_type', array('label'=>false, 'class' => 'full_width form-control discount_type','options'=>$discounts_types)); ?>
                            </td>
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add </span></td>					
                        </tr>				
                    </tbody>
                </table>
                <br>		
                <table class="table table-striped table-condensed table-bordered discount_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center">Memo Value</th>
                            <th class="text-center" width="10%">Discount</th>
                            <th class="text-center" width="10%">Discount Type</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                    </thead>					
                </table>
                </br>
                </br>
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary save')); ?>
			<?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<?php
/*$todayDate = date('Y-m-d');
$startDate = date('d-m-Y', strtotime($last_effective_date));
$endDateOfMonth = date('Y-m-t', strtotime($startDate));

if(strtotime($todayDate) < strtotime($endDateOfMonth) ){
    $endDate = date('d-m-Y');
}else{
    $endDate = date('t-m-Y', strtotime($startDate));
}*/
?>
<script>
    
    function get_effective_date(){
        var office_id = $('#office_id').val();
        var distributor_id = $('#distributor_id').val();
        if(office_id == null){
            office_id = -1;
        }
        if(distributor_id == null){
            distributor_id = -1;
        }
        //$(".effective_date").trigger("reset");
       
        $.ajax({
        type: "POST",
        url: '<?php echo BASE_URL;?>DistDiscounts/get_effective_date',
        data:  {office_id: office_id, distributor_id: distributor_id},
        //cache: false,
        success: function (response) {
            var obj = jQuery.parseJSON(response);
            //console.log(obj);
            var last_effective_date  = obj.last_effective_date;
            console.log(last_effective_date);
            //$(".effective_date").datepicker( "refresh" );
            
            //$(".effective_date").val('');
            $(".datepicker1").datepicker({
                dateFormat:'dd-mm-yyyy',
                startDate: last_effective_date,
                endDate: last_effective_date+5,
                format: "dd-mm-yyyy",
                autoclose: true,
                //todayHighlight: true
            });
            }
        });
    }
    $("body").on("change", "#office_id", function () 
        {
            //$(".effective_date").datepicker('setDate', null);
            $(".datepicker1").datepicker("refresh");
            var office_id = $('#office_id').val();
            var distributor_id = $('#distributor_id').val();
            if(office_id == null){
                office_id = -1;
            }
            if(distributor_id == null){
                distributor_id = -1;
            }
            $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>DistDiscounts/get_effective_date',
            data:  {office_id: office_id, distributor_id: distributor_id},
            //cache: false,
            success: function (response) {
                var obj = jQuery.parseJSON(response);
                //console.log(obj);
                var last_effective_date  = obj.last_effective_date;
                console.log(last_effective_date);
               
                $(".effective_date").datepicker({
                    dateFormat:'dd-mm-yyyy',
                    startDate: last_effective_date,
                    endDate: last_effective_date+5,
                    format: "dd-mm-yyyy",
                    autoclose: true,
                
                });
                }
            });
        });
        $("body").on("change", "#distributor_id", function () 
        {
            $(".effective_date").datepicker("refresh");
            //$(".datepicker_range").datepicker('setDate', null);
            //get_effective_date();
            var office_id = $('#office_id').val();
            var distributor_id = $('#distributor_id').val();
            if(office_id == null){
                office_id = -1;
            }
            if(distributor_id == null){
                distributor_id = -1;
            }
            $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>DistDiscounts/get_effective_date',
            data:  {office_id: office_id, distributor_id: distributor_id},
            //cache: false,
            success: function (response) {
                var obj = jQuery.parseJSON(response);
                //console.log(obj);
                var last_effective_date  = obj.last_effective_date;
                console.log(last_effective_date);
               
                $(".datepicker_range").datepicker({
                    dateFormat:'dd-mm-yyyy',
                    startDate: last_effective_date,
                    endDate: last_effective_date+5,
                    format: "dd-mm-yyyy",
                    autoclose: true,
                
                });
                }
            });
        });
    $(document).ready(function () {
        get_effective_date();
        
        /*$('.datepicker_range').datepicker({
            startDate: '<?php //echo $startDate; ?>',
            //endDate: '<?php //echo $endDate; ?>',
            format: "dd-mm-yyyy",
            autoclose: true,
            //todayHighlight: true
        });*/
        
        
    });
</script>
<script>
    $(document).ready(function () {
        $('.discount_table').hide();
        var rowCount = 0;
        $('#office_id').selectChain({
            target: $('#distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDiscounts/get_distributor_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

        $(".add_more").click(function () {
           
            var amount = $('.amount').val();
            var discount = $('.discount').val();
            var discount_type = $('.discount_type').val();
           
            var date_from = $('.date_from').val();
            var date_to = $('.date_to').val();
            console.log(date_from);
            console.log(date_to);
            console.log(amount);
            console.log(discount);
            if (date_from == '')
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
            else if (discount_type == '')
            {
                alert('Please Enter discount type');
                $('.discount_type').val('');
                return false;
            }
            else
            {
                var type = '';
                if(discount_type == 1){
                    type = "Percentage";
                }
                else{
                    type = "Taka";
                }
                rowCount++;

                var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td align="center">' + amount + '<input type="hidden" name="memo_value[' + rowCount + ']" value="' + amount+ '"/></td><td align="center"> '+ discount+'<input type="hidden"  name="discount_percent[' + rowCount + ']" class="' + rowCount + '_discount" value="' + discount + '" required ></td><td align="center">' + type + '<input type="hidden"  name="discount_type[' + rowCount + ']" class="' + rowCount + '_type" value="' + discount_type + '" required ></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                console.log(recRow);
                 $('.discount_table').show();
                $('.discount_table').append(recRow);
                clear_field();

                $('.save').prop('disabled', false);

                
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
            $('.amount').val('');
            $('.discount').val('');
            $('.add_more').val('');
           
        }

        $("form").submit(function () {
            $('.save').prop('disabled', true);
        });
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
            url: '<?php echo BASE_URL;?>admin/DistDiscounts/product_details',
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

</script>