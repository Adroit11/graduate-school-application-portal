<div class="page page-developer page-developer-pages page-developer-pages-tos">
    <?php echo heading('Terms of Service Page', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Title',
            'data' => array(
                'id' => 'title',
                'value' => prop('title', $record),
            ),
        ),
        array(
            'type' => 'date',
            'label' => 'Effective date',
            'data' => array(
                'id' => 'date',
                'value' => prop('date', $record, date_nice(), 'date_nice'),
                'data-date' =>  prop('date', $record, date_nice(), 'date_nice')
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'Content',
            'data' => array(
                'id' => 'content',
                'rows' => 15,
                'value' => prop('content', $record),
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'value' => 'Save',
                'id' => 'save',
                'class' => 'btn btn-success btn-lg btn-block',
            ),
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
                    url: '<?php echo site_url('developer/pages/tos/submit'); ?>',
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