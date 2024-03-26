<div class="row">
    <div class="col-xs-12">		
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php echo __('DistDistributor'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>			
            <div class="box-body">
                <table id="Territories" class="table table-bordered table-striped">
                    <tbody>
                        <tr>		
                            <td><strong><?php echo __('Id'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistDistributor']['id']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Name'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistDistributor']['name']); ?>
                                &nbsp;
                            </td>
                        </tr><tr>		
                            <td><strong><?php echo __('Office'); ?></strong></td>
                            <td>
                                <?php echo $this->Html->link($territory['Office']['id'], array('controller' => 'offices', 'action' => 'view', $territory['Office']['id']), array('class' => '')); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Address'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistDistributor']['address']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>		
                            <td><strong><?php echo __('Mobile Number'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistDistributor']['mobile_number']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        
                        <tr>		
                            <td><strong><?php echo __('Is Active'); ?></strong></td>
                            <td>
                                <?php echo h($territory['DistDistributor']['is_active']); ?>
                                &nbsp;
                            </td>
                        </tr>					
                    </tbody>
                </table>
            </div>			
        </div>


        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Related Distributor Markets'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> ' . __('New Distributor Market'), array('controller' => 'DistMarkets', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
            </div>
            <?php if (!empty($territory['DistMarket'])): ?>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo __('Id'); ?></th>
                                <th class="text-center"><?php echo __('Code'); ?></th>
                                <th class="text-center"><?php echo __('Name'); ?></th>
                                <th class="text-center"><?php echo __('Address'); ?></th>
                                <th class="text-center"><?php echo __('Location Type Id'); ?></th>
                                <th class="text-center"><?php echo __('Thana Id'); ?></th>
                                <th class="text-center"><?php echo __('Distributor Id'); ?></th>
                                <th class="text-center"><?php echo __('Is Active'); ?></th>
                                <th class="text-center"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($territory['DistMarket'] as $market):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $market['id']; ?></td>
                                    <td class="text-center"><?php echo $market['code']; ?></td>
                                    <td class="text-center"><?php echo $market['name']; ?></td>
                                    <td class="text-center"><?php echo $market['address']; ?></td>
                                    <td class="text-center"><?php echo $market['location_type_id']; ?></td>
                                    <td class="text-center"><?php echo $market['thana_id']; ?></td>
                                    <td class="text-center"><?php echo $market['dist_distributor_id']; ?></td>
                                    <td class="text-center"><?php echo $market['is_active']; ?></td>
                                    <td class="text-center">
                                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'DistMarkets', 'action' => 'view', $market['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view')); ?>
                                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'DistMarkets', 'action' => 'edit', $market['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit')); ?>
                                        <?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'DistMarkets', 'action' => 'delete', $market['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $market['id'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table><!-- /.table table-striped table-bordered -->
                </div><!-- /.table-responsive -->

            <?php endif; ?>



        </div><!-- /.related -->


        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Related Distributor People'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> ' . __('New Territory Person'), array('controller' => 'territory_people', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>					</div><!-- /.actions -->
            </div>
            <?php if (!empty($territory['TerritoryPerson'])): ?>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo __('Id'); ?></th>
                                <th class="text-center"><?php echo __('Territory Id'); ?></th>
                                <th class="text-center"><?php echo __('Sales Person Id'); ?></th>
                                <th class="text-center"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($territory['TerritoryPerson'] as $territoryPerson):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $territoryPerson['id']; ?></td>
                                    <td class="text-center"><?php echo $territoryPerson['territory_id']; ?></td>
                                    <td class="text-center"><?php echo $territoryPerson['sales_person_id']; ?></td>
                                    <td class="text-center">
                                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'territory_people', 'action' => 'view', $territoryPerson['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'view')); ?>
                                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('controller' => 'territory_people', 'action' => 'edit', $territoryPerson['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'edit')); ?>
                                        <?php echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'territory_people', 'action' => 'delete', $territoryPerson['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', $territoryPerson['id'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table><!-- /.table table-striped table-bordered -->
                </div><!-- /.table-responsive -->

            <?php endif; ?>



        </div><!-- /.related -->


    </div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

