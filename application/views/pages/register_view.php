<div class="page page-user page-user-register" style="max-width: 600px; margin: 0 auto">
    <h1 class="text-center">Applicant Registration</h1>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    $fields = array(
        array(
            'label' => 'Email address',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'email',
                'autofocus' => true,
            ),
        ),
        array(
            'label' => 'First name',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'fname',
            )
        ),
        array(
            'label' => 'Last name',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'lname',
            ),
        ),
        array(
            'label' => 'Password',
            'type' => 'password',
            'required' => true,
            'data' => array(
                'id' => 'password',
            ),
        ),
        array(
            'label' => 'Confirm password',
            'type' => 'password',
            'required' => true,
            'data' => array(
                'id' => 'passwordconf',
            ),
        ),
        array(
            'label' => sprintf('I agree to this website\'s %1$s.', anchor('tos', 'terms of service', array('target' => 'blank'))),
            'type' => 'checkbox',
            'required' => true,
            'data' => array(
                'id' => 'tos',
                'value' => 1,
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'required' => true,
                'id' => 'register',
                'value' => 'Register',
                'class' => 'btn-success btn-lg btn-block',
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theBtn = $('#register');
            var theForm;
            theBtn.click(function() {
                theForm = theBtn.parents('form');
                var params = {
                    loadingText: 'Registering...',
                    url: '<?php echo site_url('register/submit'); ?>',
                    data: {
                        email: $('#email').val(),
                        password: $('#password').val(),
                        passwordconf: $('#passwordconf').val(),
                        fname: $('#fname').val(),
                        lname: $('#lname').val(),
                        tos: $('#tos:checked').length
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