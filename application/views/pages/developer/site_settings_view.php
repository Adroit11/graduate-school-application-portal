<div class="page page-developer page-developer-site-settings">
    <?php echo heading('Website Settings', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <?php echo heading('Meta', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'Title',
            'data' => array(
                'id' => 'title',
                'value' => prop('title', $record),
            ),
        ),
        array(
            'type' => 'text',
            'label' => 'Title separator',
            'data' => array(
                'id' => 'title_sep',
                'value' => prop('title_sep', $record),
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'Robots.txt entries',
            'data' => array(
                'id' => 'robots',
                'value' => prop('robots', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Global', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'text',
            'required' => true,
            'label' => 'Navigation title',
            'data' => array(
                'id' => 'nav_title',
                'value' => prop('nav_title', $record),
            ),
        ),
        array(
            'type' => 'checkbox',
            'required' => true,
            'label' => 'In beta?',
            'data' => array(
                'id' => 'beta',
                'checked' => prop('beta', $record),
            ),
        ),
        array(
            'type' => 'textarea',
            'label' => 'Announcement (separate by line break)',
            'data' => array(
                'id' => 'announcement',
                'value' => prop('announcement', $record),
            ),
        ),
    );
    echo bs_form_fields($fields);
    ?>
    <hr />
    <?php echo heading('Tracking & performance', 2); ?>
    <?php
    $fields = array(
        array(
            'type' => 'textarea',
            'label' => 'Google Analytics code (include <code>&lt;script&gt;</code> tags)',
            'data' => array(
                'id' => 'analytics',
                'value' => prop('analytics', $record),
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'id' => 'save',
                'class' => 'btn-success btn-block btn-lg',
                'value' => 'Save',
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
                    switch (dataID) {
                        case 'beta':
                            data[dataID] = $(this).prop('checked') ? 1 : 0;
                            break;
                        default:
                            data[dataID] = $(this).val();
                    }
                });
                var params = {
                    url: '<?php echo site_url('developer/site_settings/submit'); ?>',
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