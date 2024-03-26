<script>
var tso_list='<?php echo json_encode($tso_list);?>';
var ae_list='<?php echo json_encode($ae_list);?>';
</script>

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
//pr($this->Session->read('from_outlet')); exit;
$from_outlet = $this->Session->read('from_outlet');
/* if(array_key_exists('DistMarket', $from_market)){
  $from_outlet['DistOutlet'] = $from_market['DistMarket'];
  }else{
  $from_outlet = $this->Session->read('from_outlet');
  } */

/********************** Checking data is previously submitted start ********************/

$selected_office="";
$selected_dist="";
$selected_sr="";
$selected_route="";
$selected_territory="";
$selected_thana="";
$selected_market="";
$selected_outlet="";
$selected_ae="";
$selected_tso="";
$selected_ae_name="";
$selected_tso_name="";


if(!empty($pre_submit_data))
{
   $selected_office=$pre_submit_data['office_id']; 
   $selected_dist=$pre_submit_data['distributor_id']; 
   $selected_sr=$pre_submit_data['sr_id']; 
   $selected_route=$pre_submit_data['dist_route_id'];
   $selected_territory=$pre_submit_data['territory_id']; 
   $selected_thana=$pre_submit_data['thana_id'];
   $selected_market=$pre_submit_data['market_id']; 
   $selected_outlet=$pre_submit_data['outlet_id'];
   $selected_ae=$pre_submit_data['ae_id']; 
   $selected_tso=$pre_submit_data['tso_id'];
   $selected_memo_ref_no='';
   $selected_ae_name=$ae_list[$selected_ae];
   $selected_tso_name=$tso_list[$selected_tso];
}
else 
{
    $pre_outlets=$outlets;
}




/********************** Checking data is previously submitted End ********************/
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Create Distibutor Memo'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distibutor Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistMemo', array('role' => 'form')); ?>
                
                <div class="form-group">
                    <?php echo $this->Form->input('memo_date', array('class' => 'form-control datepicker', 'value' => $current_date, 'type' => 'text', 'required' => TRUE)); ?>
                </div>
                
                
                    <?php echo $this->Form->input('entry_date', array('type'=>'hidden','class' => 'form-control datepicker', 'value' => (isset($this->request->data['DistMemo']['entry_date']) == '' ? $current_date : $this->request->data['DistMemo']['entry_date']), 'required' => TRUE)); ?>
                
                
                 <div class="form-group">
                    <?php
                    if(!empty($pre_submit_data))
                    {
                        echo $this->Form->input('memo_reference_no', array('label'=>'Memo Number :','class' => 'form-control memo_reference_no', 'maxlength' => '15', 'required' => true, 'type' => 'text','value'=>''));
                    }
                    else 
                    {
                        echo $this->Form->input('memo_reference_no', array('label'=>'Memo Number :','class' => 'form-control memo_reference_no', 'maxlength' => '15', 'required' => true, 'type' => 'text'));
                    }
                   ?>
                </div>
                
                <div class="form-group">
                    <?php
                    
                    if ($office_parent_id == 0) {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['office_id'] :$selected_office, 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                    } else {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['office_id'] :$selected_office, 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                    }
                    ?>
                </div>

                <?php if ($dist == 1) { ?>

                    <?php echo $this->Form->input('sale_type_id', array('type' => 'hidden', 'class' => 'form-control sale_type_id', 'value' => '10', 'id' => 'sale_type_id')); ?> 
                    <div class="form-group">
                        <?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'onChange' => 'rowUpdate(1);', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_distributor_id'] : $selected_dist, 'id' => 'distributor_id', 'required' => 'required','options'=>$distributors,'empty' => '--- Select Distributor ---')); ?> 
                    </div>
                
                 

                    <div class="form-group">
                        <?php echo $this->Form->input('sr_id', array('label' => 'SR', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_sales_representative_id'] : $selected_sr, 'class' => 'form-control ', 'id' => 'sr_id', 'empty' => '--- Select SR ---', 'required' => 'required','options'=>$pre_srs)); ?> 
                    </div>
                <?php } ?>
                <?php echo $this->Form->input('territory_id', array('type' => 'hidden', 'id' => 'territory_id', 'class' => 'form-control territory_id', 'value' => !empty($from_outlet) ? $from_outlet['DistOutlet']['territory_id'] : $selected_territory)); ?>


                <?php if ($dist == 1) { ?>             
                    <?php echo $this->Form->input('thana_id', array('label' => false, 'type' => 'hidden', 'id' => 'thana_id', 'value' => !empty($from_outlet) ? $from_outlet['DistOutlet']['thana_id'] : $selected_thana)); ?>                    
                <?php } ?>

                <div class="form-group">
                    <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id', 'label' => 'Route/Beat', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_route_id'] :$selected_route, 'class' => 'form-control', 'empty' => '---- Select ----','options'=>$pre_routes)); ?>
                </div>

                <div class="form-group"  id="market_id_so">
                    <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_market_id'] : $selected_market, 'required' => TRUE, 'empty' => '---- Select Market ----','options'=>$pre_markets)); ?>
                    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i></button>
                </div>

                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_outlet_id'] : $selected_outlet, 'required' => TRUE, 'empty' => '---- Select Outlet ----', 'options' => $pre_outlets)); ?>
                    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-plus"></i></button>
                </div>
                
                <div class="form-group">
                    <?php
                        echo $this->Form->input('no_of_product', array('label'=>'No of Products :','id'=>'no_of_product','class' => 'form-control', 'required' => FALSE, 'type' => 'number','placeholder'=>'1'));
                   ?>
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
                
                
                

                <?php echo $this->Form->input('memo_no', array('class' => 'form-control memo_no', 'required' => TRUE, 'type' => 'hidden', 'value' => $generate_memo_no, 'readonly')); ?>

                 <?php echo $this->Form->input('ae_id', array('type' => 'hidden','id'=>'ae_id','label' => false, 'class' => 'form-control', 'value' =>$selected_ae)); ?> 
                        <?php echo $this->Form->input('tso_id', array('type' => 'hidden', 'id'=>'tso_id','label' => false, 'class' => 'form-control', 'value' =>$selected_tso)); ?>
                    
                
               

                <div class="table-responsive">
                    <!--Set Product area-->
                    <table class="table table-striped table-condensed table-bordered invoice_table" id="main_invoice_data">
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
                                    <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_memo'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete'));  ?>
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" align="right"><b>Total : </b></td>
                                <td align="center"><input name="data[DistMemo][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right"><b>Cash Collection : </b></td>
                                <td align="center"><input name="data[DistMemo][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4"> <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal" data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
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
                                <td align="center"><input name="data[DistMemo][credit_amount]" class="form-control width_100" type="text" id="credit_amount" readonly />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group" style="padding-top:20px;">
                    <div class="pull-right">
                        <?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
                        <?php // echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
                    </div>
                </div>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>


</div>
<!-- Modal for Market -->
<div class="modal fade" id="myModal1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="box-body">		
                    <?php echo $this->Form->create('DistMarket', array('id' => 'market_model', 'controller' => 'DistMarkets', 'action' => 'admin_add_market'), array('role' => 'form')); ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('location_type_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('office_id', array('id' => 'office_id2', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('territory_id', array('id' => 'territory_id2', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('thana_id', array('id' => 'thana_id2', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('dist_route_id', array('label' => 'Route/Beat', 'id' => 'dist_route_id2', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Active :</b>', 'default' => 1)); ?>
                        <?php echo $this->Form->input('tag', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 'from_memo')); ?>
                    </div>              
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Outlet -->
<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="box-body">		
                    <?php echo $this->Form->create('DistOutlet', array('id' => 'outlet_model', 'controller' => 'DistOutlets', 'action' => 'admin_add_outlet'), array('role' => 'form')); ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('ownar_name', array('class' => 'form-control')); ?>
                    </div>               
                    <div class="form-group">
                        <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('mobile', array('class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('office_id', array('id' => 'office_id1', 'class' => 'form-control office_id', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('territory_id', array('id' => 'territory_id1', 'class' => 'form-control territory_id', 'empty' => '---- Select ----')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('thana_id', array('id' => 'thana_id1', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>	               
                    <div class="form-group">
                        <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id1', 'label' => 'Route/Beat', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>                
                    					
                    <div class="form-group">
                        <?php echo $this->Form->input('dist_market_id', array('label' => 'Distributor Market', 'id' => 'dist_market_id1', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>               
                    <div class="form-group">
                        <?php echo $this->Form->input('category_id', array('label' => 'Outlet Type', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                        <?php echo $this->Form->input('is_ngo', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 0)); ?>
                        <?php echo $this->Form->input('tag', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 'from_memo')); ?>
                       
                    
                    </div>               
                                    
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
    $(document).ready(function () {
        $('body').on('keydown', 'input, select, textarea', function(e) {
            var self = $(this)
              , form = self.parents('form:eq(0)')
              , focusable
              , next
              ;
            if (e.keyCode == 13) {
                focusable = form.find('input,select,textarea').filter(':visible');
                //console.log(this.class);
                // console.log(this.name);
                if(this.id == 'MemoProductId')
                    next = focusable.eq(focusable.index(this)+3);
                else if(this.name=='data[MemoDetail][sales_qty][]')
                    next = focusable.eq(focusable.index(this)+3);
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
<?php
if (empty($from_outlet) && (empty($pre_submit_data))) {
    ?>
            $("#office_id").prop("selectedIndex", 0);
            $("#distributor_id").prop("selectedIndex", 0);
            $("#sr_id").prop("selectedIndex", 0);
            $("#dist_route_id").prop("selectedIndex", 0);
            $("#market_id").prop("selectedIndex", 0);
            $("#outlet_id").prop("selectedIndex", 0);
            $(".memo_reference_no").val('');
    <?php
}
else if(!empty($pre_submit_data))
    {
?>
    rowUpdate(1);
<?php
    }
?>
    });
</script>



<script>
    $("#market_model").submit(function (event) {
        $("#divLoading_default").remove();
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>admin/DistMarkets/add",
            data: $(this).serializeArray(),
            success: function (response) {
                $("#market_model").attr("data-dismiss", "modal");
                window.location.assign("<?php echo BASE_URL; ?>admin/dist_memos/create_memo");
            }
        });
        //$('.img-circle').hide();
        event.preventDefault();
    });
    $("#outlet_model").submit(function (event) {
        $("#divLoading_default").remove();
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>admin/DistOutlets/add",
            data: $(this).serializeArray(),
            success: function (response) {
                $("#outlet_model").attr("data-dismiss", "modal2");
                window.location.assign("<?php echo BASE_URL; ?>admin/dist_memos/create_memo");
            }
        });
        //$('.img-circle').hide();
        event.preventDefault();
    });
    function get_territory_list(office_id) {
//        $("#office_id option:selected").each(function () {
//            $(this).removeAttr('selected');
//        });
//        $('#office_id').find('option[value="' + office_id + '"]').attr("selected", "selected");
        $.ajax({
            url: '<?php echo BASE_URL . 'sales_people/get_territory_list' ?>',
            'type': 'POST',
            data: {office_id: office_id},
            success: function (response) {
                var obj = jQuery.parseJSON(response);
                //console.log(obj);
                // optionList = '';
                var options = '';
                for (var i = 0; i < obj.length; i++) {
                    options += '<option value="' + obj[i].id + '">' + obj[i].name + '</option>';
                }
                $('#territory_id').html(options);
                $('#territory_id1').html(options);
                $('#territory_id2').html(options);
            }
        });
    }
    $('body').on("change", "#office_id1", function () {
        var office_id = $(this).val();
        get_territory_list(office_id);
        get_route_by_office_id($(this).val());
    });
    $('body').on("change", "#territory_id1", function () {
        var territory_id = $(this).val();
        $('#territory_id').val(territory_id);
    });
    $('body').on("change", "#thana_id1", function () {
        var thana_id = $(this).val();
        $('#thana_id').val(thana_id);
    });
    $('#territory_id1').selectChain({
        target: $('#thana_id1'),
        value: 'name',
        url: '<?= BASE_URL . 'distOutlets/get_thana_list' ?>',
        type: 'post',
        data: {'territory_id': 'territory_id1'}
    });
    $('body').on("change", "#office_id2", function () {
        var office_id = $(this).val();
        get_territory_list(office_id);
        get_route_by_office_id(office_id);
    });
    $('body').on("change", "#territory_id2", function () {
        var territory_id = $(this).val();
        $('#territory_id2').val(territory_id);
    });
    $('body').on("change", "#thana_id2", function () {
        var thana_id = $(this).val();
        $('#thana_id2').val(thana_id);
    });
    $('#territory_id2').selectChain({
        target: $('#thana_id2'),
        value: 'name',
        url: '<?= BASE_URL . 'distOutlets/get_thana_list' ?>',
        type: 'post',
        data: {'territory_id': 'territory_id2'}
    });
    /* on chagne office , show route*/


    function get_route_by_office_id(office_id)
    {

        $.ajax({
            url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
            data: {'office_id': office_id},
            type: 'POST',
            success: function (data)
            {
                $("#dist_route_id1").html(data);
                $("#dist_route_id2").html(data);
            }
        });
    }

    /* on change route, show market */


    $("#thana_id1").change(function () {
        get_market_data();
    });

    $("#dist_route_id1").change(function () {
        get_market_data();
    });

    $("#location_type_id1").change(function () {
        get_market_data();
    });
    $("#thana_id2").change(function () {
        get_market_data2();
    });

    $("#dist_route_id2").change(function () {
        get_market_data2();
    });

    $("#location_type_id2").change(function () {
        get_market_data2();
    });

    function get_market_data()
    {
        var dist_route_id = $("#dist_route_id1").val();
        var thana_id = $("#thana_id1").val();
        //var location_type_id = $("#location_type_id1").val();
        var territory_id = $("#territory_id1").val();

        $.ajax({
            url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
            data: {'dist_route_id': dist_route_id, 'thana_id': thana_id, 'territory_id': territory_id},
            type: 'POST',
            success: function (data)
            {
                $("#dist_market_id1").html(data);
            }
        });
    }
    function get_market_data2()
    {
        var dist_route_id = $("#dist_route_id2").val();
        var thana_id = $("#thana_id2").val();
        var location_type_id = $("#location_type_id2").val();
        var territory_id = $("#territory_id2").val();

        $.ajax({
            url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
            data: {'dist_route_id': dist_route_id, 'thana_id': thana_id, 'location_type_id': location_type_id, 'territory_id': territory_id},
            type: 'POST',
            success: function (data)
            {
                $("#dist_market_id2").html(data);
            }
        });
    }
    function productList()
    {
        var csa_id = 0;
        var distributor_id = $('#distributor_id').val();

        if (distributor_id) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'admin/DistMemos/get_product' ?>',
                data: {'distributor_id': distributor_id, 'csa_id': csa_id},
                cache: false,
                success: function (response) {
                    var json = $.parseJSON(response);
                    $('.product_id option').remove();
                    for (var i = 0; i < json.length; ++i)
                    {
                        $('.product_id').append('<option value="' + json[i].id + '">' + json[i].name + '</option>');
                    }
                }
            });
        }



        if (distributor_id) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'DistMemos/get_bonus_product' ?>',
                data: 'distributor_id=' + distributor_id,
                cache: false,
                success: function (response) {
                    var json = $.parseJSON(response);
                    //console.log(json);
                    $('.open_bonus_product_id option').remove();
                    $('.open_bonus_product_id').append('<option value="">-- Select---</option>');
                    for (var i = 0; i < json.length; ++i)
                    {
                        $('.open_bonus_product_id').append('<option value="' + json[i].Product.id + '">' + json[i].Product.name + '</option>');
                    }
                }
            });
        }
    }


    function rowUpdate(productLit) {

        sl = 1;

        product_list = '<div class="input select"><select id="MemoProductId" required="required" class="form-control width_100 product_id" name="data[MemoDetail][product_id][]"><option value="">---- Select Product ----</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[MemoDetail][product_category_id][]">';


        var current_row = '<th class="text-center sl_memo" width="5%">1</th><th id="memo_product_list" class="text-center">' + product_list + '</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" class="form-control width_100 min_qty" value="" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a></th>';

        $('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');

        $('#gross_value').val(0);
        //$('.memo_no').val('');

        if (productLit == 1) {
            productList();
        } else {
            $("#distributor_id").prop("selectedIndex", 0);
        }

    }

    $(document).ready(function ()
    {
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
            get_route_by_office_id_base($(this).val());
            check_valid_memo_no();
        });

        function get_route_by_office_id_base(office_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    var data="<option value=''>--- Select ---</option>";
                    $("#dist_route_id").html(data);
                    $("#dist_route_id1").html(data);
                    $("#dist_route_id2").html(data);
                }
            });
        }

        $("#dist_route_id").change(function () {
            get_market_data();
        });

        function get_market_data()
        {
            var dist_route_id = $("#dist_route_id").val();
            var thana_id = 0;
            var location_type_id = 0;
            var territory_id = 0;

            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
                data: {'dist_route_id': dist_route_id, 'thana_id': thana_id, 'location_type_id': location_type_id, 'territory_id': territory_id},
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
            var distributor_id = $("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'distMemos/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                    //$("#dist_route_id").html(data);
                     var data="<option value=''>--- Select ---</option>";
                    $("#dist_route_id").html(data);
                    $("#dist_route_id1").html(data);
                    $("#dist_route_id2").html(data);
                }
            });

            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        }


        $("#market_id").change(function () {
            get_territory_thana_info();
        });

        function get_territory_thana_info()
        {
            var market_id = $("#market_id").val();
            
            var distributor_id = $("#distributor_id").val();
            var office_id = $("#office_id").val();
            var memo_date = $("#DistMemoMemoDate").val();

            if (market_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'distMemos/get_territory_thana_info' ?>',
                    data: {'market_id': market_id,'distributor_id': distributor_id,'office_id':office_id,'memo_date':memo_date},
                    type: 'POST',
                    success: function (data)
                    {
                        var info = data.split("||");
                        if (info[0] !== "")
                        {
                            $('#territory_id').val(info[0]);
                        }

                        if (info[1] !== "")
                        {
                            $('#thana_id').val(info[1]);
                        }
                        
                        if (info[2] !== "")
                        {
                            $('#ae_id').val(info[2]);
                            
                            var parse_ae_list=JSON.parse(ae_list);
                            var ae_name=parse_ae_list[info[2]];
                            $("#ae_name").val(ae_name);
                        }
                        
                        if (info[3] !== "")
                        {
                            $('#tso_id').val(info[3]);
                            
                            var parse_tso_list=JSON.parse(tso_list);
                            var tso_name=parse_tso_list[info[3]];
                            $("#tso_name").val(tso_name);
                        }
                        
                        if(info[2]== "" && info[3]== "")
                        {
                            alert("Area Executive and TSO are not mapped properly. Please map first");
                        }
                        else if(info[2]== "")
                        {
                             alert("Area Executive is not mapped properly. Please map first");
                        }
                        else if(info[3]== "")
                        {
                             alert("TSO is not mapped properly. Please map first");
                        }

                    }
                });
            }
        }
    });
</script>

<script>

    function total_values() {
        var t = 0;
        $('.total_value').each(function () {
            if ($(this).val() != '') {
                t += parseFloat($(this).val());
            }
        });
        $('#gross_value').val(t);
    }

    $(document).ready(function () {
        $("body").on("click", ".add_more", function () {
            var sl = $('.invoice_table>tbody>tr').length + 1;

            var product_list = $('#memo_product_list').html();
           
            var product_box = $(this).parent().parent().parent();
            
            var current_row_no = $(this).parent().parent().attr('id');
            //alert(current_row_no);

            var current_row = '<th class="text-center sl_memo" width="5%"></th><th class="text-center">' + product_list + '</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';


            var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
            if (valid_row != '') {
                product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
                $('#' + sl + '>.sl_memo').text(sl);
                $(this).hide();
            } else {
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
                $('#' + current_row_no + '>th>.bonus').val('N.A');
                $('#' + current_row_no + '>th>.bonus_product_id').val(0);
                $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
                $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
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

                        new_product = 0;
                        //$('#myModal').modal('hide');
                        //$('#loading').hide();
                        //check_duplicate_products();
                    }

                } else
                {
                     product_change_flag = 0;
                     new_product = 0;
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
                                    data: {combined_product: combined_product_change, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list_change},
                                    success: function (result) {
                                       if(result)
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
                                            if ($(this).val() != '') {
                                                gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                            }
                                        });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val('');

                                        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

                                        if (product_category_id == 32)
                                        {
                                            $('#' + current_row_no + '>th>.product_rate').val('0.00');
                                            $('.add_more').removeClass('disabled');
                                        }

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

            var distributor_id = $('#distributor_id').val();
            var territory_id = $('.territory_id').val();
            
            if (new_product == 1) {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_product_unit' ?>',
                    'type': 'POST',
                    data: {product_id: product_id, territory_id: territory_id, distributor_id: distributor_id, product_id_list: product_id_list},
                    success: function (result)
                    {
                        var obj = jQuery.parseJSON(result);
                        product_unit.val(obj.product_unit.name);
                        product_unit_id.val(obj.product_unit.id);
                        product_rate.val(obj.product_price.general_price);
                        product_price_id.val(obj.product_price.id);
                        product_qty.val(obj.product_combination.min_qty);
                        combined_product.val(obj.combined_product);
                        
                      
                      
                        $('#' + current_row_no + '>th>.product_category_id').val(obj.product_category_id);
                        $('#' + current_row_no + '>th>.product_id_clone').val(product_id);

                        var total_qty = obj.total_qty;
                        var general_price = obj.product_price.general_price;
                        var min_qty = obj.product_combination.min_qty;
                        var total_value = parseFloat(general_price * min_qty);

                        
                        $('#' + current_row_no + '>th>.total_value').val(total_value);

                      
<?php if ($stock_validation) { ?>
                            if ($('#sale_type_id').val() == 1) {
                               
                                $('#' + current_row_no + '>th>.min_qty').attr('max', total_qty);
                            } else {
                                $('#' + current_row_no + '>th>.min_qty').removeAttr('max');
                            }
<?php } ?>

                        $('.add_more').addClass('disabled');

                        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();

                        if (product_category_id == 32)
                        {
                            $('#' + current_row_no + '>th>.product_rate').val('0.00');
                            $('.add_more').removeClass('disabled');
                           
                        } else
                        {
                            $('#' + current_row_no + '>th>.min_qty').trigger('keyup');
                        }

                        total_values();

                        if (obj.bonus_product_qty != undefined) {
                            $('#' + current_row_no + '>th>.bonus').val(obj.bonus_product_qty + '(' + obj.bonus_product_name + ')');
                            $('#' + current_row_no + '>th>.bonus_product_id').val(obj.bonus_product_id);
                            $('#' + current_row_no + '>th>.bonus_product_qty').val(obj.bonus_product_qty);
                            $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(obj.bonus_measurement_unit_id);
                        }

                        
                    }
                });
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
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            
            var combined_product = combined_product.val();
            var min_qty = product_qty.val();
            var id = product_field.val();
            
           
            
            delay(function ()
            {

                //$('#myModal').modal('show');
                //$('#loading').show();

                if (min_qty == '' || min_qty == 0)
                {
                   //// min_qty = 1;
                    //$('#' + current_row_no + '>th>.min_qty').val(1);
                }
                else 
                {
                   
                        /*************************** On change qty , make add more button click : start ***************************/
                        
                        
                        if(min_qty!=1)
                        {
                            /*
                           var next_tr=$('#' + current_row_no).next('tr').attr('id');
                            var valid_row=0;
                            var next_row_exist=0;
                            var has_empty_row = 0;
                            if(typeof(next_tr) !== "undefined")
                            {
                               next_row_exist=1;
                               valid_row = $('#' + next_tr + '>th>.min_qty').val();
                                                             
                                
                                $('.min_qty').each(function () {
                                    if ($(this).val() == '' || $(this).val() < 1) {
                                       has_empty_row=1; 
                                    }
                                });
                            }
                            
                            
                            if (next_row_exist==0) {
                                             $('#' + current_row_no + '>th>.add_more').trigger("click");
                                        }
                                        else if(next_row_exist && has_empty_row==0 && (valid_row!='' && valid_row>0))
                                        {
                                            $('#' + current_row_no + '>th>.add_more').trigger("click");
                                        }
                                        else {
                                            alert('Please fill up this row!');
                                        } 
                             */
                        }
                       
                            
                        /*************************** On change qty , make add more button click : End ***************************/
                }

                /*-----------------------------------*/
                var product_id_list = '';
                $('.product_id').each(function () {
                    if ($(this).val() != '') {
                        
                         if (product_id_list.search($(this).val()) == -1)
                                        {
                                            product_id_list = $(this).val() + ',' + product_id_list;
                                        }
                    }
                   
                });
                /*-----------------------------------*/

            
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_combine_or_individual_price' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, min_qty: min_qty, product_id: id, product_id_list: product_id_list},
                    success: function (result)
                    {
                     if(result)   
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
                            if ($(this).val() != '') {
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
                                if (parseFloat($('#' + current_row_no + '>th>.min_qty').val()) >= parseFloat(mother_product_quantity[i].min) && parseFloat($('#' + current_row_no + '>th>.min_qty').val()) <= parseFloat(mother_product_quantity[i].max))
                                {
                                    if (i == 0) {
                                        $('#' + current_row_no + '>th>.bonus').val('N.A');
                                        $('#' + current_row_no + '>th>.bonus_product_id').val(0);
                                        $('#' + current_row_no + '>th>.bonus_product_qty').val(0);
                                        $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(0);
                                    } else {
                                        $('#' + current_row_no + '>th>.bonus').val(bonus_product_quantity[i + (-1)] + '(' + bonus_product_name[i + (-1)] + ')');
                                        $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i + (-1)]);
                                        $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_product_quantity[i + (-1)]);
                                        $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i + (-1)]);
                                    }
                                    break;
                                } else {
                                    var current_qty = parseFloat($('#' + current_row_no + '>th>.min_qty').val());
                                    var bonus_qty = Math.floor(current_qty / parseFloat(mother_product_quantity_bonus));

                                    $('#' + current_row_no + '>th>.bonus').val(bonus_qty + ' (' + bonus_product_name[i] + ')');
                                    $('#' + current_row_no + '>th>.bonus_product_id').val(bonus_product_id[i]);
                                    $('#' + current_row_no + '>th>.bonus_product_qty').val(bonus_qty);
                                    $('#' + current_row_no + '>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
                                }
                            }
                        }
                        $('#cash_collection').val('');
                        //$('#loading').hide();
                        //$('#myModal').modal('hide');
                        $('.add_more').removeClass('disabled');
                    }
                }
                });
                
               

            }, 100);

        }
        


    });
</script>

<script>

    $(document).ready(function ()
    {
        
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

            var total_value = $('.tvalue-' + id).val();
            var gross_total = $('#gross_value').val();
            var new_gross_value = parseFloat(gross_total - total_value);
            $('#gross_value').val(new_gross_value);
            $('#cash_collection').val('');

            alert('Removed this row -------');

            var min_qty = product_qty.val();

            if (product_field.val() == '') {
                //console.log('if');
                product_box.remove();

                var last_row = $('.invoice_table tbody tr:last').attr('id');
                $('#' + last_row + '>th>.add_more').show();
                //console.log('if'+last_row);

                total_values();
            } else
            {
                //console.log('else');
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/delete_memo' ?>',
                    'type': 'POST',
                    data: {combined_product: combined_product, product_id: id},
                    success: function (result)
                    {
                        if (result == 'yes') {

                            product_box.remove();

                            var last_row = $('.invoice_table tbody tr:last').attr('id');
                            $('#' + last_row + '>th>.add_more').show();
                            //console.log('else'+last_row);

                            /*-----------------------------------*/
                            var product_id_list = '';
                            $('.product_id').each(function () {
                                if ($(this).val() != '') {
                                   
                                     if (product_id_list.search($(this).val()) == -1)
                                        {
                                            product_id_list = $(this).val() + ',' + product_id_list;
                                        }
                                }

                            });
                            /*-----------------------------------*/

                            var combined_product_array = combined_product.split(',');
                            var product_id_list_pre_array = product_id_list.slice(0, -1);
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
                            //console.log(no_of_new_combined_product);
                            //console.log(product_id_new);
                            //console.log(min_qty_new);

                            if (no_of_new_combined_product > 0) {
                                $.ajax({
                                    url: '<?= BASE_URL . 'memos/get_combine_or_individual_price' ?>',
                                    'type': 'POST',
                                    data: {combined_product: combined_product, min_qty: min_qty_new, product_id: product_id_new, product_id_list: product_id_list},
                                    success: function (result) {
                                       if(result)
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
                                            if ($(this).val() != '') {
                                                gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                                            }
                                        });
                                        $("#gross_value").val(gross_total.toFixed(2));

                                        $('#cash_collection').val('');


                                    }
                                }
                                });
                            }
                        }
                        var i = 1;
                        $('.new_row_number').each(function () {
                            $(this).attr('id', i);
                            $('#' + i + '>.sl_memo').text(i++);
                        });
                    }
                });
            }


            var multiple_products=0;
            multiple_products=check_duplicate_products();
            if(multiple_products>0)
            {
                    // alert("Duplicate product item has been selected");
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
            if (credit_amount >= 0) {
                $("#credit_amount").val(credit_amount.toFixed(2));
            } else {
                $("#credit_amount").val(0);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('body').on("keyup", ".memo_no", function () {
            var memo_no = $('.memo_no').val();
            var sale_type = $('#sale_type_id').val();

            delay(function () {
                $.ajax({
                    url: '<?php echo BASE_URL . 'admin/memos/memo_no_validation' ?>',
                    'type': 'POST',
                    data: {memo_no: memo_no, sale_type: sale_type},
                    success: function (result) {
                        obj = jQuery.parseJSON(result);
                        /*if(obj == 1){
                         alert('Memo Number Already Exist');
                         $('.submit').prop('disabled', true);
                         }*/
                        if (obj == 0) {
                            $('.submit_btn').prop('disabled', false);
                        } else {
                            alert('Memo Number Already Exist');
                            $('.submit_btn').prop('disabled', true);
                        }
                    }
                });
            }, 100);

        });

        /********************check ************/
        $('body').on("keyup", ".memo_reference_no", function () {
            check_valid_memo_no();
        });
    });
    
    
    function check_valid_memo_no()
    {
        
        var memo_reference_no = $('.memo_reference_no').val();
            var office_id = $('#office_id').val();

            if (memo_reference_no != "" || memo_reference_no != " ")
            {
                delay(function () {
                    $.ajax({
                        url: '<?php echo BASE_URL . 'distMemos/memo_reference_no_validation' ?>',
                        'type': 'POST',
                        data: {memo_reference_no: memo_reference_no, office_id: office_id},
                        success: function (result) {
                            obj = jQuery.parseJSON(result);
                            if (obj == 0) {
                                $('.submit_btn').prop('disabled', false);
                            } else {
                                alert('Memo Number Already Exist');
                                $('.submit_btn').prop('disabled', true);
                            }
                        }
                    });
                }, 100);
            }
        
    }

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    /*For Adding Bonus Product list : START*/
    $(document).ready(function () {
        $("body").on("click", ".bonus_add_more", function () {
            var product_list = $('#bonus_product_list').html();
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
            if (product_id)
            {
                $(this).hide();
                $(".bonus_product").append(product_bonus_row);
            } else
            {
                alert('plese select product first');
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
            var territory_id = $('.territory_id').val();
            $.ajax({
                url: '<?= BASE_URL . 'memos/get_bonus_product_details' ?>',
                'type': 'POST',
                data: {product_id: product_id, territory_id: territory_id},
                success: function (result)
                {
                    var data = $.parseJSON(result);

                    product_category_id.val(data.category_id);
                    product_unit_name.val(data.measurement_unit_name);
                    product_unit_id.val(data.measurement_unit_id);
                    product_qty.val(1);
                    product_qty.attr('min', 1);
                    product_qty.attr('max', data.total_qty);
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
<?php if ($dist == 1) { ?>
        /*For Csa Memo Need Two Extra field (csa and thana) : Start*/
        $(document).ready(function () {
            $(".office_id").change(function () {
               // console.log($(this).val());
                var office_id = $(this).val();
                //get_territory_list(office_id);
                get_dist_by_office_id(office_id);
                
                $("#sr_id").html("<option value=''>Select SR</option>");
            });



            $("#distributor_id").change(function () {
                get_sr_list_by_distributor_id($(this).val());
            });
            
            $("#sr_id").change(function () {
                get_route_data_from_sr_id($(this).val());
            });
            
              function get_route_data_from_sr_id()
                    {
                        var distributor_id = $("#distributor_id").val();
                        var sr_id = $("#sr_id").val();
                        var office_id = $("#office_id").val();
                        var memo_date = $("#DistMemoMemoDate").val();
                        
                        if(memo_date!="" && sr_id)
                        {
                            $.ajax({
                                url: '<?= BASE_URL . 'distMemos/get_route_list_from_memo_date' ?>',
                                data: {'distributor_id': distributor_id,'sr_id': sr_id,'office_id': office_id,'memo_date': memo_date},
                                type: 'POST',
                                success: function (data)
                                {
                                    $("#dist_route_id").html(data);
                                }
                            });
                        }
                        else if(memo_date=="")
                        {
                        alert("Please enter Memo Date");
                         $("#sr_id option[value='']").attr('selected', true);
                        }

                        $('.outlet_id').html('<option value="">---- Select Outlet ----');
                    }

            function get_dist_by_office_id(office_id)
            {
                var memo_date = $("#DistMemoMemoDate").val();
                if(memo_date!="")
                {
                $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_dist_list_by_office_id' ?>',
                    data: {'office_id': office_id,'memo_date':memo_date},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#distributor_id").html(data);
                    }
                });
                }
                else 
                {
                 var office_id = $("#office_id").val();
                  $("#office_id option[value='']").attr('selected', true);
                  alert("Please enter Memo Data");
                  // var data="<option value=''>---- Select Office ----</option>";
                  // $("#office_id").html(data);
                }
            }

            function get_sr_list_by_distributor_id(distributor_id)
            {
                var memo_date = $("#DistMemoMemoDate").val();
                if(memo_date!="")
                {
                 $.ajax({
                    url: '<?= BASE_URL . 'DistMemos/get_sr_list_by_distributot_id' ?>',
                    data: {'distributor_id': distributor_id,'memo_date':memo_date},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#sr_id").html(data);
                    }
                });
                }
                else 
                {
                     alert("Please enter Memo Date");
                     $("#sr_id option[value='']").attr('selected', true);
                }

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
                    }
                }
            });
            
          /******  Submit data on CTRL+S event End ************/  
          
          /******  Normal form submission start ************/
          
           $("form#DistMemoAdminCreateMemoForm").submit(function(e){
                       var error_count=0;
                       error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                e.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            
           });
          
          /******  Normal form submission end ************/
          
            
        });
        /*For Csa Memo Need Two Extra field (csa and thana) : End*/
<?php } ?>

  /************************* generating Products UI start ***********************************/
  
$("body").on("focusout", "#no_of_product", function () {
            var no_of_product = $("#no_of_product").val();
            
              if(no_of_product!='' && (!isNaN(no_of_product)) && no_of_product>1)
              {
              generate_product_ui(no_of_product);
              }
              else 
              {
                  generate_product_ui(1);
              }
        });
 
    /************************* generating Products UI End ***********************************/     
    
   function  generate_product_ui(no_of_product)
   {             
            var product_box = $('.product_row_box').html('');
            
            rowUpdate(1);
           
            var product_list = $('#memo_product_list').html();
            var product_box = $('.product_row_box').html('');
            var current_row = '<th class="text-center sl_memo" width="5%"></th><th id="memo_product_list" class="text-center">' + product_list + '</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" min="0" name="data[MemoDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[MemoDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[MemoDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[MemoDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[MemoDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%">';
            var current_row_btn_part='<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';
            var current_row_btn_hide_part='<a class="btn btn-primary btn-xs add_more" style="display:none"><i class="glyphicon glyphicon-plus"></i></a></th>';
            var rn;
            
            for(rn=1;rn<=no_of_product;rn++)
            {
                if(rn==no_of_product)
                {
                    product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_part +'</tr>');
                   $('#' + rn + '>.sl_memo').text(rn);
                }
                else 
                {
                     product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_part +'</tr>');
                   // product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_hide_part + '</tr>');
                    $('#' + rn + '>.sl_memo').text(rn);
                }
                
            }
            
             //$('#loading').hide();
             //$('#myModal').modal('hide');
            
    }      
    
    
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
</script>
