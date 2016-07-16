<div class="page page-developer page-developer-emails page-developer-emails-templates">
    <?php echo heading('Email Templates', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php echo heading('Select a template', 2, 'class="text-center"'); ?>
    <?php
    $fields = array(
        array(
            'type' => 'select',
            'label' => '',
            'data' => array(
                'id' => 'template',
                'options' => $options,
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Subject',
            'data' => array(
                'id' => 'subject',
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'Message',
            'data' => array(
                'id' => 'message',
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'save',
                'value' => 'Save',
                'class' => 'btn btn-success btn-block btn-lg',
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theForm, elem, dataInspect;
            var theSelect = $('#template');
            var theSubj = $('#subject');
            var theMsg = $('#message');

            // fetch templates on select change
            theSelect.change(function() {
                theForm = $(this).parents('form');
                $.ajax({
                    url: '<?php echo site_url('developer/emails/templates/data'); ?>',
                    type: 'get',
                    cache: false,
                    data: {
                        template_name: $(this).val()
                    },
                    success: function(data) {
                        if ($.isJSON(data)) {
                            dataInspect = $.parseJSON(data);
                            if (dataInspect.hasOwnProperty('subject')) {
                                theSubj.val(dataInspect.subject);
                                theMsg.val(dataInspect.message);
                                theForm.removeAlerts();
                            } else {
                                theForm.find('input[type="text"], textarea').val('');
                                theForm.formAlert(data);
                            }
                        }
                    }
                });
            }).trigger('change');


            $('#save').click(function() {
                theForm = $(this).parents('form');
                var params = {
                    url: '<?php echo site_url('developer/emails/templates/submit'); ?>',
                    data: {
                        template_name: theSelect.val(),
                        subject: theSubj.val(),
                        message: theMsg.val()
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