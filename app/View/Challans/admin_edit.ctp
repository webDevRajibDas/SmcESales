<?php
//pr($challan_info);die();
?>
<div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
         <div class="box-header">
            <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Challan'); ?></h3>
            <div class="box-tools pull-right">
               <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Challan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
           </div>
       </div>
       <div class="box-body">		
         <?php echo $this->Form->create('Challan', array('role' => 'form')); ?>
         <div class="form-group">
            <?php echo $this->Form->input('receiver_store_id', array('class' => 'form-control','options'=>$receiver_store,'required'=>true, 'selected'=>$challan_info[0]['Challan']['receiver_store_id'])); ?>
        </div>
        <div class="form-group">
            <?php echo $this->Form->input('challan_referance_no', array('class' => 'form-control challan_referance_no', 'value'=>$challan_info[0]['Challan']['challan_referance_no'], 'required'=>true)); ?>
        </div>
        <div class="form-group">
            <?php echo $this->Form->input('challan_date', array('type'=>'text', 'value'=>date('d-m-Y', strtotime($challan_info[0]['Challan']['challan_date'])), 'class' => 'form-control challan-datepicker','required'=>true, 'readonly' => true)); ?>
        </div>
        <div class="form-group">
            <?php echo $this->Form->input('remarks', array('value'=>$challan_info[0]['Challan']['remarks'], 'class' => 'form-control')); ?>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
               <tr>
				   <th>Product Type</th>
                  <th>Product Name</th>
                  <th class="text-center">Batch No.</th>
                  <th class="text-center">Quantity</th>
                  <th class="text-center">Expire Date</th>
                  <th class="text-center">Action</th>					
              </tr>
          </thead>
          <tbody>
           <tr>
            <td>
                <?php echo $this->Form->input('product_type', array('label'=>false,'id'=>'product_type', 'class' => 'full_width form-control product_type','empty'=>'---- Select Product Type ----')); ?>
            </td>
            <td>
                <?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'full_width form-control product_id chosen','id'=>'product_id','empty'=>'---- Select Product ----')); ?>
            </td>
            <!-- <td>
             <?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'full_width form-control product_id chosen','empty'=>'---- Select Product ----')); ?>
         </td> -->
         <td width="12%" align="center">							
             <?php echo $this->Form->input('batch_no', array('label'=>false, 'class' => 'full_width form-control batch_no')); ?>
         </td>
         <td width="12%" align="center">							
             <?php echo $this->Form->input('challan_qty', array('label'=>false, 'class' => 'full_width form-control quantity')); ?>
         </td>
         <td width="12%" align="center">							
             <?php echo $this->Form->input('expire_date', array('label'=>false, 'class' => 'full_width form-control expire_date expireDatepicker')); ?>
         </td>
         <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
     </tr>				
 </tbody>
</table>
<div class="table-responsive">			
<table class="table table-striped table-condensed table-bordered invoice_table">
    <thead>
       <tr>
          <th class="text-center" width="5%">SL.</th>
          <th class="text-center">Product Name</th>
          <th class="text-center" width="12%">Unit</th>
          <th class="text-center" width="12%">Batch No.</th>
          <th class="text-center" width="10%">Quantity</th>
          <th class="text-center" width="10%">Total Quantity</th>
          <th class="text-center" width="12%">Expire Date</th>
          <th class="text-center" width="15%">Remarks</th>
          <th class="text-center" width="10%">Action</th>					
      </tr>
  </thead>

  <tbody>
    <?php
	$display_serial_number = 1;
    $row_count_for_remove = 0;
    foreach ($challan_info[0]['ChallanDetail'] as $value) {
        $row_count_for_remove++;
        ?>

        <tr class="table_row" id="rowCount<?php echo $row_count_for_remove;?>">
            <td align="center">
                <?php echo $display_serial_number; 
				
				$display_serial_number++?>
            </td>
            <td>
                <input type="hidden" name="product_id_batch_date[<?php echo $row_count_for_remove;?>]" class="selected_product_id" value="<?php echo $value['product_id']; echo $value['batch_no']; echo $this->App->expire_dateformat($value['expire_date']); ?>"/>
                <?php echo $value['Product']['name']; ?>
                <input type="hidden" name="product_id[<?php echo $row_count_for_remove;?>]" value="<?php echo $value['product_id']; ?>"/>
            </td>
            <td align="center">
                <?php echo $value['MeasurementUnit']['name']; ?>
                <input type="hidden" name="measurement_unit[<?php echo $row_count_for_remove;?>]" value="<?php echo $value['measurement_unit_id']; ?>">
            </td>
            <td align="center">
                <input type="hidden" name="batch_no[<?php echo $row_count_for_remove;?>]" value="<?php echo $value['batch_no']; ?>">
                <?php echo $value['batch_no']; ?>
            </td>
            <td align="center">
                <input name="quantity[<?php echo $row_count_for_remove;?>]" class="product_quantity" value="<?php echo $value['challan_qty']; ?>" required >
            </td>
            <td align="center" class="product_id">
                <?php echo $value['challan_qty']; ?>
            </td>
            <td align="center">
                <input type="hidden" name="expire_date[<?php echo $row_count_for_remove;?>]" value="<?php if($value['expire_date'] !=' ') {echo $this->App->expire_dateformat($value['expire_date']);} ?>">
                <?php if($value['expire_date'] !=' ') {echo $this->App->expire_dateformat($value['expire_date']);} ?>
            </td>
            <td align="center">
                <input type="text" class="full_width form-control" name="remarks[<?php echo $row_count_for_remove;?>]" value="<?php echo $value['remarks']; ?>">
            </td>
            <td align="center">
                <button class="btn btn-danger btn-xs remove" value="<?php echo $row_count_for_remove?>"><i class="fa fa-times"></i></button>
            </td>
        </tr>

        <?php
    } 
    ?>
</tbody>
</table>
</div>
</br>
<div class="pull-right">
    <?php echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); ?>
    <?php echo $this->Form->submit('Draft', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft'));
	
	?>
	<?php //echo $this->Form->submit('btn_1', array('name' => 'btn','class' => 'btn btn-large btn-warning ')); ?>
<?php //echo $this->Form->submit('btn_2', array('name' => 'btn','class' => 'btn btn-large btn-warning')); ?>
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
        $('#product_type').selectChain({
            target: $('#product_id'),
            value:'name',
            url: '<?= BASE_URL.'Challans/get_product'?>',
            type: 'post',
            data:{'product_type_id': 'product_type'  }
        });
        var is_maintain_batch = 1;
        var is_maintain_expire_date = 1;
        $(".chosen").change(function () {
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
            }
        });
        });


        var rowCount=0;
        $('.table_row').each(function(){
          rowCount++;
        });

        $(".add_more").click(function () {

            var product_id = $('.product_id').val();
            var quantity = $('.quantity').val();
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
            } else if (is_maintain_batch == 1 && (batch_no == '' || batch_no ==0))
            {
                alert('Please enter batch number.');
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
            }else if(stock_check == true){
                alert('This product already added.');
                clear_field();
                return false;
            } else
            {
                rowCount++;
                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL;?>admin/products/product_details',
                    data: 'product_id=' + product_id,
                    cache: false,
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);

                        var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td>' + obj.Product.name + '<input type="hidden" name="product_check[' + rowCount + ']" class="selected_product_id" value="' +obj.Product.id+batch_no+expire_date+ '"/><input type="hidden" name="product_id[' + rowCount + ']"  value="' + obj.Product.id + '"/></td><td align="center">' + obj.ChallanMeasurementUnit.name + '<input type="hidden" name="measurement_unit[' + rowCount + ']" value="' + obj.Product.challan_measurement_unit_id + '"></td><td align="center"><input type="hidden" name="batch_no[' + rowCount + ']" value="' + batch_no + '">' + batch_no + '</td><td align="center"><input  name="quantity[' + rowCount + ']" class="' + product_id + '_product_qty  p_quantity" value="' + quantity + '" required ></td><td align="center" class="' + product_id + '"></td><td align="center"><input type="hidden" name="expire_date[' + rowCount + ']" class="p_expire_date" value="' + expire_date + '">' + expire_date + '</td><td align="center"><input type="text" class="full_width form-control" name="remarks[' + rowCount + ']"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('.invoice_table').append(recRow);
                        product_wise_total_quantity(product_id);
                        clear_field();

                        var total_quantity = set_total_quantity();
                        if (total_quantity > 0)
                        {
                            //$('.draft').prop('disabled', false);
                            //$('.save').prop('disabled', false);
							$('.draft').show();
                            $('.save').show();
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
            if (total_quantity <= 0)
            {
               //$('.draft').prop('disabled', true);
               //$('.save').prop('disabled', true);
			   $('.draft').hide();
               $('.save').hide();
           }
       });


       // $('.draft').prop('disabled', false);
       // $('.save').prop('disabled', false);

        function clear_field() {
            $('.product_type').val('');
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
                sum += parseFloat(total_quantity);
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
            //$('.draft').prop('disabled', true);
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

<script>
    $(document).ready(function()
	{
        $('body').on("blur", ".challan_referance_no", function challanReferance(){
			
            var challan_referance_no = $('.challan_referance_no').val();
			var challan_id = <?=$challan_info[0]['Challan']['id']?>;
            			
            $.ajax({
				url: '<?php echo BASE_URL.'admin/challans/challan_referance_validation' ?>',
                'type': 'POST',
                data: {
					challan_id: challan_id,
					challan_referance_no: challan_referance_no
					},
				//beforeSend: function(){alert(11)},
				//complete: function(){alert(12)},
                success: function(result)
				{
                    obj = jQuery.parseJSON(result);
					
                    if(obj == 1){
                        alert('Challan Referance Number Already Exist');
                       // $('.draft').prop('disabled', true);
                    }
					
                    if(obj == 0){
                        //$('.draft').prop('disabled', false);
                    }
                }
            });
        });
    });
</script>