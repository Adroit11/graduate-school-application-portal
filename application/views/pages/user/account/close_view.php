<div class="page page-user-account page-user-account-close">
    <?php echo heading('Close Account'); ?>
    <hr />
    <p class="lead">Delete your account (and all data associated with it).</p>
    <?php echo form_open('', array('role' => 'form', 'id' => 'form-user-account-close')); ?>
    <div class="alert alert-danger">
        <?php echo bs_glyph('exclamation-sign', '<strong>Warning:</strong> Closing an account means you\'ll neither be able to proceed with your application online, nor hear from us again. Only proceed if <strong>absolutely certain about this decision.</strong>'); ?>
    </div>
    <?php
    $fields = array(
        array(
            'label' => 'Password',
            'required' => true,
            'type' => 'password',
            'data' => array(
                'id' => 'password',
            ),
        ),
        array(
            'label' => 'Confirm password',
            'required' => true,
            'type' => 'password',
            'data' => array(
                'id' => 'passwordconf',
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'delete',
                'value' => 'Delete my account',
                'class' => 'btn-lg btn-danger btn-block',
            ),
        )
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theBtn = $('#delete');
            var theForm = theBtn.parents('form');
            theBtn.click(function() {
                if (!confirm('This is an irreversible process, are you sure you want to proceed?'))
                    return;
                var params = {
                    loadingText: 'Processing',
                    url: '<?php echo site_url('user/account/close/submit'); ?>',
                    data: {
                        password: $('#password').val(),
                        passwordconf: $('#passwordconf').val()
                    },
                    success: function(data) {
                        theForm.formAlert(data);
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>