<div class="page page-developer page-developer-maintenance">
    <?php echo heading('Put website under maintenance', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <div class="alert alert-danger text-center">
        <p><?php echo bs_glyph('warning-sign'); ?> Putting the site <em>under maintenance</em> will <strong>disable</strong> both user and admin logins. Proceed with caution.</p>
    </div>
    <div class="text-center">
        <div class="form-group">
            <label for="admin">Maintenance Status: </label><br />
            <?php
            $data = array(
                'id' => 'maintenance',
                'value' => $maintenance ? 1 : 0,
            );
            echo form_checkbox($data, '', $maintenance);
            ?>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type = "text/javascript" >
        $(function() {
            var theBtn = $('#maintenance');
            var theForm = theBtn.parents('form');
            theBtn.on('switch-change', function() {
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('developer/maintenance/submit'); ?>',
                    data: {
                        maintenance: $(this).prop('checked') ? 1 : 0,
                    },
                    success: function(data) {
                        theForm.formAlert(data);
                    }
                });
            });
            theBtn.bootstrapSwitch('setSizeClass', 'switch-large');
            theBtn.bootstrapSwitch('setOnClass', 'success');
            theBtn.bootstrapSwitch('setOffClass', 'default');
        });
    </script>
</div>