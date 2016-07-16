<div class="page page-admin page-admin-contact-developer">
    <?php echo heading('Contact Developer'); ?>
    <hr />
    <p class="lead">Use this form to issue a request to the developer. The average turnaround time for minor issues is 3-5 working days.</p>
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    $fields = array(
        array(
            'type' => 'select',
            'required' => true,
            'label' => 'Subject',
            'data' => array(
                'id' => 'subject',
                'options' => array(
                    'Page modification' => 'Page modification',
                    'Account creation' => 'Account creation',
                    'Bug report' => 'Bug report',
                    'Feature request' => 'Feature request',
                    'Other' => 'Other',
                ),
            ),
        ),
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'Short description',
            'data' => array(
                'id' => 'short_description',
                'placeholder' => '(E.g. Unable to log in John Smith\'s account)',
            ),
        ),
        array(
            'type' => 'textarea',
            'required' => true,
            'label' => 'Long description',
            'data' => array(
                'id' => 'long_description',
                'placeholder' => '(Please be as comprehensive and concise as possible)',
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'send',
                'value' => 'Send Request',
                'class' => 'btn-block btn-success btn-lg'
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var theForm;
            $('#send').click(function() {
                theForm = $(this).parents('form');
                var params = {
                    url: '<?php echo site_url('admin/contact_developer/submit'); ?>',
                    data: {
                        subject: $('#subject').val(),
                        short_description: $('#short_description').val(),
                        long_description: $('#long_description').val()
                    },
                    success: function(data) {
                        theForm.formAlert(data);
                        data = $.parseJSON(data);
                        if (data.type === 'success') {
                            theForm.find('input[type="text"], textarea').val('');
                        }
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>