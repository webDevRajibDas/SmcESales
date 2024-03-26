<?php
App::import('Controller', 'ProgramsController');
$ProgramsController = new ProgramsController;
?>

<style type="text/css">
    .border,
    .border td {
        border: 1px solid black;
        white-space: nowrap;
    }
</style>
<table id="gspprogram" class="table border" border="1">
    <thead>
        <tr>

            <th class="text-center" colspan="10">
                <?php
                if ($program_type_id == 2) {
                    echo 'BSP Program List';
                }
                if ($program_type_id == 3) {
                    echo 'Pink Star Program List';
                }
                ?>
            </th>

        </tr>
        <tr>
            <th width="60" class="text-center"><?php echo h('ID'); ?></th>
            <th class="text-center"><?php echo h('Office'); ?></th>
            <th class="text-center"><?php echo h('Territory'); ?></th>
            <th class="text-center"><?php echo h('Thana'); ?></th>
            <th class="text-center"><?php echo h('Market'); ?></th>
            <th class="text-center"><?php echo h('Outlet/Clinic Name'); ?></th>
            <th class="text-center">Doctor Name</th>
            <th class="text-center"><?php echo h('Code'); ?></th>
            <th class="text-center"><?php echo h('Assigned Date'); ?></th>

            <th class="text-center"><?php echo h('Program officer '); ?></th>
            <th class="text-center"><?php echo h('Status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($programs as $program) : ?>
            <tr>
                <td class="text-center"><?php echo h($program['Program']['id']); ?></td>
                <td class="text-left"><?php echo h($program['Office']['office_name']); ?></td>
                <td class="text-left"><?php echo h($program['Territory']['name']); ?></td>

                <td class="text-left">
                    <?= $ProgramsController->get_thana_info($program['Market']['thana_id'])['Thana']['name'] ?>
                </td>

                <td class="text-left"><?php echo h($program['Market']['name']); ?></td>
                <td class="text-left"><?php echo h($program['Outlet']['name']); ?></td>
                <td class="text-left"><?php echo h($program['Doctor']['name']); ?></td>

                <td class="text-center"><?php echo h($program['Program']['code']); ?></td>
                <td class="text-center"><?php echo $this->App->dateformat($program['Program']['assigned_date']); ?></td>
                <td class="text-center"><?php echo $program['SalesPerson']['name']; ?></td>
                <td class="text-center">
                    <?php
                    if ($program['Program']['status'] == 1) {
                        echo '<span class="btn btn-success btn-xs">Assigned</span>';
                    } else if ($program['Program']['status'] == 2) {
                        echo '<span class="btn btn-danger btn-xs">De-Assigned</span>';
                    } else {
                        echo '<span class="btn btn-warning btn-xs">Not Assigned</span>';
                    }
                    ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>