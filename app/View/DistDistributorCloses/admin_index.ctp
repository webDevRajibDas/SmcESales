<?php
$office_parent_id = $this->Session->read('Office.parent_office_id');

?>
<style>
#effective_date table tr td.disabled, .datepicker table tr td.disabled {
    color: #c7c7c7;
}
#loading{
    position: absolute;
    width: auto;
    height: auto;
    text-align: center;
    top: 45%;
    left: 50%;
    display: none;
    z-index: 999;
}
#loading img{
    display: inline-block;
    height: 100px;
    width: auto;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Distributor CLose'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('controller'=>'DistDistributors','action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">      
                <?php echo $this->Form->create('DistDistributor', array('role' => 'form')); ?>
                
                <?php if($office_parent_id == 0) {?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id', 'empty' => '---- Select ----')); ?>
                </div>
                <?php }
                else{
                ?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id', 'value' => $this->UserAuth->getOfficeId())); ?>
                </div>
                <?php }?>
                <div class="form-group">
                    <?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'empty' => '---- Select ----', 'id'=>'distributor_id','options'=>'')); ?>
                </div>
                <div class="form-group" id="balsnce_div">
                    <?php echo $this->Form->input('balance', array('class' => 'form-control balance', 'id'=>'balance','readonly')); ?>
                    <?php echo $this->Form->input('stock_balance', array('class' => 'form-control stock_balance', 'id'=>'stock_balance','readonly','type'=>'hidden')); ?>
                    <?php echo $this->Form->input('pending_order', array('class' => 'form-control pending_order', 'id'=>'pending_order','readonly','type'=>'hidden')); ?>
                </div>
               <div class="form-group" id="effective_close_date">
                   <?php echo $this->Form->input('effective_date', array('type'=>'text', 'class' => 'form-control effective_date', 'id'=>'effective_date', 'required' => TRUE, 'disabled'=> false)); ?> 
               </div>
                <?php echo $this->Form->submit('Close', array('class' => 'btn btn-large btn-info','id'=>'close')); ?>
            
                <?php echo $this->Form->end(); ?>
            </div>
        </div>          
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="cross_closs" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" style="text-align: center;"><span class="label label-danger">Attention</span></h4>
      </div>
      <div class="modal-body">
        <p>You Can not Close Distributor</p>
        <b><p id="show_balance"></p></b>
        <p id="msg_balance"></p>
        <b><p id="show_stock"></p></b>
        <b><p id="pending_order"></p></b>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default modal_close" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<div class="modal" id="gifLoader"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
    $(document).ready(function () {
        
        $('#openModal').hide();
        $('#effective_close_date').hide();
        $(".office_id").change(function () {
            get_distributor_list_by_office_id();
        });
        <?php if($office_parent_id != 0){?>
            get_distributor_list_by_office_id();
        <?php }?>
        function get_distributor_list_by_office_id(){
            var office_id = $('.office_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributors/get_dsitributor_list' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        console.log(data);
                        $('.distributor_id').html(data);     
                    }
                });
            }
        }
        $(".distributor_id").change(function () {
            get_distributor_balance();
        });

        function get_distributor_balance(){
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id').val();
            $('#gifLoader').modal('show');
            $('#loading').show();
            if(distributor_id)
            {

                $.ajax({
                    url: '<?= BASE_URL . 'admin/dist_distributor_closes/get_dsitributor_balance' ?>',
                    data: {office_id:office_id,distributor_id: distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        var obj = JSON.parse(data);
                        console.log(obj);
                        var effective_date =obj.effective_date;
                        $("#balance").val(obj.balance);
                        $("#stock_balance").val(obj.stock_balance);
                        $("#pending_order").val(obj.pending_order);
                        if(obj.balance > 0){
                            $("#show_balance").text("Balance : "+obj.balance);
                            $("#msg_balance").text("Please Close the Balance First");
                            if(obj.pending_order == 1){
                                $("#pending_order").text("Requisition Is Pending");
                            }
                        }
                        if(obj.stock_balance > 0 && obj.balance == 0){
                            $("#show_balance").text("Balance : "+obj.balance);
                            $("#show_stock").text("Stock is not Empty");
                            if(obj.pending_order == 1){
                                $("#pending_order").text("Requisition Is Pending");
                            }
                        }
                        if(obj.stock_balance == 0 && obj.balance == 0){
                            $("#show_balance").text("Balance : "+obj.balance);
                            if(obj.pending_order == 1){
                                $("#pending_order").text("Requisition Is Pending");
                            }
                            
                        }
                        
                        
                        $('#effective_close_date').show();

                        //$("#effective_date").datepicker('option', 'maxDate', effective_date);
                        $("#effective_date").datepicker({
                            dateFormat:'dd-mm-yyyy',
                            startDate: new Date(effective_date),
                            endDate: new Date(effective_date)+5,
                            format: "dd-mm-yyyy",
                            autoclose: true,
                            todayHighlight: true
                        });

                        $('#gifLoader').modal('hide');
                        $('#loading').hide();
                    }
                });
            }
        }
        $("#close").click(function(){
            if ($('#balance').val() != 0) {
                 $('#myModal').modal('show');
                return false;
            }
            if($('#stock_balance').val() != 0){
                $('#myModal').modal('show');
                return false;
            }
            if ($('#pending_order').val() != 0) {
                 $('#myModal').modal('show');
                return false;
            }
            return true;
        });
        $(".modal_close").click(function(){
            location.reload();
        });
        $("#cross_closs").click(function(){
            location.reload();
        });

    });
</script>
<script>
    /*$('.datepicker1').datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
        todayHighlight: true
    })*/;
</script>