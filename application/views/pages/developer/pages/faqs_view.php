<div class="page page-developer page-developer-pages page-developer-pages-faqs">
    <?php echo heading('Frequently Asked Questions Page', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <div class="form-group panel-group" id="accordion">
        <?php
        // sort the results
        if (!$records) {
            // set the default fields if no records exist yet
            $records = array(
                'faq_question_1' => '',
                'faq_answer_1' => '',
            );
        }
        // first part of the regex gets the type (e.g faq_question)
        // second part gets the index (e.g. 1)
        // from there, push results to the new sorted records array
        $sorted_records = array();
        $rgx = '/(.*)_(\d)/';
        foreach ($records as $key => $value) {
            preg_match($rgx, $key, $m);
            $type = $m[1];
            $idx = $m[2];

            $sorted_records[$idx][$type] = $value;
        }
        $i = 0;
        // iterate through the new sorted records
        foreach ($sorted_records as $record):
            ?>
            <div class="panel panel-default"<?php echo (++$i > 1) ? ' style="margin-top:25px"' : null; ?>>
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-parent="#accordion" style="cursor:pointer"></a>
                    </h4>
                </div>
                <div class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        $fields = array(
                            array(
                                'type' => 'text',
                                'label' => 'Question',
                                'required' => true,
                                'data' => array(
                                    'class' => 'faq_question',
                                    'value' => $record['faq_question'],
                                ),
                            ),
                            array(
                                'type' => 'textarea',
                                'label' => 'Answer',
                                'required' => true,
                                'data' => array(
                                    'class' => 'faq_answer',
                                    'rows' => 5,
                                    'value' => $record['faq_answer'],
                                ),
                            ),
                        );
                        echo bs_form_fields($fields);
                        ?>
                        <div class="row">
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-clone btn-info btn-block btn-sm">
                                    <?php echo bs_glyph('plus', 'Clone'); ?>
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-remove btn-danger btn-block btn-sm">
                                    <?php echo bs_glyph('minus', 'Delete'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    $field = array(
        'type' => 'button',
        'data' => array(
            'id' => 'save',
            'class' => 'btn btn-success btn-lg btn-block',
            'value' => 'Save'
        ),
    );
    echo bs_form_fields($field);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var btnClone = $('.btn-clone');
            var btnRemove = $('.btn-remove');
            var btnSave = $('#save');
            var theForm, thePanel;
            var elem, question, answer, clone, data, idx;

            btnClone.click(function() {
                elem = $(this);
                thePanel = elem.parents('.panel');
                clone = thePanel.clone(true);
                clone
                        .css({'margin-top': 25})
                        .find('input[type="text"], textarea').val('')
                        .end()
                        .find('.form-group').removeClass('has-error')
                        .end()
                        ;
                thePanel.after(clone.fadeIn('slow'));
            });

            btnRemove.click(function() {
                elem = $(this);
                theForm = elem.parents('form');
                if (theForm.find('.panel').length <= 1) {
                    alert('Deletion limit reached.');
                    return;
                }
                thePanel = elem.parents('.panel');
                thePanel.fadeOut('slow', function() {
                    thePanel.remove();
                });
            });

            btnSave.click(function() {
                data = {};
                idx = 0;

                elem = $(this);
                theForm = elem.parents('form');
                theForm.find('.faq_answer').each(function() {
                    idx++;

                    answer = $(this);
                    question = answer.parents('.panel-body').find('.faq_question');

                    // give a unique key to every field
                    data['faq_question_' + idx] = question.val();
                    data['faq_answer_' + idx] = answer.val();
                });

                var params = {
                    url: '<?php echo site_url('developer/pages/faqs/submit'); ?>',
                    data: data,
                    success: function(data) {
                        theForm.formAlert(data, {indexed: true});
                    }
                };
                theForm.postmask(params);
            });
        });

        $(function() {
            var panel = $('.panel');
            var panelTitle = panel.find('input[type="text"]');

            panelTitle.on('keyup', function() {
                var elem = $(this);
                var val = elem.val() || 'Enter a question';
                elem.parents('.panel').find('.panel-title a').text(val.substringMore());
            }).trigger('keyup');
        });
    </script>
</div>