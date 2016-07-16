<div class="page page-developer page-developer-account">
    <?php echo heading('Account Settings', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php echo heading('Your information', 3, 'class="text-center"'); ?>
    <?php
    $fields = array(
        array(
            'label' => 'Email address',
            'required' => true,
            'type' => 'text',
            'data' => array(
                'id' => 'email',
                'value' => prop('email', $record),
            ),
        ),
        array(
            'label' => 'First name',
            'required' => true,
            'type' => 'text',
            'data' => array(
                'id' => 'fname',
                'value' => prop('fname', $record),
            ),
        ),
        array(
            'label' => 'Last name',
            'required' => true,
            'type' => 'text',
            'data' => array(
                'id' => 'lname',
                'value' => prop('lname', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Change password', 3, 'class="text-center"'); ?>
    <?php
    $fields = array(
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
    <?php echo heading('Validate changes', 4, 'class="text-center"'); ?>
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
                'value' => 'Submit',
                'class' => 'btn-success btn-block btn-lg'
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theForm, data = {};
            $('#save').click(function() {
                theForm = $(this).parents('form');
                theForm.find('[id]').filter(function() {
                    data[$(this).attr('id')] = $(this).val();
                });
                var params = {
                    url: '<?php echo site_url('developer/account/submit'); ?>',
                    data: data,
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