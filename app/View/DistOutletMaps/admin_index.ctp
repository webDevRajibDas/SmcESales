<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:0px !important">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor and Outlet Mapping List'); ?></h3>
                <div class="box-tools pull-right">
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistOutletMap', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control dist_outlet_map_id', 'required' => false, 'empty' => '---- Select Area Office ----')); ?></td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                    <div id="show_data"></div>
                    
                </div>		
            </div>			
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        catch_content = null;
        $("body").on("change", "#office_id", function () {
            var office_id = $(this).val();
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>DistOutletMaps/get_distributor_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (response) {
                        $('#show_data').html(response);
                        catch_content = response;
                    }
                });
            } else {
                $('#show_data').html('<option value="">---- Select Area Office ----</option>');
            }
        });
    });
</script>