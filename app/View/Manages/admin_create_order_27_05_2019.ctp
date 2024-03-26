<?php
    // pr($this->params);
    // echo $this->params['url']['csa'];
?>
<style>
    .width_100{
       width:100%;
   }
   input[type=number]::-webkit-inner-spin-button, 
   input[type=number]::-webkit-outer-spin-button { 
      -webkit-appearance: none; 
      margin: 0; 
  }
  .bonus{
   width: 130px !important;
}
.product_unit_name{
	width: 80px !important;
}
.product_id{
    width: 150px !important;
}
.open_bonus_product_id{
	width: 150px !important;
}
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
/*#bonus_product
{
    position: relative;
    bottom: 181px;
    left: 320px;
    width: 626px;
}*/
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Create Memo'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Order List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('Order', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('company_id', array('id' => 'company_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control company_id','empty' => '---- Select Company ----'));
                   }
                   else
                   {
                       echo $this->Form->input('company_id', array('id' => 'company_id', 'class' => 'form-control company_id'));
                   }
                   ?>
                </div>
                
                <div class="form-group">
                   <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id','empty' => '---- Select Office ----'));
                   }
                   else
                   {
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id'));
                   }
                   ?>
               </div>

          <div class="form-group required" id="spo_territory_id_div" style="display:none;">
            <?php echo $this->Form->input('spo_territory_id', array('id' => 'spo_territory_id', 'class' => 'form-control spo_territory_id', 'onChange' => 'rowUpdate(1);', 'required' => false, 'empty' => '---- Select SPO Territory ----', 'options' => $spo_territories)); ?>
        </div>


        <div class="form-group" id="territory_id_div">
            <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'onChange' => 'rowUpdate(1);', 'required' => TRUE, 'empty' => '---- Select Territory ----', 'options' => $territories)); ?>
        </div>

        <?php //if($csa==1){ ?>
             <div class="form-group">
                <?php echo $this->Form->input('thana_id', array('class' => 'form-control ', 'id' => 'thana_id','empty'=>'--- Select Thana ---','required'=>'required')); ?> 
            </div>
        <?php//}?>

        <div class="form-group"  id="market_id_so">
            <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'empty' => '---- Select Market ----')); ?>
        </div>

                <?php /*?><div class="form-group"  id="market_id_csa">
                    <?php echo $this->Form->input('market', array('type'=>'text', 'id' => 'market_name', 'class' => 'form-control market_name', 'required'=>TRUE)); ?>
                </div><?php */?>
                
                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => TRUE, 'empty' => '---- Select Outlet ----', 'options' => $outlets)); ?>
                </div>

                <?php /*?><div class="form-group"  id="outlet_id_csa">
                    <?php echo $this->Form->input('outlet', array('type'=>'text', 'id' => 'outlet_name', 'class' => 'form-control outlet_name', 'required'=>TRUE)); ?>
                </div><?php */?>

                <?php /*?><div class="form-group csa_name">
                    <?php echo $this->Form->input('csa_name', array('label'=>'CSA Name :', 'id' => 'csa_name', 'class' => 'form-control', 'required'=>TRUE)); ?>
                </div><?php */?>

                <div class="form-group">
                    <?php echo $this->Form->input('entry_date', array('class' => 'form-control datepicker fromDate','id'=>'fromDate', 'value' => (isset($this->request->data['Memo']['entry_date']) == '' ? $current_date : $this->request->data['Memo']['entry_date']), 'required' => TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker toDate','id'=>'toDate', 'value'=>$current_date, 'type' => 'text', 'required'=>TRUE)); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no', 'required'=>TRUE, 'type' => 'text', 'value'=>$generate_memo_no, 'readonly')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('memo_reference_no', array('class' => 'form-control memo_reference_no', 'maxlength' => '15', 'required'=>false,'type' => 'text')); ?>
                </div>
                
                <div class="table-responsive">
                    <!--Set Product area-->
                    <table class="table table-striped table-condensed table-bordered invoice_table">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">ID</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center" width="12%">Unit</th>
                                <th class="text-center" width="12%">Price</th>
                                <th class="text-center" width="12%">QTY</th>
                                <th class="text-center" width="12%">Value</th>
                                <th class="text-center" width="10%">Bonus</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="product_row_box">
                            <tr id="1" class="new_row_number">
                                <th class="text-center sl_memo" width="5%">1</th>
                                <th class="text-center" id="memo_product_list">
                                    <?php
                                    echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 product_id', 'empty' => '---- Select Product ----', 'label' => false, 'required' => true));
                                    ?>
                                    <input type="hidden" class="product_id_clone" />
                                    <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>
                                    <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly />
                                    <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" required />
                                    <input type="hidden" class="combined_product"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly />
                                </th>
                                <th class="text-center" width="10%">
                                    <input type="text" class="form-control width_100 bonus" disabled />
                                    <input type="hidden" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                                    <input type="hidden" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                                    <input type="hidden" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                    <!-- <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a> -->
                                    <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" align="right"><b>Total : </b></td>
                                <td align="center"><input name="data[Memo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right"><b>Cash Collection : </b></td>
                                <td align="center"><input name="data[Memo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" required />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4"> <a class="btn btn-primary btn-xs show_bonus" id= "show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
                                    <div id="bonus_product" class="modal fade" role="dialog">>

                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                   <h4 class="modal-title">Open Bonus</h4>
                                               </div>
                                               <div class="modal-body">
                                                <table class="table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Product Name</th>
                                                            <th class="text-center" width="12%">Unit</th>
                                                            <th class="text-center" width="12%">QTY</th>
                                                            <th class="text-center" width="10%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bonus_product">
                                                        <tr  class="bonus_row">
                                                            <th class="text-center" id="bonus_product_list">
                                                                <?php
                                                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'label' => false));
                                                                ?>
                                                                <input type="hidden" class="product_id_clone" />
                                                                <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
                                                            </th>
                                                            <th class="text-center" width="12%">
                                                                <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>
                                                                <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>

                                                                <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                                                <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                                                            </th>
                                                            <th class="text-center" width="12%">
                                                                <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" />
                                                                <input type="hidden" class="combined_product"/>
                                                            </th>
                                                            <th class="text-center" width="10%">
                                                                <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
											</div>
                                        </div>
                                    </div>        
                                </div>
                            </td>
                            <td  align="right"><b>Credit : </b></td>
                            <td align="center"><input name="data[Memo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" readonly />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-group" style="padding-top:20px;">
                <div class="pull-right">
                    <?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); ?>
                    <?php echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
                </div>
            </div>

            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>


</div>




<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
    $(document).ready(function(){
       $("#office_id").prop("selectedIndex", 0);
       $("#sale_type_id").prop("selectedIndex", 0);
       $("#territory_id").prop("selectedIndex", 0);
       $("#spo_territory_id").prop("selectedIndex", 0);
       $("#market_id").prop("selectedIndex", 0);
       $("#outlet_id").prop("selectedIndex", 0);
       $(".memo_reference_no").val('');
    /* $(".show_bonus").click(function(){
        $('#bonus_product').toggle(400);
    });
    $('#bonus_product').hide();*/
});
</script>




<script>
    function productList()
    {
        var csa_id=0
        var territory_id = $('#territory_id').val();	

        var sale_type_id = $("#sale_type_id option:selected").val(); 

        if(sale_type_id==3)
        {
            territory_id = $("#spo_territory_id option:selected").val(); 
        }
        if(sale_type_id==2)
        {
            csa_id = $("#csa_id option:selected").val(); 
        }
	//alert(territory_id);
	
	if(territory_id){
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL . 'admin/Memos/get_product'?>',
			data: {'territory_id':territory_id,'csa_id':csa_id},
			cache: false, 
			success: function(response){
				var json = $.parseJSON(response);
				$('.product_id option').remove();
				for (var i=0;i<json.length;++i)
				{
					$('.product_id').append('<option value="'+json[i].id+'">'+json[i].name+'</option>');
                }
            }
        });		
	}
};

function rowUpdate(productLit){

	sl = 1;
	
	product_list = '<div class="input select"><select id="MemoProductId" required="required" class="form-control width_100 product_id" name="data[MemoDetail][product_id][]"><option value="">---- Select Product ----</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[MemoDetail][product_category_id][]">';
	
	
	
	var current_row = '<th class="text-center sl_memo" width="5%">1</th><th id="memo_product_list" class="text-center">'+product_list+'</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" value="" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a></th>';
	
	$('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');
	
	$('#gross_value').val(0);
	//$('.memo_no').val('');
	
	if(productLit==1){
		productList();
	}else{
		$("#territory_id").prop("selectedIndex", 0);
	}

}

$(document).ready(function () 
{
    $('.company_id').selectChain({
            target: $('.office_id'),
            value:'name',
            url: '<?= BASE_URL.'admin/territories/get_office_list'?>',
            type: 'post',
            data:{'company_id': 'company_id' }
        });
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list_memo'?>',
        type: 'post',
        data: {'office_id': 'office_id'}
    });


		//start for spo
		$('.office_id').selectChain({
            target: $('.spo_territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_spo_territory_list'?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
		
		$('.spo_territory_id').selectChain({
            target: $('.territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_child_territory_list'; ?>',
            type: 'post',
            data: {'spo_territory_id': 'spo_territory_id'}
        });
		
		$('.sale_type_id').change(function () {
            var sale_type_id = $(this).val();
            if(sale_type_id==3){
                $('#spo_territory_id_div').show();	
                $("#spo_territory_id").prop('required', true);
            }else{
                $('#office_id').trigger('change');
                $('#spo_territory_id_div').hide();
                $("#spo_territory_id").prop('required', false);

            }
        });
		//end for spo

        $('.territory_id').selectChain({
            target: $('.market_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });

        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/doctors/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

        $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');           
        });

        $('.territory_id').change(function () {
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });




        /* temporary commented */
      /*
        $("body").on("change", "#sales_person_id", function () {
            var sales_person_id = $(this).val();
            $.ajax({
                url: '<?= BASE_URL . 'orders/get_territory_id' ?>',
                type: 'POST',
                data: {sales_person_id: sales_person_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    if (obj.territory_id != null) {
                        $("#territory_id").val(obj.territory_id);
                    } else {
                        alert('Territory Id not be Null');
                        return false;
                    }

                }

            });

        });
        */

    });
</script>

<script>

    function total_values(){
     var t = 0;
     $('.total_value').each(function(){
       if($(this).val()!=''){
        t += parseFloat($(this).val());
    }
});
     $('#gross_value').val(t);
 } 

 $(document).ready(function () {
    $("body").on("click", ".add_more", function () {
        var sl = $('.invoice_table>tbody>tr').length+1;

        var product_list = $('#memo_product_list').html();
        var product_box = $(this).parent().parent().parent();
        var current_row_no = $(this).parent().parent().attr('id');
            //alert(current_row_no);
            
            var current_row = '<th class="text-center sl_memo" width="5%"></th><th class="text-center">'+product_list+'</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';
            

            /*  var current_row = '<th class="text-center sl_memo" width="5%"></th><th class="text-center">'+product_list+'</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';*/
            
            var valid_row = $('#'+current_row_no+'>th>.product_rate').val();
            if (valid_row != '') {
                product_box.append('<tr id='+sl+' class=new_row_number>' + current_row + '</tr>');
                $('#'+sl+'>.sl_memo').text(sl);
              
              
                 //$(this).hide();
            
             
                
            }else{
                alert('Please fill up this row!');
            }

        });


    $("body").on("change", ".product_id", function () 
    {
        var ajax_img = 1;
        var new_product = 1;
        $('#myModal').modal('show');
        $('#loading').show();
            //console.log($('#'+sl+'>th>.bonus').val());
            $('#gross_value').val(0);
            
            var sl = $('.invoice_table>tbody>tr').length;

            var current_row_no = $(this).parent().parent().parent().attr('id');

            if ($('#'+current_row_no+'>th>.product_rate').val() == '') {
                $('#'+current_row_no+'>th>.bonus').val('N.A');
                $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
            }
            
            var product_change_flag = 1;
            var product_id_list = '';
            $('.product_id').each(function () 
            {
                if ($(this).val() != '') 
                {
                    //product_id_list = $(this).val()+','+product_id_list;
                    if (product_id_list.search($(this).val()) == -1) 
                    {
                        product_id_list = $(this).val() + ',' + product_id_list;
                    } 
                    else 
                    {
                        alert("This poduct already exists");
                        product_change_flag = 0;
                        $('#'+current_row_no+'>th>div>select').val($('#'+current_row_no+'>th>.product_id_clone').val());
                        if($('#'+current_row_no+'>th>.product_rate').val() == ''){
                            $(this).val('').attr('selected', true);
                            $('#'+current_row_no+'>th>.bonus').val('');
                        }
                        total_values();
                        //$(this).val('').attr('selected', true);
                        
                        new_product = 0;
                        $('#myModal').modal('hide');
                        $('#loading').hide();
                        return false; 
                    }

                }
                else 
                {

                   pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();

                   if(pro_val){
                      alert("Please select any product from last row or remove it!");
                  }else{
                      alert("Please select any product!");
                  }

                  product_change_flag = 0;
                  $('#'+current_row_no+'>th>div>select').val($('#'+current_row_no+'>th>.product_id_clone').val());
                  if($('#'+current_row_no+'>th>.product_rate').val() == ''){
                      $(this).val('').attr('selected', true);
                      $('#'+current_row_no+'>th>.bonus').val('');
                  }
                  total_values();
					//$(this).val('').attr('selected', true);
					
					new_product = 0;
					$('#myModal').modal('hide');
					$('#loading').hide();   
					return false;             
                }
            });

            var product_id = $(this).val();
            var product_box = $(this).parent().parent().parent();
            var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
            var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            var total_val = product_box.find("th:nth-child(6) .total_value");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            var combined_product_change = combined_product.val();


            if ($('#'+current_row_no+'>th>.product_rate').val() != '' && product_change_flag == 1) {

                var product_id_for_change = $('#'+current_row_no+'>th>.product_id_clone').val();
                
                $.ajax({
                    url: '<?= BASE_URL . 'orders/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product_change, product_id: product_id_for_change},
                    success: function (result) {
                        if (result == 'yes') {


                            /*-----------------------------------*/
                            var product_id_list_change = '';
                            $('.product_id').each(function () {
                                if ($(this).val() != '') {
                                    product_id_list_change = $(this).val() + ',' + product_id_list_change;
                                }
                            });
                            /*-----------------------------------*/

                            var combined_product_array = combined_product_change.split(',');
                            var product_id_list_pre_array = product_id_list_change.slice(0,-1);
                            var product_id_list_array = product_id_list_pre_array.split(',');

                            $.arrayIntersect = function(a, b)
                            {
                                return $.grep(a, function(i)
                                {
                                    return $.inArray(i, b) > -1;
                                });
                            };
                            var arr_intersect = $.arrayIntersect(combined_product_array, product_id_list_array);
                            var product_id_new = arr_intersect[0];
                            var no_of_new_combined_product = arr_intersect.length;
                            var prev_combined_row_no = $('.prate-'+product_id_new).parent().parent().attr('id');
                            var min_qty_new = $('#'+prev_combined_row_no+'>th>.min_qty').val();
                            //console.log(no_of_new_combined_product);
                            //console.log(product_id_new);
                            //console.log(min_qty_new);
                            //var territory_id = $('.territory_id').val();
                            
                            /*$('.product_id').each(function(){
                                $(this).trigger('change');
                            });*/
                            
                            if (no_of_new_combined_product > 0) {

                                $.ajax({
                                    url: '<?= BASE_URL . 'orders/get_combine_or_individual_price' ?>',
                                    'type': 'POST',
                                    data: {combined_product: combined_product_change, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list_change},
                                    success: function (result) {

                                        var obj = jQuery.parseJSON(result);

                                        if (obj.unit_rate != '') {
                                            product_rate.val(obj.unit_rate);
                                        }
                                        if (obj.total_value) {
                                            total_value.val(obj.total_value);
                                        }

                                        $.each(obj, function (index, value) {
                                            var prate = $(".prate-" + index);
                                            var tvalue = $(".tvalue-" + index);
                                            prate.val(value.unit_rate);
                                            tvalue.val(value.total_value);
                                        });

                                        var gross_total = 0;
                                        $('.total_value').each(function () {
                                          if($(this).val() !=''){
                                           gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                       }
                                   });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val('');

                                        var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

                                        if(product_category_id==32)
                                        {
                                          $('#'+current_row_no+'>th>.product_rate').val('0.00');
                                          $('.add_more').removeClass('disabled');
                                      }

                                  }
                              });
                            }
                            
                        }
                        
                    }
                });
}

            //product_rate.addClass('p_rate_'+product_id);
            //total_val.addClass('t_value_'+product_id);

            var rate_class = product_rate.attr('class').split(' ').pop();
            var value_class = total_val.attr('class').split(' ').pop();

            //console.log(rate_class);

            if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1)
            {
                product_rate.removeClass(rate_class);
                total_val.removeClass(value_class);
                /*-----------*/
                product_rate.addClass('prate-' + product_id);
                total_val.addClass('tvalue-' + product_id);
            } else {
                product_rate.addClass('prate-' + product_id);
                total_val.addClass('tvalue-' + product_id);
            }
            if($('#sale_type_id').val()==3)
            {
                var territory_id = $('.spo_territory_id').val();
            }
            else
            {
                var territory_id = $('.territory_id').val();
            }
            var toDate=$('.toDate').val();
            var fromDate=$('.fromDate').val();

             console.log(toDate);
            if (new_product == 1) {
                $.ajax({
                    url: '<?= BASE_URL . 'orders/get_product_unit' ?>',
                    'type': 'POST',
                    data: {product_id: product_id, territory_id: territory_id, product_id_list: product_id_list, fromDate:fromDate, toDate:toDate},
                    success: function (result) 
                    {
                        var obj = jQuery.parseJSON(result);
                        console.log(result);
                        product_unit.val(obj.product_unit.name);
                        product_unit_id.val(obj.product_unit.id);
                        product_rate.val(obj.product_price.general_price);
                        product_price_id.val(obj.product_price.id);
                        product_qty.val(obj.product_combination.min_qty);
                        combined_product.val(obj.combined_product);

                    //$('#product_category_id').val(obj.product_category_id);
                    $('#'+current_row_no+'>th>.product_category_id').val(obj.product_category_id);
                    $('#'+current_row_no+'>th>.product_id_clone').val(product_id);

                    var total_qty = obj.total_qty;
                    var general_price = obj.product_price.general_price;
                    var min_qty = obj.product_combination.min_qty;                 
                    var total_value = parseFloat(general_price*min_qty);

                    //var product_id = $('#'+sl+'>th>div>#MemoProductId').val();
                    //var a = '#'+sl+'>.tvalue-'+product_id;
                    //$('#'+current_row_no+'>th>.tvalue-'+product_id).val(total_value);
                    $('#'+current_row_no+'>th>.total_value').val(total_value);

                    //console.log(current_row_no);
					//alert($('#sale_type_id').val());
					<?php if($stock_validation){ ?>
                       if($('#sale_type_id').val()==1 || $('#sale_type_id').val()==3){
						//alert(total_qty);
                       $('#'+current_row_no+'>th>.min_qty').attr('max', total_qty);
                   }else{
                      $('#'+current_row_no+'>th>.min_qty').removeAttr('max');
                  }
                  <?php } ?>

                  $('.add_more').addClass('disabled');

                  var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

                  if(product_category_id==32)
                  {
                      $('#'+current_row_no+'>th>.product_rate').val('0.00');
                      $('.add_more').removeClass('disabled');
                      $('#loading').hide();
                      $('#myModal').modal('hide');
                  }
                  else
                  {
                    $('#'+current_row_no+'>th>.min_qty').trigger('keyup');	
                }

                total_values();

                if (obj.bonus_product_qty != undefined) {
                    $('#'+current_row_no+'>th>.bonus').val(obj.bonus_product_qty+'('+obj.bonus_product_name+')');
                    $('#'+current_row_no+'>th>.bonus_product_id').val(obj.bonus_product_id);
                    $('#'+current_row_no+'>th>.bonus_product_qty').val(obj.bonus_product_qty);
                    $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(obj.bonus_measurement_unit_id);
                }

                    //$('#loading').hide();
                    //$('#myModal').modal('hide');
                }
            });
            }

        });

/*------- unset session -------*/
});
</script>
<script>
    /*--------- check combined or individual product price --------*/
    $("body").on("keyup", ".min_qty", function () 
    {
       var current_row_no = $(this).parent().parent().attr('id');
       var product_category_id = $('#'+current_row_no+'>th>.product_category_id').val();

       pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();

       if (product_category_id != 32 && pro_val) {

          var sl = $('.invoice_table>tbody>tr').length;

          var product_box = $(this).parent().parent();
          var product_field = product_box.find("th:nth-child(2) .product_id");
          var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
          var product_rate = product_box.find("th:nth-child(4) .product_rate");
          var product_qty = product_box.find("th:nth-child(5) .min_qty");
          var total_value = product_box.find("th:nth-child(6) .total_value");
          var combined_product = product_box.find("th:nth-child(5) .combined_product");
          var combined_product = combined_product.val();
          var min_qty = product_qty.val();
          var id = product_field.val();

          delay(function()
          {

             $('#myModal').modal('show');
             $('#loading').show();

             if(min_qty == '' || min_qty == 0)
             {
                min_qty = 1;
                $('#'+current_row_no+'>th>.min_qty').val(1);
            }

            /*-----------------------------------*/
            var product_id_list = '';
            $('.product_id').each(function () {
                if ($(this).val() != '') {
                    product_id_list = $(this).val() + ',' + product_id_list;
                }
            });
            /*-----------------------------------*/

            $.ajax({
                url: '<?= BASE_URL . 'orders/get_combine_or_individual_price' ?>',
                'type': 'POST',
                data: {combined_product: combined_product, min_qty: min_qty, product_id: id, product_id_list: product_id_list},
                success: function (result) 
                {
                   var obj = jQuery.parseJSON(result);

                   if (obj.unit_rate != '') {
                      product_rate.val(obj.unit_rate);
                  }
                  if (obj.total_value) {
                      total_value.val(obj.total_value);
                  }

                  $.each(obj, function (index, value) {
                      var prate = $(".prate-" + index);
                      var tvalue = $(".tvalue-" + index);
                      prate.val(value.unit_rate);
                      tvalue.val(value.total_value);
                  });

                  var gross_total = 0;
                  $('.total_value').each(function () {
                      if($(this).val() !=''){
                         gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                     }
                 });
                  $("#gross_value").val(gross_total.toFixed(2));

                  if (obj.mother_product_quantity != undefined) {
                      var mother_product_quantity = obj.mother_product_quantity;
                      var bonus_product_id = obj.bonus_product_id;
                      var bonus_product_name = obj.bonus_product_name;
                      var bonus_product_quantity = obj.bonus_product_quantity;
                      var sales_measurement_unit_id = obj.sales_measurement_unit_id;
                      var no_of_bonus_slap = mother_product_quantity.length;
                      var mother_product_quantity_bonus = obj.mother_product_quantity_bonus;

                      for (var i = 0; i < no_of_bonus_slap; i++) 
                      {
                         if (parseFloat($('#'+current_row_no+'>th>.min_qty').val())>=parseFloat(mother_product_quantity[i].min) && parseFloat($('#'+current_row_no+'>th>.min_qty').val())<=parseFloat(mother_product_quantity[i].max)) 
                         {
                            if (i == 0) {
                               $('#'+current_row_no+'>th>.bonus').val('N.A');
                               $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                               $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                               $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                           }else{
                               $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i+(-1)]+'('+bonus_product_name[i+(-1)]+')');
                               $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i+(-1)]);
                               $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i+(-1)]);
                               $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i+(-1)]);
                           }
                           break;
                       }
                       else{
                        var current_qty = parseFloat($('#'+current_row_no+'>th>.min_qty').val());
                        var bonus_qty = Math.floor(current_qty/parseFloat(mother_product_quantity_bonus));

                        $('#'+current_row_no+'>th>.bonus').val(bonus_qty+' ('+bonus_product_name[i]+')');
                        $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
                        $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_qty);
                        $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
                    }
                }  
            }
            $('#cash_collection').val('');
            $('#loading').hide();
            $('#myModal').modal('hide');
            $('.add_more').removeClass('disabled');
        }
    });

}, 1000 );

}

});
</script>

<script>

    $(document).ready(function () 
    {
        $('body').on('click', '.delete_item', function () {
            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            combined_product = combined_product.val();
            var id = product_field.val();

            var total_value = $('.tvalue-'+id).val();
            var gross_total = $('#gross_value').val();
            var new_gross_value = parseFloat(gross_total-total_value);
            $('#gross_value').val(new_gross_value);
            $('#cash_collection').val('');
            
            alert('Removed this row -------');
            
            var min_qty = product_qty.val();
            
            if (product_field.val() == '') {
                //console.log('if');
                product_box.remove();

                var last_row = $('.invoice_table tbody tr:last').attr('id');
                $('#'+last_row+'>th>.add_more').show();
                console.log('if'+last_row);

                total_values();
            } 
            else 
            {
                //console.log('else');
                $.ajax({
                    url: '<?= BASE_URL . 'orders/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, product_id: id},
                    success: function (result) 
                    {
                        if (result == 'yes') {

                            product_box.remove();

                            var last_row = $('.invoice_table tbody tr:last').attr('id');
                            $('#'+last_row+'>th>.add_more').show();
                            console.log('else'+last_row);

                            /*-----------------------------------*/
                            var product_id_list = '';
                            $('.product_id').each(function () {
                                if($(this).val()!=''){
                                   product_id_list = $(this).val() + ',' + product_id_list;
                               }

                           });
                            /*-----------------------------------*/

                            var combined_product_array = combined_product.split(',');
                            var product_id_list_pre_array = product_id_list.slice(0,-1);
                            var product_id_list_array = product_id_list_pre_array.split(',');

                            $.arrayIntersect = function(a, b)
                            {
                                return $.grep(a, function(i)
                                {
                                    return $.inArray(i, b) > -1;
                                });
                            };
                            var arr_intersect = $.arrayIntersect(combined_product_array, product_id_list_array);
                            var product_id_new = arr_intersect[0];
                            var no_of_new_combined_product = arr_intersect.length;
                            var prev_combined_row_no = $('.prate-'+product_id_new).parent().parent().attr('id');
                            var min_qty_new = $('#'+prev_combined_row_no+'>th>.min_qty').val();
                            //console.log(no_of_new_combined_product);
                            //console.log(product_id_new);
                            //console.log(min_qty_new);

                            if (no_of_new_combined_product > 0) {
                                $.ajax({
                                    url: '<?= BASE_URL . 'orders/get_combine_or_individual_price' ?>',
                                    'type': 'POST',
                                    data: {combined_product: combined_product, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list},
                                    success: function (result) {

                                        var obj = jQuery.parseJSON(result);

                                        if (obj.unit_rate != '') {
                                            product_rate.val(obj.unit_rate);
                                        }
                                        if (obj.total_value) {
                                            total_value.val(obj.total_value);
                                        }

                                        $.each(obj, function (index, value) {
                                            var prate = $(".prate-" + index);
                                            var tvalue = $(".tvalue-" + index);
                                            prate.val(value.unit_rate);
                                            tvalue.val(value.total_value);
                                        });

                                        var gross_total = 0;
                                        $('.total_value').each(function () {
                                            if($(this).val() !=''){
                                               gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                           }
                                       });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val('');


                                    }
                                });
                            }
                        }
                        var i = 1;
                        $('.new_row_number').each(function(){
                            $(this).attr('id',i);
                            $('#'+i+'>.sl_memo').text(i++);
                        });
                    }
                });
}



            /*var i = 1;
            $('.new_row_number').each(function(){
                $(this).attr('id',i++);
                $('#'+i+'>.sl_memo').text(i++);
            });*/

        });

/*--------------------------------*/
$("body").on("keyup", "#cash_collection", function () {
    var gross_value = parseFloat($("#gross_value").val());
    var collect_cash = parseFloat($(this).val());
    var credit_amount = gross_value - collect_cash;
    if(credit_amount >= 0){
        $("#credit_amount").val(credit_amount.toFixed(2));
    }else{
        $("#credit_amount").val(0);
    }
});
});
</script>

<script>
    $(document).ready(function(){
        $('body').on("keyup", ".memo_no", function(){
            var memo_no = $('.memo_no').val();
            var sale_type = $('#sale_type_id').val();
            
            delay(function(){
              $.ajax({
                url: '<?php echo BASE_URL.'admin/memos/memo_no_validation' ?>',
                'type': 'POST',
                data: {memo_no: memo_no, sale_type: sale_type},
                success: function(result){
                    obj = jQuery.parseJSON(result);
                        /*if(obj == 1){
                            alert('Memo Number Already Exist');
                            $('.submit').prop('disabled', true);
                        }*/
                        if(obj == 0){
                            $('.submit_btn').prop('disabled', false);
                        }else{
                            alert('Memo Number Already Exist');
                            $('.submit_btn').prop('disabled', true);
                        }
                    }
                });
          }, 1000 );

        });
    });

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    /*For Adding Bonus Product list : START*/
    $(document).ready(function(){
        $("body").on("click", ".bonus_add_more", function () {
            var product_list = $('#bonus_product_list').html();
            var product_bonus_row =
            '\
            <tr class="bonus_row">\
                <th class="text-center">'+product_list+'</th>\
                <th class="text-center" width="12%">\
                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>\
                    <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
                    <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
                    <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
                </th>\
                <th class="text-center" width="12%">\
                    <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required />\
                    <input type="hidden" class="combined_product"/>\
                </th>\
                <th class="text-center" width="10%">\
                    <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
                    <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
                </th>\
            </tr>\
            ';
            var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
            if(product_id)
            {
                $(this).hide();
                $(".bonus_product").append(product_bonus_row);
            }
            else
            {
                alert('plese select product first');
            }
        });
        $("body").on("click", ".bonus_remove", function () {
            $(this).parent().parent().remove();
            // var total_tr= $(".bonus_row").length;
            $(".bonus_row").last().find('.bonus_add_more').show();
        });
        $("body").on("change",".open_bonus_product_id",function(){
            var product_id=$(this).val();
            var product_box=$(this).parent().parent().parent();  
            var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
            var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
            var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
            var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
            var territory_id = $('.territory_id').val();
            $.ajax({
                url: '<?= BASE_URL . 'orders/get_bonus_product_details' ?>',
                'type': 'POST',
                data: {product_id: product_id,territory_id:territory_id},
                success: function (result) 
                {
                    var data = $.parseJSON(result);                  
                    
                    product_category_id.val(data.category_id);
                    product_unit_name.val(data.measurement_unit_name);
                    product_unit_id.val(data.measurement_unit_id);
                    product_qty.val(1);
                    product_qty.attr('min',1);
                    product_qty.attr('max',data.total_qty);
                },
                error:function(error)
                {
                    product_category_id.val();
                    product_unit_name.val();
                    product_unit_id.val();
                    product_qty.val(0);
                }
            });
        });
        $("body").on("change","#toDate",function(){
            var territory_id = $('#territory_id').val();    

            var sale_type_id = $("#sale_type_id option:selected").val(); 

            if(sale_type_id==3){
              territory_id = $("#spo_territory_id option:selected").val(); 
          }

    //alert(territory_id);
    var toDate=$('.toDate').val();
    console.log(toDate);
    console.log(territory_id);

    if(territory_id){
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL . 'orders/get_bonus_product'?>',
            data: {territory_id:territory_id, toDate:toDate},
            cache: false, 
            success: function(response){
                var json = $.parseJSON(response);
                console.log(json);
                $('.open_bonus_product_id option').remove();
                $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
                for (var i=0;i<json.length;++i)
                {
                    $('.open_bonus_product_id').append('<option value="'+json[i].Product.id+'">'+json[i].Product.name+'</option>');
                }
            }
        });     
    }
});
    });
/*For Adding Bonus Product list : START*/
<?php if($csa==1) {?>
    /*For Csa Memo Need Two Extra field (csa and thana) : Start*/
    $(document).ready(function(){
        if($(".office_id").val())
        {
            get_csa_by_office_id($(".office_id").val());
        }
        $(".office_id").change(function(){
            get_csa_by_office_id($(this).val());
        });
        $("#csa_id").change(function(){
            get_territory_id_by_csa_id($(this).val());
        });
        $(".territory_id").change(function(){
            get_thana_by_territory_id($(this).val());
        });
        $("#thana_id").change(function(){
            get_market_by_thana_id($(this).val());
        });
        function get_csa_by_office_id(office_id)
        {
           $.ajax({
                url:'<?= BASE_URL . 'orders/get_csa_list_by_office_id'?>',
                data:{'office_id':office_id},
                type:'POST',
                success:function(data)
                {
                    $("#csa_id").html(data);
                }
           });
        }
        function get_territory_id_by_csa_id(csa_id)
        {
            $.ajax({
                url:'<?= BASE_URL . 'orders/get_territory_list_by_csa_id'?>',
                data:{'csa_id':csa_id},
                type:'POST',
                success:function(data)
                {
                    // console.log(data);
                    $(".territory_id").html(data);
                }
           });
        }

        function get_thana_by_territory_id(territory_id)
        {
            $.ajax({
                url:'<?= BASE_URL . 'orders/get_thana_by_territory_id'?>',
                data:{'territory_id':territory_id},
                type:'POST',
                success:function(data)
                {
                    // console.log(data);
                    $("#thana_id").html(data);
                }
           });
        }
         function get_market_by_thana_id(thana_id)
        {
            $.ajax({
                url:'<?= BASE_URL . 'orders/get_market_by_thana_id'?>',
                data:{'thana_id':thana_id},
                type:'POST',
                success:function(data)
                {
                    // console.log(data);
                    $("#market_id").html(data);
                }
           });
        }
    });
    /*For Csa Memo Need Two Extra field (csa and thana) : End*/
<?php } ?>
</script>
