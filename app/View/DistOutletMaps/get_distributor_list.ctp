<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('Mapping List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php // echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> TSO List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Mapped Outlet Name</th> 
                        <th>Territory Name</th> 
                        <th class="text-center">Action</th> 
                    </tr>
                    <?php
                    foreach ($distDistributors as $key => $distDistributor) {
                                                                     
                        $dist_name = $distDistributor['DistDistributor']['name'];
                        $dist_outlet_id = $distDistributor['DistOutletMap']['outlet_id'];
                        $dist_office_id = $distDistributor['DistDistributor']['office_id'];
                        $dist_id = $distDistributor['DistDistributor']['id'];
                        
                        $dist_outlet_name="";
                        $dist_outlet_map_id="";
                        $dist_terriority_name="";
                        if($dist_outlet_id)
                        {
                        $dist_outlet_name = $outlets[$dist_outlet_id]['name'];
                        $dist_terriority_name = $outlets[$dist_outlet_id]['territory_name'];
                        $dist_outlet_map_id = $distDistributor['DistOutletMap']['id'];
                        }
                        
                        
                        ?>
                        <tr>		
                            <td><?php echo $dist_name; ?></td>
                            <td>
                                <?php echo $dist_outlet_name; ?>
                            </td>
                            
                            <td>
                                <?php echo $dist_terriority_name; ?>
                            </td>
                        
                        <?php
                        echo "<td class='text-center'>";
                        echo $this->Form->create('DistOutletMap', array('url'=>array('controller'=>'admin/DistOutletMaps','action'=>'mapping'),'role' => 'form','class'=>'form-horizontal')); 
                        echo $this->Form->input('office_id', array('type' => 'hidden', 'class' => 'form-control office_id', 'value' => $dist_office_id));
                        echo $this->Form->input('dist_distributor_id', array('type' => 'hidden', 'class' => 'form-control dist_distributor_id', 'value' => $dist_id));
                        
                        if($dist_outlet_map_id)
                        {
                           echo $this->Form->input('id', array('type' => 'hidden', 'class' => 'form-control dist_outlet_map_id', 'value' => $dist_outlet_map_id)); 
                        }
                        
                        echo $this->Form->button("<i class='glyphicon glyphicon-link'></i>", array('class' => 'btn btn-large btn-primary','title' => 'Mapped','escape' => false));
                        echo $this->Form->end();
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    
                </table>
            </div>			
        </div>
    </div>
</div>

