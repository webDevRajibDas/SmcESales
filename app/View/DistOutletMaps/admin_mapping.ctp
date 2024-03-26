<?php 

?>
<style>
    .width_100{
       width:100%;
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
.sub_btn
{
    margin-left: 30%;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            
             <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Mapping Distributor to Outlet'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('controller'=>'DistDistributors','action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistOutletMaps', array('role' => 'form','action'=>'mapping','name'=>'final_mapping')); ?>
                <div class="form-group">
                   <?php                    
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id'));
                   ?>
               </div>

          
        <div class="form-group" id="territory_id_div">
            <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id', 'onChange' => 'rowUpdate(1);', 'required' => TRUE, 'empty' => '---- Select Territory ----','selected'=>$territory_id, 'options' => $territories)); ?>
        </div>

        <div class="form-group"  id="market_id_so">
            <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'class' => 'form-control market_id', 'required' => TRUE, 'empty' => '---- Select Market ----','selected'=>$market_id, 'options' => $markets)); ?>
        </div>
                
         <div class="form-group"  id="market_id_so">
            <?php echo $this->Form->input('outlet_type', array('type'=>'text','label'=>'Outlet Type','id' => 'outlet_type','class' => 'form-control outlet_type','value' => 'Distributor',"disabled")); ?>
        </div>       

               
                
                <div class="form-group"  id="outlet_id_so">
                    <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'class' => 'form-control outlet_id', 'required' => TRUE, 'empty' => '---- Select Outlet ----','selected'=>$outlet_id, 'options' => $outlets)); ?>
                </div>
                
                <?php echo $this->Form->input('re_url', array('type' => 'hidden', 'class' => 'form-control re_url', 'value' => $re_url)); ?>
                 <?php echo $this->Form->input('dist_distributor_id', array('type' => 'hidden', 'class' => 'form-control dist_distributor_id', 'value' => $dist_distributor_id)); ?>
                 <?php
                   if($id)
                   echo $this->Form->input('id', array('type' => 'hidden', 'class' => 'form-control mapped_id', 'value' => $id)); 
                   ?>
  
                <div class="form-group">
                    <?php echo $this->Form->submit('Save & Submit', array('class' => 'submit_btn btn btn-large btn-primary save sub_btn', 'div'=>false, 'name'=>'save')); ?>                  
                </div>

            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>


</div>




<div class="modal" id="myModal"></div>
<div id="loading">
    <?php echo $this->Html->image('load.gif'); ?>
</div>


<script>
    $(document).ready(function(){
       $("#office_id").prop("selectedIndex", 0);
       <?php 
       if(!$id)
       {
       ?>
       $("#territory_id").prop("selectedIndex", 0);
       $("#market_id").prop("selectedIndex", 0);
       $("#outlet_id").prop("selectedIndex", 0);
        <?php 
       }
       ?>
});
</script>




<script>

function rowUpdate(productLit){
	sl = 1;		
	//$("#territory_id").prop("selectedIndex", 0);
}

$(document).ready(function () 
{
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list_memo'?>',
        type: 'post',
        data: {'office_id': 'office_id'}
    });


        $('.territory_id').selectChain({
            target: $('.market_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOutletMaps/get_market'; ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });

        $('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOutletMaps/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });

        $('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');           
        });

        $('.territory_id').change(function () {
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        });

    });
</script>