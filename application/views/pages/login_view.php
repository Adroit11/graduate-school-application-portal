<div class="page page-user page-user-login">
    <?php if (isset($activate)): ?>
        <?php if ($activate): ?>
            <div class="alert alert-success">
                <p>Congratulations, your account has been successfully activated. You may now <?php echo anchor('login', 'log in', array('class' => 'alert-link')); ?>.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <p>You may have <strong>(a)</strong> already activated your account or <strong>(b)</strong> entered an invalid activation key. To go back, <?php echo anchor('login', 'click here.', array('class' => 'alert-link')); ?></p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <h2 class="form-heading">Sign In</h2>
        <?php if (isset($delete) && $delete === true): ?>
            <div class="alert alert-danger">
                <p>Account successfully <strong>deleted</strong>. If you wish to apply again, you can <?php echo anchor('register', 're-register', array('class' => 'alert-link')); ?> any time.</p>
            </div>
        <?php endif; ?>
        <?php echo form_open('', array('role' => 'form', 'id' => 'form-login')); ?>
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
                'label' => 'Password name',
                'type' => 'password',
                'data' => array(
                    'id' => 'password',
                    'placeholder' => 'Password',
                )
            ),
            array(
                'type' => 'button',
                'data' => array(
                    'required' => true,
                    'id' => 'login',
                    'value' => 'Log in',
                    'class' => 'btn-success btn-lg btn-block',
                ),
            ),
            array(
                'label' => 'Remember me',
                'type' => 'checkbox',
                'data' => array(
                    'id' => 'remember',
                    'value' => 1,
                ),
            ),
        );
        ?>
        <?php echo bs_form_fields($input_data, true); ?>
        <div class="text-center">
            <p>Don't have an account yet? <?php echo anchor('register', 'Register now &rarr;'); ?></p>
            <p class="small"><?php echo anchor('recover', 'Forgot your password?'); ?></p>
        </div>
        <?php echo form_close(); ?>
        <script type="text/javascript">
            $(function() {
                var theBtn = $('#login');
                var theForm = theBtn.parents('form');

                // "click" the login button when Enter key is pressed.
                theForm.find('input').keyup(function(e) {
                    if (parseInt(e.keyCode) === 13) {
                        theBtn.click();
                    }
                });

                // transmit the data
                theBtn.click(function() {
                    theForm = $(this).parents('form');
                    var params = {
                        loadingText: 'Authenticating...',
                        url: '<?php echo site_url('login/submit'); ?>',
                        data: {
                            email: $('#email').val(),
                            password: $('#password').val(),
                            remember: $('#remember:checked').length
                        },
                        success: function(data) {
                            if (data === 'true') {
                                if (getURLParameter('goto') !== 'null') {
                                    location = getURLParameter('goto');
                                } else {
                                    location.reload();
                                }
                            }
                            else
                                theForm.formAlert(data);
                        }
                    };
                    theForm.postmask(params);
                });
            });

        </script>
    <?php endif; ?>
</div>