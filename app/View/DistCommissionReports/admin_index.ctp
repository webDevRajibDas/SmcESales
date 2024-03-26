
<?php
$obj = new DistCommissionReportsController();
?>
<style>
    .search .radio label {
        width: auto;
        float:none;
        padding:0px 15px 0px 5px;
        margin:0px;
    }
    .radio, .checkbox {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
    }
    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 34%;
        display: inline-block;
        font-weight: 700;
        font-size:14px;
        border-bottom:none;
    }
    .radio input[type="radio"], .radio-inline input[type="radio"]{
        margin-left: 0px;
        position: relative;
        margin-top:8px;
    }

    #market_list .checkbox label{
        padding-left:10px;
        width:auto;
    }
    #market_list .checkbox{
        width:33%;
        float:left;
        margin:1px 0;
    }
    .submit_bt{
        margin-left:170px;
    }
</style>
<div class="box box-primary">

    <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> Distributor Commission Report</h3>
    </div>	

    <div class="box-body">
        <div class="search-box">
            <div class="row">
                <div class="col-sm-12">
                    <?php echo $this->Form->create('Memo', array('role' => 'form')); ?>					
                    <table class="search">
                        <tbody>
                            <tr>
                                <td class="required" style="width:50%">
                                    <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                                </td>
                                <td class="required">
                                    <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                                </td>						
                            </tr>
                            <tr>
                                <td><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'options' => $region_offices,)); ?></td>
                                <td><?php echo $this->Form->input('dist_area_executive_id', array('label' => 'Area Executive :', 'id' => 'dist_area_executive_id', 'class' => 'form-control', 'empty' => '---- Please Select ----')); ?></td>
                            </tr>
                            <tr>
                                <td ><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control', 'required' => false, 'empty' => '---- Please Select ----')); ?></td>
                                <td ><?php echo $this->Form->input('dist_tso_id', array('label' => 'Tso :', 'id' => 'dist_tso_id', 'class' => 'form-control', 'empty' => '---- Please Select ----')); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'required' => false, 'empty' => '---- Select Territory ----', 'options' => $territories)); ?></td>
                                <td ><?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor :', 'id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'empty' => '---- Please Select ----')); ?></td>
                            </tr>
                            <tr>
                            <td><?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control thana_id', 'empty' => '--- Select---', 'options' => '', 'label' => 'Thana','options' => $thanas));?></td>
                                <td ><?php echo $this->Form->input('product_category_id', array('id' => 'product_category_id', 'class' => 'form-control', 'empty' => '---- Please Select ----')); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => false, 'empty' => '---- Select Market ----', 'options' => $markets)); ?></td>
                                <td ><?php echo $this->Form->input('product_id', array('label' => 'Products :', 'id' => 'product_id', 'class' => 'form-control', 'empty' => '---- Please Select ----')); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <?php echo $this->Form->input('report_type', array('legend' => 'Report Type :', 'class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => array(1 => 'Details', 2 => 'Summary'), 'required' => true)); ?>
                                </td>
                            </tr>

                            <tr align="center">
                                <td colspan="2">
                                    <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                    <?php
                                    if (!empty($data)) {
                                        ?>
                                        <a class="btn btn-success" id="download_xl">Download XL</a>
                                        <?php
                                    }
                                    ?>
                                </td>				
                            </tr>
                        </tbody>
                    </table>
                    <?php echo $this->Form->end(); ?>			
                </div>
            </div>
            <br>
            <div id="content">
                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                    <h3 style="margin:2px 0;"><?= $page_title; ?></h3>
                    <p>
                        <b> Time Frame : <?= $date_from = @date('d M, Y', strtotime($this->request->data['Memo']['date_from'])) ?> to <?= $date_to = @date('d M, Y', strtotime($this->request->data['Memo']['date_to'])) ?></b>
                    </p>
                    <?php
                    $date_from = array_key_exists('Memo', $this->request->data) ? $this->request->data['Memo']['date_from'] : '';
                    $date_to = array_key_exists('Memo', $this->request->data) ? $this->request->data['Memo']['date_to'] : '';
                    ?>
                    <p>
                        <?php if (!empty($region_office_id)) { ?>
                            <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                        <?php } ?>


                        <?php if ($office_id) { ?>
                            <span><?= ($region_office_id) ? ', ' : '' ?>Area Office: <?= $offices[$office_id] ?></span>
                        <?php } ?>
                        <?php /* ?><?php if($territory_id){ ?>
                          <span>, Territory Name: <?=$territories[$territory_id]?></span>
                          <?php } ?><?php */ ?>
                    </p>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="table-responsive" style="font-size:13px">
                            <?php
                            if ($report_type == 1) {
                                ?>
                                <table width="100%" class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th><strong>#</strong></th>
                                            <th><strong>Region Office</strong></th>
                                            <th><strong>Area Office</strong></th>
                                            <th><strong>Area Executive</strong></th>
                                            <th  ><strong>TSO</strong></th>
                                            <th  ><strong>Distributor Name</strong></th>
                                            <th ><div align="center">Numer of challans</div></th>
                                            <th  ><div align="center">Product Name</div></th>
                                            <th  ><div align="center">Challan Quantity</div></th>
                                            <th  ><div align="center">TP</div></th>
                                            <th  ><div align="center">DP</div></th>
                                            <th  ><div align="center">Commissions</div></th>
                                        </tr>
                                        <?php
                                        if (!empty($data)) {
                                            $n = count($data) - 1;
                                            $distributor = $data[0][0]['distributor_name'];
                                            $i = 0;
                                            $total_number_of_challans = 0;
                                            $total_challan_qty = 0;
                                            $total_commission = 0;
                                            foreach ($data as $key => $value) {
                                                ++$i;
                                                ?>
                                                <?php
                                                if ($distributor == $value[0]['distributor_name']) {
                                                    $office_id=$value[0]['office_id'];
                                                    $parent_office_id=$value[0]['parent_office_id'];
                                                    $dist_distributor_id=$value[0]['dist_distributor_id'];
                                                    $distributor = $value[0]['distributor_name'];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php
                                                            echo $i;
                                                            ?>
                                                        </td>
                                                        <td><div align="center"><?= $office_list[$obj->get_region_office($value[0]['office_name'])] ?></div></td>
                                                        <td><div align="center"><?= $value[0]['office_name'] ?></div></td>
                                                        <td><div align="center"><?= $obj->get_area_executive_name($value[0]['office_id'], $value[0]['dist_distributor_id']); ?></div></td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                $tso_id = $obj->get_tso_name($value[0]['office_id'], $value[0]['dist_distributor_id']);
                                                                echo!empty($tso_id) ? $distTsos_list[$tso_id] : '';
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td><div align="center"><?= $value[0]['distributor_name'] ?></div></td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                echo $value[0]['number_of_challans'];
                                                                $total_number_of_challans += $value[0]['number_of_challans'];
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?= $products[$value[0]['id']] ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                $total_challan_qty += $value[0]['total_challan_qty'];
                                                                echo $value[0]['total_challan_qty'];
                                                                ?>
                                                                <?php
                                                                $commission = $obj->get_commissions($date_from, $date_to, $value[0]['price'], $value[0]['office_id'], $value[0]['dist_distributor_id'], $value[0]['id']);
                                                                $total_commission += $commission;
                                                                ?>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                echo sprintf("%01.2f", $value[0]['price']);
                                                                $tp = $value[0]['price'];
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $dp = $tp - $commission;
                                                            echo sprintf("%01.2f", $dp);
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                echo sprintf("%01.2f", $commission);
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    if ($n == $key) {
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td align="right">
                                                              <?php echo $this->Form->create('OutletCharacteristicReports', array('role' => 'form', 'action' => 'index')); ?>
                                                              <?php echo $this->Form->input('date_from', array('type' =>'hidden','label' =>false,'class' => 'form-control datepicker date_from', 'value' => $date_from)); ?>
                                                              <?php echo $this->Form->input('date_to', array('type' =>'hidden','label' =>false,'class' => 'form-control datepicker date_from', 'value' => $date_to)); ?>
                                                              <?php echo $this->Form->input('report_type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => 'detail')); ?>
                                                                <?php echo $this->Form->input('region_office_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $parent_office_id)); ?>
                                                                <?php echo $this->Form->input('office_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $office_id)); ?>
                                                                <?php echo $this->Form->input('type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => 'territory')); ?>
                                                                <?php //echo $this->Form->input('territory_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'territory_id'))); ?>
                                                                <?php echo $this->Form->input('so_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => '')); ?>
                                                                <?php echo $this->Form->input('unit_type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' =>1)); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][outlet_category_id][0]','class' => 'form-control', 'value' => 17)); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][district_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'district_id'))); ?>
                                                                <?php //echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][thana_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'thana_id'))); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][market_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'market_id'))); ?>
                                                                 <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][outlet_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'outlet_id'))); ?>   
                                                                 <?php echo $this->Form->button("<i class='glyphicon glyphicon-link'></i>", array('target'=>'_blank','class' => 'btn btn-xs btn-primary','title' => 'Mapped','escape' => false)); ?>
                                                                <?php  echo $this->Form->end();?>
                                                            </td>
                                                            <td>
                                                                <div align="center">
                                                                    <strong>
                                                                        <?php
                                                                        echo $obj->get_total_challan($office_id,$dist_distributor_id,$date_from,$date_to);
                                                                        //echo $total_number_of_challans;
                                                                        $total_number_of_challans = 0;
                                                                        ?>
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                            <td><div align="center"></div></td>
                                                            <td>
                                                                <div align="center">
                                                                    <strong>
                                                                        <?php
                                                                        echo $total_challan_qty;
                                                                        $total_challan_qty = 0;
                                                                        ?>
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                            <td><div align="center"></div></td>
                                                            <td>&nbsp;</td>
                                                            <td>
                                                                <div align="center">
                                                                    <strong>
                                                                        <?php
                                                                        echo sprintf("%01.2f", $total_commission);
                                                                        $total_commission = 0;
                                                                        ?>
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                    }
                                                    if ($distributor != $value[0]['distributor_name']) {
                                                        //$temp_office_id=
                                                        $distributor = $value[0]['distributor_name'];
                                                        ?>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>
                                                            <div align="right">
                                                              <?php echo $this->Form->create('OutletCharacteristicReports', array('role' => 'form', 'action' => 'index')); ?>
                                                              <?php echo $this->Form->input('date_from', array('type' =>'hidden','label' =>false,'class' => 'form-control datepicker date_from', 'value' => $date_from)); ?>
                                                              <?php echo $this->Form->input('date_to', array('type' =>'hidden','label' =>false,'class' => 'form-control datepicker date_from', 'value' => $date_to)); ?>
                                                              <?php echo $this->Form->input('report_type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => 'detail')); ?>
                                                                <?php echo $this->Form->input('region_office_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $parent_office_id)); ?>
                                                                <?php echo $this->Form->input('office_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $office_id)); ?>
                                                                <?php echo $this->Form->input('type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => 'territory')); ?>
                                                                <?php //echo $this->Form->input('territory_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'territory_id'))); ?>
                                                                <?php echo $this->Form->input('so_id', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' => '')); ?>
                                                                <?php echo $this->Form->input('unit_type', array('type' =>'hidden','label' =>false,'class' => 'form-control', 'value' =>1)); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][outlet_category_id][0]','class' => 'form-control', 'value' => 17)); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][district_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'district_id'))); ?>
                                                                <?php //echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][thana_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'thana_id'))); ?>
                                                                <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][market_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'market_id'))); ?>
                                                                 <?php echo $this->Form->input('', array('type' =>'hidden','label' =>false,'name'=>'data[OutletCharacteristicReports][outlet_id][0]','class' => 'form-control', 'value' => $obj->get_respective_id($office_id,$dist_distributor_id,'outlet_id'))); ?>   
                                                                 <?php echo $this->Form->button("<i class='glyphicon glyphicon-link'></i>", array('class' => 'btn btn-xs btn-primary','title' => 'Mapped','escape' => false)); ?>
                                                                <?php  echo $this->Form->end();?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <strong>
                                                                    <?php
                                                                    echo $obj->get_total_challan($office_id,$dist_distributor_id,$date_from,$date_to);
                                                                    //echo $total_number_of_challans;
                                                                    $total_number_of_challans = 0;
                                                                    ?>
                                                                </strong>
                                                            </div>
                                                        </td>
                                                        <td><div align="center"></div></td>
                                                        <td>
                                                            <div align="center">
                                                                <strong>
                                                                    <?php
                                                                    echo $total_challan_qty;
                                                                    $total_challan_qty = 0;
                                                                    ?>
                                                                </strong>
                                                            </div>
                                                        </td>
                                                        <td><div align="center"></div></td>
                                                        <td>&nbsp;</td>
                                                        <td>
                                                            <div align="center">
                                                                <strong>
                                                                    <?php
                                                                    echo sprintf("%01.2f", $total_commission);
                                                                    $total_commission = 0;
                                                                    ?>
                                                                </strong>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?=$i?></td>
                                                        <td><div align="center"><?= $office_list[$obj->get_region_office($value[0]['office_name'])] ?></div></td>
                                                        <td><div align="center"><?= $value[0]['office_name'] ?></div></td>
                                                        <td><div align="center"><?= $obj->get_area_executive_name($value[0]['office_id'], $value[0]['dist_distributor_id']); ?> </div></td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                $tso_id = $obj->get_tso_name($value[0]['office_id'], $value[0]['dist_distributor_id']);
                                                                echo!empty($tso_id) ? $distTsos_list[$tso_id] : '';
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td><div align="center"><?= $value[0]['distributor_name'] ?></div></td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                 //echo $obj->get_total_challan($value[0]['office_id'], $value[0]['dist_distributor_id'],$date_from,$date_to);
                                                                echo $value[0]['number_of_challans'];
                                                                $total_number_of_challans += $value[0]['number_of_challans'];
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td><div align="center"><?= $products[$value[0]['id']] ?></div></td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                $total_challan_qty += $value[0]['total_challan_qty'];
                                                                echo $value[0]['total_challan_qty'];
                                                                ?>

                                                                <?php
                                                                $commission = $obj->get_commissions($date_from, $date_to, $value[0]['price'], $value[0]['office_id'], $value[0]['dist_distributor_id'], $value[0]['id']);
                                                                $total_commission += $commission;
                                                                ?>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                echo sprintf("%01.2f", $value[0]['price']);
                                                                $tp = $value[0]['price'];
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $dp = $tp - $commission;
                                                            echo sprintf("%01.2f", $dp);
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <div align="center">
                                                                <?php
                                                                echo sprintf("%01.2f", $commission);
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            }
                                        }else{
                                            ?>
                                            <tr>
                                                <td colspan="12"><div style="margin-left:auto;margin-right:auto;width:90px;color:red"><strong>No data found!</strong></div></td>
                                            </tr>       
                                             <?php
                                             } 
                                            ?>
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                if (!empty($data)) {
                                    ?>
                                    <table width="100%" class="table table-bordered" style="text-align: center;">
                                        <tbody>
                                            <tr>
                                                <th  ><strong>#</strong></th>
                                                <th  ><strong>Region Office</strong></th>
                                                <th  ><strong>Area Office</strong></th>
                                                <th  ><strong>Area Executive</strong></th>
                                                <th  ><strong>TSO</strong></th>
                                                <th  ><strong>Distributor Name</strong></th>
                                                <th ><div align="center">Numer of challans</div></th>
                                                <th  ><div align="center">TP</div></th>
                                                <th  ><div align="center">DP</div></th>
                                                <th  ><div align="center">Commissions</div></th>
                                            </tr>
                                            <?php
                                            //pr($sanitize_array);die();
                                            $number_of_challans = $number_of_challans_ae=$number_of_challans_office=$number_of_challans_region=0;
                                            $tp = $tp_ae=$tp_office=$tp_region=0;
                                            $dp =$dp_ae=$dp_office=$dp_region=0;
                                            $commissions =$commissions_ae=$commissions_office=$commissions_region=0;
                                            $i = 0;
                                            $tso_id = $obj->get_tso_name($data[0][0]['office_id'], $data[0][0]['dist_distributor_id']);
                                            $area_excutive_id = $obj->get_area_executive_id($data[0][0]['office_id'], $data[0][0]['dist_distributor_id']);
                                            $office_id = $data[0][0]['office_id'];
                                            $parent_office_id = $data[0][0]['parent_office_id'];
                                            foreach ($sanitize_array as $key1 => $value1) {
                                                foreach ($value1 as $key2 => $value2) {
                                                    foreach ($value2 as $key => $value) {
                                                        ?>
                                                        <?php
                                                        if ($tso_id == $key2) {
                                                            ++$i;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    echo $i;
                                                                    ?> 
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $office_list[$obj->get_region_office($value['office_name'])];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $office_list[$key1];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $obj->get_area_executive_name($key1, $key);
                                                                    // echo 'Executive';
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo!empty($key2) ? $distTsos_list[$key2] : '';
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $distDistributors_list[$key];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $get_number_of_challans=$obj->get_total_challan($key1,$key,$date_from,$date_to);
                                                                    echo $get_number_of_challans;
                                                                    $number_of_challans += $get_number_of_challans;
                                                                    $number_of_challans_ae += $get_number_of_challans;
                                                                    $number_of_challans_office += $get_number_of_challans;
                                                                    $number_of_challans_region += $get_number_of_challans;
                                                                    ?>
                                                                </td>
                                                                <td>

                                                                    <?php
                                                                    echo number_format($value['tp'], 2);
                                                                    $tp += $value['tp'];
                                                                    $tp_ae += $value['tp'];
                                                                    $tp_office += $value['tp'];
                                                                    $tp_region += $value['tp'];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo number_format($value['dp'], 2);
                                                                    $dp += $value['dp'];
                                                                    $dp_ae += $value['dp'];
                                                                    $dp_office += $value['dp'];
                                                                    $dp_region += $value['dp'];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $value['commissions'];
                                                                    $commissions += $value['commissions'];
                                                                    $commissions_ae += $value['commissions'];
                                                                    $commissions_office += $value['commissions'];
                                                                    $commissions_region += $value['commissions'];
                                                                    ?>

                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($tso_id != $key2) {
                                                            $temp_tso_id = $tso_id;
                                                            $tso_id = $key2;
                                                            ?>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <div align="right">
                                                                        <strong>Total of <?php echo!empty($temp_tso_id) ? $distTsos_list[$temp_tso_id] : ''; ?> :</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo $number_of_challans;
                                                                            $number_of_challans = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <?php
                                                                        echo sprintf("%01.2f", $tp);
                                                                        $tp = 0;
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $dp);
                                                                            $dp = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $commissions);
                                                                            $commissions = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        $area_ex_id = $obj->get_area_executive_id($key1, $key);
                                                        if ($area_excutive_id != $area_ex_id) {
                                                            $temp_area_ex_id = $area_excutive_id;
                                                            $area_excutive_id = $area_ex_id;
                                                            ?>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <div align="right">
                                                                        <strong>Total of <?php echo!empty($temp_area_ex_id) ? $dist_ex_list[$temp_area_ex_id] : ''; ?> :</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo $number_of_challans_ae;
                                                                            $number_of_challans_ae = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <?php
                                                                        echo sprintf("%01.2f", $tp_ae);
                                                                        $tp_ae = 0;
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $dp_ae);
                                                                            $dp_ae = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $commissions_ae);
                                                                            $commissions_ae = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                            
                                                        <?php
                                                        if ($office_id != $key1) {
                                                            $temp_office_id = $office_id;
                                                            $office_id = $key1;
                                                            ?>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <div align="right">
                                                                        <strong>Total of <?php echo!empty($temp_office_id) ? $offices_list[$temp_office_id] : ''; ?> :</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo $number_of_challans_office;
                                                                            $number_of_challans_office = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <?php
                                                                        echo sprintf("%01.2f", $tp_office);
                                                                        $tp_office = 0;
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                             echo sprintf("%01.2f", $dp_office);
                                                                            $dp_office = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $commissions_office);
                                                                            $commissions_office = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                            
                                                        <?php
                                                        if ($parent_office_id != $value['parent_office_id']) {
                                                            $temp_parent_office_id = $parent_office_id;
                                                            $parent_office_id = $value['parent_office_id'];
                                                            ?>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <div align="right">
                                                                        <strong>Total of <?php echo!empty($temp_parent_office_id) ? $offices_list[$temp_parent_office_id] : ''; ?> :</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo $number_of_challans_region;
                                                                            $number_of_challans_region = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <?php
                                                                        echo sprintf("%01.2f", $tp_region);
                                                                        $tp_region = 0;
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $dp_region);
                                                                            $dp_region = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="center">
                                                                        <strong>
                                                                            <?php
                                                                            echo sprintf("%01.2f", $commissions_region);
                                                                            $commissions_region = 0;
                                                                            ?>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                            
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>

                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><div align="right"><strong>Total of <?php echo!empty($key2) ? $distTsos_list[$key2] : ''; ?> :</strong></div></td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo $number_of_challans;
                                                            $number_of_challans = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <?php
                                                        echo sprintf("%01.2f", $tp);
                                                        $tp = 0;
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $dp);
                                                            $dp = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><div align="center">
                                                        <strong>
                                                            <?php
                                                             echo sprintf("%01.2f", $commissions);
                                                            $commissions = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><div align="right"><strong>Total of <?php echo!empty($area_ex_id) ? $dist_ex_list[$area_ex_id] : ''; ?> :</strong></div></td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo $number_of_challans_ae;
                                                            $number_of_challans_ae = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <?php
                                                        echo sprintf("%01.2f", $tp_ae);
                                                        $tp_ae = 0;
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $dp_ae);
                                                            $dp_ae = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $commissions_ae);
                                                            $commissions_ae = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr> 
                                            
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><div align="right"><strong>Total of <?php echo!empty($key1) ? $offices_list[$key1] : ''; ?> :</strong></div></td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo $number_of_challans_office;
                                                            $number_of_challans_office = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <?php
                                                        echo sprintf("%01.2f", $tp_office);
                                                        $tp_office = 0;
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $dp_office);
                                                            $dp_office = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $commissions_office);
                                                            $commissions_office = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>     
                                            
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><div align="right"><strong>Total of <?php echo!empty($parent_office_id) ? $offices_list[$parent_office_id] : ''; ?> :</strong></div></td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo $number_of_challans_region;
                                                            $number_of_challans_region = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <?php
                                                        echo sprintf("%01.2f", $tp_region);
                                                        $tp_region = 0;
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $dp_region);
                                                            $dp_region = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><div align="center">
                                                        <strong>
                                                            <?php
                                                            echo sprintf("%01.2f", $commissions_region);
                                                            $commissions_region = 0;
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>       
                                        
                                        </tbody>
                                    </table>
                                    <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td colspan="12"><div style="margin-left:auto;margin-right:auto;width:90px;color:red"><strong>No data found!</strong></div></td>
                                    </tr>       
                                     <?php
                                     } 
                                            
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
//$(input[type='checkbox']).iCheck(false); 
        $(document).ready(function () {
            $("input[type='checkbox']").iCheck('destroy');
            $("input[type='radio']").iCheck('destroy');
            $('#checkall2').click(function () {
                var checked = $(this).prop('checked');
                $('.selection2').find('input:checkbox').prop('checked', checked);
            });
            $('#checkall').click(function () {
                var checked = $(this).prop('checked');
                $('.selection').find('input:checkbox').prop('checked', checked);
            });
        });
    </script>



</div>

<script>
    function get_area_executive(office_id,market_id,territory_id,thana_id) {
        if (office_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>DistCommissionReports/get_area_executive',
                'type': 'POST',
                data: {office_id: office_id,market_id:market_id,territory_id:territory_id,thana_id:thana_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    $('#dist_area_executive_id option').remove();
                    $('#dist_area_executive_id').append('<option value="">---- Please Select ----</option>');
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].DistAreaExecutive.id + '">' + obj[i].DistAreaExecutive.name + '</option>';
                        $('#dist_area_executive_id').append(optionList);
                    }
                }
            });

        } else {
            $('#dist_distributor_id').html('');
            $('#dist_tso').html('');
        }
    }
    function get_tso(office_id, dist_area_executive_id) {
        if (office_id != '' && dist_area_executive_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>DistCommissionReports/get_tso',
                'type': 'POST',
                data: {office_id: office_id, dist_area_executive_id: dist_area_executive_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    $('#dist_tso_id option').remove();
                    if (obj.length != 0) {
                        $('#dist_tso_id').append('<option value="">---- Please Select ----</option>');
                    }
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].DistTso.id + '">' + obj[i].DistTso.name + '</option>';
                        $('#dist_tso_id').append(optionList);
                    }
                }
            });
        } else {
            $('#dist_tso_id').html('');
        }
    }
    /*function get_distributor(office_id, dist_tso_id) {
        if (office_id != '' && dist_tso_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>DistCommissionReports/get_distributor',
                'type': 'POST',
                data: {office_id: office_id, dist_tso_id: dist_tso_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    $('#dist_distributor_id option').remove();
                    if (obj.length != 0) {
                        $('#dist_distributor_id').append('<option value="">---- Please Select ----</option>');
                    }
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].DistDistributor.id + '">' + obj[i].DistDistributor.name + '</option>';
                        $('#dist_distributor_id').append(optionList);
                    }
                }
            });
        } else {
            $('#dist_distributor_id').html('');
        }
    }*/
    function  get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,tag) {
        if(tag ==='ae'){
           var selector='#dist_area_executive_id'; 
        }else if(tag ==='tso'){
          var selector='#dist_tso_id';  
        }else{
           var selector='#dist_distributor_id'; 
        }
        
        if(tag ==='tso' && dist_area_executive_id == ''){
          $('#dist_tso_id option').remove(); 
          $('#dist_distributor_id option').remove();
          return false;
        }
        
        if(tag ==='dist' && dist_tso_id == ''){
          $('#dist_distributor_id option').remove();
          return false;
        }
        
        if (office_id != '' && territory_id != '' && thana_id != '' && market_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>DistCommissionReports/get_ae_tso_dist',
                'type': 'POST',
                data: {office_id: office_id,
                        territory_id: territory_id,
                        thana_id: thana_id,
                        market_id: market_id,
                        dist_area_executive_id: dist_area_executive_id,
                        dist_tso_id: dist_tso_id,
                        tag: tag              
                    },
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    console.log(response);
                    $(selector+' option').remove();
                    if (obj.length != 0) {
                        $(selector).append('<option value="">---- Please Select ----</option>');
                    }
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].id + '">' + obj[i].name + '</option>';
                        $(selector).append(optionList);
                    }
                }
            });
        } else {
            $('#dist_area_executive_id').html('');
            $('#dist_tso_id').html('');
            $('#dist_distributor_id').html('');
        }
    }
    function get_products(product_category_id) {
        if (product_category_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>DistCommissionReports/get_products',
                'type': 'POST',
                data: {product_category_id: product_category_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    $('#product_id option').remove();
                    if (obj.length != 0) {
                        $('#product_id').append('<option value="">---- Please Select ----</option>');
                    }
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].Product.id + '">' + obj[i].Product.name + '</option>';
                        $('#product_id').append(optionList);
                    }
                }
            });
        } else {
            $('#dist_tso_id').html('');
        }
    }
    function get_territories(office_id) {
        if (office_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>sales_people/get_territory_list',
                'type': 'POST',
                data: {office_id: office_id},
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    $('.territory_id option').remove();
                    for (var i = 0; i < obj.length; i++) {
                        var optionList = '<option value="' + obj[i].id + '">' + obj[i].name + '</option>';
                        $('.territory_id').append(optionList);
                    }
                }
            });

        } else {
            $('#territory_id').html('');
            $('#thana_id').html('');
            $('#market_id').html('');
        }
    }
    function get_thanas(territory_id) {
        if (territory_id != '') {
            $.ajax({
                url: '<?= BASE_URL ?>memos/get_thana_by_territory_id',
                'type': 'POST',
                data: 'territory_id=' + territory_id,
                success: function (response) {
                    //var obj = jQuery.parseJSON(response);
                    //console.log(obj);
                    $('.thana_id option').remove();
                    $('.thana_id').html(response);
                }
            });

        } else {
            $('#thana_id').html('');
            $('#market_id').html('');
        }
    }
    
    $('body').on("change", "#office_id", function () {
        var office_id = $(this).val();
        var territory_id = $('#territory_id').val();
        var thana_id = $('#thana_id').val();
        var market_id = $('#market_id').val();
        var dist_area_executive_id = $('#dist_area_executive_id').val()
        var dist_tso_id = $('#dist_tso_id').val()
        get_territories(office_id);
        get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,'false');
        //get_area_executive(office_id);
        //get_distributor(office_id, dist_tso_id);
    });
    $('body').on("change", ".territory_id", function () {
        var territory_id = $(this).val();
        var office_id = $('#office_id').val();
        var thana_id = $('#thana_id').val();
        var market_id = $('#market_id').val();
        var dist_area_executive_id = $('#dist_area_executive_id').val()
        var dist_tso_id = $('#dist_tso_id').val()
        get_thanas(territory_id);
        get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,'false');
    });
    $('body').on("change", "#market_id", function () {
        var office_id = $('#office_id').val();
        var territory_id = $('#territory_id').val();
        var thana_id = $('#thana_id').val();
        var dist_area_executive_id = $('#dist_area_executive_id').val()
        var dist_tso_id = $('#dist_tso_id').val()
        var market_id = $(this).val();
        get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,'ae');
    });
    $('body').on("change", "#product_category_id", function () {
        var product_category_id = $(this).val();
        get_products(product_category_id);
    });

    $('body').on("change", "#dist_area_executive_id", function () {
        var office_id = $('#office_id').val();
        var territory_id = $('#territory_id').val();
        var thana_id = $('#thana_id').val();
        var dist_area_executive_id = $(this).val()
        var dist_tso_id = $('#dist_tso_id').val()
        var market_id = $('#market_id').val();
        get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,'tso');
    });
    $('body').on("change", "#dist_tso_id", function () {
        var office_id = $('#office_id').val();
        var territory_id = $('#territory_id').val();
        var thana_id = $('#thana_id').val();
        var dist_area_executive_id = $('#dist_area_executive_id').val()
        var dist_tso_id = $(this).val()
        var market_id = $('#market_id').val();
        get_ae_tso_dist(office_id,territory_id,thana_id,market_id,dist_area_executive_id,dist_tso_id,'dist');
    });
    /*$('.territory_id').selectChain({
        target: $('.market_id'),
        value: 'name',
        url: '<?= BASE_URL . 'admin/doctors/get_market'; ?>',
        type: 'post',
        data: {'territory_id': 'territory_id'}
    });
    function get_thana_list(territory_id)
    {
    $.ajax
            ({
                type: "POST",
                url: '<?= BASE_URL ?>memos/get_thana_by_territory_id',
                data: 'territory_id=' + territory_id,
                cache: false,
                success: function (response)
                {
                    $('.thana_id').html(response);
                    <?php if (isset($this->request->data['Memo']['thana_id'])) { ?>
                    $('.thana_id option[value="<?= $this->request->data['Memo']['thana_id'] ?>"]').attr("selected", true);
                    <?php } ?>
                }
            });
    }
    if ($('.territory_id').val() != '')
    {
      get_thana_list($('.territory_id').val());
    }
    $('body').on('change', '.territory_id', function () {

        get_thana_list($(this).val());
    });*/
    $('.thana_id').selectChain({
        target: $('.market_id'),
        value: 'name',
        url: '<?= BASE_URL . 'memos/market_list'; ?>',
        type: 'post',
        data: {'thana_id': 'thana_id'}
    });
</script>

<script>
    $('.region_office_id').selectChain({
        target: $('#office_id'),
        value: 'name',
        url: '<?= BASE_URL ?>market_characteristic_reports/get_office_list',
        type: 'post',
        data: {'region_office_id': 'region_office_id'}
    });
    $("#download_xl").click(function (e) {
        e.preventDefault();
        var html = $("#content").html();
        // console.log(html);
        var blob = new Blob([html], {type: 'data:application/vnd.ms-excel'});
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "distributor_commission_reports.xls";
        document.body.appendChild(a);
        a.click();
    });

</script>

