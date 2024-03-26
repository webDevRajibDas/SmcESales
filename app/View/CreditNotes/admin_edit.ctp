
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
            <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Credit Note Edit'); ?></h3>
                <div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Credit Note List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('CreditNote', array('role' => 'form')); ?>
                <div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
                <div class="form-group">
				    <?php echo $this->Form->input('office_id', array('class' => 'form-control', 'id'=>"office_id",  'options'=>$offices, 'required'=>true,)); ?>
                </div>

                <div class="form-group" id="territory_id_div">
                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'options'=>$territory_list, 'required' => TRUE, )); ?>
                </div>

                <div class="form-group" id="market_id_so">
                    <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'options'=>$market_list, 'required' => TRUE, )); ?>
                </div>

                <div class="form-group" id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control', 'options'=>$outlet_list, 'required' => TRUE,)); ?>
                </div>
                

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Date From</th>
                            <th width="10%">Date To</th>
                            <th width="15%">Memo No</th>
                            <th width="15%">Product</th>
                            <th width="7%">Memo Qty</th>
                            <th width="7%">Retun Qty</th>
                            <th width="15%">Reason</th>
                            <th width="8%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                             <td>
                                <?php echo $this->Form->input('date_from', array( 'type'=>'text', 'label'=>false,'id'=>'date_from', 'class' => 'select_date full_width  form-control')); ?>
                            </td>
                            <td>
                              <?php  echo $this->Form->input('date_to', array( 'type'=>'text', 'label'=>false,'id'=>'date_to', 'class' => 'select_date full_width  form-control')); ?>
                            </td>
                            <td>
							    <?php echo $this->Form->input('memo_no', array('label'=>false, 'options'=>array(), 'class' => 'full_width form-control memo_id chosen','id'=>'memo_id','empty'=>'---- Select Memo ----')); ?>
                            </td>
                          
                            <td>
							    <?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'full_width form-control product_id chosen','id'=>'product_id','empty'=>'---- Select Product ----')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('sales_qty', array( 'type'=>'number', 'readonly'=>true, 'label'=>false,'id'=>'sales_qty', 'class' => 'full_width  form-control')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('return_qty', array( 'type'=>'number', 'label'=>false,'id'=>'return_qty', 'class' => 'full_width  form-control')); ?>
                            </td>
                            <td>
                                <?php echo $this->Form->input('reason', array( 'type'=>'text', 'label'=>false,'id'=>'reason', 'class' => 'full_width  form-control')); ?>
                            </td>
                           
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add </span></td>					
                        </tr>				
                    </tbody>
                </table>	
                <br><br>
                <div class="table-responsive">		
                <table class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">SL.</th>
                            <th class="text-center" width="15%">Memo No</th>
                            <th class="text-center" width="12%">Memo Date</th>
                            <th class="text-center" width="12%">Product</th>
                            <th class="text-center" width="10%">Sales Qty</th>
                            <th class="text-center" width="8%">Return Qty</th>
                            <th class="text-center" width="25%">Reason</th>
                            <th class="text-center" width="10%">Action</th>					
                        </tr>
                        <?php 
                            $i=1;
                            foreach($exiting_data as $v){ ?>
                        <tr class="table_row" id="rowCount<?=$i;?>">
                            <td align="center"><?=$i;?></td>
                            <td> <?=$v['Memo']['memo_no'];?>
                                <input type="hidden" name="memo_no_check[<?=$i;?>]" class="selected_product_id" value=" <?=$v['MemoDetail']['id'];?> "/>
                            </td>
                            <td align="center"><?=$v['Memo']['memo_date'];?></td>
                            <td align="center"><?=$v['Product']['name'];?></td>
                            <td align="center">
                                <?=$v['MemoDetail']['sales_qty'];?>
                                <input type="hidden" class="p_quantity" value="<?=$v['MemoDetail']['sales_qty'];?>">
                            </td>
                            <td align="center">
                                <input type="number" style="width: 50px;" max="<?=$v['MemoDetail']['sales_qty'];?>" name="return_qty[<?=$i;?>]" value="<?=$v['CreditNoteDetail']['return_qty'];?>">
                            </td>
                            <td align="center"><input type="text" value="<?=$v['CreditNoteDetail']['reason'];?>" class="full_width form-control" name="reason[<?=$i;?>]"></td>
                            <td align="center"><button type="button" class="btn btn-danger btn-xs remove" value="<?=$i;?>"><i class="fa fa-times"></i></button></td>
                        </tr>
                        <?php $i++; } ?>
                    </thead>					
                </table>
                </div>
                
                </br>
                <div class="pull-right">
                    <?php //echo $this->Form->submit('Save & Submit', array('class' => 'btn btn-large btn-primary save', 'div'=>false, 'name'=>'save', 'disabled')); ?>
                    <?php echo $this->Form->submit('Update', array('class' => 'btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
                </div>
			    <?php echo $this->Form->end(); ?>
                <br><br>&nbsp;<br>
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


$('#office_id').selectChain({
    target: $('#territory_id'),
    value:'name',
    url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
        type: 'post',
        data:{'office_id': 'office_id' }
});


$(document).ready(function () {

    ///get_territory_list();

    var rowCount=0;
    $('.table_row').each(function(){
        rowCount++;
    });

    $(".add_more").click(function () {
    
        var date_from = $("#date_from").val();
        var date_to = $("#date_to").val();
        var office_id = $("#office_id").val();
        var territory_id = $("#territory_id").val();
        var market_id = $("#market_id").val();
        var outlet_id = $("#outlet_id").val();
        var memo_id = $("#memo_id").val();
        var product_id = $("#product_id").val();
        var sales_qty = parseInt($("#sales_qty").val());
        var return_qty = parseInt($("#return_qty").val());
        var reason = $("#reason").val();

        if(office_id == ''){
            alert('Office can not empty.');
            return false;
        }
        if(territory_id == ''){
            alert('Territory can not empty.');
            return false;
        }
        if(market_id == ''){
            alert('Market can not empty.');
            return false;
        }
        if(outlet_id == ''){
            alert('Outlet can not empty.');
            return false;
        }
        if(date_from == ''){
            alert('From Date can not empty.');
            return false;
        } 
        if(memo_id == ''){
            alert('Memo No can not empty.');
            return false;
        }   
        if(product_id == ''){
            alert('Product can not empty.');
            return false;
        } 
        
        if(return_qty == ''){
            alert('Return can not empty.');
            return false;
        }else if(return_qty > sales_qty){
            alert('Return qty < sales qty.');
            return false;
        }

        

        if(reason == ''){
            alert('Reason can not empty.');
            return false;
        }

        var selected_stock_array = $(".selected_product_id").map(function() {
               return $(this).val();
        }).get();
        var product_check_id = product_id; 
        var stock_check = $.inArray(product_check_id,selected_stock_array) != -1;
        if(stock_check == true){
            alert('This product already added.');
            clear_field();
            return false;
        } 
        
        rowCount++;
        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>credit_notes/get_product_memo_detils',
            data: 'memodetail_product_id=' + product_id,
            cache: false,
            success: function (response) {
                var obj2 = jQuery.parseJSON(response);
                var obj = obj2.info;
                var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center">' + rowCount + '</td><td>' + obj.memo_no + '<input type="hidden" name="memo_no_check[' + rowCount + ']" class="selected_product_id" value="' +obj.md_id+ '"/></td><td align="center">' + obj.memo_date +'</td><td align="center">' + obj.p_name + '</td><td align="center"><input type="hidden" class="p_quantity" value="' + obj.sales_qty + '">' + obj.sales_qty + '</td><td align="center"><input type="number" style="width: 50px;" max="'+ sales_qty +'" name="return_qty[' + rowCount + ']" value="' + return_qty + '"></td><td align="center"><input type="text" value="'+ reason +'" class="full_width form-control" name="reason[' + rowCount + ']"></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                $('.invoice_table').append(recRow);
                clear_field();
                var total_quantity = set_total_quantity();
                if (total_quantity > 0)
                {
                    $('.draft').prop('disabled', false);
                }

            }
        });

        
    });

    $(document).on("click", ".remove", function () {
        var removeNum = $(this).val();
        $('#rowCount' + removeNum).remove();
        var total_quantity = set_total_quantity();
        if (total_quantity <= 0)
        {
            $('.draft').prop('disabled', true);
        }
    });

    $("#territory_id").change(function() {
        var territory_id  =$(this).val();
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_market_list' ?>',
            data: {
                'territory_id': territory_id
            },
            type: 'POST',
            success: function(data) {
                $("#market_id").html(data);
            }
        });
    });
    $("#market_id").change(function() {
        var market_id  =$(this).val();
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_outlet_list' ?>',
            data: {
                'market_id': market_id
            },
            type: 'POST',
            success: function(data) {
                $("#outlet_id").html(data);
            }
        });
    });

    //-------------get memo list ------------\\
    $('.select_date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
    });

    $("#date_to").datepicker().on('changeDate', function(e) {
        get_memo_function();
    });
    //----------------end-----------\\

    $("#memo_id").change(function() {
        var memo_id  =$(this).val();
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_memo_product_list' ?>',
            data: {
                'memo_id': memo_id
            },
            type: 'POST',
            success: function(data) {
                $("#product_id").html(data);
            }
        });
    });

    $("#product_id").change(function() {
        var memo_details_product_id  =$(this).val();
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_product_memo_details' ?>',
            data: {
                'memo_details_product_id': memo_details_product_id
            },
            type: 'POST',
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                $("#sales_qty").val(obj.sales_qty);
            }
        });
    });

  
});

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

    function clear_field(){
        $("#product_id").val('');
        $("#sales_qty").val('');
        $("#return_qty").val('');
        $("#reason").val('');
    }

    function get_memo_function(){

     
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();
            var office_id = $("#office_id").val();
            var territory_id = $("#territory_id").val();
            var market_id = $("#market_id").val();
            var outlet_id = $("#outlet_id").val();

             if(office_id == ''){
                alert('Office can not empty.');
                return false;
            }
            if(territory_id == ''){
                alert('Territory can not empty.');
                return false;
            }
            if(market_id == ''){
                alert('Market can not empty.');
                return false;
            }
            if(outlet_id == ''){
                alert('Outlet can not empty.');
                return false;
            }
            if(date_from == ''){
                alert('From Date can not empty.');
                return false;
            } 

            $.ajax({
                url: '<?= BASE_URL . 'credit_notes/get_memo_list' ?>',
                data: {
                    'office_id': office_id,
                    'territory_id': territory_id,
                    'market_id': market_id,
                    'outlet_id': outlet_id,
                    'date_from': date_from,
                    'date_to': date_to,
                },
                type: 'POST',
                success: function(data) {
                    $("#memo_id").html(data);
                }
            });
           


    } 

    function get_territory_list(){

        var office_id = $("#office_id").val();
        
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_territory_list_for_edit' ?>',
            data: {
                'office_id': office_id,
            },
            type: 'POST',
            success: function(data) {
                //console.log(data);
                $("#territory_id").html(data);
                $('#territory_id option[value="<?=$this->request->data['CreditNote']["territory_id"]?>"]').prop("selected", true);
                get_market_list();
            }
        });

    }

    function get_market_list(){

        var territory_id = $("#territory_id").val();
        
        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_market_list' ?>',
            data: {
                'territory_id': territory_id
            },
            type: 'POST',
            success: function(data) {
                $("#market_id").html(data);
                //$('#market_id').val('<?=$this->request->data['CreditNote']["market_id"]?>').change();
                $('#market_id option[value="<?=$this->request->data['CreditNote']["market_id"]?>"]').prop("selected", true);
                get_outlet_list();
            }
        });
    }

   function get_outlet_list() {

        var market_id  =$("#market_id").val();

        $.ajax({
            url: '<?= BASE_URL . 'credit_notes/get_outlet_list' ?>',
            data: {
                'market_id': market_id
            },
            type: 'POST',
            success: function(data) {
                $("#outlet_id").html(data);
                //$('#outlet_id').val('<?=$this->request->data['CreditNote']["outlet_id"]?>').change();
                $('#outlet_id option[value="<?=$this->request->data['CreditNote']["outlet_id"]?>"]').prop("selected", true);
            }
        });
    }


</script>
