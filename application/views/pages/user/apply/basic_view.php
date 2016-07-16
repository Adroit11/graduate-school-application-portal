<div class="page page-user page-user-apply page-user-apply-basic">
    <?php echo heading('Basic Information'); ?>
    <hr />
    <?php echo sprintf('<p class="lead">%1$s</p>', bs_glyph('hand-right', 'Make sure to enter <strong>up-to-date</strong> information (e.g. mobile number, mailing address)')); ?>
    <?php echo form_open('', array('role' => 'form', 'id' => 'form-user-apply-basic', 'onsubmit' => 'return false')); ?>
    <?php
    $fields = array(
        array(
            'label' => 'Title',
            'type' => 'select',
            'required' => true,
            'data' => array(
                'id' => 'title',
                'options' => array(
                    'mr' => 'Mr.',
                    'ms' => 'Ms.',
                ),
                'selected' => prop('studapp_basic_title', $record),
            ),
        ),
        array(
            'label' => 'First name',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'fname',
                'value' => prop('studapp_basic_fname', $record),
            )
        ),
        array(
            'label' => 'Last name',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'lname',
                'value' => prop('studapp_basic_lname', $record),
            ),
        ),
        array(
            'label' => 'Middle name',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'mname',
                'value' => prop('studapp_basic_mname', $record),
            ),
        ),
        array(
            'label' => 'Suffix',
            'type' => 'text',
            'data' => array(
                'placeholder' => '(e.g. Jr., C.P.A., M.B.A., M.D., Ph.D.)',
                'id' => 'suffix',
                'value' => prop('studapp_basic_suffix', $record),
            ),
        ),
        array(
            'label' => 'Profession',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'profession',
                'value' => prop('studapp_basic_profession', $record),
            ),
        ),
        array(
            'label' => 'Birth date',
            'type' => 'date',
            'required' => true,
            'data' => array(
                'id' => 'bdate',
                'data-date' => prop('studapp_basic_bdate', $record, date_nice(), 'date_nice'),
            ),
        ),
        array(
            'label' => 'Citizenship',
            'type' => 'select',
            'required' => true,
            'data' => array(
                'id' => 'citizenship',
                'options' => array(
                    'filipino' => 'Filipino',
                    'foreign' => 'Foreign',
                    'naturalized' => 'Naturalized Filipino',
                ),
                'selected' => prop('studapp_basic_citizenship', $record),
            ),
        ),
        array(
            'label' => 'Mobile number',
            'type' => 'phone',
            'required' => true,
            'data' => array(
                'id' => 'phone',
                'data-format' => '+dd (ddd) ddd-dddd',
                'number' => prop('studapp_basic_address', $record),
                'value' => prop('studapp_basic_phone', $record),
            ),
        ),
        array(
            'label' => 'Address',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'address',
                'value' => prop('studapp_basic_address', $record),
            ),
        ),
        array(
            'label' => 'Address 2',
            'type' => 'text',
            'data' => array(
                'id' => 'address_2',
                'value' => prop('studapp_basic_address_2', $record),
            ),
        ),
        array(
            'label' => 'City/Town/Municipality',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'city',
                'value' => prop('studapp_basic_city', $record),
            ),
        ),
        array(
            'label' => 'State/Province',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'state',
                'value' => prop('studapp_basic_state', $record),
            ),
        ),
        array(
            'label' => 'ZIP',
            'type' => 'text',
            'required' => true,
            'data' => array(
                'id' => 'zip',
                'value' => prop('studapp_basic_zip', $record),
            ),
        ),
        array(
            'label' => 'Country',
            'type' => 'country',
            'required' => true,
            'data' => array(
                'id' => 'country',
                'data-country' => prop('studapp_basic_country', $record, 'PH'),
                'blank' => true
            ),
        ),
        array(
            'type' => 'button',
            'data' => array(
                'required' => true,
                'id' => 'save',
                'value' => 'Save',
                'class' => 'btn-success btn-block btn-lg',
            ),
        ),
    );
    ?>
    <?php echo bs_form_fields($fields); ?>
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
                    url: '<?php echo site_url('user/apply/basic/submit'); ?>',
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