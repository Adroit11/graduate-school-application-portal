<div class="page page-user-account page-user-account-home">
    <?php echo heading('General Settings'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form', 'id' => 'form-user-account-home')); ?>
    <div class="alert alert-info"><?php echo bs_glyph('exclamation-sign', 'You need to enter your <strong>current password</strong> in order to make any changes.'); ?></div>
    <?php
    echo heading('Your information', 4, 'class="text-center"');
    $fields = array(
        array(
            'label' => 'Email address',
            'required' => true,
            'type' => 'text',
            'data' => array(
                'id' => 'email',
                'value' => $email,
            ),
        ),
        array(
            'label' => 'New password',
            'type' => 'password',
            'data' => array(
                'id' => 'newpassword',
            ),
        ),
        array(
            'label' => 'Confirm new password',
            'type' => 'password',
            'data' => array(
                'id' => 'newpasswordconf',
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Validate changes', 3, 'class="text-center"'); ?>
    <?php
    $fields = array(
        array(
            'label' => 'Current password',
            'required' => true,
            'type' => 'password',
            'data' => array(
                'id' => 'password',
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'save',
                'value' => 'Save',
                'class' => 'btn-success btn-block btn-lg'
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theBtn = $('#save');
            var theForm = theBtn.parents('form');
            $('#save').click(function() {
                if (!confirm('Changing your email requires you to re-confirm the new one. Are you sure you want to proceed?'))
                    return;
                var params = {
                    url: '<?php echo site_url('user/account/home/submit'); ?>',
                    data: {
                        email: $('#email').val(),
                        newpassword: $('#newpassword').val(),
                        newpasswordconf: $('#newpasswordconf').val(),
                        password: $('#password').val()
                    },
                    success: function(data) {
                        console.log(data);
                        theForm.formAlert(data);
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>