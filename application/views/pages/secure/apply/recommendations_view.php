<div class="page-secure page-secure-apply page-secure-apply-recommendations">
    <?php echo heading('Instructor Recommendation', 1, 'class="text-center"'); ?>
    <hr />
    <?php if (isset($invalid) && $invalid): ?>
        <div class="alert alert-danger">
            <p>You are seeing this error for <strong>one of two reasons</strong>:</p>
            <ol>
                <li>Your recommendation has already been <strong>received</strong>.</li>
                <li>The token you're using is invalid or may have already <strong>expired</strong>.</li>
            </ol>
        </div>
    <?php else: ?>
        <?php echo form_open('', array('role' => 'form', 'id' => 'form-page-secure-apply-recommendations')); ?>
        <?php
        echo <<<EOT
<div class="well">
<p>Dear <strong>$instructor->name</strong>,</p>
<p>Your former student, <strong>$student->fname $student->lname</strong>, is requesting your 
recommendation in order to fulfill his application at 
    Holy Angel University Graduate School. Kindly give us your <strong>unbiased evaluation</strong> of $student->fname as a student.</p>
<p><em>Please be assured that that your submission will be dealt with <strong>strict confidentiality</strong>.</em></p>
</div>
EOT;
        ?>
        <?php
        $fields = array(
            array(
                'label' => "Your recommendation for $student->fname <small>(maximum of 2000 words)</small>",
                'required' => true,
                'type' => 'textarea',
                'data' => array(
                    'id' => 'recommendation',
                )
            ),
            array(
                'type' => 'button',
                'data' => array(
                    'id' => 'save',
                    'class' => 'btn btn-success btn-lg btn-block submit',
                    'value' => 'Submit your recommendation',
                )
            )
        );
        echo bs_form_fields($fields);
        ?>
        <?php echo form_close(); ?>
        <script type="text/javascript">
            $(document).ready(function() {
                var theForm;
                $('#save').click(function() {
                    theForm = $(this).parents('form');
                    var params = {
                        url: '<?php echo site_url('secure/apply/recommendations_submit'); ?>',
                        data: {
                            recommendation: $('#recommendation').val()
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
<?php endif; ?>