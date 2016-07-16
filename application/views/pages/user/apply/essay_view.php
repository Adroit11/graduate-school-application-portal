<?php echo heading('Essay'); ?>
<hr />
<p class="lead">In 1000 words or less, give a compelling reason on why you should be accepted into your program of choice.</p>
<?php echo form_open('', array('role' => 'form', 'onsubmit' => 'return false', 'id' => 'form-user-apply-essay')); ?>
<?php
$field = array(
    array(
        'type' => 'textarea',
        'data' => array(
            'id' => 'essay',
            'placeholder' => 'Your essay goes here.',
            'rows' => 15,
            'value' => $record ? $record : '',
        )
    ),
);
echo bs_form_fields($field);
?>
<p class="small">
    <strong>Word count</strong>: <span class="char-count">0</span>
</p>
<?php
$field = array(
    array(
        'type' => 'button',
        'data' => array(
            'id' => 'save',
            'value' => 'Save',
            'class' => 'btn-lg btn-success btn-block'
        ),
    )
);
echo bs_form_fields($field);
?>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        var charCount;
        var theForm = $('#form-user-apply-essay');
        $('#save').click(function() {
            var params = {
                url: '<?php echo site_url('user/apply/essay/submit'); ?>',
                data: {
                    essay: $('#essay').val()
                },
                success: function(data) {
                    theForm.formAlert(data);
                }
            };
            theForm.postmask(params);
        });
        $('#essay').keyup(function() {
            charCount = $(this).val().match(/\S+/g).length;
            $('.char-count').text(charCount);
        }).trigger('keyup');
    });
</script>