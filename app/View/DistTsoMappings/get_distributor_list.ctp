<?php
$obj = new DistTsoMappingsController();
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
                        $dist_tso_mapping_info = $obj->get_tso_mapping_id($dis['office_id'], $this->request->data('dist_tso_id'), $dis['id']);
                       // pr($dist_tso_mapping_info);
                        $tso_effective_date="";
                        $tso_assigned_date="";
                        $dist_tso_mapped_id=0;
                        $end_date="";
                        
                        if($dist_tso_mapping_info)
                        {
                            $curr_dist_tso_id=$this->request->data('dist_tso_id');
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
                           $dist_tso_history = $obj->get_mapping_end_date($dis['office_id'], $this->request->data('dist_tso_id'), $dis['id']); 
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
                             <?php echo $this->Form->input('effective_date', array('id' => "effective_date_$dist_id", 'label' => false, 'value' =>$tso_effective_date, 'name' => "data[DistDistributor][effective_date][$dist_id]", 'class' => 'form-control datepicker action_date', 'style' => 'width:70%','autocomplete'=>'off'));
                                   
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
                                <input type="hidden"  id="end_effective_date_<?= $dist_id ?>"   value="<?= $end_date; ?>" name="data[DistTsoMapping][end_eff_date][<?= $dist_id ?>]" />
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

<script>
$('.datepicker').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true
  });  
</script>
