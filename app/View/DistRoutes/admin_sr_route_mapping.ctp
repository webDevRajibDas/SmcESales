<?php
$obj = new DistRoutesController();
?>
<style>
    .errorInput
    {
            border:1px solid #ff0000;
    }
</style> 

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Sales Representatives and Route/Beat Mapping'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Representatives List'), array('controller'=>'DistSalesRepresentatives','action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistRoute', array('role' => 'form','id'=>'distSrRouteMapping')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'default' => $office_id, 'disabled' => TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor', 'class' => 'form-control','options'=>$distDistributors, 'default' => $dist_distributor_id, 'disabled' => TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_sr_id', array('label' => 'Sales Representatives', 'class' => 'form-control','options'=>$distSalesRepresentatives, 'default' => $dist_sr_id, 'disabled' => TRUE)); ?>
                </div>
                <?php echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'default' => $office_id, 'type' => 'hidden')); ?>
                <?php echo $this->Form->input('dist_distributor_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'default' => $dist_distributor_id, 'type' => 'hidden')); ?>
                <?php echo $this->Form->input('dist_sr_id', array('label' => 'Sales Representatives', 'class' => 'form-control', 'empty' => '---- Select ----', 'default' => $dist_sr_id, 'type' => 'hidden')); ?>
                <br><br>
                <table  class="table table-bordered table-striped">
                    <tr>
                        <th width="150" class="text-center"><?php echo __('Route/Beat Name'); ?></th>
                        <th class="text-center"><?php echo __('Create date'); ?></th>
                        <th class="text-center"><?php echo __('Start date'); ?></th>
                        <th class="text-center"><?php echo __('End date'); ?></th>
                        <th class="text-center"><?php echo __('Action'); ?></th>
                        <th width="160" class="text-center"><?php echo __('Effective Date'); ?></th>
                    </tr>
                    <?php
                    //pr($distRoutes);
                    foreach ($distRoutes as $key => $value) {
                        $dis_route_id = $value['DistRoute']['id'];
                        $create_at="";
                        $start_at="";
                        $end_at="";
                        ?>
                        <tr>
                            <td class="text-center"><?= $value['DistRoute']['name'] ?></td>
                            <td class="text-center">
                                <?php
                                if (array_key_exists($dis_route_id, $mappingData)) {
                                    //echo $mappingData[$value['DistRoute']['id']]['created_at'];
                                    $create_at=date("d-m-Y",strtotime($mappingData[$value['DistRoute']['id']]['created_at']));
                                    echo $create_at;
                                    $start_at=date("d-m-Y",strtotime($mappingData[$value['DistRoute']['id']]['effective_date']));
                                    ?>
                                    <input type="hidden" value="<?= $mappingData[$value['DistRoute']['id']]['dist_route_id'] ?>" name="data[DistSrRouteMapping][dist_route_id][<?= $dis_route_id ?>][dist_route_id]" />
                                    <input type="hidden" id="pre_effective_id_<?= $dis_route_id ?>"   value="<?= $mappingData[$value['DistRoute']['id']]['effective_date'] ?>" name="data[DistSrRouteMapping][dist_route_id][<?= $dis_route_id ?>][effective_date]" />
                                    <input type="hidden" value="<?= $mappingData[$value['DistRoute']['id']]['created_at'] ?>" name="data[DistSrRouteMapping][dist_route_id][<?= $dis_route_id ?>][assign_date]" />
                                    <input type="hidden" id="pre_effective_date_<?= $dis_route_id ?>"  value="<?= $mappingData[$value['DistRoute']['id']]['effective_date'] ?>" name="data[DistSrRouteMapping][pre_date][<?= $dis_route_id ?>][effective_date]" />   
                                <?php
                                }
                                else {
                                    
                                    $dist_beat_history = $obj->get_sr_mapping_end_date($office_id, $dist_distributor_id,$dis_route_id,$dist_sr_id); 
                                    
                                    if(!empty($dist_beat_history) && $dist_beat_history['end_date'])
                                                               {
                                                                    $end_at=date("d-m-Y",strtotime($dist_beat_history['end_date']));
                                                               }
                                                               ?>
                                  <input type="hidden" id="end_effective_date_<?= $dis_route_id ?>"  value="<?=$end_at?>" name="data[DistSrRouteMapping][end_date][<?= $dis_route_id ?>][effective_date]" />
                                <?php 
                                
                                                               }
                                ?>
                            </td>
                            <td class="text-center"><?php echo $start_at; ?></td>
                            <td class="text-center"><?php echo $end_at; ?></td>
                            <td class="text-center">
                                <input value="<?= $dis_route_id ?>" id="route_id_<?= $dis_route_id ?>" class="route" style="margin:0px 5px 0px 0px;" 
                                <?php
                                if (array_key_exists($dis_route_id, $mappingData)) {
                                    echo 'checked';
                                }
                                ?> 
                                       type="checkbox" selected  name="data[DistRoute][id][<?= $dis_route_id ?>]" />
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_date', array('id' => "effective_date_$dis_route_id", 'label' => false, 'value' => (array_key_exists($dis_route_id, $mappingData)) ? $mappingData[$value['DistRoute']['id']]['effective_date'] : '', 'name' => "data[DistRoute][effective_date][$dis_route_id]", 'class' => 'form-control datepicker action_date', 'style' => 'width:100%'));
                                    ?>
                                </div>
                            </td>
                        </tr> 
                        <?php
                    }
                    ?>
                </table>
                <br>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary', 'id' => 'btn_id')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<?php 
$startDate = date('d-m-Y', strtotime('0 day'));
?>
<script>
/*Challan Datepicker : Start*/
/*
    $(document).ready(function () {
            var today = new Date(new Date().setDate(new Date().getDate()));
            $('.effective_datepicker').datepicker({
                    startDate: '<?php echo $startDate; ?>',
                    format: "dd-mm-yyyy",
                    autoclose: true,
                    todayHighlight: true,
            });
    });
*/
/*Challan Datepicker : End*/

    $(document).ready(function () {
        
          $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

		});

 
         
        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });
        $(document).on("click", "#btn_id", function () {
            $('form').find('input').each(function () {
                if (!$(this).prop('required')) {
                   // $("#divLoading_default.show").attr("position", "none");
                } else {
                    //$("#divLoading_default.show").attr("position", "fixed");
                }
            });
        });
        $(document).on("click", ".route", function () {
            var getId = $(this).attr('id');
            var effective_date_id = getId.replace("route_id_", "effective_date_");
            var pre_effective_date_id = getId.replace("route_id_", "pre_effective_id_");
            
            
            
            if ($(this).is(':checked')) {
                $("#" + effective_date_id).attr("required", "required");
            } else {
                //$("#" + effective_date_id).removeAttr("required");
                
                var date_val=$("#"+pre_effective_date_id).val();
                if(date_val)
                {
                     $("#" + effective_date_id).val("");
                     $("#" + effective_date_id).attr("required", "required");
                }
                else
                {    
                $("#" + effective_date_id).removeAttr("required");
                }
            }
        });
        $("input[type='checkbox']").iCheck('destroy');
        
        
        $("form#distSrRouteMapping").submit(function(e){
              
               $('.action_date').removeClass("errorInput");  
               var error_count=0;
               
                $('.action_date').each(function () {
                    var curr_action_id=this.id;
                    var curr_action_date=this.value;
                    
                    
                    if(curr_action_date)
                    {
                        var end_id="end_"+curr_action_id;
                        var eff_id="pre_"+curr_action_id;
                        
                        if (document.getElementById(end_id)) {
                            // input date should be greater than the last end date
                            var last_end_date=$("#"+end_id).val();
                           
                            if(last_end_date && curr_action_date)
                            {
                                var last_end_date_v1=last_end_date.split('-');
                                var new_last_end_date = new Date(last_end_date_v1[2],last_end_date_v1[1],last_end_date_v1[0]);
                                
                                var curr_action_date_v1=curr_action_date.split('-');
                                var new_curr_action_date = new Date(curr_action_date_v1[2],curr_action_date_v1[1],curr_action_date_v1[0]);
                                
                            }
                            
                            
                            if(last_end_date && curr_action_date && (new_last_end_date >= new_curr_action_date))
                            {
                               //alert("Effective date should be greater then "+last_end_date); 
                               error_count++;
                               $("#"+curr_action_id).addClass('errorInput');
                            }
                            // if invalid then show alert and marked the box 
                        }
                        else if(document.getElementById(eff_id))
                        {
                            // input date should be greater or equal than eff date
                           var last_eff_date=$("#"+eff_id).val();
                            // alert(last_eff_date);
                           if(last_eff_date && curr_action_date)
                            {
                                var last_eff_date_v1=last_eff_date.split('-');
                                var new_last_eff_date = new Date(last_eff_date_v1[2],last_eff_date_v1[1],last_eff_date_v1[0]);
                                
                                var curr_action_date_v1=curr_action_date.split('-');
                                var new_curr_action_date = new Date(curr_action_date_v1[2],curr_action_date_v1[1],curr_action_date_v1[0]);
                                
                            }
                            
                            if(last_eff_date && curr_action_date && ( new_last_eff_date>new_curr_action_date))
                            {
                               //alert("Effective date should be greater then "+last_eff_date); 
                               error_count++;
                               $("#"+curr_action_id).addClass('errorInput');
                            } 
                            // if invalid then show alert and marked the box 
                        }
                    }
                    
                });
               
               if(error_count>0)
               {
                   e.preventDefault();
               }
               else 
               {
                         var isValidForm = 1;
                        $(this.form).find(':input[required]:visible').each(function() {
                          if (!this.value.trim()) {
                            isValidForm = 0;
                            $("#"+this.id).addClass('errorInput');
                          }
                        });
                        
                        if(isValidForm!=1)
                        {
                            e.preventDefault();
                        }
                            
               }
                
               
           });
           
    });
</script>