<?php 

?>
<style>
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
</style>

<?php /*?><div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div><?php */?>

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
                <div class="col-lg-6">
                    <div class="form-group">
                        <?php echo $this->Form->input('id', array('class' => 'form-control id', 'type'=>"hidden")); ?>
                        </div>
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
                        <?php echo $this->Form->input('description', array('class' => 'form-control description','type' => 'text')); ?>
                        </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                    <?php echo $this->Form->input('date_from', array('label'=>'Date From:','class' => 'form-control datepicker date_from ','id'=>'date_from', 'type' => 'text', 'required'=>TRUE )); ?>
                    </div>
                    <div class="form-group">
                    <?php echo $this->Form->input('date_to', array('label'=>'Date TO:','class' => 'form-control datepicker date_to ','id'=>'date_to', 'type' => 'text', 'required'=>TRUE)); ?>
                    </div>
                    <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control','type'=>'checkbox','label'=>'<b>Is Active :</b>')); ?>
                    </div>
                </div>
            </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Memo Value</th>
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
							<?php echo $this->Form->input('discount', array('label'=>false, 'class' => 'full_width form-control discount','type'=> 'number')); ?>
                            </td>
							
							<td width="12%" align="center">							
							<?php echo $this->Form->input('discount_type', array('label'=>false, 'class' => 'full_width form-control discount_type','options'=>$discount_types)); ?>
                            </td>
							
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add </span></td>					
                        </tr>				
                    </tbody>
                </table>
                <br>		
                <table class="table table-striped table-condensed table-bordered discount_table" id ="discount_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center">Memo Value</th>
                            <th class="text-center" width="10%">Discount</th>
							<th class="text-center" width="10%">Discount Type</th>
                            <th class="text-center" width="10%">Action</th>                 
                        </tr>
                    </thead>
                    <tbody>
                    	<?php 
						$key=0;
						if($distDiscount['DistDiscountDetail'])
						{
							foreach ($distDiscount['DistDiscountDetail'] as $key => $value) 
							{ 
                    	?>
							<tr class="table_row" id="rowCount<?php echo $key+1;?>">
								<td align="center" id="<?php echo $key+1;?>" > <?php echo $key+1;?></td>
								<td align="center"><?php echo $value['memo_value'];?>
									<input type="hidden" name="memo_value[<?php echo $key+1;?>]" value="<?php echo $value['memo_value'];?>"/>
								</td>
								<td align="center"><?php echo $value['discount_percent'];?>
									<input name="discount_percent[<?php echo $key+1;?>]" type="hidden" class="<?php echo $value['discount_percent'];?>_discount" value="<?php echo $value['discount_percent'];?>" >
								</td>
								<td align="center"><?php echo $value['discount_type']==2?'Taka':'Percentage';?>
									<input name="discount_type[<?php echo $key+1;?>]" type="hidden" class="<?php echo $value['discount_type'];?>_discount" value="<?php echo $value['discount_type'];?>" >
								</td>
								<td align="center">
									<button class="btn btn-danger btn-xs remove" value="<?php echo $key+1;?>">
										<i class="fa fa-times"></i>
									</button>
								</td>
							</tr>
                    	<?php  
							}
						}
						?>
                    </tbody>					
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
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '0d',
            'autoclose': true
        });
    });
</script>
<script>
    $(document).ready(function () {
        
        $('#office_id').selectChain({
            target: $('#distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDiscounts/get_distributor_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

		var rowCount = <?=($distDiscount['DistDiscountDetail'])?count($distDiscount['DistDiscountDetail']):1?>;
        $(".add_more").click(function () {
            //alert(rowCount);
            var amount = $('.amount').val();
            var discount = $('.discount').val();
			var discount_type = $('.discount_type').val();
			
			discount_text = discount_type==1?'Percentage':'Taka';
           
            var date_from = $('.date_from').val();
            var date_to = $('.date_to').val();

            //var rowCount = parseInt($('#discount_table tr:last').attr('id')) + 1;
           
           console.log(rowCount);
            if (amount == '')
            {
                alert('Please entry amount');
			    $('.amount').val('');
                return false;
            }
            else if (discount == '')
            {
                alert('Please entry discount');
                $('.discount').val('');
                return false;
            }
            else
            {
                rowCount++;

                var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td align="center">' + amount + '<input type="hidden" name="memo_value[' + rowCount + ']" value="' + amount+ '"/></td><td align="center"> '+ discount+'<input type="hidden"  name="discount_percent[' + rowCount + ']" class="' + rowCount + '_discount" value="' + discount + '" required ></td><td align="center"> '+ discount_text +'<input type="hidden"  name="discount_type[' + rowCount + ']" class="' + rowCount + '_discount_type" value="' + discount_type + '" required ></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                console.log(recRow);
               
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


        //$('.save').prop('disabled', true);

        function clear_field() {
            $('.amount').val('');
            $('.discount').val('');
			//$('.discount_type').val('');
            $('.add_more').val('');
           
        }

        $("form").submit(function () {
            $('.save').prop('disabled', true);
        });
    });
</script>
