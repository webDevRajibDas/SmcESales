<?php 

?>
<script>
var tso_list='<?php echo json_encode($tso_list);?>';
var ae_list='<?php echo json_encode($ae_list);?>';
</script>

<style>
    .form-control
    {
        width: 60%;
    }
    .width_100_this
    {
         width:100%;
     }
     .display_none{display:none;}
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
    .policy_bonus_product_id{
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
    label
    {
        width: 25%;
    }
   .errorInput
    {
            border:1px solid #ff0000;
    }
    .bonus_dis_row{
    display:none;
}
option:disabled{
    color: #999;
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

    //pr($pre_markets);
   // pr($from_outlet); 
    //pr($pre_submit_data); 
    //exit;

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
$selected_order_date="";
$selected_no_of_products="";

if(!empty($pre_submit_data))
{
   $selected_office=$pre_submit_data['office_id']; 
   $selected_dist=$pre_submit_data['distributor_id']; 
   $selected_sr=$pre_submit_data['sr_id']; 
   $selected_route=$pre_submit_data['dist_route_id'];
   $selected_territory=$pre_submit_data['territory_id']; 
   $selected_thana=$pre_submit_data['thana_id'];
   $selected_market=$pre_submit_data['market_id']; 
   //$selected_outlet=$pre_submit_data['outlet_id'];
   $selected_ae=$pre_submit_data['ae_id']; 
   $selected_tso=$pre_submit_data['tso_id'];
   $selected_order_ref_no='';
   $selected_ae_name=$ae_list[$selected_ae];
   $selected_tso_name=$tso_list[$selected_tso];
   $selected_order_date=date("d-m-Y",strtotime($pre_submit_data['order_date']));

}
else 
{
   $pre_outlets=$outlets;
   
   if(!empty($from_outlet))
   {
        if(isset($ae_id))
        {
           $selected_ae=$ae_id; 
           if($selected_ae)
           $selected_ae_name=$ae_list[$selected_ae];
        }

       if(isset($tso_id))
        {
        $selected_tso=$tso_id;
        
         if($selected_tso)
         $selected_tso_name=$tso_list[$selected_tso];
        }
       
        if($territory_id)
        $from_outlet['DistOutlet']['territory_id']=$territory_id;
        
        if($thana_id)
        $from_outlet['DistOutlet']['thana_id']=$thana_id;
       
        $selected_order_date=date("d-m-Y",strtotime($from_outlet['DistOutlet']['market_order_date']));
   }

}




/********************** Checking data is previously submitted End ********************/
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Create SR Delivery'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistOrder', array('role' => 'form')); ?>
                <div class="row">
                    <!-- First Part : Start -->
                    <div class="col-xs-6">
                        <div class="form-group">
                            <?php echo $this->Form->input('order_date', array('class' => 'form-control datepicker','id'=>'order_date', 'value' => ($selected_order_date)?$selected_order_date:$current_date, 'type' => 'text', 'required' => TRUE)); ?>
                        </div>
                        
                        
                            <?php echo $this->Form->input('entry_date', array('type'=>'hidden','class' => 'form-control datepicker', 'value' => (isset($this->request->data['DistOrder']['entry_date']) == '' ? $current_date : $this->request->data['DistOrder']['entry_date']), 'required' => TRUE)); ?>
                        
                        
                         
                        
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
                                <?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'onChange' => 'rowUpdate(1);', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_distributor_id'] : $selected_dist, 'id' => 'distributor_id', 'required' => 'required','options'=>$distributors,'empty' => '--- Select Distributor ---')); ?> 
                                
                                <?php echo $this->Form->input('dist_territory_id', array('class' => 'form-control dist_territory_id','id' => 'dist_territory_id','type'=> 'hidden')); ?> 

                                <?php echo $this->Form->input('dist_thana_id', array('class' => 'form-control dist_thana_id','id' => 'dist_thana_id','type'=>'hidden')); ?>

                            </div>
                        
                         

                            <div class="form-group">
                                <?php echo $this->Form->input('sr_id', array('label' => 'SR :', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_sales_representative_id'] : $selected_sr, 'class' => 'form-control ', 'id' => 'sr_id', 'empty' => '--- Select SR ---', 'required' => 'required','options'=>$pre_srs)); ?> 
                            </div>
                        <?php } ?>
                        <?php echo $this->Form->input('territory_id', array('type' => 'hidden', 'id' => 'territory_id', 'class' => 'form-control territory_id', 'value' => !empty($from_outlet) ? $from_outlet['DistOutlet']['territory_id'] : $selected_territory)); ?>


                        <?php if ($dist == 1) { ?>             
                            <?php echo $this->Form->input('thana_id', array('label' => false, 'type' => 'hidden', 'id' => 'thana_id', 'value' => !empty($from_outlet) ? $from_outlet['DistOutlet']['thana_id'] : $selected_thana)); ?>                    
                        <?php } ?>

                    </div>
                    <!-- First Part : END -->
                    <!-- Second Pard : Start -->
                    <div class="col-xs-6">
                        <div class="form-group">
                            <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id', 'label' => 'Route/Beat :', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_route_id'] :$selected_route, 'class' => 'form-control', 'empty' => '---- Select ----','options'=>$pre_routes)); ?>
                        </div>
                        <div class="form-group"  id="market_id_so">
                            <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_market_id'] : $selected_market, 'required' => TRUE, 'empty' => '---- Select Market ----','options'=>$pre_markets,'div'=>false)); ?>
                            <button type="button" class="btn btn-primary btn-xs make_market"  data-toggle="modal" data-target="#myModal1"><i class="glyphicon glyphicon-plus"></i></button>
                        </div>

                        <div class="form-group"  id="outlet_id_so">
                            <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['dist_outlet_id'] : $selected_outlet, 'required' => TRUE, 'empty' => '---- Select Outlet ----', 'options' => $pre_outlets,'div'=>false)); ?>
                            <button type="button" class="btn btn-primary btn-xs make_outlet" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-plus"></i></button>
                        </div>
                        
                        <div class="form-group">
                            <?php
                            
                            if(!empty($pre_submit_data))
                            {
                                echo $this->Form->input('order_reference_no', array('label'=>'Reference Order Number :','class' => 'form-control order_reference_no','selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['market_order_reference_no'] :'', 'maxlength' => '15', 'required' => true, 'type' => 'text','value'=>''));
                            }
                            else if(!empty($from_outlet))
                            {
                               echo $this->Form->input('order_reference_no', array('label'=>'Reference Order Number :','class' => 'form-control order_reference_no', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['market_order_reference_no'] :'','maxlength' => '15', 'required' => true,'value'=>!empty($from_outlet) ? $from_outlet['DistOutlet']['market_order_reference_no'] :'', 'type' => 'text')); 
                            }
                            else 
                            {
                                echo $this->Form->input('order_reference_no', array('label'=>'Reference Order Number :','class' => 'form-control order_reference_no', 'selected' => !empty($from_outlet) ? $from_outlet['DistOutlet']['market_order_reference_no'] :'','maxlength' => '15', 'required' => true, 'type' => 'text'));
                            }
                           ?>
                        </div>
                        
                        <div class="form-group">
                            <?php
                                echo $this->Form->input('no_of_product', array('label'=>'No of Products :','id'=>'no_of_product','class' => 'form-control', 'required' => FALSE, 'type' => 'number','value'=>""));
                           ?>
                         </div>

                        <div class="form-group">
                            <?php
                               echo $this->Form->input('ae_name', array('label'=>'Area Executive :','id'=>'ae_name','class' => 'form-control', 'required' => true, 'type' => 'hidden','value'=>"$selected_ae_name",'readonly'));
                           ?>
                         </div>
                         
                         <div class="form-group">
                            <?php
                                echo $this->Form->input('tso_name', array('label'=>'TSO :','id'=>'tso_name','class' => 'form-control', 'required' => true, 'type' => 'hidden','value'=>"$selected_tso_name",'readonly'));
                           ?>
                         </div>
                        
                        
                        

                        <?php echo $this->Form->input('order_no', array('class' => 'form-control order_no', 'required' => TRUE, 'type' => 'hidden', 'value' => $generate_order_no, 'readonly')); ?>

                         <?php echo $this->Form->input('ae_id', array('type' => 'hidden','id'=>'ae_id','label' => false, 'class' => 'form-control', 'value' =>$selected_ae)); ?> 
                                <?php echo $this->Form->input('tso_id', array('type' => 'hidden', 'id'=>'tso_id','label' => false, 'class' => 'form-control', 'value' =>$selected_tso)); ?>
                    </div>
                 <!-- Second Pard : Start -->   
                </div>
               

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
                                <th class="text-center" width="12%">Discount Value</th>
                                <th class="text-center" width="10%">Bonus</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="product_row_box">
                            <tr id="1" class="new_row_number">
                                <th class="text-center sl_Order" width="5%">1</th>
                                <th class="text-center" id="order_product_list">
                                    <?php
                                    echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 product_id', 'empty' => '---- Select Product ----', 'label' => false, 'required' => true));
                                    ?>
                                    <input type="hidden" class="product_id_clone" />
                                    <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 product_category_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly />
                                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty sales_qty_validation_check" required />
                                    <input type="hidden" class="combined_product"/>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly />
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />
                                    <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>
                                </th>
                                <th class="text-center" width="10%">
                                    <input type="text" class="form-control width_100 bonus" disabled />
                                    <input type="hidden" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>
                                    <input type="hidden" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>
                                    <input type="hidden" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                    <!-- <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a> -->
                                    <?php //echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete_Order'), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete'));  ?>
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" align="right"><b>Total : </b></td>
                                <td align="center"><input name="data[DistOrder][gross_value]" class="form-control width_100" type="text" id="gross_value" value="0" readonly />
                                </td>
                                <td></td>
                               
                            </tr>
                            <tr>
                                <td colspan="5" align="right"><b>Discount : </b></td>
                                <td align="center">
                                    <input name="data[DistOrder][total_discount]" class="form-control width_100 total_discount" type="text" id="total_discount" value="0" readonly />
                                    <input name="data[DistOrder][discount_percent]" class="form-control width_100 discount_percent" type="hidden" id="discount_percent" readonly/>
                                    <input name="data[DistOrder][discount_value]" class="form-control width_100 discount_value" type="hidden" id="discount_value" readonly />
                                    <input name="data[DistOrder][discount_type]" class="form-control width_100 discount_value" type="hidden" id="discount_type_memo_total" readonly />
                                </td>
                                <td></td>
                              
                            </tr>
                            <tr>
                                <td colspan="5" align="right"><b>Net Payable: </b></td>
                                <td align="center"><input name="data[Memo][gross_value]" class="form-control width_100 net_payable" type="text" id="net_payable" value="0" readonly />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right"><b>Cash Collection : </b></td>
                                <td align="center">
                                    <input name="data[DistOrder][cash_recieved]" class="form-control width_100" type="text" id="cash_collection" />
                                    <input name="data[DistOrder][credit_amount]" class="form-control width_100" type="hidden" id="credit_amount" readonly />
                                </td>
                                <td></td>
                              
                            </tr>
                            <tr>
                            <td colspan="4"> 
                                <a class="btn btn-primary btn-xs show_bonus" data-toggle="modal"  data-backdrop="static" data-keyboard="false"   data-target="#bonus_product"><i class="glyphicon glyphicon-plus"></i>Bonus</a>
                                <div id="bonus_product" class="modal fade" role="dialog">

                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Bonus</h4>
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
                                                        <tr>
                                                            <td colspan="4">
                                                                Open Bonus Part
                                                            </td>
                                                        </tr>
                                                        <tr  class="bonus_row">
                                                            <th class="text-center" id="bonus_product_list">
                                                                <?php
                                                                echo $this->Form->input('product_id', array('name' => 'data[OrderDetail][product_id][]', 'class' => 'form-control width_100 open_bonus_product_id', 'empty' => '---- Select Product ----', 'label' => false,'autofocus' => true));
                                                                ?>
                                                                <input type="hidden" class="product_id_clone" />
                                                                <input type="hidden" name="data[OrderDetail][product_category_id][]" class="form-control width_100 open_bonus_product_category_id"/>
                                                            </th>
                                                            <th class="text-center" width="12%">
                                                                <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" disabled/>
                                                                <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>

                                                                <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />
                                                                <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>
                                                            </th>
                                                            <th class="text-center" width="12%">
                                                                <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" id='open_bonus_min_qty' class="form-control width_100 open_bonus_min_qty" />
                                                                <input type="hidden" class="combined_product"/>
                                                                <input type="hidden" name="data[OrderDetail][discount_amount][]" />
                                                                <input type="hidden" name="data[OrderDetail][disccount_type][]"/>
                                                                <input type="hidden" name="data[OrderDetail][policy_type][]"/>
                                                                <input type="hidden" name="data[OrderDetail][policy_id][]"/>
                                                                <input type="hidden" name="data[OrderDetail][is_bonus][]" value="1"/>
                                                            </th>
                                                            <th class="text-center" width="10%">
                                                                <a class="btn btn-primary btn-xs bonus_add_more"><i class="glyphicon glyphicon-plus"></i></a>
                                                            </th>
                                                        </tr>
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
                            <td align="right"></td>
                            <td align="center">
                                
                            </td>
                            <td></td>
                              
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group" style="padding-top:20px;">
                    <div>
                        <p>
                            <b>CTRL+S</b> = Order/Market(If Panel Open)/Outlet(If Panel Open) Save, 
                            <b>CTRL+L</b> = Last Order Info Open/Close, 
                            <b>CTRL+B</b> = Bonus Panel Open, 
                            <b>CTRL+C</b> = Bonus Panel Close,  
                        </p>
                        <p>
                            <b>CTRL+F</b> = Bonus Product Row Add,  
                            <b>CTRL+M</b> = New Market Panel Open/Close,   
                            <b>CTRL+O</b> = New Outlet Panel Open/Close,  
                            <b>CTRL+P</b> = Add New Product Row,  
                        </p>
                    </div>
                    <div class="pull-right">
                        <?php //echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save', 'div' => false, 'name' => 'save')); ?>
                        <?php  echo $this->Form->submit('Draft', array('class' => 'submit_btn btn btn-large btn-warning draft', 'div' => false, 'name' => 'draft')); ?>
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
                <h4 class="modal-title">Create Market</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">		
                    <?php echo $this->Form->create('DistMarket', array('id' => 'market_model', 'controller' => 'DistMarkets', 'action' => 'admin_add_market'), array('role' => 'form')); ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('name', array('class' => 'form-control dist_markets','autofocus' => true)); ?>
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
                    <?php echo $this->Form->input('market_distributor_id', array('type'=>'hidden','id' => 'market_distributor_id')); ?>
                    <?php echo $this->Form->input('market_sr_id', array('type'=>'hidden','id' => 'market_sr_id')); ?>
                    <?php echo $this->Form->input('market_route_id', array('type'=>'hidden','id' => 'market_route_id')); ?>
                    <?php echo $this->Form->input('market_order_date', array('type'=>'hidden','id' => 'market_order_date')); ?>
                    
                    <?php echo $this->Form->input('market_ae_id', array('type'=>'hidden','id' => 'market_ae_id')); ?>
                    <?php echo $this->Form->input('market_tso_id', array('type'=>'hidden','id' => 'market_tso_id')); ?>
                    <?php echo $this->Form->input('market_order_reference_no', array('type'=>'hidden','id' => 'market_order_reference_no')); ?>
                    
                    <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Active :</b>', 'default' => 1)); ?>
                        <?php echo $this->Form->input('tag', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 'from_Order')); ?>
                    </div>              
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','id'=>'market_model_btn')); ?>
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
                <h4 class="modal-title">Create Outlet</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">		
                    <?php echo $this->Form->create('DistOutlet', array('id' => 'outlet_model', 'controller' => 'DistOutlets', 'action' => 'admin_add_outlet'), array('role' => 'form')); ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('name', array('class' => 'form-control','autofocus'=>true)); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('category_id', array('label' => 'Outlet Type', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                        <?php echo $this->Form->input('is_ngo', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 0)); ?>
                        <?php echo $this->Form->input('tag', array('type' => 'hidden', 'label' => false, 'class' => 'form-control', 'value' => 'from_Order')); ?>
                       
                    
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
                    <!-- <div class="form-group">
                                        <?php //echo $this->Form->input('territory_id', array('id' => 'territory_id1', 'class' => 'form-control territory_id', 'empty' => '---- Select ----')); ?>
                                     </div>
                                     <div class="form-group">
                                        <?php //echo $this->Form->input('thana_id', array('id' => 'thana_id1', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                                     </div>  -->                 
                    <div class="form-group">
                        <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id1', 'label' => 'Route/Beat', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>                
                    					
                    <div class="form-group">
                        <?php echo $this->Form->input('dist_market_id', array('label' => 'Distributor Market', 'id' => 'dist_market_id1', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    </div>               
                       
                    
                    <?php echo $this->Form->input('market_distributor_id', array('type'=>'hidden','id' => 'outlet_distributor_id')); ?>
                    <?php echo $this->Form->input('market_sr_id', array('type'=>'hidden','id' => 'outlet_sr_id')); ?>
                    <?php echo $this->Form->input('market_route_id', array('type'=>'hidden','id' => 'outlet_route_id')); ?>
                    <?php echo $this->Form->input('market_order_date', array('type'=>'hidden','id' => 'outlet_order_date')); ?>
                    <?php echo $this->Form->input('market_market_id', array('type'=>'hidden','id' => 'outlet_market_id')); ?>
                    
                    <?php echo $this->Form->input('market_ae_id', array('type'=>'hidden','id' => 'outlet_ae_id')); ?>
                    <?php echo $this->Form->input('market_tso_id', array('type'=>'hidden','id' => 'outlet_tso_id')); ?>
                    <?php echo $this->Form->input('market_order_reference_no', array('type'=>'hidden','id' => 'outlet_order_reference_no')); ?>
                                    
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','id'=>'outlet_model_btn')); ?>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Last Order info: Start  -->
<div class="modal fade" id="last_Order" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Last Order Info.</h4>
            </div>
            <div class="modal-body">
                <p>No Data Availabe. Please Select Office First.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Last Order info: END  -->
<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
    $(document).ready(function () {
   

        /********* for market creation start ******/
        $('body').on("click", ".make_market", function () 
        {
            //$("#DistMarketName").trigger("focus");
            var dist_idd=$("#distributor_id").val();
            var dist_sr_idd=$("#sr_id").val();
            var market_route_id=$("#dist_route_id").val();
            var market_order_date=$("#order_date").val();
            var market_ae_id=$("#ae_id").val();
            var market_tso_id=$("#tso_id").val();
            var DistOrderOrderReferenceNo=$("#DistOrderOrderReferenceNo").val();
            var office_id=$("#office_id").val();
            var territory_thana_id=get_territory_and_thana_id_by_distributor_id(dist_idd);

            var territory_id = territory_thana_id['territory_id'];
            var thana_id=territory_thana_id['thana_id'];

            if(market_route_id != ""){
               var thana_territory_id =  get_territory_and_thana_id_by_route_id(market_route_id);
               thana_id = thana_territory_id['thana_id'];
               territory_id = thana_territory_id['territory_id'];
            }

            $('#dist_territory_id').val(territory_id);
            $('#dist_thana_id').val(thana_id);
            //var territory_id=territory_thana_id.territory_id;
            //var thana_id=territory_thana_id.thana_id;
            get_thana_by_territory_id(territory_id,thana_id);
            get_territory_list(office_id);
            var route_id=$("#dist_route_id").val();

            $("#market_distributor_id").val(dist_idd).delay(300);
            $("#market_sr_id").val(dist_sr_idd);
            $("#market_route_id").val(market_route_id);
            $("#market_order_date").val(market_order_date);
            $("#market_ae_id").val(market_ae_id);
            $("#market_tso_id").val(market_tso_id);
            $("#market_order_reference_no").val(DistOrderOrderReferenceNo);

            /**** for default selection ******/
            
            $("#office_id2").val(office_id);
            //$("#territory_id2").val(territory_id);
            //$("#territory_id2[value="+territory_id+"]").prop('selected', true);
            //$('#territory_id2').find('option[value="' + territory_id + '"]').attr("selected", "selected");
            //$("#dist_route_id2").val(route_id);
    });
   $('#myModal1').on('shown.bs.modal', function () {
        $('#DistMarketName').focus();
    }) ;
 /*  $('#myModal1').on('hidden.bs.modal', function () {
        console.log('hi! i m closed');
        $('body').trigger($.Event('keyup', {keyCode:9, shiftKey: true}));
    })*/
       /********* for market creation end ******/
       
       
       
       
       /********* for Outlet creation start ******/
       
        $('body').on("click", ".make_outlet", function () {
            
        var dist_idd=$("#distributor_id").val();
        var dist_sr_idd=$("#sr_id").val();
        var market_route_id=$("#dist_route_id").val();
        var market_order_date=$("#order_date").val();
        var market_ae_id=$("#ae_id").val();
        var market_tso_id=$("#tso_id").val();
        var market_market_id=$("#market_id").val();
        var DistOrderOrderReferenceNo=$("#DistOrderOrderReferenceNo").val();
        var office_id=$("#office_id").val();
        
        var route_id=$("#dist_route_id").val();
        var market_id=$("#market_id").val();

        var territory_thana_id=get_territory_and_thana_id_by_distributor_id(dist_idd);

        var territory_id = territory_thana_id['territory_id'];
        var thana_id=territory_thana_id['thana_id'];

        if(market_route_id != ""){
           var thana_territory_id =  get_territory_and_thana_id_by_route_id(market_route_id);
           thana_id = thana_territory_id['thana_id'];
           territory_id = thana_territory_id['territory_id'];
        }

        $('#dist_territory_id').val(territory_id);
        $('#dist_thana_id').val(thana_id);
        if(market_id)
        {
            var territory_id=$("#dist_territory_id").val();
            var thana_id=$("#dist_thana_id").val();

            get_thana_by_territory_id2(territory_id,thana_id);
        }
        else
        {
            territory_thana_id=get_territory_and_thana_id_by_distributor_id(dist_idd);
            var territory_id=territory_thana_id.territory_id;
            var thana_id=territory_thana_id.thana_id;
            get_thana_by_territory_id2(territory_id,thana_id);
        }
        get_route_list_by_thana_id2();

        
        
        //var territory_id=territory_thana_id.territory_id;
        //var thana_id=territory_thana_id.thana_id;
        get_thana_by_territory_id2(territory_id,thana_id);
        get_territory_list(office_id);
        $("#outlet_distributor_id").val(dist_idd);
        $("#outlet_sr_id").val(dist_sr_idd);
        $("#outlet_route_id").val(market_route_id);
        $("#outlet_order_date").val(market_order_date);
        $("#outlet_ae_id").val(market_ae_id);
        $("#outlet_tso_id").val(market_tso_id);
        $("#outlet_market_id").val(market_market_id);
        $("#outlet_order_reference_no").val(DistOrderOrderReferenceNo);

        //$("#dist_route_id1").val(market_route_id);
        $("#dist_market_id1").val(market_market_id);

        /**** for default selection ******/
        $("#office_id1").val(office_id);
        $("#territory_id1").val(territory_id);
        //get_route_data_from_dist_id();
        $("#dist_route_id1").val(route_id);
        market_html=$("#market_id").html();
        if($("#dist_market_id1").html(market_html)&&market_id)
        {
            $("#dist_market_id1").val(market_id);
        }
         // $("#DistOutletName").focus();
    });
    $('#myModal2').on('shown.bs.modal', function () {
        $('#DistOutletName').focus();
    }) ;
       /********* for Outlet creation end ******/
        
       function get_territory_and_thana_id_by_distributor_id(dist_id)
       {
            var territory_thana_id_json;
            $.ajax({
                async:false,
                type: "POST",
                url: "<?php echo BASE_URL; ?>DistOrders/get_territory_and_thana_id_by_distributor_id",
                data: {'dist_id':dist_id},
                dataType: "JSON",
                success: function (response) {
                    territory_thana_id_json=response;
                    // return response;
                }
            });
           return territory_thana_id_json;
        }
        function get_territory_and_thana_id_by_route_id(dist_route_id)
       {
            var territory_thana_id_json;
            $.ajax({
                async:false,
                type: "POST",
                url: "<?php echo BASE_URL; ?>DistOrderDeliveries/get_territory_and_thana_id_by_route_id",
                data: {'dist_route_id':dist_route_id},
                dataType: "JSON",
                success: function (response) {
                    territory_thana_id_json=response;
                    // return response;
                }
            });
           return territory_thana_id_json;
        } 
        function get_thana_by_territory_id(territory_id,thana_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_thana_by_territory_id' ?>',
                data: {'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    if($("#thana_id1").html(data)&&thana_id)
                    {

                        $("#thana_id1").val(thana_id);
                        
                    }
                    if($("#thana_id2").html(data)&&thana_id)
                    {

                        $("#thana_id2").val(thana_id);
                    }
                }
            });
        }
        function get_thana_by_territory_id2(territory_id,thana_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_thana_by_territory_id' ?>',
                data: {'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    
                    if($("#thana_id2").html(data)&&thana_id)
                    {

                        $("#thana_id2").val(thana_id);
                    }
                }
            });
        }
        $('body').on('keydown', 'input, select, textarea', function(e) {
            var self = $(this)
              , form = self.parents('form:eq(0)')
              , focusable
              , next
              ;
            if (e.keyCode == 13) {
                focusable = form.find('input,select,textarea').filter(':visible');
                /*console.log(this.class);
                console.log(this.name);
                console.log(this);
                console.log(focusable.index(this)+1);
                console.log(focusable.index(this));
                console.log(focusable);*/
                if(this.id == 'OrderProductId')
                    next = focusable.eq(focusable.index(this)+3);
                else if(this.id=='open_bonus_min_qty')
                      next = focusable.eq(focusable.index(this)+1);
                else if(this.name=='data[OrderDetail][sales_qty][]')
                    next = focusable.eq(focusable.index(this)+4);
                else if(this.id=='DistOrderProductId')
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
<?php
if (empty($from_outlet) && (empty($pre_submit_data))) {
    ?>
            $("#office_id").prop("selectedIndex", 0);
            $("#distributor_id").prop("selectedIndex", 0);
            $("#sr_id").prop("selectedIndex", 0);
            $("#dist_route_id").prop("selectedIndex", 0);
            $("#market_id").prop("selectedIndex", 0);
            $("#outlet_id").prop("selectedIndex", 0);
            $(".order_reference_no").val('');
            $("#office_id").focus();
    <?php
}
else if(!empty($pre_submit_data))
    {
?>
    $("#outlet_id").focus();     
    rowUpdate(1);
   
<?php
    }
else if(!empty($from_outlet))
   {    
?>
    $("#outlet_id").focus();          
    rowUpdate(1);
<?php 
   } 
  ?>       
    });
</script>



<script>
    $("#distributor_id").change(function () {
        //get_route_data_from_dist_id();
        $('.market_id').html('<option value="">---- Select ----');
        $('.outlet_id').html('<option value="">---- Select ----');
        $('#dist_route_id').html('<option value="">---- Select ----');
    });
    $("#dist_route_id").change(function () {
        //get_route_data_from_dist_id();
        $('#market_id').html('<option value="">---- Select ----');
        $('#outlet_id').html('<option value="">---- Select ----');
    });

    $("#market_model").submit(function (event) {
        $("#divLoading_default").remove();
         $("#market_model_btn").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>admin/DistMarkets/add",
            data: $(this).serializeArray(),
            success: function (response) {
                $("#market_model").attr("data-dismiss", "modal");
                window.location.assign("<?php echo BASE_URL; ?>admin/DistOrderDeliveries/create_order");
            }
        });
        //$('.img-circle').hide();
        event.preventDefault();
    });
    $("#outlet_model").submit(function (event) {
        $("#divLoading_default").remove();
        $("#outlet_model_btn").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>admin/DistOrderDeliveries/add_outlet",
            data: $(this).serializeArray(),
            success: function (response) {
                $("#outlet_model").attr("data-dismiss", "modal2");
                window.location.assign("<?php echo BASE_URL; ?>admin/DistOrderDeliveries/create_order");
                var market_id = $("#market_id").val();
                /*$.ajax({
                    type: "POST",
                    url: '<?= BASE_URL . 'DistOrders/get_outlet_after_create'; ?>',
                    data: {'market_id': 'market_id'},
                    success: function (response) {
                        $('.outlet_id').html(response);
                    }
                });*/
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

                var territory_id = $('#dist_territory_id').val();
                var thana_id = $('#dist_thana_id').val();
                $("#territory_id2").val(territory_id);
                if(territory_id != ''){
                    get_route_list_by_thana_id2();
                }
            }
        });
    }
    $('body').on("change", "#office_id1", function () {
        var office_id = $(this).val();
        get_territory_list(office_id);
        //get_route_by_office_id($(this).val());
    });
    $('body').on("change", "#territory_id1", function () {
        var territory_id = $(this).val();
        $('#territory_id').val(territory_id);
    });
    $('body').on("change", "#thana_id1", function () {
        var thana_id = $(this).val();
        $('#thana_id').val(thana_id);
        get_route_list_by_thana_id();
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
        //get_route_by_office_id(office_id);
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


   /* function get_route_by_office_id(office_id)
    {
        //alert(office_id);

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
    }*/

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
        //get_market_data2();
        get_route_list_by_thana_id2();

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
        //var thana_id = $("#thana_id1").val();
        //var location_type_id = $("#location_type_id1").val();
        //var territory_id = $("#territory_id1").val();

        $.ajax({
            url: '<?= BASE_URL . 'DistOrderDeliveries/get_market_list' ?>',
            //data: {'dist_route_id': dist_route_id, 'thana_id': thana_id, 'territory_id': territory_id},
            data: {'dist_route_id': dist_route_id},
            type: 'POST',
            success: function (data)
            {
                $("#dist_market_id1").html(data);
            }
        });
    }
    function get_route_list_by_thana_id()
    {
        
        var thana_id = $("#thana_id1").val();
        if(thana_id == '' || thana_id == undefined){
            thana_id = $('#dist_thana_id').val();
            if(thana_id == '' || thana_id == undefined){
                thana_id = $('#thana_id').val();
            }
        }
        $.ajax({
            url: '<?= BASE_URL . 'distOrders/get_route_list_by_thana_id' ?>',
            data: {'thana_id': thana_id,},
            type: 'POST',
            success: function (data)
            {
                $("#dist_route_id1").html(data);
                var dist_route_id = $("#dist_route_id").val();
                $("#dist_route_id1").val(dist_route_id);
                $("#dist_route_id2").html(data);
                $("#dist_route_id2").val(dist_route_id);
            }
        });
    }
    function get_route_list_by_thana_id2()
    {
        
        var thana_id = $("#thana_id2").val();
        if(thana_id == '' || thana_id == undefined){
            thana_id = $('#dist_thana_id').val();
            if(thana_id == '' || thana_id == undefined){
                thana_id = $('#thana_id').val();
            }
        }
        $.ajax({
            url: '<?= BASE_URL . 'distOrders/get_route_list_by_thana_id' ?>',
            data: {'thana_id': thana_id,},
            type: 'POST',
            success: function (data)
            {
                //$("#dist_route_id1").html(data);
                $("#dist_route_id2").html(data);
                $("#dist_route_id1").html(data);
                var dist_route_id = $("#dist_route_id").val();
                $("#dist_route_id2").val(dist_route_id);
                $("#dist_route_id1").val(dist_route_id);
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

    var special_groups=[];
    var outlet_category_id;
    function productList()
    {
        var csa_id = 0;
        var distributor_id = $('#distributor_id').val();
        var order_date=$('#order_date').val();

        if (distributor_id) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'admin/DistOrders/get_product' ?>',
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
                url: '<?= BASE_URL . 'DistOrders/get_bonus_product' ?>',
                data: {'distributor_id': distributor_id,'order_date':order_date},
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
    function get_special_group()
    {
        var outlet_id=$('#outlet_id').val(); 
        var memo_date=$('#order_date').val();
        var office_id=$('#office_id').val();
        var territory_id=$('#territory_id').val();
        if(outlet_id){
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL . 'DistOrderDeliveries/get_spcial_group_and_outlet_category_id'?>',
                data: {
                    'outlet_id':outlet_id,
                    'memo_date':memo_date,
                    'office_id':office_id,
                },
                cache: false, 
                success: function(response){
                    var res=$.parseJSON(response);
                    outlet_category_id=res.outlet_category_id;
                    special_groups=res.special_group_id;
                }
            });     
        }
    }

    function rowUpdate(productLit) {

        sl = 1;

        product_list = '<div class="input select"><select id="OrderProductId" required="required" class="form-control width_100 product_id" name="data[OrderDetail][product_id][]"><option value="">---- Select Product ----</option></select></div><input type="hidden" class="product_id_clone"><input type="hidden" class="form-control width_100 product_category_id" name="data[OrderDetail][product_category_id][]">';


        var current_row = '<th class="text-center sl_Order" width="5%">1</th>\
        <th id="order_product_list" class="text-center">' + product_list + '</th>\
        <th class="text-center" width="12%">\
            <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
            <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
        </th>\
        <th class="text-center" width="12%">\
            <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
            <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
        </th>\
        <th>\
            <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty sales_qty_validation_check" value="" required/>\
            <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
            <input type="hidden" class="combined_product"/>\
        </th>\
        <th class="text-center" width="12%">\
            <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
        </th>\
        <th class="text-center" width="12%">\
            <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
            <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
            <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
            <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>\
            <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>\
            <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>\
        </th>\
        <th class="text-center" width="10%">\
            <input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
            <input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
            <input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
            <input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
        </th>\
        <th class="text-center" width="10%">\
            <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>\
        </th>';

        $('.product_row_box').html('<tr id="1" class=new_row_number>' + current_row + '</tr>');

        $('#gross_value').val(0);
        //$('.order_no').val('');

        if (productLit == 1) {
            productList();
            get_special_group();
        } else {
            $("#distributor_id").prop("selectedIndex", 0);
        }

    }

    $(document).ready(function ()
    {
        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOrders/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

        /*$('.office_id').selectChain({
            target: $('.distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOrders/get_distributer'; ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });*/

        $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });

        $("#office_id").change(function () {
            //get_route_by_office_id_base($(this).val());
            check_valid_order_no();
        });

        function get_route_by_office_id_base(office_id)
        {

            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    //alert(data);
                    //var data="<option value=''>--- Select ---</option>" + data;
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
                    $("#dist_market_id1").html(data);
                    
                }
            });
        }

        

        function get_route_data_from_dist_id()
        {
            var distributor_id = $("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'distOrders/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                   // alert(data);
                    //$("#dist_route_id").html(data);
                     //var data="<option value=''>--- Select ---</option>";
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
            var order_date = $("#order_date").val();

            if (market_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'distOrders/get_territory_thana_info' ?>',
                    data: {'market_id': market_id,'distributor_id': distributor_id,'office_id':office_id,'order_date':order_date},
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
        // $('#cash_collection').val(t);
    }
	var sl_no_of_product = 1;
    $(document).ready(function () {
        $("body").on("click", ".add_more", function () {
            var sl = $('.invoice_table>tbody>tr').length + 1;
			sl_no_of_product = sl;
            var product_list = $('#order_product_list').html();
           
            var product_box = $(this).parent().parent().parent();
            
            var current_row_no = $(this).parent().parent().attr('id');
            //alert(current_row_no);

            // var current_row = '<th class="text-center sl_Order" width="5%"></th><th class="text-center">' + product_list + '</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number" name="data[OrderDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty sales_qty_validation_check" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%"><a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';

            var current_row = '<th class="text-center sl_Order" width="5%">1</th>\
            <th id="order_product_list" class="text-center">' + product_list + '</th>\
            <th class="text-center" width="12%">\
                <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
                <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
                <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
            </th>\
            <th>\
                <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty sales_qty_validation_check" value="" required/>\
                <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
                <input type="hidden" class="combined_product"/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
                <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
                <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
                <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>\
                <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>\
                <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>\
            </th>\
            <th class="text-center" width="10%">\
                <input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
                <input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
                <input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
                <input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
            </th>\
            <th class="text-center" width="10%">\
                <a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a>\
            </th>';
            var valid_row = $('#' + current_row_no + '>th>.product_rate').val();
            if (valid_row != '') {
                product_box.append('<tr id=' + sl + ' class=new_row_number>' + current_row + '</tr>');
                $('#' + sl + '>.sl_Order').text(sl);
                $(this).hide();
            } else {
                alert('Please fill up this row!');
            }

        });


        $("body").on("change", ".product_id", function ()
        {
            $('#gross_value').val(0);
            var sl = $('.invoice_table>tbody>tr').length;
            var current_row_no = $(this).parent().parent().parent().attr('id');
            if ($('#'+current_row_no+'>th>.product_rate').val() == '') {
                $('#'+current_row_no+'>th>.bonus').val('N.A');
                $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
            }
          

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
            var outlet_id=$('#outlet_id').val();
            var rate_class = product_rate.attr('class').split(' ').pop();
            var value_class = total_val.attr('class').split(' ').pop();
            var distributor_id = $('#distributor_id').val();
            var territory_id = $('.territory_id').val();

            var order_date=$('#order_date').val();  
            $.ajax
            ({
                url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_unit' ?>',
                type: 'POST',
                data: {product_id: product_id, territory_id: territory_id, distributor_id: distributor_id,order_date:order_date},
                success: function (result) 
                {
                    var obj = jQuery.parseJSON(result);
                    product_unit.val(obj.product_unit.name);
                    product_unit_id.val(obj.product_unit.id);
                    var total_qty = obj.total_qty;
                    product_box.find("th:nth-child(6) input").val('');
                    product_box.find("th:nth-child(8) input").val('');
                    $('#'+current_row_no+'>th>.min_qty').attr('max',total_qty);
                    $('#'+current_row_no+'>th>.product_rate').val('0.00');
                }
            });
            

            if (rate_class.lastIndexOf('-') && value_class.lastIndexOf('-') > -1)
            {
                product_rate.removeClass(rate_class);
                total_val.removeClass(value_class);
                /*-----------*/
                product_rate.addClass('prate-' + product_id);
                total_val.addClass('tvalue-' + product_id);
            } 
            else 
            {
                product_rate.addClass('prate-' + product_id);
                total_val.addClass('tvalue-' + product_id);
            }
        });

        /*------- unset session -------*/
    });
</script>
<script>
    /*--------- check combined or individual product price --------*/
    var selected_bonus=[];
    var selected_set=[];
    var selected_policy_type=[];
    var selected_option_id=[];
    $("body").on("keyup", ".min_qty", function (e)
    {
        var min_qty = $(this).val();
        var current_row_no = $(this).parent().parent().attr('id');

        var keycode = (e.keyCode ? e.keyCode : e.which);
        if(keycode == '13'){
            e.preventDefault();
            /*if($(this).val() != ""){
                $('.product_id').eq(current_row_no+1).focus();
            }*/

           var i = 0;
           if($(this).val() != ""){
            $('.product_id').each(function ()
            {   
                
                if($(this).val() == ""){
                    $('.product_id').eq(i).focus();
                    return false;
                }
                i++;
                
            }); 
           }   
        }
        
        var product_category_id = $('#' + current_row_no + '>th>.product_category_id').val();
        pro_val = $('.product_row_box tr#' + current_row_no + ' .product_id').val();

        if ( pro_val) 
        {
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
            var min_qty = product_qty.val();
            var id = product_field.val();
            var product_id = id;
            var order_date=$('#order_date').val();
            
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
            if (new_product == 1)
            {
                
                var product_wise_qty= {};
                $('.product_id').each(function(index,value){
                var producct_box_each=$(this).parent().parent().parent();
                if(producct_box_each.find("th:nth-child(5) .min_qty").val())
                {
                  product_wise_qty[$(this).val()]=producct_box_each.find("th:nth-child(5) .min_qty").val();
                }
                });
                pro_val = $('.product_row_box tr#'+current_row_no+' .product_id').val();
                var sl = $('.invoice_table>tbody>tr').length;
                var product_box = $(this).parent().parent();
                var product_field = product_box.find("th:nth-child(2) .product_id");

                var product_rate = product_box.find("th:nth-child(4) .product_rate");
                var product_price_id = product_box.find("th:nth-child(4) .product_price_id");
                var product_qty = product_box.find("th:nth-child(5) .min_qty");
                var total_val = product_box.find("th:nth-child(6) .total_value");
                var combined_product_obj = product_box.find("th:nth-child(5) .combined_product");
                var combined_product_id_obj = product_box.find("th:nth-child(5) .combination_id");
                var combined_product = combined_product_obj.val();
                var min_qty = product_qty.val();
                var id = product_field.val();

                var order_date=$('#order_date').val(); 
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
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_price' ?>',
                    'type': 'POST',
                    data: {
                        combined_product: combined_product,
                        min_qty: min_qty, 
                        product_id: id,
                        memo_date:order_date ,
                        cart_product:product_wise_qty,
                        special_group:JSON.stringify(special_groups),
                        outlet_category_id:outlet_category_id
                    },
                    success: function (result) 
                    {
                      var obj = jQuery.parseJSON(result);

                      if (obj.price != '')
                      {
                          product_rate.val(obj.price);
                      }
                      if (obj.price_id != '')
                      {
                          product_price_id.val(obj.price_id);
                      }
                      if (obj.total_value)
                      {
                          total_val.val(obj.total_value);
                      }
                      combined_product_obj.val(obj.combine_product);
                      if (obj.combination != undefined)
                      {
                        combined_product_id_obj.val(obj.combination_id);
                        $.each(obj.combination, function (index, value)
                        {
                            var prate = $(".prate-" + value.product_id);
                            var tvalue = $(".tvalue-" + value.product_id);
                            prate.val(value.price);
                            tvalue.val(value.total_value);
                            prate.next('.product_price_id').val(value.price_id);
                            prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
                            prate.parent().parent().find("th:nth-child(5) .combination_id").val(obj.combination_id);
                        });
                      }

                      if (obj.recall_product_for_price != undefined)
                      {
                        $.each(obj.recall_product_for_price, function (index, value)
                        {
                            var prate = $(".prate-" + value);
                            var tvalue = $(".tvalue-" + value);
                            prate.parent().parent().find("th:nth-child(5) .combined_product").val(obj.combine_product);
                            prate.parent().parent().find("th:nth-child(5) .combination_id").val('');
                            prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
                        });
                      }

                      var gross_total = 0;
                      $('.total_value').each(function ()
                      {
                          if($(this).val() !='')
                          {
                            gross_total = parseFloat(gross_total) + parseFloat($(this).val());
                          }
                      });
                      if($("#gross_value").val(gross_total.toFixed(2))){
                        $('.n_bonus_row').remove();
                        $('.discount_value').val(0.00);
                        $('.discount_amount').val(0.00);
                        get_policy_data();
                      }

                      if (obj.mother_product_quantity != undefined)
                      {
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
                            if (i == 0)
                            {
                               $('#'+current_row_no+'>th>.bonus').val('N.A');
                               $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                               $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                               $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                            }
                            else
                            {
                               $('#'+current_row_no+'>th>.bonus').val(bonus_product_quantity[i+(-1)]+'('+bonus_product_name[i+(-1)]+')');
                               $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i+(-1)]);
                               $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_product_quantity[i+(-1)]);
                               $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i+(-1)]);
                            }
                            break;
                          }
                          else
                          {
                            var current_qty = parseFloat($('#'+current_row_no+'>th>.min_qty').val());
                        
                            var bonus_qty = Math.floor(current_qty/parseFloat(mother_product_quantity_bonus)) * bonus_product_quantity[i];
                            $('#'+current_row_no+'>th>.bonus').val(bonus_qty+' ('+bonus_product_name[i]+')');
                            $('#'+current_row_no+'>th>.bonus_product_id').val(bonus_product_id[i]);
                            $('#'+current_row_no+'>th>.bonus_product_qty').val(bonus_qty);
                            $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(sales_measurement_unit_id[i]);
                          }
                        }  
                      }
                      else
                      {
                        $('#'+current_row_no+'>th>.bonus').val('N.A');
                        $('#'+current_row_no+'>th>.bonus_product_id').val(0);
                        $('#'+current_row_no+'>th>.bonus_product_qty').val(0);
                        $('#'+current_row_no+'>th>.bonus_measurement_unit_id').val(0);
                      }
                      $('#cash_collection').val('');
                      $('#loading').hide();
                      $('#myModal').modal('hide');
                      $('.add_more').removeClass('disabled');
                    }
                });

                function get_policy_data()
                {
                  $.ajax({
                      url: '<?= BASE_URL . 'DistOrderDeliveries/get_product_policy' ?>',
                      'type': 'POST',
                      data: {
                                min_qty: min_qty,
                                product_id: id,
                                order_date:order_date ,
                                cart_product:product_wise_qty,
                                memo_total:$("#gross_value").val(),
                                special_group:JSON.stringify(special_groups),
                                outlet_category_id:outlet_category_id,
                                outlet_id:$('#outlet_id').val(),
                                office_id:$('#office_id').val(),
                                distributor_id: distributor_id,
                                selected_bonus: JSON.stringify(selected_bonus),
                                selected_set: JSON.stringify(selected_set),
                                selected_policy_type: JSON.stringify(selected_policy_type),
                                selected_option_id: JSON.stringify(selected_option_id),
                            },
                      success: function (result) 
                      {
                        var response=$.parseJSON(result);
                        if(response.discount)
                        {
                          var discount=response.discount;
                          var total_discount=response.total_discount;
                          $.each(discount,function(ind,val){
                            $.each(val,function(ind1,val1){
                              var prate = $(".prate-" + val1.product_id);
                              var tvalue = $(".tvalue-" + val1.product_id);
                              prate.val(val1.price);
                              tvalue.val(val1.total_value);
                              prate.next('.product_price_id').val(val1.price_id);
                              prate.parent().parent().find("th:nth-child(7) .discount_value").val(val1.total_discount_value);
                              prate.parent().parent().find("th:nth-child(7) .discount_amount").val(val1.discount_amount);
                              prate.parent().parent().find("th:nth-child(7) .disccount_type").val(val1.discount_type);
                              prate.parent().parent().find("th:nth-child(7) .policy_type").val(val1.policy_type);
                              prate.parent().parent().find("th:nth-child(7) .policy_id").val(val1.policy_id);
                              prate.parent().parent().find("th:nth-child(7) .is_bonus").val('0');
                            });
                          });
                          $('.total_discount').val(total_discount.toFixed(2));
                          gross_value = $('#gross_value').val();
                          net_payable = (gross_value)-(total_discount);
                          $('.net_payable').val(net_payable.toFixed(2));
                        }
                        if(response.bonus_html)
                        {
                          var b_html = response.bonus_html;
                          selected_bonus=response.selected_bonus;
                          selected_set=response.selected_set;
                          selected_policy_type=response.selected_policy_type;
                          $('.bonus_product').append(b_html);
                        }
                      selected_option_id=response.selected_option_id;
                      }
                  });
                }

                }, 1000 );
            }
        
        }
        
    });
$("body").on("click",".is_bonus_checked",function(){
    if($(this).prop('checked'))
    {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly',false);
      $(this).parent().prev().find('.policy_min_qty').prop('required',true);
      $(this).parent().prev().find('.policy_min_qty').attr('min',1);
    }
    else
    {
      $(this).parent().prev().find('.policy_min_qty').prop('readonly',true);
      $(this).parent().prev().find('.policy_min_qty').prop('required',false);
      $(this).parent().prev().find('.policy_min_qty').attr('min',0);
      $(this).parent().prev().find('.policy_min_qty').val(0.00);
    }
});


$("body").on("keyup",".policy_min_qty",function(){
    var class_list=$(this).attr('class');
    class_list=class_list.split(" ");
    var policy_set_class=class_list[2];
    var max_qty=parseFloat($(this).attr('max'));
    var stock_qty=parseFloat($(this).data('stock'));
    var total_provide_qty=0.00;
    $("."+policy_set_class).not(this).each(function(ind,val){
      total_provide_qty+=parseFloat($(this).val());
    });
    var given_qty=parseFloat($(this).val());
    var max_provide_qty=max_qty-total_provide_qty;
    if(stock_qty < given_qty && stock_qty<=max_provide_qty)
    {
        $(this).val(stock_qty);
    }
    else if(given_qty > max_provide_qty)
    {
      $(this).val(max_provide_qty);
    }
    
    var set=$(this).data('set');
    var policy_id=$(this).parent().prev().find('.policy_id').val();
        // selected_bonus[policy_id]=0;
    $("."+policy_set_class).each(function(ind,val){
      var product_id=$(this).parent().prev().prev().find('.policy_bonus_product_id').val();
      selected_bonus[policy_id][set][product_id]=$(this).val();
    });
});
$("body").on("click",".btn_set",function(e){
    e.preventDefault();
    var set=$(this).data('set');
    var policy_id=$(this).data('policy_id');
    var prev_selected=selected_set[policy_id];
    if(set!=prev_selected)
    {
      $(".btn_set[data-set='"+set+"'][data-policy_id='"+policy_id+"']").addClass('btn-success');
      $(".btn_set[data-set='"+set+"'][data-policy_id='"+policy_id+"']").removeClass('btn-default');

      $(".btn_set[data-set='"+prev_selected+"'][data-policy_id='"+policy_id+"']").addClass('btn-default');
      $(".btn_set[data-set='"+prev_selected+"'][data-policy_id='"+policy_id+"']").removeClass('btn-success');

      $(".bonus_policy_id_"+policy_id+".set_"+set).removeClass('display_none');
      $(".bonus_policy_id_"+policy_id+".set_"+set+" :input:not(:checkbox)").prop('disabled',false);
      $(".bonus_policy_id_"+policy_id+".set_"+prev_selected).addClass('display_none');
      $(".bonus_policy_id_"+policy_id+".set_"+prev_selected+" :input:not(:checkbox)").prop('disabled',true);
      selected_set[policy_id]=set;
    }
});
$("body").on("click",".btn_type",function(e){
    e.preventDefault();
    var type=$(this).data('type');
    var policy_id=$(this).data('policy_id');
    var prev_selected=selected_policy_type[policy_id];
    if(type!=prev_selected)
    {
      $(".btn_type[data-type='"+type+"'][data-policy_id='"+policy_id+"']").addClass('btn-primary');
      $(".btn_type[data-type='"+type+"'][data-policy_id='"+policy_id+"']").removeClass('btn-basic');

      $(".btn_type[data-type='"+prev_selected+"'][data-policy_id='"+policy_id+"']").addClass('btn-basic');
      $(".btn_type[data-type='"+prev_selected+"'][data-policy_id='"+policy_id+"']").removeClass('btn-primary');
      selected_policy_type[policy_id]=type;
      $(".min_qty:last").trigger('keyup');
    }
});
</script>

<script>

    $(document).ready(function ()
    {
        
           $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

		});
        
        $('body').on('click', '.delete_item', function (){
            var product_box = $(this).parent().parent();
            var product_field = product_box.find("th:nth-child(2) .product_id");
            var product_rate = product_box.find("th:nth-child(4) .product_rate");
            var combined_product = product_box.find("th:nth-child(5) .combined_product");
            var product_qty = product_box.find("th:nth-child(6) .min_qty");
            combined_product = combined_product.val();
            var id = product_field.val();

            var total_value = $('.tvalue-'+id).val();
            var gross_total = $('#gross_value').val();
            var new_gross_value = parseFloat(gross_total-total_value);
            $('#gross_value').val(new_gross_value);
            $('#cash_collection').val('');
            alert('Removed this row -------');
            var min_qty = product_qty.val();
            if (product_field.val() == '')
            {
                product_box.remove();

                var last_row = $('.invoice_table>tbody tr:last').attr('id');
                $('#'+last_row+'>th>.add_more').show();
                
                total_values();
            } 
            else
            {
              product_box.remove();
              if(combined_product)
              {
                $.each(combined_product.split(','),function(index , value){
                  if(value!=product_field.val())
                  {
                    var prate = $(".prate-" + value);
                    prate.parent().parent().find("th:nth-child(5) .combined_product").val('');
                    prate.parent().parent().find("th:nth-child(5) .min_qty").trigger('keyup');
                  }
                });
              }
              var last_row = $('.invoice_table>tbody tr:last').attr('id');
              $('#'+last_row+'>th>.add_more').show();
              total_values();
            }
        });

        /*--------------------------------*/
        $("body").on("keyup", "#cash_collection", function () {
            var net_payable = parseFloat($("#net_payable").val());
            var collect_cash = parseFloat($(this).val());
            var credit_amount = net_payable - collect_cash;
            if (credit_amount >= 0) 
            {
                $("#credit_amount").val(credit_amount.toFixed(2));
            } 
            else 
            {
                $("#credit_amount").val(0);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('body').on("keyup", ".order_no", function () {
            var order_no = $('.order_no').val();
            var sale_type = $('#sale_type_id').val();

            delay(function () {
                $.ajax({
                    url: '<?php echo BASE_URL . 'admin/Orders/order_no_validation' ?>',
                    'type': 'POST',
                    data: {order_no: order_no, sale_type: sale_type},
                    success: function (result) {
                        obj = jQuery.parseJSON(result);
                        /*if(obj == 1){
                         alert('Order Number Already Exist');
                         $('.submit').prop('disabled', true);
                         }*/
                        if (obj == 0) {
                            $('.submit_btn').prop('disabled', false);
                        } else {
                            alert('Order Number Already Exist');
                            $('.submit_btn').prop('disabled', true);
                        }
                    }
                });
            }, 100);

        });

        /********************check ************/
        $('body').on("blur", ".order_reference_no", function () {
            check_valid_order_no();
        });
    });
    
    
    function check_valid_order_no()
    {
        
        var order_reference_no = $('.order_reference_no').val();
            var office_id = $('#office_id').val();

            if (order_reference_no != "" || order_reference_no != " ")
            {
                /*delay(function () {*/
                    $.ajax({
                        url: '<?php echo BASE_URL . 'distOrders/order_reference_no_validation' ?>',
                        'type': 'POST',
                        data: {order_reference_no: order_reference_no, office_id: office_id},
                        success: function (result) {
                            obj = jQuery.parseJSON(result);
                            if (obj == 0) {
                                $('.submit_btn').prop('disabled', false);
                            } else {
                                alert('Order Number Already Exist');
                                $('.submit_btn').prop('disabled', true);
                            }
                        }
                    });
                /*}, 100);*/
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
                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 open_bonus_product_unit_id"/>\
                    <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0" />\
                    <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 open_bonus_product_price_id" value=""/>\
                </th>\
                <th class="text-center" width="12%">\
                    <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 open_bonus_min_qty" required />\
                    <input type="hidden" class="combined_product"/>\
                    <input type="hidden" name="data[OrderDetail][discount_amount][]"/>\
                    <input type="hidden" name="data[OrderDetail][disccount_type][]"/>\
                    <input type="hidden" name="data[OrderDetail][policy_type][]"/>\
                    <input type="hidden" name="data[OrderDetail][policy_id][]"/>\
                    <input type="hidden" name="data[OrderDetail][is_bonus][]" value="1"/>\
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
                $("#DistOrderProductId").focus();
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
            var distributor_id = $("#distributor_id").val();
           
            $.ajax({
                url: '<?= BASE_URL . 'dist_Orders/get_bonus_product_details' ?>',
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
<?php if ($dist == 1) { ?>
        /*For Csa Order Need Two Extra field (csa and thana) : Start*/
        $(document).ready(function () {
            $(".office_id").change(function () {
               // console.log($(this).val());
                var office_id = $(this).val();
                //get_territory_list(office_id);
                get_dist_by_office_id(office_id);
                get_last_order_info();
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
                        //alert("here");
                        var distributor_id = $("#distributor_id").val();
                        var sr_id = $("#sr_id").val();
                        var office_id = $("#office_id").val();
                        var order_date = $("#order_date").val();
                        
                        if(order_date!="" && sr_id)
                        {
                            $.ajax({
                                url: '<?= BASE_URL . 'distOrders/get_route_list_from_order_date' ?>',
                                data: {'distributor_id': distributor_id,'sr_id': sr_id,'office_id': office_id,'order_date': order_date},
                                type: 'POST',
                                success: function (data)
                                {
                                    $("#dist_route_id").html(data);
                                    $("#dist_route_id1").html(data);
                                    $("#dist_route_id2").html(data);
                                }
                            });
                        }
                        else if(order_date=="")
                        {
                        alert("Please enter Order Date");
                         $("#sr_id option[value='']").attr('selected', true);
                        }

                        $('.outlet_id').html('<option value="">---- Select Outlet ----');
                    }

            function get_dist_by_office_id(office_id)
            {
                var order_date = $("#order_date").val();
                if(order_date!="")
                {
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id' ?>',
                    data: {'office_id': office_id,'order_date':order_date},
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
                  alert("Please enter Order Data");
                  // var data="<option value=''>---- Select Office ----</option>";
                  // $("#office_id").html(data);
                }
            }

            function get_sr_list_by_distributor_id(distributor_id)
            {
                var order_date = $("#order_date").val();
                if(order_date!="")
                {
                 $.ajax({
                    url: '<?= BASE_URL . 'DistOrderDeliveries/get_sr_list_by_distributot_id' ?>',
                    data: {'distributor_id': distributor_id,'order_date':order_date},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#sr_id").html(data);
                    }
                });
                }
                else 
                {
                     alert("Please enter Order Date");
                     $("#sr_id option[value='']").attr('selected', true);
                }

            }

            function get_thana_by_territory_id(territory_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrders/get_thana_by_territory_id' ?>',
                    data: {'territory_id': territory_id},
                    type: 'POST',
                    success: function (data)
                    {
                        // console.log(data);
                        $("#thana_id").html(data);
                        $("#thana_id1").html(data);
                        $("#thana_id2").html(data);
                    }
                });
            }
            function get_market_by_thana_id(thana_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'DistOrders/get_market_by_thana_id' ?>',
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
                    if ($('#myModal1').is(':visible')) 
                    {
                        event.preventDefault();
                        
                        $("#market_model_btn").trigger("click");
                    }  
                    else if ($('#myModal2').is(':visible')) 
                    {
                        event.preventDefault();
                        
                        $("#outlet_model_btn").trigger("click");
                    } 
                    else
                    {
                        var error_count=0;
                        error_count=check_duplicate_products();

                        var open_bonus_error=0;
                        open_bonus_error=check_duplicate_open_bonus_products();

                        if(error_count>0)
                        {
                            event.preventDefault();
                            alert("Duplicate product item has been selected");
                        }
                        else if(open_bonus_error>0)
                        {
                            event.preventDefault();
                            alert("Empty Open Bonus Product has been selected");
                        }
                        else 
                        {
                            event.preventDefault();
                            $(".draft").trigger("click");
                        }
                    }
                       break;
                       
                       case 'b':
                               event.preventDefault();
                                $(".open_bonus_product_id").focus();
                                $(".show_bonus").trigger("click");
                       break;
                       
                       case 'c':
                                event.preventDefault();
                                $("#model_close_btn").trigger("click");
                       break;

                       case 'm':
                                event.preventDefault();
                                $(".make_market").trigger("click");
                                // $(".dist_markets").trigger("focus");
                                
                       break;

                       case 'o':
                                event.preventDefault();
                                $(".make_outlet").trigger("click");
                                $("#DistOutletName").trigger("focus");
                       break;
                       case 'p':
                                event.preventDefault();
                                $(".new_row_number").last().find('a.add_more').trigger("click");
                                //$("#DistOutletName").trigger("focus");
                       break;

                       case 'l':
                                event.preventDefault();
                                // $("#last_Order").trigger("click");
                                $('#last_Order').modal('toggle');
                                //$("#DistOutletName").trigger("focus");
                       break;
                       case 'f':
                                 event.preventDefault();
                                 $(".bonus_row").last().find('a.bonus_add_more').trigger("click");
                       break;
                    }
                }
            });
            
        /************************* Test *********************************/
        /*$(document).keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                console.log('You pressed a "enter" key in somewhere');
                event.preventDefault();
                $('.product_id').eq(0).focus();
                var sl = $('.invoice_table>tbody>tr').length;
                var i = 0;
                console.log(sl);
                $('.product_id').each(function ()
                {   
                    i++;
                    if($(this).val() == ""){
                        $('.product_id').eq(i-1).focus();
                        return false;
                    }
                });    
            }
        });*/
        /************************* Test *********************************/
            
          /******  Submit data on CTRL+S event End ************/  
          
          /******  Normal form submission start ************/
          
           $("form#DistOrderAdminCreateOrderForm").submit(function(e){
                       var error_count=0;
                       error_count=check_duplicate_products();
                        
                        if(error_count>0)
                            {
                                e.preventDefault();
                                alert("Duplicate product item has been selected");
                            }
                            
           });
          
          /******  Normal form submission end ************/
          
          $('body').on('shown.bs.modal', '#bonus_product', function () {
                $('select:visible:enabled:first', this).focus();
            });
            
            /*
            
          $("body").on("focusout", ".open_bonus_min_qty", function () {
               var enable_click=1;
              $('.open_bonus_product_id').each(function ()
                            {
                                var cur_val=this.value;
                                if(cur_val>0)
                                {
                                    
                                }
                                else 
                                {
                                    enable_click=0;
                                }
                                
                            });
              
                if(enable_click==1)
                {
                     $("a.bonus_add_more").trigger("click");
                }
        });
          
          */
            
        });
        /*For Csa Order Need Two Extra field (csa and thana) : End*/
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
           
            var product_list = $('#order_product_list').html();
            var product_box = $('.product_row_box').html('');
            // var current_row = '<th class="text-center sl_Order" width="5%"></th><th id="order_product_list" class="text-center">' + product_list + '</th><th class="text-center" width="12%"><input type="text" name="" class="form-control width_100 product_unit_name" disabled/><input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate readonly" readonly/><input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/></th><th><input type="number"  name="data[OrderDetail][sales_qty][]" step="any" value="" class="form-control width_100 min_qty sales_qty_validation_check" required/><input type="hidden" class="combined_product"/></th><th class="text-center" width="12%"><input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/></th><th class="text-center" width="10%"><input type="text" id="bonus" class="form-control width_100 bonus" disabled /><input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/><input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/><input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/></th><th class="text-center" width="10%">';
            var current_row = '<th class="text-center sl_Order" width="5%">1</th>\
            <th id="order_product_list" class="text-center">' + product_list + '</th>\
            <th class="text-center" width="12%">\
                <input type="text" name="" class="form-control width_100 product_unit_name" disabled/>\
                <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="form-control width_100 product_unit_id"/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][Price][]" class="form-control width_100 product_rate" readonly/>\
                <input type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100 product_price_id"/>\
            </th>\
            <th>\
                <input type="number"  name="data[OrderDetail][sales_qty][]" step="any" class="form-control width_100 min_qty sales_qty_validation_check" value="" required/>\
                <input type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value=""/>\
                <input type="hidden" class="combined_product"/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][total_price][]" class="form-control width_100 total_value" readonly/>\
            </th>\
            <th class="text-center" width="12%">\
                <input type="text" name="data[OrderDetail][discount_value][]" class="form-control width_100 discount_value" readonly />\
                <input type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" readonly />\
                <input type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type"/>\
                <input type="hidden" name="data[OrderDetail][policy_type][]" class="form-control width_100 policy_type"/>\
                <input type="hidden" name="data[OrderDetail][policy_id][]" class="form-control width_100 policy_id"/>\
                <input type="hidden" name="data[OrderDetail][is_bonus][]" class="form-control width_100 is_bonus" value="0"/>\
            </th>\
            <th class="text-center" width="10%">\
                <input type="text" id="bonus" class="form-control width_100 bonus" disabled />\
                <input type="hidden" id="bonus_product_id" name="data[OrderDetail][bonus_product_id][]" class="form-control width_100 bonus_product_id"/>\
                <input type="hidden" id="bonus_product_qty" name="data[OrderDetail][bonus_product_qty][]" class="form-control width_100 bonus_product_qty"/>\
                <input type="hidden" id="bonus_measurement_unit_id" name="data[OrderDetail][bonus_measurement_unit_id][]" class="form-control width_100 bonus_measurement_unit_id"/>\
            </th>\
            <th class="text-center" width="10%">\
            ';
            var current_row_btn_part='<a class="btn btn-primary btn-xs add_more"><i class="glyphicon glyphicon-plus"></i></a> <a class="btn btn-danger btn-xs delete_item"><i class="glyphicon glyphicon-remove"></i></a></th>';
            var current_row_btn_hide_part='<a class="btn btn-primary btn-xs add_more" style="display:none"><i class="glyphicon glyphicon-plus"></i></a></th>';
            var rn;
            
            for(rn=1;rn<=no_of_product;rn++)
            {
                if(rn==no_of_product)
                {
                    product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_part +'</tr>');
                   $('#' + rn + '>.sl_Order').text(rn);
                }
                else 
                {
                     product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_part +'</tr>');
                   // product_box.append('<tr id=' + rn + ' class=new_row_number>' + current_row + current_row_btn_hide_part + '</tr>');
                    $('#' + rn + '>.sl_Order').text(rn);
                }
                
            }
            $('.product_id').eq(0).focus();
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
    /*-------------------------- Last Order info print : START ------------------------------------*/
    get_last_order_info();
    function get_last_order_info()
    {
        var office_id=$(".office_id").val();
        if(office_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_last_order_info' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                        
                        $("#last_Order .modal-body").html(data);
                }
            });
        }
        else
        {
            $("#last_Order .modal-body").html("<p>No Data Availabe. Please Select Office First.</p>");
        }
    }
    /*-------------------------- Last Order info print : END --------------------------------------*/

/*---------- Add funciont for policy ------------*/
function setBonusDiscount(policy_id, policy_type){
    //alert(policy_id);
    //alert(policy_type);
    
    if(policy_type==1){
        //for btn selected
        $('.policy_info_'+policy_id+' .btn_1').addClass('btn-primary');
        $('.policy_info_'+policy_id+' .btn_1').removeClass('btn-default');
        $('.policy_info_'+policy_id+' .btn_0').addClass('btn-default');
        $('.policy_info_'+policy_id+' .btn_0').removeClass('btn-primary');
        
        //for bonus product enable
        $('.bonus_policy_id_'+policy_id).show();
        $('.bonus_policy_id_'+policy_id+' select').prop('disabled', false);
        $('.bonus_policy_id_'+policy_id+' input').prop('disabled', false);
        
        
        $('.set_policy_type'+policy_id).val(1);
        
        
        var discount_value = 0;
        $('.discount_amount_p_'+policy_id).each(function () {
            if ($(this).val() > 0) {
                discount_value = parseFloat($(this).val()) + (discount_value);
            }
        });
        
        //alert(discount_value);
        
        gross_value = $('#gross_value').val();
        total_discount = $('#total_discount').val();
        n_total_discount = (total_discount)-(discount_value);
        if(n_total_discount<0){
        n_total_discount=0; 
        }
        $('#total_discount').val(n_total_discount.toFixed(2));
        net_payable = (gross_value)-(n_total_discount);
        $('#net_payable').val(net_payable.toFixed(2));
        
        $('#cash_collection').val(net_payable.toFixed(2));
        $('.discount_amount_p_'+policy_id).val(0);

        $('.main_discount_amount_p_'+ policy_id).val(0);
        
    }else{
        
        //for btn selected
        $('.policy_info_'+policy_id+' .btn_0').addClass('btn-primary');
        $('.policy_info_'+policy_id+' .btn_0').removeClass('btn-default');
        $('.policy_info_'+policy_id+' .btn_1').addClass('btn-default');
        $('.policy_info_'+policy_id+' .btn_1').removeClass('btn-primary');
        
        //for bonus product disabled
        $('.bonus_policy_id_'+policy_id).hide();
        $('.bonus_policy_id_'+policy_id+' select').prop('disabled', true);
        $('.bonus_policy_id_'+policy_id+' input').prop('disabled', true);
        
        $('.set_policy_type'+policy_id).val(0);
        
        $( ".min_qty" ).keyup();
    }
}
function get_bonus_product_info(option_id, policy_id)
{
    var product_id= $('.bonus_policy_id_'+policy_id+' .policy_bonus_product_id').val();
    var territory_id = $('.territory_id').val();
    
    $.ajax({
        url: '<?= BASE_URL . 'memos/get_bonus_product_info' ?>',
        'type': 'POST',
        data: {product_id: product_id, territory_id:territory_id, option_id:option_id},
        beforeSend: function() {$('.m_loading').show();},
        success: function (result) 
        {
            $('.m_loading').hide();
            var data = $.parseJSON(result);                  
            
            $('.bonus_policy_id_'+policy_id+' .open_bonus_product_unit_name').val(data.measurement_unit_name);
            $('.bonus_policy_id_'+policy_id+' .open_bonus_min_qty').val(data.bonus_qty);
            $('.bonus_policy_id_'+policy_id+' .open_bonus_product_unit_id').val(data.measurement_unit_id);
            
        },
    });
}
function checkBonusProductQty(option_id, bonus_qty){
    
    delay(function()
    {
        
        p_t_qty = 0;
        total_row=0;
        $(".open_bonus_option_id_"+option_id).each(function(){
            p_t_qty+=parseFloat($(this).val());
            total_row++;
        });
        
        t_qty = parseFloat($('.bonus_option_id_'+option_id+':first').val());
        
        if(p_t_qty>t_qty){
            alert('Maximum total quantity is '+t_qty);
            $('.open_bonus_option_id_'+option_id).val(t_qty/total_row);
        }
            
    }, 1000 );
}
$("body").on('click','.remove_policy_bonus',function(){
    $(this).parent().parent().remove();
});

/*---------- Add funciont for policy ------------*/
</script>
