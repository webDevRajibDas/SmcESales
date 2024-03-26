<?php 
//pr($outlets[0]['DistOutletImage'][0]['image_url']);die();

?>
<style>
body {font-family: Arial, Helvetica, sans-serif;}

.img-responsive:hover{
 cursor : pointer;
}
.img {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

.img:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css">
<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('SR Outlet Details'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>	
            <?php $outlet=$outlets;?>
            <div class="box-body">                
                <table id="Outlet" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong>Name </strong></td>
                            <td><?php echo $outlet[0]['DistOutlet']['name']; ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong>Owner Name </strong></td>
                            <td><?php echo $outlet[0]['DistOutlet']['ownar_name']; ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong>Address </strong></td>
                            <td><?php echo $outlet[0]['DistOutlet']['address']; ?></td>
                        </tr>

                        <tr>		
                            <td><strong>Mobile </strong></td>
                            <td><?php echo $outlet[0]['DistOutlet']['mobile']; ?></td>
                        </tr>

                        <tr>		
                            <td><strong>Office </strong></td>
                            <td><?php echo $outlet[0]['Office']['office_name']; ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong>Territory </strong></td>
                            <td><?php echo $outlet[0]['Territory']['name']; ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong>Thana </strong></td>
                            <td><?php echo $outlet[0]['Thana']['name']; ?></td>
                        </tr>
                        
                        <tr>		
                            <td><strong>Route/Beat </strong></td>
                            <td><?php echo $outlet[0]['DistRoute']['name']; ?></td>
                        </tr>

                        <tr>		
                            <td><strong>Distributor Market </strong></td>
                            <td><?php echo $outlet[0]['DistMarket']['name']; ?></td>
                        </tr>
                        <tr>		
                            <td><strong>Outlet Type </strong></td>
                            <td><?php echo $outlet[0]['OutletCategory']['category_name']; ?></td>
                        </tr>
                        <tr>
                          <td><strong>Bonus Type</strong></td>
                          <td>
                            <?php
                            
                            if($outlet[0]['DistOutlet']['bonus_type_id'] == 1){
                              echo h('Small Bonus');
                            }elseif ($outlet[0]['DistOutlet']['bonus_type_id'] == 2) {
                              echo h('Big Bonus');
                            }else{
                              echo h('Not Applicable');
                            } 
                          ?>
                          </td>
                        </tr>
                        <tr>
                            <td><strong>Images </strong></td>
                            <td><?php foreach ($outlet[0]['DistOutletImage'] as $key => $value) { ?>
                                <a data-lightbox="roadtrip" href="<?php echo $value['image_url'];?>">
                                    <img src="<?php echo $value['image_url'];?>" alt="test" style="width:100px;height:100px;" id="<?php echo $key+1;?>" data-toggle="modal" data-target="#myModal">
                                </a>
                                <?php }?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>			
        </div>		
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" align="center">
                <img class="img-responsive" src="" align="center"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox-plus-jquery.min.js"></script>
<script>
$(document).ready(function () {
    /*$('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });*/
});
</script>

<script>
    lightbox.option({
      'resizeDuration': 200,
      'wrapAround': true
    })
</script>