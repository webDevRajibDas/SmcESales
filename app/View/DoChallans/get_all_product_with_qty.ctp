
<tbody>
<?php $rowcount=1; foreach($do_product_list as $data) {
    
    if($data['RequisitionDetail']['virtual_product_name_show'] > 0){
        $data['Product']['id'] = $data['VirtualProduct']['id'];
        $data['Product']['name'] = $data['VirtualProduct']['name'];
    }
    
    ?>
    <tr class="table_row" id="rowCount<?=$rowcount;?>">
        <td align="center"><?=$rowcount;?></td>
        <td>
            <input type="hidden" name="selected_product_id[]" class="selected_product_id" value=""/><?=$data['Product']['name']?>
            <input type="hidden" name="product_id[]" value="<?=$data['Product']['id']?>"/>
        </td>
        <td align="center">
            <select name="batch_no[]" class=" batch_no_do" id="product_<?=$data['Product']['id']?>" required></select>
            <div></div>
        </td>
        <td align="center">
            <select  name="expire_date[]" class="expire_date_do" id="expire_date_product_<?=$data['Product']['id']?>" required style="width:120px;">
                <option value="">--- Select ----</option>
            </select>
            <div></div>
        </td>
        <td align="center"><?=$data['MeasurementUnit']['name']?>
            <input type="hidden" name="measurement_unit[]" value="<?=$data['MeasurementUnit']['id']?>">
        </td>
        <!-- <td align="center">'+obj.ProductPrice.general_price+'</td> -->
        <td align="center">
            <input type="text" name="quantity[]" class="p_quantity" value="<?=$data['RequisitionDetail']['remaining_qty'];?>">
        </td>
        <td align="center">
            <input type="text" class="full_width form-control" name="remarks[]" placeholder="Remarks Here">
        </td>
        <td align="center">
            <button class="btn btn-danger btn-xs remove" value="<?=$rowcount++;?>"><i class="fa fa-times"></i></button>
        </td>
    </tr>
<?php }?>
</tbody>
