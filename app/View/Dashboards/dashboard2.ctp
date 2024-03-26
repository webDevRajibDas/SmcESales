<!-- <?php

if(CakeSession::read('UserAuth.User.user_group_id') == 3)
{		
?>
<script language="javascript">
    setTimeout(function () {
        window.location.reload(1);
    }, 300000);
</script>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:50px !important;">
            <div class="box-header">
                <h3 class="box-title">Data Sync History</h3>
            </div>
            <div class="box-body">
                <div class="row">
					<?php 
					foreach($data_array as $so){ 
						$color = ($so['hours'] > 24 ? 'color:red;' : '');
					?>
                    <div class="col-md-3">
                        <div class="dashboard_icon">			
                            <div style="text-align:center;font-size:30px;<?php echo $color; ?>"><i class="fa fa-user"></i></div>
                            <div style="text-align:center;"><b><?php echo $so['name']; ?></b></div>				
                            <div style="text-align:center;"><?php echo $so['territory']; ?></div>				
                            <div style="text-align:center;">Last Sync Date : <?php echo $this->App->datetimeformat($so['time']); ?></div>
                            <div style="text-align:center;">Last Memo Sync : <?php echo $this->App->datetimeformat($so['last_memo_sync']); ?></div>                               
                            <div style="text-align:center;">No. of Memos : <?php echo $so['total_sync_memo']; ?></div>
                            <div style="text-align:center;">Sync Memos : <?php echo $so['total_memo']; ?></div>
                            <div style="text-align:center;">Total Amount : <?php echo $so['total_memo_value']; ?></div>
                        </div>	
                    </div>
					<?php 
					} 
					?>
                </div>
            </div>
        </div>
    </div>	
</div>
<?php
}	
?> -->

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:50px !important;">
            <div class="box-header">
                <h3 class="box-title">Memu Shortcut</h3>
            </div>
            <div class="box-body">				
                <div class="row">
					<?php
					foreach($menu as $key=>$val)
					{
						if($this->App->menu_permission($val['controller'],$val['action'])){
					?>		
                    <div class="col-md-2">
                        <div class="dashboard_icon">
                            <a href="<?=Router::url('/admin/'.$key);?>">
                                <div style="text-align:center;font-size:30px;color:"><?php echo $val['icon']; ?></div>
                                <div style="text-align:center;"><?php echo $val['title']; ?></div>	
                            </a>
                        </div>	
                    </div>							
					<?php			
						}		
					}
					
					?>	
                </div>
            </div>			
        </div>	
    </div>
</div>
