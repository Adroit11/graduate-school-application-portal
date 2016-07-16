<div class="page page-admin-account page-admin-account-close">
    <?php echo heading('Request Account Deletion'); ?>
    <hr />
    <p class="lead">Request your account (and all data associated with it) to be deleted by the developer.</p>
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    echo isset($errors) ? bs_form_validation_errors($errors) : '';
    $fields = array(
        array(
            'label' => 'Reason/notes',
            'required' => true,
            'type' => 'textarea',
            'data' => array(
                'id' => 'reason',
            ),
        ),
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
                'value' => 'Request deletion of my account',
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
                if (!confirm('Your request is subject to the developer\'s discretion. That being said, are you sure you want to proceed?'))
                    return;
                var params = {
                    loadingText: 'Processing',
                    url: '<?php echo site_url('admin/account/close/submit'); ?>',
                    data: {
                        reason: $('#reason').val(),
                        password: $('#password').val(),
                        passwordconf: $('#passwordconf').val()
                    },
                    success: function(data) {
                        theForm.formAlert(data);
                        data = $.parseJSON(data);
                        if (data.type === 'success') {
                            theForm.find('input[type="password"], textarea').val('');
                        }
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>