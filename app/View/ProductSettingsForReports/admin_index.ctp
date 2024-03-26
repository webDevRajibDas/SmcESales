<?php //pr($exist_data);die();?>
<style>
    #divLoading {
        display : none;
    }
    #divLoading.show {
        display : block;
        position : fixed;
        z-index: 100;
        background-image : url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;   
        opacity : 0.4;
        background-repeat : no-repeat;
        background-position : center;
        left : 0;
        bottom : 0;
        right : 0;
        top : 0;
    }
    #loadinggif.show {
        left : 50%;
        top : 50%;
        position : absolute;
        z-index : 101;
        width : 32px;
        height : 32px;
        margin-left : -16px;
        margin-top : -16px;
    }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                 <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Product Settings for Report'); ?></h3> 
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('ProductSetting', array('role' => 'form')); ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Item</th>
                            <th class="text-center">Product Name</th>
                            <!--<th class="text-center">Order Number</th>-->                 
                            <th class="text-center">Action</th>					
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="12%" align="center">                         
                            <?php echo $this->Form->input('item_type', array('label'=>false, 'class' => 'full_width form-control item_type','id'=>'item_type','empty'=>'---- Select ----','options'=>$item_type)); ?>
                            </td>
                            <td  align="center">
                            <?php echo $this->Form->input('group_item', array('label'=>false, 'class' => 'full_width form-control group_item','empty'=>'---- Select ----','options'=>$items,'required'=> false)); ?>
                            </td>
                            <td  align="center">
                            <?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'full_width form-control product_id','empty'=>'---- Select ----','required'=> false)); ?>
                            </td>
                            <!-- <td width="12%" align="center">                         
                            <?php //echo $this->Form->input('order', array('label'=>false, 'class' => 'full_width form-control order')); ?>
                            </td> -->
                            <td width="10%" align="center"><span class="btn btn-xs btn-primary add_more"> Add Product </span></td>					
                        </tr>				
                    </tbody>
                </table>
                <br>

                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <style>
                    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
                    #sortable li { width: 100%; float:left; margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 0px; font-size: 1.4em; height: auto; font-size:15px;}
                    #sortable li:hover{ cursor:move; }
                </style>

                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script>
                    $( function() {
                        var currentlyScrolling = false;

                        var SCROLL_AREA_HEIGHT = 140;
                        //$( "#sortable" ).sortable();
                        $('#sortable').sortable({
                            axis: 'y',
                            scroll: true,
                            scrollSensitivity: 80,
                            scrollSpeed: 3,
                            sort: function(event, ui) {
                                if (currentlyScrolling) {
                                    return;
                                }

                                var windowHeight = $(window).height();
                                var mouseYPosition = event.clientY;

                                if (mouseYPosition < SCROLL_AREA_HEIGHT) {
                                    currentlyScrolling = true;

                                    $('html, body').animate({
                                        scrollTop: "-=" + windowHeight / 2 + "px"
                                    }, 
                                    400, 
                                    function() {
                                        currentlyScrolling = false;
                                    });
                                } else if (mouseYPosition > (windowHeight - SCROLL_AREA_HEIGHT)) {

                                    currentlyScrolling = true;

                                    $('html, body').animate({
                                        scrollTop: "+=" + windowHeight / 2 + "px"
                                    }, 
                                    400, 
                                    function() {
                                        currentlyScrolling = false;
                                    });
                                }
                            },
                            update: function (event, ui) {
                                var data = $(this).sortable('serialize');
                                // POST to server using $.post or $.ajax
                                $.ajax({
                                    data: data,
                                    beforeSend: function() {$("div#divLoading").addClass('show');},
                                    type: 'POST',
                                    url: '<?= BASE_URL.'ProductSettingsForReports/product_rearrange_update'?>',
                                    success: function()
                                    {
                                        $("div#divLoading").removeClass('show');
                                    }
                                });
                            }
                        });
                        $( "#sortable" ).disableSelection();
                    } );
                </script>		
                <table class="table table-striped table-condensed table-bordered invoice_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="10%">Item Type</th>
                            <th class="text-center" width="10%">Item</th>
                            <th class="text-center">Product Name</th>
                            <!--<th class="text-center" width="10%">order</th>	-->		
                            <th class="text-center" width="10%">Action</th>		
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        <?php if(!empty($exist_data)){
							foreach($exist_data as $key => $value){
								$p_name = $value['Product']['name'];
								$p_id = $value['Product']['id'];
								$c_id = $value['Product']['product_category_id'];
								$b_id = $value['Product']['brand_id'];
								$source = $value['Product']['source'];
								$item_type = $value['ProductSettingsForReport']['item_type'];
								$item = $value['ProductSettingsForReport']['item'];
								if($item_type == 0){
									$group_item = $value['ProductCategory']['name'];
									$item_name = 'Category';
								}elseif($item_type == 1){
									$group_item = $value['Brand']['name'];
									$item_name = 'Brand';
								}else{
									$group_item = $value['Product']['source'];
									$item_name = 'Company';
								}
								$item = $value['ProductSettingsForReport']['item'];
							?>
						<tr class="table_row" id="rowCount<?=$key?>">
							<td align="center">
								<input type="hidden" name="item_type['<?=$key?>']" class="'<?=$p_id?>'_item_type  p_item_type" value="<?=$item_type?>" required >
								<?php echo $item_name;?>
							</td>
							<td align="center">
								<input type="hidden" name="group_item['<?=$key?>']" class="'<?=$p_id?>'_group_item  p_group_item" value="<?=$item?>" required >
								<?php echo $group_item;?>
							</td>
							<td  align="center"><?php echo $p_name;?>
								<input type="hidden" name="product_id['<?=$key?>']" value="<?=$p_id?>"/>
							</td>
							<td align="center">
								<button class="btn btn-danger btn-xs remove" value="<?=$key?>">
									<i class="fa fa-times"></i>
								</button>
							</td>
						</tr>
						<?php } }?>
                    </tbody>					
                </table>
                <br>
                <br>
                <br>
			<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary save')); ?>
			<?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<script>
    var item_type_array = new Array();
    $(document).ready(function () {
        var rowCount =  document.getElementById('sortable').rows.length;;
        var p_rowCount = 0;
        
        item_type_array = ['Category','Brand','Company'];
		//item_type_array.push('Category');
		//item_type_array.push('Brand');
		//item_type_array.push('Company');
        
		console.log('item_type_array');
		console.log(item_type_array);
        $(".add_more").click(function () {
            var product_id = $('.product_id').val();
            var item_type = $('.item_type').val();
            var group_item = $('.group_item').val();
			var group_item_text = $('.group_item option:selected').text();
           // var order = $('.order').val();
            var group_name = item_type_array[item_type];
            console.log(group_name);
            if (product_id == '')
            {
                alert('Please select any product.');
                return false;
            } else if (item_type == '')
            {
                alert('Please enter valid group by.');
                return false;
            }
            /*else if (order == '')
            {
                alert('Please Enter Order Number');
                return false;
            }*/
            else
            {
                rowCount++;
                $.ajax({
                    type: "POST",
                    url: '<?php echo BASE_URL;?>admin/ProductSettingsForReports/product_details',
                    data:  {product_id: product_id},
                    cache: false,
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                        console.log(obj);
						//var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center"><input type="hidden" name="item_type[' + rowCount + ']" class="' + product_id + '_item_type  p_item_type" value="' + item_type + '" required >'+group_name+'</td><td align="center"><input type="hidden" name="group_item[' + rowCount + ']" class="' + product_id + '_group_item  p_group_item" value="' + group_item + '" required >'+group_item_text+'</td><td  align="center">'+obj.Product.name+'<input type="hidden" name="product_id[' + rowCount + ']" value="'+product_id+'"/></td></tr>';
						var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center"><input type="hidden" name="item_type[' + rowCount + ']" class="' + product_id + '_item_type  p_item_type" value="' + item_type + '" required >'+group_name+'</td><td align="center"><input type="hidden" name="group_item[' + rowCount + ']" class="' + product_id + '_group_item  p_group_item" value="' + group_item + '" required >'+group_item_text+'</td><td  align="center">'+obj.Product.name+'<input type="hidden" name="product_id[' + rowCount + ']" value="'+product_id+'"/></td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                     
                        //var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td align="center"><input type="hidden" name="item_type[' + rowCount + ']" class="' + product_id + '_item_type  p_item_type" value="' + item_type + '" required >'+group_name+'</td><td align="center"><input type="hidden" name="group_item[' + rowCount + ']" class="' + product_id + '_group_item  p_group_item" value="' + group_item + '" required >'+group_item_text+'</td><td  align="center">'+obj.Product.name+'<input type="hidden" name="product_id[' + rowCount + ']" value="'+product_id+'"/></td><td align="center"><input type="hidden" name="order[' + rowCount + ']" class="' + product_id + '_order  p_order" value="' + order + '" required >' + order + '</td><td align="center"><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                        $('#sortable').append(recRow);
                        
                        clear_field();
                        $('.save').prop('disabled', false);
                       
                    }
                });
            }
        });
        
        

        

        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            var p_id = $("input[name~='product_id[" + removeNum + "]']").val();
            $('#rowCount' + removeNum).remove();
        });


        //$('.save').prop('disabled', true);

        function clear_field() {
            //$('#product_type').val('');
            $('.product_id').val('');
            //$('.item_type').val('');
            //$('.order').val('');
            //$('.chosen').val('').trigger('chosen:updated');
            $('.add_more').val('');
            
        }
        $("form").submit(function () {
            $('.save').prop('disabled', true);
        });

        

        $('body').on("change", ".item_type", function () {
            var item_type = $(this).val();
            get_items(item_type);
        });
        function get_items(item_type){
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL;?>ProductSettingsForReports/get_items',
                data:  {item_type: item_type},
                cache: false,
                success: function (response) {
                    //var obj = jQuery.parseJSON(response);
                    //console.log(obj);
                    $(".group_item").html(response);
                }
            });
        }
		
		/*$('.group_item').selectChain({
			target: $('.product_id'),
			value:'name',
			url: '<?= BASE_URL.'ProductSettingsForReports/get_products'?>',
			type: 'post',
			data:{'group_item': 'group_item','item_type': 'item_type' }
		});*/
        $('body').on("change", ".group_item", function () {
            var group_item = $(this).val();
            if(group_item != ''){
                get_products(group_item);
            }
        });
        function get_products(group_item){
            var item_type = $('.item_type').val();
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL;?>ProductSettingsForReports/get_products',
                data:  {item_type:item_type,group_item: group_item},
                cache: false,
                success: function (response) {
                    $(".product_id").html(response);
                }
            });
        }
		
    });
</script>




