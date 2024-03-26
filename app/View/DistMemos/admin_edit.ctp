<script>
var tso_list='<?php echo json_encode($tso_list);?>';
var ae_list='<?php echo json_encode($ae_list);?>';
</script>

<?php 

  //pr($existing_record);die();
   $memo_date =date('d-m-Y',strtotime( $existing_record['DistMemo']['memo_date']));
   $selected_ae=$existing_record['DistMemo']['ae_id']; 
   $selected_tso=$existing_record['DistMemo']['tso_id'];
   
   $selected_ae_name="";
   $selected_tso_name="";
           
           
   if($selected_ae)
   $selected_ae_name=$ae_list[$selected_ae];
   
   if($selected_tso)
   $selected_tso_name=$tso_list[$selected_tso];


   $open_bonus_product_option = $product_list;
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
    
   .errorInput
    {
            border:1px solid #ff0000;
    }
    
    /*#bonus_product
    {
        position: relative;
        bottom: 181px;
        left: 320px;
        width: 626px;
    }*/
</style>
<?php 
/*
pr($srs);
pr($distributors);
pr($existing_record);exit;
 * 
 */
?>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit SR Memo'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('DistMemo', array('role' => 'form')); ?>
                            
                 <div class="form-group">
                    <?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker', 'type' => 'text', 'required'=>TRUE,  'value'=>$existing_record['memo_date'])); ?>
                </div>
                            
                
                    <?php echo $this->Form->input('entry_date', array('type'=>'hidden','class' => 'form-control datepicker', 'value' => $existing_record['memo_time'], 'required' => TRUE)); ?>
               
                            
                 <div class="form-group">
                    <?php echo $this->Form->input('memo_reference_no', array('label'=>'Memo Number :','class' => 'form-control memo_reference_no','value'=>$existing_record['dist_memo_no'], 'maxlength' => '15', 'required'=>TRUE,'type' => 'text' ,'readonly')); ?>
                </div>
               
                
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'selected'=>$existing_record['office_id'], 'disabled')); ?>
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['office_id'])); ?>
                </div>	                   
                   
 <?php echo $this->Form->input('sale_type_id', array('class' => '', 'required'=>TRUE, 'id' => 'sale_type_id', 'type' => 'hidden', 'value'=> '10')); ?>                    
               
                 <div class="form-group">
                <?php echo $this->Form->input('distributor_id', array('id' => 'distributor_id', 'class' => 'form-control distributor_id', 'required' => TRUE, 'options'=>$distributors, 'selected'=>$existing_record['distributor_id'], 'disabled')); ?>
                 <?php echo $this->Form->input('distributor_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['distributor_id'])); ?>
                </div>

             <div class="form-group">
                <?php echo $this->Form->input('sr_id', array('label'=>'SR','id' => 'sr_id', 'class' => 'form-control sr_id', 'required' => TRUE, 'options'=>$srs, 'selected'=>$existing_record['sr_id'], 'disabled')); ?>
                 <?php echo $this->Form->input('sr_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['sr_id'])); ?>
              </div>
                
            

                
		
                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['territory_id'])); ?>
                    <?php echo $this->Form->input('thana_id', array('id' => 'thana_id','class' => 'form-control thana_id','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['thana_id'])); ?>

               <div class="form-group">
                   <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','label'=>'Route/Beat', 'class' => 'form-control','required' => TRUE, 'options'=>$distRoutes, 'selected'=>$existing_record['dist_route_id'], 'disabled')); ?>
                   <?php echo $this->Form->input('dist_route_id', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['dist_route_id'])); ?>
                </div>

                <div class="form-group"  id="market_id_so">
                    <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'options' => $markets, 'selected'=>$existing_record['market_id'])); ?>
                </div>
                
                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => TRUE, 'options' => $outlets, 'selected'=>$existing_record['outlet_id'])); ?>
                </div>

                <div class="form-group">
                    <?php
                        echo $this->Form->input('ae_name', array('label'=>'Area Executive :','id'=>'ae_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"$selected_ae_name",'readonly'));
                   ?>
                 </div>
                 
                 <div class="form-group">
                    <?php
                        echo $this->Form->input('tso_name', array('label'=>'TSO :','id'=>'tso_name','class' => 'form-control', 'required' => true, 'type' => 'text','value'=>"$selected_tso_name",'readonly'));
                   ?>
                 </div>  
                            
                        <?php echo $this->Form->input('ae_id', array('type' => 'hidden','id'=>'ae_id','label' => false, 'class' => 'form-control', 'value' =>$selected_ae)); ?> 
                        <?php echo $this->Form->input('tso_id', array('type' => 'hidden', 'id'=>'tso_id','label' => false, 'class' => 'form-control', 'value' =>$selected_tso)); ?>
                    
               
                
                    <?php echo $this->Form->input('dist_memo_no', array('class' => 'form-control','required'=>TRUE,'type' => 'hidden', 'value'=>$existing_record['dist_memo_no'], 'readonly')); ?>
                
                
				
                
                <div class="table-responsive">	
				<!--Set Product area-->
				<table class="table table-striped table-condensed table-bordered invoice_table">
				<thead>
					<tr>
						<th class="text-center" width="5%">ID</th>
						<th class="text-center">Product Name</th>
						<th class="text-center" width="12%">Unit</th>
						<th class="text-center" width="12%">Rate</th>
						<th class="text-center" width="12%">QTY</th>
						<th class="text-center" width="12%">Value</th>
<!--						<th class="text-center" width="10%">Bonus</th>-->
						<th class="text-center" width="10%">Action</th>
					</tr>
				</thead>
				<tbody class="product_row_box">
				<?php
					if(!empty($existing_record)) {
            $sl = 1;
						$total_price = 0;
						$gross_val = 0;
						foreach($existing_record['DistMemoDetail'] as $val){

              if($val['price']==0.000)
                continue;

							$total_price = $val['price'] * $val['sales_qty'];
							$gross_val = $gross_val + $total_price;
				?>
					<tr id="<?php echo $sl?>" class="new_row_number">
						<th class="text-center sl_memo" width="5%"><?php echo $sl?></th>
						<th class="text-center">
							<?php
								echo $this->Form->input('product_id',array('name'=>'data[MemoDetail][product_id][]','class'=>'form-control width_100 product_id','required'=>TRUE,'options'=>$product_list,'empty'=>'---- Select Product ----','label'=>false,'default'=>$val['product_id'],'id'=>"MemoProductId"));
							?>
                            <input type="hidden" class="product_id_clone" value="<?php echo $val['product_id']; ?>" />
                            <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                            <input type="hidden" class="ajax_flag" value="1">
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="" class="form-control width_100 product_unit_name" value="<?=$val['measurement_unit_name']?>" disabled/>
							<input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id" value="<?=$val['measurement_unit_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate <?='prate-'.$val['product_id']?>" value="<?=$val['price']?>" readonly />
							<input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id" value="<?=$val['product_price_id']?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="number" step="any" min="0" name="data[MemoDetail][sales_qty][]" class="form-control width_100 min_qty" value="<?=$val['sales_qty']?>" data-prev_value="<?=$val['sales_qty']?>" required/>
                            <input type="hidden" value="<?=$val['sales_qty']?>" class="prev_min_qty">
							<input type="hidden" class="combined_product" value="<?php if(isset($val['combined_product'])){ echo $val['combined_product'];}?>"/>
						</th>
						<th class="text-center" width="12%">
							<input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value <?='tvalue-'.$val['product_id']?>" value="<?=$total_price?>" readonly />
						</th>
						
						<th class="text-center" width="10%">
							<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                            <?php
                                if ($sl != 1) {
                                    echo '<a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a>';
                                }
                            ?>
							
							<?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete')); ?>
						</th>
					</tr>
				<?php
					$sl++;
						}
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right"><b>Total : </b></td>
						<td align="center"><input name="data[DistMemo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="<?php echo $existing_record['DistMemo']['gross_value']; ?>" readonly />
						</td>
						<td></td>
						
					</tr>
					<tr>
						<td colspan="5" align="right"><b>Cash Collection : </b></td>
						<td align="center"><input name="data[DistMemo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" value="<?php echo $existing_record['DistMemo']['cash_recieved']; ?>" />
						</td>
						<td></td>
						
					</tr>
					<tr>
            <td colspan="4"> <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal"  data-backdrop="static" data-keyboard="false"   data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
              <div id="bonus_product" class="modal fade" role="dialog">

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
                          <?php
                          if(!empty($existing_record)) {
                            foreach($existing_record['DistMemoDetail'] as $key=>$val){
                              $sl=1;
                              if($val['price'] > 0.00)
                                continue;
                              ?>
                          <tr  class="bonus_row">
                            <th class="text-center" <?php if($sl==1)?> id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true,'default'=>$val['product_id']));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value="<?php echo $product_category_id_list[$val['product_id']]; ?>"/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="<?=$val['measurement_unit_name']?>" disabled/>
                              <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value="<?=$val['measurement_unit_id']?>"/>

                              <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value="<?=$val['bonus_qty']?>"/>
                              <input type="hidden" class="combined_product"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>
                            </th>
                          </tr>
                          <?php 
                          $sl++;
                        }
                      } 
                      ?>
                      <?php if($sl==1) {?>
                          <tr  class="bonus_row">
                            <th class="text-center" id="bonus_product_list">
                              <?php
                                echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true));
                              ?>
                              <input type="hidden" class="product_id_clone" />
                              <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="" disabled/>
                              <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id" value=""/>

                              <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                              <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                            </th>
                            <th class="text-center" width="12%">
                              <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" id="open_bonus_min_qty" value=""/>
                              <input type="hidden" class="combined_product"/>
                            </th>
                            <th class="text-center" width="10%">
                              <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                              
                            </th>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" id="model_close_btn" class="btn btn-default" data-dismiss="modal">Ok</button>
                    </div>
                  </div>
                </div>        
              </div>
            </td>
						<td align="right"><b>Credit : </b></td>
						<td align="center"><input name="data[DistMemo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" value="<?php echo $existing_record['DistMemo']['credit_amount']; ?>" readonly />
						</td>
						<td></td>
					
					</tr>
          <tr>
            <td colspan="5" align="right"><b>Discount : </b></td>
            <td align="center"><input name="data[DistOrder][discount_percent]" class="form-control width_100 discount_percent" type="text" id="discount_percent" value="<?php echo $existing_record['DistMemo']['discount_percent']; ?>" readonly/>
            </td>
            <td></td>
          
        </tr>
        <tr>
            <td colspan="5" align="right"><b>After Discount : </b></td>
            <td align="center"><input name="data[DistOrder][discount_value]" class="form-control width_100 discount_value" type="text" id="discount_value" value="<?php echo $existing_record['DistMemo']['discount_value']; ?>" readonly />
            </td>
            <td></td>
          
        </tr>
				</tfoot>	
				</table>
                </div>
			
			<?php //echo $this->Form->submit('Submit', array('class' => 'submit btn btn-large btn-primary')); ?>
            
            <div class="form-group" style="padding-top:20px;">
                <div class="pull-right">
                	<?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div'=>false, 'name'=>'save')); ?>
            		<?php // echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div'=>false, 'name'=>'draft')); ?>
                </div>
            </div>
            
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
	
</div>

<div id="memo_product_list">
    <?php
        echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]','class' => 'form-control width_100 product_id', 'options' => $product_list, 'empty' => '---- Select Product ----', 'label' => false, 'required'=>TRUE,'id'=>"MemoProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
</div>


<div id="open_bonus_product_list">
    <?php
      echo $this->Form->input('product_id', array('name' => 'data[MemoDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id','options'=>$open_bonus_product_option,'empty'=>'---- Select Product ----', 'label' => false,'autofocus' => true ,'id'=>"DistMemoProductId"));
    ?>
    <input type="hidden" class="product_id_clone" />
    <input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
</div>


<style>
    .bonus{
        width: 130px !important;
    }
    .product_unit_name{
        width: 80px !important;
    }
    .product_id{
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
</style>

<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
$(document).ready(function () 
{
    
    $('body').on('keydown', 'input, select, textarea', function(e) {
            var self = $(this)
              , form = self.parents('form:eq(0)')
              , focusable
              , next
              ;
            if (e.keyCode == 13) {
                focusable = form.find('input,select,textarea').filter(':visible');
                // console.log(this.class);
                // console.log(this.name);
                // console.log(this.id);
                // console.log(this);
                // console.log(focusable);
                // console.log(focusable.index(this));
                if(this.id == 'MemoProductId')
                    next = focusable.eq(focusable.index(this)+3);
                else if(this.id=='open_bonus_min_qty')
                    next = focusable.eq(focusable.index(this)+1);
                else if(this.name=='data[MemoDetail][sales_qty][]')
                    next = focusable.eq(focusable.index(this)+2);
                else if(this.id=='DistMemoProductId')
                     next = focusable.eq(focusable.index(this)+2);
                else
                    next = focusable.eq(focusable.index(this)+1);
                if (next.length) {
                    next.focus();
                } else {
                   // form.submit();
                }
                return false;
            }
        });
        
        /*For Adding Bonus Product list : START*/
    $(document).ready(function () {
      $('#open_bonus_product_list').hide();
      $('.bonus_add_more').hide();
      $(".bonus_row").last().find('.bonus_add_more').show();
        $("body").on("click", ".bonus_add_more", function () {
            var product_list = $('#open_bonus_product_list').html();
            var product_bonus_row =
                    '\
            <tr class="bonus_row">\
                <th class="text-center">' + product_list + '</th>\
                <th class="text-center" width="12%">\
                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>\
                    <input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
                    <input type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
                    <input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
                </th>\
                <th class="text-center" width="12%">\
                    <input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required  id="open_bonus_min_qty" />\
                    <input type="hidden" class="combined_product"/>\
                </th>\
                <th class="text-center" width="10%">\
                    <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>\
                    <a class="btn btn-danger btn-xs bonus_remove"><i class="glyphicon glyphicon-remove"></i></a>\
                </th>\
            </tr>\
            ';
            var product_id = $(this).parent().parent().find('.open_bonus_product_id').val();
            if (product_id)
            {
                $(this).hide();
                $(".bonus_product").append(product_bonus_row);
                 $("#DistMemoProductId").focus();
            } else
            {
                 $("#DistMemoProductId").focus();
            }
        });
        $("body").on("click", ".bonus_remove", function () {
            $(this).parent().parent().remove();
            // var total_tr= $(".bonus_row").length;
            $(".bonus_row").last().find('.bonus_add_more').show();
        });
        $("body").on("change", ".open_bonus_product_id", function () {
            var product_id = $(this).val();
            var product_box = $(this).parent().parent().parent();
            var product_category_id = product_box.find("th:nth-child(1) .open_bonus_product_category_id");
            var product_unit_name = product_box.find("th:nth-child(2) .open_bonus_product_unit_name");
            var product_unit_id = product_box.find("th:nth-child(2) .open_bonus_product_unit_id");
            var product_qty = product_box.find("th:nth-child(3) .open_bonus_min_qty");
            var distributor_id = $("#distributor_id").val();
           
            $.ajax({
                url: '<?= BASE_URL . 'dist_memos/get_bonus_product_details' ?>',
                'type': 'POST',
                data: {product_id: product_id, distributor_id: distributor_id},
                success: function (result)
                {
                    var data = $.parseJSON(result);

                    product_category_id.val(data.category_id);
                    product_unit_name.val(data.measurement_unit_name);
                    product_unit_id.val(data.measurement_unit_id);
                    product_qty.val(1);
                    //product_qty.attr('min', 1);
                    //product_qty.attr('max', data.total_qty);
                },
                error: function (error)
                {
                    product_category_id.val();
                    product_unit_name.val();
                    product_unit_id.val();
                    product_qty.val(0);
                }
            });
        });

    });
    /*For Adding Bonus Product list : START*/
        
        /******  Submit data on CTRL+S event start ************/
            $(window).bind('keydown', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    switch (String.fromCharCode(event.which).toLowerCase()) {
                    case 's':
                        
                        var error_count=0;
                        error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                event.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            else 
                            {
                                event.preventDefault();
                                $(".save").trigger("click");
                            }
                       break;
                       case 'b':
                               event.preventDefault();
                                 $("#DistMemoProductId").focus();
                                $(".show_bonus").trigger("click");
                       break;
                       
                       case 'c':
                                event.preventDefault();
                                $("#model_close_btn").trigger("click");
                       break;
                       case 'p':
                                event.preventDefault();
                                $(".new_row_number").last().find('a.add_more').trigger("click");
                                $(".new_row_number").last().find('.product_id').focus();
                                //$("#DistOutletName").trigger("focus");
                       break;
                       case 'f':
                                 event.preventDefault();
                                 $(".bonus_row").last().find('a.bonus_add_more').trigger("click");
                       break;
                    }
                }
            });
            
          /******  Submit data on CTRL+S event End ************/  
          
          /******  Normal form submission start ************/
          
           $("form#DistMemoAdminEditForm").submit(function(e){
                       var error_count=0;
                       error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                e.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            
           });
          
          /******  Normal form submission end ************/
        
        
        
        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistMemos/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

        $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');           
        });
        
        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
        });

        function get_route_by_office_id(office_id)
        {
            
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
        }
        
         $("#dist_route_id").change(function () {
            get_market_data();
        });  
        
      function get_market_data()
        {
            var dist_route_id=$("#dist_route_id").val();
            var thana_id=0;
            var location_type_id=0;
            var territory_id=0;
            
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
                data: {'dist_route_id': dist_route_id,'thana_id': thana_id,'location_type_id': location_type_id,'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    $("#market_id").html(data);
                }
            });
        }
        
     $("#distributor_id").change(function () {
            get_route_data_from_dist_id();
        });  
        
      function get_route_data_from_dist_id()
        {
            var distributor_id=$("#distributor_id").val();
            
             $.ajax({
                url: '<?= BASE_URL . 'distMemos/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
            
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        }
        
        
      $("#market_id").change(function () {
            get_territory_thana_info();
        });  
        
      function get_territory_thana_info()
        {
            var market_id=$("#market_id").val();
            
            if(market_id)
            {
             $.ajax({
                url: '<?= BASE_URL . 'distMemos/get_territory_thana_info' ?>',
                data: {'market_id': market_id},
                type: 'POST',
                success: function (data)
                {
                   var info=data.split("||");
                   if(info[0]!=="")
                    {
                        $('#territory_id').val(info[0]);
                    }
                   
                   if(info[1]!=="")
                    {
                        $('#thana_id').val(info[1]);
                    }
                   
                }
            });
            }
        }
        
     
            $(".office_id").change(function () {
                get_dist_by_office_id($(this).val());
                $("#sr_id").html("<option value=''>Select SR</option>");
            });



            $("#distributor_id").change(function () {
                get_sr_list_by_distributor_id($(this).val());
            });

            function get_dist_by_office_id(office_id)
            {

                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_dist_list_by_office_id' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#distributor_id").html(data);
                    }
                });
            }
            
            function get_sr_list_by_distributor_id(distributor_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_sr_list_by_distributot_id' ?>',
                    data: {'distributor_id': distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#sr_id").html(data);
                    }
                });
            }

            function get_thana_by_territory_id(territory_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_thana_by_territory_id' ?>',
                    data: {'territory_id': territory_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#thana_id").html(data);
                    }
                });
            }
            function get_market_by_thana_id(thana_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_market_by_thana_id' ?>',
                    data: {'thana_id': thana_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#market_id").html(data);
                    }
                });
            }
        
        
    });
</script>
<script>
    $(document).ready(function(){
        if ($('#sale_type_id').val() == 1) {
            $('.csa_name').hide();
            $('#market_id_csa').hide();
            $('#outlet_id_csa').hide();
            $('#market_name').attr('required',false);
            $('#outlet_name').attr('required',false);
        }
        $('#sale_type_id').change(function(){
            if ($('#sale_type_id').val() == 1) {
               
                $('#market_id_so').show();
                $('#outlet_id_so').show();
                $('#market_id_csa').hide();
                $('#outlet_id_csa').hide();
                $('.csa_name').hide();
                $('#market_name').attr('required',false);
                $('#outlet_name').attr('required',false);
            }
            if ($('#sale_type_id').val() == 2) {
                $('#market_id_so').hide();
                $('#outlet_id_so').hide();
                $('#market_id_csa').show();
                $('#outlet_id_csa').show();
                $('.csa_name').show();
                $('#market_id').attr('required',false);
                $('#outlet_id').attr('required',false);
            }
        });
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
        $('#memo_product_list').hide();
        $('.add_more').hide();
        var last_row_number = $('.invoice_table tbody tr.new_row_number:last').attr('id');
        $('#'+last_row_number+'>th>.add_more').show();

        $("body").on("click", ".add_more", function () {
            var sl = $('.invoice_table>tbody>tr').length+1;
            
            var product_list = $('#memo_product_list').html();
            var product_box = $(this).parent().parent().parent();
            var current_row_no = $(this).parent().parent().attr('id');

            var current_row = '<th class="text-center sl_memo" width="5%"></th><th class="text-center">'+product_list+'<input type="hidden" name="data[MemoDetail][product_category_id][]" class="form-control width_100 product_category_id"/><input type="hidden" class="ajax_flag" value=0></th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" step="any" min="0" name="data[MemoDetail][sales_qty][]" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';
           

            var valid_row = $('#'+current_row_no+'>th>.product_rate').val();
            if (valid_row != '') {
                product_box.append('<tr id='+sl+' class=new_row_number>' + current_row + '</tr>');
                $('#'+sl+'>.sl_memo').text(sl);
                $('#cash_collection').val('');
                $(this).hide();
            }else{
                alert('Please fill up this row!');
            }

        });
        

       $("body").on("change", ".product_id", function ()
        {
            var ajax_img = 1;
            var new_product = 1;
            //$('#myModal').modal('show');
            //$('#loading').show();
            //console.log($('#'+sl+'>th>.bonus').val());
            $('#gross_value').val(0);

            var sl = $('.invoice_table>tbody>tr').length;

            var current_row_no = $(this).parent().parent().parent().attr('id');

            if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
                /*
                $('#' + current_row_no + '>th>.bonus').val('N.A');
                $('#' + current_row_no + '>th>.bonus_product_id').val(0);
                $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
                $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
                */
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
                    } else
                    {
                        product_change_flag = 0;
                        
                        /********************* Temporary commented start ********/
                        /*
                        alert("This poduct already exists");
                        product_change_flag = 0;
                        $('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
                        if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
                            $(this).val('').attr('selected', true);
                            $('#' + current_row_no + '>th>.bonus').val('');
                        }
                       */
                      /********************* Temporary commented start ********/
                        
                        total_values();
                        //$(this).val('').attr('selected', true);

                        new_product = 1;
                        //$('#myModal').modal('hide');
                        //$('#loading').hide();
                        //check_duplicate_products();
                    }

                } else
                {
                     product_change_flag = 0;
                  /*
                    pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

                    if (pro_val) {
                        alert("Please select any product from last row or remove it!");
                    } else {
                        alert("Please select any product!");
                    }
                
                    product_change_flag = 0;
                    $('#' + current_row_no + '>th>div>select').val($('#' + current_row_no + '>th>.product_id_clone').val());
                    if ($('#' + current_row_no + '>th>.product_rate').val() == '') {
                        $(this).val('').attr('selected', true);
                        $('#' + current_row_no + '>th>.bonus').val('');
                    }
                    
                    total_values();
                    new_product = 0;
                    $('#myModal').modal('hide');
                    $('#loading').hide();
                    return false;
                   */
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


            if ($('#' + current_row_no + '>th>.product_rate').val() != '' && product_change_flag == 1) {

                var product_id_for_change = $('#' + current_row_no + '>th>.product_id_clone').val();

                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product_change, product_id: product_id_for_change},
                    success: function (result) {
                        if (result == 'yes') {


                            /*-----------------------------------*/
                            var product_id_list_change = '';
                            $('.product_id').each(function () {
                                if ($(this).val() != '') {
                                    if (product_id_list_change.search($(this).val()) == -1)
                                        {
                                             product_id_list_change = $(this).val() + ',' + product_id_list_change;
                                        }
                                  
                                }
                                
                                
                    
                    
                            });
                            /*-----------------------------------*/

                            var combined_product_array = combined_product_change.split(',');
                            var product_id_list_pre_array = product_id_list_change.slice(0, -1);
                            var product_id_list_array = product_id_list_pre_array.split(',');

                            $.arrayIntersect = function (a, b)
                            {
                                return $.grep(a, function (i)
                                {
                                    return $.inArray(i, b) > -1;
                                });
                            };
                            var arr_intersect = $.arrayIntersect(combined_product_array, product_id_list_array);
                            var product_id_new = arr_intersect[0];
                            var no_of_new_combined_product = arr_intersect.length;
                            var prev_combined_row_no = $('.prate-' + product_id_new).parent().parent().attr('id');
                            var min_qty_new = $('#' + prev_combined_row_no + '>th>.min_qty').val();
                            var memo_date=$('#DistMemoMemoDate').val();
                            //console.log(no_of_new_combined_product);
                            //console.log(product_id_new);
                            //console.log(min_qty_new);
                            //var territory_id = $('.territory_id').val();

                            /*$('.product_id').each(function(){
                             $(this).trigger('change');
                             });*/

                            if (no_of_new_combined_product > 0) {

                                $.ajax({
                                    url: '<?= BASE_URL . 'DistMemos/get_combine_or_individual_price' ?>',
                                    'type': 'POST',
                                    data: {combined_product: combined_product_change, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list_change,memo_date:memo_date},
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
                                            if ($(this).val() != '') {
                                                gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                            }
                                        });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val(gross_total.toFixed(2));

                                        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

                                        if (product_category_id == 32)
                                        {
                                            $('#' + current_row_no + '>th>.product_rate').val('0.00');
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
        });
        /*------- unset session -------*/
    });
</script>
<script>

/*--------- check combined or individual product price --------*/
$("body").on("keyup", ".min_qty", function (e)
    { 
       
        var current_row_no = $(this).parent().parent().attr('id');
        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();
        pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

        if (product_category_id != 32 && pro_val) {

            var sl = $('.invoice_table>tbody>tr').length;

            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");
            var product_unit = product_box.find("th:nth-child(3) .product_unit_name");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var product_qty = product_box.find("th:nth-child(5) .min_qty");
            var total_value = product_box.find("th:nth-child(6) .total_value");
            var total_value_field = product_box.find("th:nth-child(6) .total_value");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            var combined_product_field=combined_product;
            
            
           var product_unit_id = product_box.find("th:nth-child(3) .product_unit_id");
           var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
          // var combined_product = combined_product.val();
            var min_qty = product_qty.val();
            var prev_product_qty= parseFloat(product_qty.data('prev_value'));
            if(!prev_product_qty)
            {
              prev_product_qty=0
            }
            // console.log(prev_product_qty);
            var id = product_field.val();
            var product_id = id;
            var memo_date=$('#DistMemoMemoDate').val();
          //var combined_product_change = combined_product.val();
            
            
            
             /********************* new part start ******************/
         var new_product=0;
        if (min_qty != '' && min_qty != 0 && parseFloat(min_qty)>0)
                {
                   
                               var multiple_products=0;
                               multiple_products=check_duplicate_products_all();
        if(multiple_products>0)
        {
                  alert("Duplicate product item has been selected");
                                    $('#' + current_row_no + '>th>.min_qty').val('');
        }
                                else 
                                {
                                    new_product=1; 
                                }
                   
                   
                }
                
        var distributor_id = $('#distributor_id').val();
            var territory_id = $('.territory_id').val();
            if (new_product == 1) {
                
                 var product_id_list = '';
                $('.product_id').each(function () {
                    if ($(this).val() != '') {
                        
                         if (product_id_list.search($(this).val()) == -1)
                                        {
                                            product_id_list = $(this).val() + ',' + product_id_list;
                                        }
                    }
                   
                });
                
                
                
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrders/get_product_unit' ?>',
                    'type': 'POST',
                    data: {product_id: product_id, territory_id: territory_id, distributor_id: distributor_id, product_id_list: product_id_list,order_date:memo_date},
                    success: function (result)
                    {
                        var obj = jQuery.parseJSON(result);
                        console.log("get_product_unit on change qty change:"+result);
                        
                        product_unit.val(obj.product_unit.name);
                        product_unit_id.val(obj.product_unit.id);
                        product_rate.val(obj.product_price.general_price);
                        product_price_id.val(obj.product_price.id);
                        ////product_qty.val(obj.product_combination.min_qty);
                        combined_product.val(obj.combined_product);

                     
                        $('#' + current_row_no + '>th>.product_category_id').val(obj.product_category_id);
                        $('#' + current_row_no + '>th>.product_id_clone').val(product_id);
                         
                        var total_qty = obj.total_qty;
                        var general_price = obj.product_price.general_price;
                        ////var min_qty = obj.product_combination.min_qty;
                        var total_value_amount = parseFloat(general_price * min_qty);
                       
                        $('#' + current_row_no + '>th>.total_value').val(total_value_amount);
                        $('#'+current_row_no+'>th>.min_qty').attr('max', parseFloat(total_qty)+prev_product_qty);
                        $('.add_more').addClass('disabled');

                        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

                        if (product_category_id == 32)
                        {
                            $('#' + current_row_no + '>th>.product_rate').val('0.00');
                            $('.add_more').removeClass('disabled');
                        } else
                        {
                           // $('#' + current_row_no + '>th>.min_qty').trigger('keyup');
                        }

                       
                        total_values();

                        if (obj.bonus_product_qty != undefined) {
                            /*
                            $('#' + current_row_no + '>th>.bonus').val(obj.bonus_product_qty + '(' + obj.bonus_product_name + ')');
                            $('#' + current_row_no + '>th>.bonus_product_id').val(obj.bonus_product_id);
                            $('#' + current_row_no + '>th>.bonus_product_qty').val(obj.bonus_product_qty);
                            $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(obj.bonus_measurement_unit_id);
                            */
                        }
                        
                        
                        
                        //
                        
                        /******************** New Part End *********************/
   
            console.log("inside second step1 :"+min_qty);
            delay(function ()
            {
                 var combined_product = combined_product_field.val();
                 console.log("inside second step min_qty :"+min_qty);
                 console.log("inside second step combined_product :"+combined_product);
                 console.log("inside second step product_id :"+id);
                 console.log("inside second step product_id_list:"+product_id_list);
                
                 
                
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_combine_or_individual_price' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, min_qty: min_qty, product_id: id, product_id_list: product_id_list,memo_date:memo_date},
                    success: function (result)
                    {
                        var obj = jQuery.parseJSON(result);
                         console.log("get_combine_or_individual_price on change qty change:"+result);
                        if (obj.unit_rate != '') {
                            product_rate.val(obj.unit_rate);
                        }
                        if (obj.total_value) {
                            total_value_field.val(obj.total_value);
                        }

                        $.each(obj, function (index, value) {
                            var prate = $(".prate-" + index);
                            var tvalue = $(".tvalue-" + index);
                            prate.val(value.unit_rate);
                            tvalue.val(value.total_value);
                        });

                        var gross_total = 0;
                        $('.total_value').each(function () {
                            if ($(this).val() != '') {
                                gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                            }
                        });
                        $("#gross_value").val(gross_total.toFixed(2));
                        var order_date = $('#DistMemoMemoDate').val();

                        /********************** Check Discount ******************************/
                        $.ajax({
                            url: '<?= BASE_URL . 'distOrders/check_discount_amount' ?>',
                            data: {gross_value:gross_total,order_date:order_date},
                            type: 'POST',
                            success: function (data)
                            {
                                var obj = JSON.parse(data);
                                var discount_percent = obj.discount_percent;
                                var discount_type = obj.discount_type;
                                if(discount_type == 1){
                                    $('#discount_percent').val(discount_percent);
                                    var discount = discount_percent / 100;
                                    var total_val = gross_total -(gross_total * discount);
                                    $('#discount_value').val(total_val);
                                }
                                else{
                                    $('#discount_percent').val(discount_percent);
                                    var discount = discount_percent;
                                    var total_val = gross_total - discount;
                                    $('#discount_value').val(total_val);
                                } 
                               
                            }
                        });

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
                                if (parseFloat($('#' + current_row_no + '>th>.min_qty').val()) >= parseFloat(mother_product_quantity[i].min) && parseFloat($('#' + current_row_no + '>th>.min_qty').val()) <= parseFloat(mother_product_quantity[i].max))
                                {
                                    if (i == 0) {
                                        /*
                                        $('#' + current_row_no + '>th>.bonus').val('N.A');
                                        $('#' + current_row_no + '>th>.bonus_product_id').val(0);
                                        $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
                                        $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
                                        */
                                        
                                    } else {
                                        /*
                                        $('#' + current_row_no + '>th>.bonus').val(bonus_product_quantity[i + (-1)] + '(' + bonus_product_name[i + (-1)] + ')');
                                        $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i + (-1)]);
                                        $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_product_quantity[i + (-1)]);
                                        $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i + (-1)]);
                                       */
                                    }
                                    break;
                                } else {
                                    var current_qty = parseFloat($('#' + current_row_no + '>th>.min_qty').val());
                                    var bonus_qty = Math.floor(current_qty / parseFloat(mother_product_quantity_bonus));
                                    
                                   /*
                                    $('#' + current_row_no + '>th>.bonus').val(bonus_qty + ' (' + bonus_product_name[i] + ')');
                                    $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i]);
                                    $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_qty);
                                    $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
                                   */
                                  
                                }
                            }
                        }
                        $('#cash_collection').val(gross_total.toFixed(2));
                        //$('#loading').hide();
                        //$('#myModal').modal('hide');
                        $('.add_more').removeClass('disabled');
                    }
                });

            }, 100);
                        //
                                                
                    }
                });
                
            }
        
        }
        


    });

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
})();
</script>

<script>
    $(document).ready(function () {
        
           $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

		});
                
                
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
                product_box.remove();

                var last_row = $('.invoice_table tbody tr.new_row_number:last').attr('id');
                $('#'+last_row+'>th>.add_more').show();
                //console.log('if'+last_row);
                
				total_values();
            } else {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, product_id: id},
                    success: function (result) {
                        if (result == 'yes') {

                            product_box.remove();

                            //$('#dlt_product_id_class').removeClass('product_id');
                            var last_row = $('.invoice_table tbody tr.new_row_number:last').attr('id');
                            $('#'+last_row+'>th>.add_more').show();
                            //console.log('else'+last_row);

                            /*-----------------------------------*/
                            var product_id_list = '';
                            $('.product_id').each(function () {
                                if ($(this).val() != '') {
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
                            //var territory_id = $('.territory_id').val();
                            
                            /*$('.product_id').each(function(){
                                $(this).trigger('change');
                            });*/
                            
                            if (no_of_new_combined_product > 0) {

                                $.ajax({
                                url: '<?= BASE_URL . 'DistMemos/get_combine_or_individual_price' ?>',
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
    
    function check_duplicate_products()
    {
        
                        var error_count=0;
                        var product_ids = [];
                        
                        $('.product_id').removeClass("errorInput");  
                        
                        $('.product_id').each(function ()
                            {
                                var cur_val=this.value;
                                if(product_ids.indexOf(cur_val) === -1){
                                    product_ids.push(cur_val);
                                } else {
                                    error_count++;
                                    $(this).addClass('errorInput');
                                    //alert(cur_val);
                                }
            
                            });
                            
                            return error_count;
    }
    
    function check_duplicate_products_all()
    {
        
                        var error_count=0;
                        var product_ids = [];
                        
                        $('.product_id').removeClass("errorInput");  
                        
                        $('.product_id').each(function ()
                            {
                                var cur_val=this.value;
                                if(cur_val)
                                {
                                    if(product_ids.indexOf(cur_val) === -1){
                                    product_ids.push(cur_val);
                                    } else {
                                        error_count++;
                                        $(this).addClass('errorInput');
                                    }
                                }
                              
            
                            });
                            
                            return error_count;
    }
    
    
    
    function check_duplicate_open_bonus_products()
    {
        
                        var error_count=0;
                        var count=0;
                        
                        $('.open_bonus_product_id').removeClass("errorInput");  
                        
                        $('.open_bonus_product_id').each(function ()
                            {
                                count++;
                                var cur_val=this.value;
                                if(cur_val>0)
                                {
                                    
                                }
                                else 
                                {
                                   error_count++;
                                   $(this).addClass('errorInput'); 
                                }
                              
            
                            });
                            
                            if(count==1)
                            {
                                error_count=0;
                            }
                            
                            return error_count;
    }
</script>
