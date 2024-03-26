<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Distributor'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributor', array('role' => 'form')); ?>       
                
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                <!------------add new field to input db_code-------------->
                <div class="form-group">
                    <?php echo $this->Form->input('db_code', array('class' => 'form-control')); ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                </div>		
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {

        $('.district_id').selectChain({
            target: $('.thana_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/territories/get_thana'; ?>',
            type: 'post',
            data: {'district_id': 'district_id'}
        });

        var rowCount = 1;
        $(".add_more").click(function () {

            var district = $(".district_id option:selected").text();
            var thana = $(".thana_id option:selected").text();
            var thana_id = $('.thana_id').val();
            var selected_stock_array = $(".selected_thana_id").map(function () {
                return $(this).val();
            }).get();
            var stock_check = $.inArray(thana_id, selected_stock_array) != -1;

            if (thana_id == '')
            {
                alert('Please select thana.');
                return false;
            } else if (stock_check == true) {
                alert('This thana already added.');
                $('.district_id').val('');
                $('.thana_id').val('');
                return false;
            } else
            {
                rowCount++;
                var recRow = '<tr class="table_row" id="rowCount' + rowCount + '"><td colspan="2" style="padding:5px 0;"><input type="hidden" name="thana_id[]" class="selected_thana_id" value="' + thana_id + '"/>' + thana + '</td><td><button class="btn btn-danger btn-xs remove" value="' + rowCount + '"><i class="fa fa-times"></i></button></td></tr>';
                $('.thana_table').append(recRow);
                $('.district_id').val('');
                $('.thana_id').val('');
            }
        });

        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });

    });
</script>