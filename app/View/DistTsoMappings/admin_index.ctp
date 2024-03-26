<?php
$obj = new DistTsoMappingsController();
?>
<style>
    .errorInput
    {
            border:1px solid #ff0000;
    }
</style>    

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:0px !important">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Distributor TSO Mapping'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Mapping List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                     <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Mapping History'), array('action' => 'history'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>	
            
            
            
            
            
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistTsoMapping', array('role' => 'form','id'=>'DistTsoMapping')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => TRUE, 'empty' => '---- Select Office ----', 'options' => $offices,'selected'=>$office_id)); ?></td>

                        </tr>
                        <tr>
                            <td width="50%">
                                <?php 
                                echo $this->Form->input('dist_tso_id', array('label' => 'TSO', 'id' => 'dist_tso_id', 'class' => 'form-control dist_tso_id', 'required' => TRUE, 'empty' => '---- Select TSO ----','selected'=>$dist_tso_id,'options'=>$dist_tsos));
                                ?>
                            </td>

                        </tr>
                        
                        <tr align="center">
                            <td colspan="2">
                                <div id="show_data" style="width: 95%;">
                                    <?php if($is_data_post)
                                    {
                                    ?>
                                    
                                    

                                            <table class="table table-bordered table-striped" width="90%">
                                                        <tr>
                                                            <th>Distributor Name</th>
                                                            <th>Create date</th>
                                                            <th>Start date</th>
                                                            <th>End date</th>
                                                            <th>Action</th> 
                                                            <th>Effective Date</th>
                                                        </tr>
                                                        <?php

                                                        foreach ($distDistributors as $key => $distDistributor) {
                                                            $dis = $distDistributor['DistDistributor'];
                                                            $dist_tso_mapping_info = $obj->get_tso_mapping_id($dis['office_id'],$dist_tso_id, $dis['id']);
                                                           // pr($dist_tso_mapping_info);
                                                            $tso_effective_date="";
                                                            $tso_assigned_date="";
                                                            $dist_tso_mapped_id=0;
                                                            $end_date="";

                                                            if($dist_tso_mapping_info)
                                                            {
                                                                $curr_dist_tso_id=$dist_tso_id;
                                                                $checked_tso_id=$dist_tso_mapping_info['dist_tso_id'];
                                                                if($curr_dist_tso_id!=$checked_tso_id)
                                                                {
                                                                    continue; 
                                                                }
                                                                $tso_effective_date=date("d-m-Y",strtotime($dist_tso_mapping_info['effective_date']));
                                                                $tso_assigned_date=date("d-m-Y",strtotime($dist_tso_mapping_info['created_at']));
                                                                $dist_tso_mapped_id=$dist_tso_mapping_info['id'];

                                                            }

                                                            if(!($dist_tso_mapped_id > 0))
                                                            {
                                                               $dist_tso_history = $obj->get_mapping_end_date($dis['office_id'], $dist_tso_id, $dis['id']); 
                                                              // pr($dist_tso_history); 
                                                               if(!empty($dist_tso_history))
                                                               {
                                                                    $end_date=date("d-m-Y",strtotime($dist_tso_history['end_date']));
                                                               }

                                                            }

                                                           $dist_id=$dis['id'];
                                                            ?>
                                                            <tr>		
                                                                <td><?php echo $dis['name']; ?></td>
                                                                <td><?php echo $tso_assigned_date; ?></td>
                                                                <td><?php echo $tso_effective_date; ?></td>
                                                                <td><?php echo $end_date; ?></td>
                                                                <td>
                                                                    <input value="<?= $dist_id ?>" id="dist_id_<?= $dist_id ?>" class="check_dist" style="margin:0px 5px 0px 0px;" type="checkbox" selected  name="data[DistDistributor][id][<?php echo $dist_id;?>]"
                                                                           <?php
                                                                           echo ($dist_tso_mapped_id > 0) ? 'checked' : '';
                                                                           ?> />
                                                                </td>
                                                                <td>
                                                                 <?php echo $this->Form->input('effective_date', array('type'=>'text','id' => "effective_date_$dist_id", 'label' => false, 'value' =>$tso_effective_date, 'name' => "data[DistDistributor][effective_date][$dist_id]", 'class' => 'form-control datepicker action_date', 'style' => 'width:70%','autocomplete'=>'off'));

                                                                 if($dist_tso_mapping_info)
                                                                 { 
                                                                    ?>
                                                                    <input type="hidden" value="<?= $dist_tso_mapping_info['dist_distributor_id'] ?>" name="data[DistTsoMapping][dist_distributor_id][<?= $dist_id ?>][dist_distributor_id]" />
                                                                    <input type="hidden"  id="pre_effective_id_<?= $dist_id ?>"  value="<?= date("d-m-Y",strtotime($dist_tso_mapping_info['effective_date'])); ?>" name="data[DistTsoMapping][dist_distributor_id][<?= $dist_id ?>][effective_date]" />
                                                                    <input type="hidden" value="<?= $dist_tso_mapping_info['created_at'] ?>" name="data[DistTsoMapping][dist_distributor_id][<?= $dist_id ?>][assign_date]" />
                                                                    <input type="hidden" value="<?= $dist_tso_mapping_info['dist_tso_id'] ?>" name="data[DistTsoMapping][dist_distributor_id][<?= $dist_id ?>][dist_tso_id]" />
                                                                    <input type="hidden"  id="eff_effective_date_<?= $dist_id ?>"  value="<?= date("d-m-Y",strtotime($dist_tso_mapping_info['effective_date'])); ?>" name="data[DistTsoMapping][dist_distributor_id][<?= $dist_id ?>][eff_effective_date]" />
                                                                    <?php 
                                                                 }
                                                                 else 
                                                                {
                                                                    ?>
                                                                   <input type="hidden" id="end_effective_date_<?= $dist_id ?>"   value="<?= $end_date; ?>" name="data[DistTsoMapping][end_eff_date][<?= $dist_id ?>]" />
                                                                   <?php 
                                                                }
                                                                 ?>                                                                
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php 
                                                        if(is_array($distDistributors) && !empty($distDistributors))
                                                        {
                                                           echo $this->Form->input('office_id', array('type' => 'hidden', 'class' => 'form-control office_id', 'value' => $distDistributors[0]['DistDistributor']['office_id']));
                                                        }
                                                        ?>
                                                    </table>

                                    

                                    
                                    
                                    
                                    
                                    
                                    <?php 
                                    }
                                    ?>
                                    
                                </div>
                                </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Submit', array('id' => 'btn_id','type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                            </td>
                        </tr>
                        
                    </table>
                    
                    
                     <br><br>
                    <?php echo $this->Form->end(); ?>
                </div>
                
                	
            </div>			
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        catch_content = null;
        $("body").on("change", "#dist_tso_id", function () {
            var dist_tso_id = $(this).val();
             var office_id = $("#office_id").val();
             
            if (dist_tso_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>DistTsoMappings/get_distributor_list',
                    type: 'POST',
                    data: {dist_tso_id: dist_tso_id,office_id:office_id},
                    success: function (response) {
                        $('#show_data').html(response);
                        catch_content = response;
                    }
                });
            } else {
                $('#show_data').html("");
            }
        });
        
        
        
        $('body').on('change', '#office_id', function () {
            var office_id = $(this).val();
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>dist_tsos/get_tso_info',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        if (result.length != 0) {
                             var options = "<option value=''>------ Please Select ------</option>";
                            //var options='';
                            for (var x in result) {
                                options += '<option value=' + '"' + x + '">' + result[x] + '</option>'
                            }
                            $('#dist_tso_id').html(options);
                             $('#show_data').html("");
                        } else {
                            var options = "<option value=''>------ Please Select ------</option>";
                             $('#dist_tso_id').html(options);
                             $('#show_data').html("");
                        }
                    }
                });
            }

        });
        
        
         $("input[type='submit']").on("click", function(e){
   	        $("div#divLoading_default").addClass('hide');
		});
                
                
                
        $("form#DistTsoMapping").submit(function(e){
              
               $('.action_date').removeClass("errorInput");  
               var error_count=0;
               
                $('.action_date').each(function () {
                    var curr_action_id=this.id;
                    var curr_action_date=this.value;
                    
                    
                    if(curr_action_date)
                    {
                        var end_id="end_"+curr_action_id;
                        var eff_id="eff_"+curr_action_id;
                        
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
                
        $(document).on("click", "#btn_id", function () {
            $('form').find('input').each(function () {
                if (!$(this).prop('required')) {
                   // $("#divLoading_default.show").attr("position", "none");
                } else {
                    //$("#divLoading_default.show").attr("position", "fixed");
                }
            });
        });
        
        $(document).on("click", ".check_dist", function () {
            var getId = $(this).attr('id');
            var effective_date_id = getId.replace("dist_id_", "effective_date_");
            var pre_effective_date_id = getId.replace("dist_id_", "pre_effective_id_");
            
            if ($(this).is(':checked')) {
                $("#" + effective_date_id).attr("required", "required");
            } else {
                var date_val=$("#"+pre_effective_date_id).val();
                //alert("id:"+pre_effective_date_id);
                //alert(date_val);
                
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
        
        
    });
    
$('.datepicker').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true
  });  
</script>
