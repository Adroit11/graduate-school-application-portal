<div class="page page-developer page-developer-pages page-developer-pages-about">
    <?php echo heading('About Page', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php
    for ($i = 1; $i <= 3; $i++) {
        $fields = array(
            array(
                'type' => 'text',
                'label' => 'Tab ' . $i . ' title',
                'data' => array(
                    'id' => 'title_' . $i,
                    'value' => prop('title_' . $i, $record),
                ),
            ),
            array(
                'type' => 'textarea',
                'label' => 'Tab ' . $i . ' content',
                'data' => array(
                    'rows' => 10,
                    'id' => 'content_' . $i,
                    'value' => prop('content_' . $i, $record),
                ),
            ),
        );

        echo bs_form_fields($fields);

        if ($i < 3) {
            echo '<hr />';
        }
    }
    $field = array(
        'type' => 'button',
        'data' => array(
            'id' => 'save',
            'value' => 'Save',
            'class' => 'btn btn-success btn-block btn-lg'
        ),
    );
    echo bs_form_fields($field);
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
                    url: '<?php echo site_url('developer/pages/about/submit'); ?>',
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