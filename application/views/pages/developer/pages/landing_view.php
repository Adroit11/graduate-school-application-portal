<div class="page page-developer page-developer-pages page-developer-pages-landing">
    <?php echo heading('Landing Page', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php echo heading('Jumbotron', 3); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Lead text',
            'data' => array(
                'id' => 'jumbotron_lead',
                'value' => prop('jumbotron_lead', $record),
            )
        ),
        array(
            'type' => 'textarea',
            'label' => 'Call-to-action',
            'data' => array(
                'id' => 'jumbotron_cta',
                'rows' => 5,
                'value' => prop('jumbotron_cta', $record),
            )
        ),
        array(
            'type' => 'text',
            'label' => 'Button text',
            'data' => array(
                'id' => 'jumbotron_btn',
                'value' => prop('jumbotron_btn', $record),
            )
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Main content', 3); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Title',
            'data' => array(
                'id' => 'main_title',
                'value' => prop('main_title', $record),
            )
        ),
        array(
            'type' => 'textarea',
            'label' => 'Content',
            'data' => array(
                'id' => 'main_content',
                'value' => prop('main_content', $record),
            )
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Sidebar', 3); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'label' => 'Title',
            'data' => array(
                'id' => 'sidebar_title',
                'value' => prop('sidebar_title', $record),
            )
        ),
        array(
            'type' => 'text',
            'label' => 'Google Calendar public URL',
            'data' => array(
                'id' => 'sidebar_gcal_url',
                'value' => prop('sidebar_gcal_url', $record),
            )
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Trifecta', 3); ?>
    <?php
    for ($i = 1; $i <= 3; $i++) {
        $fields = array(
            array(
                'type' => 'text',
                'label' => 'Item ' . $i . ' Title',
                'data' => array(
                    'id' => 'trifecta_title_' . $i,
                    'value' => prop('trifecta_title_' . $i, $record),
                )
            ),
            array(
                'type' => 'textarea',
                'label' => 'Item ' . $i . ' Content',
                'data' => array(
                    'id' => 'trifecta_content_' . $i,
                    'rows' => 4,
                    'value' => prop('trifecta_content_' . $i, $record),
                )
            ),
        );
        echo bs_form_fields($fields);
    }
    ?>
    <?php
    $field = array(
        'type' => 'button',
        'data' => array(
            'id' => 'save',
            'value' => 'Save',
            'class' => 'btn btn-success btn-block btn-lg'
        )
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
                    url: '<?php echo site_url('developer/pages/landing/submit'); ?>',
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