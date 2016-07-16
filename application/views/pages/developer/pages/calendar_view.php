<div class="page page-developer page-developer-pages page-developer-pages-calendar">
    <?php echo heading('Event Calendar Page', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Google Calendar public URL',
            'data' => array(
                'id' => 'gcal_url',
                'value' => prop('gcal_url', $record),
            ),
        ),
        array(
            'type' => 'textarea',
            'required' => true,
            'label' => '"Calendar not loaded" placeholder text',
            'data' => array(
                'rows' => 5,
                'id' => 'fallback',
                'value' => prop('fallback', $record),
            )
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'save',
                'class' => 'btn btn-success btn-block btn-lg',
                'value' => 'Save'
            )
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theForm, data = {}, dataID;
            $('#save').click(function() {
                theForm = $(this).parents('form');
                theForm.find('[id]').filter(function() {
                    dataID = $(this).attr('id');
                    data[dataID] = $(this).val();
                });
                var params = {
                    url: '<?php echo site_url('developer/pages/calendar/submit'); ?>',
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