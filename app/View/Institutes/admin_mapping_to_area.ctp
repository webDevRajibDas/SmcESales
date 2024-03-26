<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Mapping Institute with Area Office'); ?></h3>
                <div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Institute List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
			<?php echo $this->Form->create('Institute', array('role' => 'form')); ?>
                <div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>true,'empty'=>'---- Select Office ----')); ?>
                </div>

                <div class="form-group ">
                    <label id="ins_label"></label>                   
                </div>

                <div class="form-group ">                    
                    <div id="institute_list" style="padding-left: 230px;"></div>
                </div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<script>
    /*
     $('.office_id').selectChain({
     target: $('.territory_id'),
     value:'name',
     url: '<?= BASE_URL.'institutes/get_institute_list'?>',
     type: 'post',
     data:{'office_id': 'office_id' }
     });
     */
    $(".office_id").change(function () {
        var office_id = $(this).val();
        if (office_id)
        {
            $.ajax({
                type: "POST",
                url: '<?php echo BASE_URL;?>institutes/get_mapping_institute_list',
                data: 'office_id=' + office_id,
                cache: false,
                success: function (data) {
                    var obj = jQuery.parseJSON(data);
                    $("label#ins_label").html("Check Institute");
                     $("div#institute_list").html("");
                    $.each(obj, function (key, item) {
                        var checkBox = "<input type='checkbox' " + item.is_check + "  name='data[checked_ngo][" + item.id + "]' value='" + item.id + "'/>&nbsp;&nbsp;&nbsp;" + item.name + "<br/>";
                        $(checkBox).appendTo('div#institute_list');
                    });

                }
            });
        } else
        {
            $("label#ins_label").html("");
            $("div#institute_list").html("");
        }
    });
</script>