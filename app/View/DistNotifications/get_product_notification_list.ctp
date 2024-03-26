<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            			
            <div class="box-body">
                
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Id</th>
                        <th>Product</th>
                        <th>Max Qty</th> 
                    </tr>
                    <?php  
                    if(empty($products))
                    {
                        
                        foreach ($con_product_info as $key => $product) {                                                                     
                        $p_id = $product['Product']['id'];
                        $p_name = $product['Product']['name'];   
                        $p_qty = $product['DistNotification']['max_qty'];  
                        $p_qty=($p_qty>0)?$p_qty:0;
                        ?>
                        <tr>		
                            <td><?php echo $p_id; ?></td>
                            <td>
                                <?php echo $p_name; ?>
                            </td>
                        
                        <?php
                        echo "<td class='text-left'>";  
                        ?>
                       
                        <?php 
                        echo $this->Form->input('max_qty', array('type' => 'text','label'=>false, 'class' => 'form-control max_qty','name'=>"data[DistNotification][max_qty][$p_id]", 'value' =>$p_qty));
                        echo "</td>";
                        echo "</tr>";
                    }
                        
                    }
                    else 
                    {   
                    foreach ($products as $key => $product) {                                                                     
                        $p_id = $product['Product']['id'];
                        $p_name = $product['Product']['name'];                       
                        ?>
                        <tr>		
                            <td><?php echo $p_id; ?></td>
                            <td>
                                <?php echo $p_name; ?>
                            </td>
                        
                        <?php
                        echo "<td class='text-left'>";  
                        ?>
                       
                        <?php 
                        echo $this->Form->input('max_qty', array('type' => 'text','label'=>false, 'class' => 'form-control max_qty','name'=>"data[DistNotification][max_qty][$p_id]", 'value' =>0));
                        echo "</td>";
                        echo "</tr>";
                    }
                    }
                                                          
                    ?>
                    
                </table>
                <?php 
                echo "<br>";
                echo $this->Form->input('office_id', array('type' => 'hidden', 'class' => 'form-control dist_office_id', 'value' => $dist_office_id));
                echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary'));      
                ?>    
            </div>			
        </div>
    </div>
</div>

<?php 
if($con_user_info)
{
    $selected_vals="";
    $user_ids=array();
    foreach ($con_user_info as $k => $v) {
        $user_ids[]=$v['DistNotificationUserMap']['user_id'];
    }
    
    //echo $selected_vals; exit;
    ?>
    <script>
       var dataarray = <?php echo json_encode($user_ids); ?>;
       //var dataarray=data.split(",");
       $("#multiselect").val(dataarray);
       $("#multiselect").multiselect("refresh");
    </script>
    <?php 
}
?>
