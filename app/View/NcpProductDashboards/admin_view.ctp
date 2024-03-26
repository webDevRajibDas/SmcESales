<div class='row'>
    <div class='col-xs-12'>
        <div class='box box-primary'>
            <div class='box-header'>
                <h3 class='box-title'><i
                            class='glyphicon glyphicon-eye-open'></i> <?php echo __('NCP product type Details '); ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> NCP Product Dashboard'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>

            <div class="box-body">
                <?php
                
                
                $twoDimensionalArray = [];
                foreach ($data_arrays as $firstDimension) {
                    foreach ($firstDimension as $secondDimension) {
                        $twoDimensionalArray[] = $secondDimension;
                    }
                }
                
                    $ncpType = null;
                    $currentOffice = null;
                    $territoryName = null;
                    
                    echo '<table class="table table-bordered">';
                    echo '<tr><th>NCP Type</th><th>Area Office</th><th>Territory</th><th>Product Name</th> <th>Product Qty</th></tr>';
                    
                    for ($i = 0; $i < count($twoDimensionalArray); $i++) {
                        $row = $twoDimensionalArray[$i];
//                        echo '<pre>';
//                        print_r($row);
//                        echo '</pre>';
                        
                        if ($row['NcpType'] !== $ncpType) {
                            $ncpType = $row['NcpType'];
                            $areaRowspan = countRows($twoDimensionalArray, 'NcpType', $ncpType, $i);
                            echo "<tr><td rowspan='$areaRowspan'>$ncpType</td>";
                            $currentOffice = null;
                            $territoryName = null;
                        }
                        
                        if ($row['AreaOffice'] !== $currentOffice) {
                            $currentOffice = $row['AreaOffice'];
                            $officeRowspan = countRows($twoDimensionalArray, 'AreaOffice', $currentOffice, $i);
                            echo "<td rowspan='$officeRowspan'>$currentOffice</td>";
                            $territoryName = null;
                        }
                        
                        if ($row['teritorryName'] !== $territoryName) {
                            $territoryName = $row['teritorryName'];
                            $soNameRowspan = countRows($twoDimensionalArray, 'teritorryName', $territoryName, $i);
                            echo "<td rowspan='$soNameRowspan'>$territoryName</td>";
                        }
                        
                        echo "<td>{$row['ProductName']}</td>";
                        echo "<td>{$row['totalProductQty']}</td>";
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    
                    
        
                function countRows($array, $column, $value, $startIndex){
                    $count = 0;
                    for ($i = $startIndex; $i < count($array); $i++) {
                        if ($array[$i][$column] === $value) {
                            $count++;
                        } else {
                            break;
                        }
                    }
                    return $count;
                }
                ?>

            </div>

            <div class="box-body">
            </div>
        </div>

    </div>
</div>


<script>

</script>



