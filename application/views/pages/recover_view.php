<div class="page page-user page-user-recover">
    <h2 class="form-heading">Reset Password</h2>
    <!-- show this page if the user passed a meta key and email as GET parameters -->
    <?php if (isset($reset)): ?>
        <?php if (!$reset): ?>
            <div class="alert alert-danger">
                <p>The password reset key you entered is incorrect. <?php echo anchor('recover', 'Go back?', array('class' => 'alert-link')); ?></p>
            </div>
        <?php else: ?>
            <p class="text-center">Create a new password by filling up the fields below.</p>
            <?php echo form_open('', array('role' => 'form', 'id' => 'form-recover')); ?>
            <input type="hidden" id="user_email" value="<?php echo $reset['email']; ?>"/>
            <input type="hidden" id="reset_key" value="<?php echo $reset['key']; ?>"/>
            <?php
            $input_data = array(
                array(
                    'label' => 'New password',
                    'type' => 'password',
                    'data' => array(
                        'id' => 'password',
                        'autofocus' => true,
                    ),
                ),
                array(
                    'label' => 'Confirm new password',
                    'type' => 'password',
                    'data' => array(
                        'id' => 'passwordconf',
                        'autofocus' => true,
                    ),
                ),
                array(
                    'type' => 'button',
                    'data' => array(
                        'required' => true,
                        'id' => 'reset',
                        'value' => 'Reset',
                        'class' => 'btn-success btn-lg btn-block',
                    ),
                ),
            );
            ?>
            <?php echo bs_form_fields($input_data); ?>
            <div class="form-group text-center">
                Suddenly remembered your old password? <?php echo anchor('login', '&larr; Go back'); ?>
            </div>
            <?php echo form_close(); ?>
            <script type="text/javascript">
                $(function() {
                    var theForm = $('#form-recover');
                    $('#reset').click(function() {
                        var params = {
                            loadingText: 'Resetting...',
                            url: '<?php echo site_url('recover/reset'); ?>',
                            data: {
                                password: $('#password').val(),
                                passwordconf: $('#passwordconf').val(),
                                user_email: $('#user_email').val(),
                                reset_key: $('#reset_key').val(),
                            },
                            success: function(data) {
                                theForm.formAlert(data);
                            }
                        };
                        theForm.postmask(params);
                    });
                });
            </script>
        <?php endif; ?>
    <?php else: ?>
        <!-- show this page if no get parameters are passed -->
        <p class="text-center">Enter your email below to recover your password.</p>
        <?php echo form_open('', array('role' => 'form', 'id' => 'form-recover')); ?>
        <?php
        $input_data = array(
            array(
                'label' => 'Email address',
                'type' => 'text',
                'data' => array(
                    'id' => 'email',
                    'placeholder' => 'Email address',
                    'autofocus' => true,
                ),
            ),
            array(
                'type' => 'button',
                'data' => array(
                    'required' => true,
                    'id' => 'recover',
                    'value' => 'Recover',
                    'class' => 'btn-success btn-lg btn-block',
                ),
            ),
        );
        ?>
        <?php echo bs_form_fields($input_data, true); ?>
        <div class="form-group text-center">
            Already know your password? <?php echo anchor('login', '&larr; Go back'); ?>
        </div>
        <?php echo form_close(); ?>
        <script type="text/javascript">
            $(function() {
                var theForm = $('#form-recover');
                $('#recover').click(function() {
                    var params = {
                        loadingText: 'Requesting...',
                        url: '<?php echo site_url('recover/submit'); ?>',
                        data: {
                            email: $('#email').val(),
                        },
                        success: function(data) {
                            theForm.formAlert(data);
                        }
                    };
                    theForm.postmask(params);
                });
            });
        </script>
    <?php endif; ?>
</div>