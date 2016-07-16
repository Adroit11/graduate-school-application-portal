<div class="page page-developer page-developer-emails page-developer-emails-smtp">
    <?php echo heading('SMTP Settings', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php echo heading('Sender details', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'From name',
            'data' => array(
                'id' => 'from_name',
                'value' => prop('from_name', $record),
            ),
        ),
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'From email',
            'data' => array(
                'id' => 'from_email',
                'value' => prop('from_email', $record),
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Reply-to name',
            'data' => array(
                'id' => 'reply_to_name',
                'value' => prop('reply_to_name', $record),
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Reply-to email',
            'data' => array(
                'id' => 'reply_to_email',
                'value' => prop('reply_to_email', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('SMTP server settings', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'Hostname',
            'data' => array(
                'id' => 'host',
                'value' => prop('host', $record),
            ),
        ),
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'Port number',
            'data' => array(
                'id' => 'port',
                'value' => prop('port', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('SMTP authentication details', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'checkbox',
            'label' => 'Enable authentication?',
            'data' => array(
                'id' => 'authentication',
                'checked' => prop('authentication', $record),
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Username',
            'data' => array(
                'id' => 'username',
                'value' => prop('username', $record),
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Password',
            'data' => array(
                'id' => 'password',
                'value' => prop('password', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Other settings'); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Subject line prefix',
            'data' => array(
                'id' => 'subject_prefix',
                'value' => prop('subject_prefix', $record)
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'CC list (separate with line breaks)',
            'data' => array(
                'id' => 'cc',
                'rows' => 5,
                'value' => ($d = prop('cc', $record)) && is_array($d) && !empty($d) ? join('\n', $d) : '',
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'BCC list (separate with line breaks)',
            'data' => array(
                'id' => 'bcc',
                'rows' => 5,
                'value' => ($d = prop('bcc', $record)) && is_array($d) && !empty($d) ? join('\n', $d) : '',
            ),
        ),
        array(
            'type' => 'select',
            'required' => true,
            'label' => 'Email timeout',
            'data' => array(
                'id' => 'timeout',
                'options' => array(
                    '0' => '0 (None/send immediately)',
                    '2' => '2 (Two seconds)',
                    '5' => '5 (Five seconds)',
                    '10' => '10 (Ten seconds)',
                ),
                'selected' => prop('timeout', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php
    $fields = array(
        'type' => 'button',
        'data' => array(
            'id' => 'save',
            'class' => 'btn-success btn-block btn-lg',
            'value' => 'Save',
        )
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theForm, data = {}, dataID, dataVal;
            $('#save').click(function() {
                theForm = $(this).parents('form');
                theForm.find('[id]').filter(function() {
                    dataID = $(this).attr('id');
                    switch (dataID) {
                        case 'authentication':
                            data[dataID] = $(this).prop('checked') ? 1 : 0;
                            break;
                        case 'timeout':
                        case 'port':
                            data[dataID] = parseInt($(this).val());
                            break;
                        default:
                            data[dataID] = $(this).val();
                    }
                });
                var params = {
                    url: '<?php echo site_url('developer/emails/smtp/submit'); ?>',
                    data: data,
                    success: function(data) {
                        theForm.formAlert(data);
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>