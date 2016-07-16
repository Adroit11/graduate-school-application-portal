<div class="page page-user page-user-apply page-user-apply-home">
    <?php echo heading('Dashboard'); ?>
    <hr />
    <?php if ($last_email): ?>
        <div class="alert alert-danger">
            <p>The Graduate School cannot proceed with your application because it has deficiencies. Refer to the contents of the last email sent to you to figure out what you need to fix. For convenience, it reads:</p>
        </div>
        <div class="well"><?php echo auto_typography($last_email); ?></div>
    <?php endif; ?>
    <p class="lead" id="reminder">Below is a checklist of what you need to accomplish.</p>
    <div class="progress progress-striped active">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
            <span class="sr-only"></span>
        </div>
    </div>
    <?php
    $list_items = array(
        bs_glyph('pushpin', anchor('user/apply/basic', 'Basic information', array('id' => 'basic'))),
        bs_glyph('pushpin', anchor('user/apply/education', 'Educational information', array('id' => 'education'))),
        bs_glyph('pushpin', anchor('user/apply/documents', 'Electronic documents', array('id' => 'documents'))),
        bs_glyph('pushpin', anchor('user/apply/essay', 'Application essay', array('id' => 'essay'))),
        bs_glyph('pushpin', anchor('user/apply/recommendations', 'Instructor recommendations', array('id' => 'recommendations'))),
    );
    echo ul($list_items, array('class' => 'pending', 'style' => 'list-style-type:none; padding-left: 0'));
    ?>
    <hr />
    <?php if (!$programs): ?>
        <div class="alert alert-info text-center">
            <?php echo bs_glyph('asterisk'); ?> The curriculum for this academic year is still being generated. Please come back later.
        </div>
    <?php else: ?>
        <?php echo form_open('', array('role' => 'form', 'class' => 'form-dashboard', 'onsubmit' => 'return false')); ?>
        <?php
        $field = array(
            array(
                'label' => 'What program are you applying for?',
                'type' => 'select',
                'data' => array(
                    'options' => $programs,
                    'id' => 'program',
                ),
            ),
            array(
                'type' => 'button',
                'data' => array(
                    'id' => 'submit',
                    'class' => 'btn btn-lg btn-success btn-block',
                    'disabled' => true,
                    'value' => 'Submit application'
                )
            ),
        );
        echo bs_form_fields($field);
        ?>
        <?php echo form_close(); ?>
    <?php endif; ?>
    <script type="text/javascript">
        $(function() {
            var task, taskText, step = 0, theToken, formFields, alertMsg;
            var theBar = $('.progress-bar');
            var theProgram = $('#program');
            var theReminder = $('#reminder');
            var theBtn = $('#submit');
            var theReplacement = $('<div />').addClass('alert alert-success lead text-center');

            $.ajax({
                cache: false,
                type: 'get',
                url: '<?php echo site_url(uri_string() . '/home/progress'); ?>',
                success: function(data) {
                    data = $.parseJSON(data);

                    // check if there's a token
                    // if there is, activate the button and create a token field
                    if (data.hasOwnProperty('token')) {
                        theToken = $('<input />').attr({
                            id: 'token',
                            type: 'hidden',
                            value: data.token
                        });
                        theBtn.removeAttr('disabled').before(theToken);
                    }
                    $.each(data, function(k, v) {
                        // increment the progress bar
                        step += 20;
                        theBar.attr('aria-valuenow', step).width(step + '%');

                        // strike out the tasks
                        task = $('#' + k);
                        taskText = task.text();
                        task.parents('li').find('a').fadeOut(function() {
                            $(this).remove();
                        }).end().html('<span class="glyphicon glyphicon-check"></span> ' + '<strike>' + taskText + '</strike> (Done)');
                    });
                    console.log(step);
                    if (step >= 100) {
                        // congratulate the user
                        theReplacement.text('You are now ready to submit your application.')
                        theReminder.fadeOut('slow', function() {
                            $(this).after(theReplacement.hide().fadeIn('slow'));
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });

            theBtn.click(function() {
                if (!confirm('Your application is about to be reviewed by Graduate School personnel. Are you ready to proceed?'))
                    return;
                theToken = theBtn.parents('form').find('#token');
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('user/apply/home/submit'); ?>',
                    data: {
                        token: theToken.val(),
                        program: theProgram.val()
                    },
                    beforeSend: function() {
                        formFields = theBtn.parents('form').find('input, button');
                        formFields.attr('disabled', true);
                    },
                    success: function(data) {
                        data = $.parseJSON(data);
                        if (typeof data === 'object') {
                            if (data.type === 'error') {
                                alertMsg = 'There were errors with your submission:\n\n';
                                $.each(data.data, function(k, v) {
                                    alertMsg += '- ' + v + '\n';
                                });
                                alert(alertMsg);
                            }
                        } else {
                            location.reload();
                        }
                    },
                    complete: function() {
                        formFields.removeAttr('disabled');
                    }
                });
            });
        });
    </script>
</div>