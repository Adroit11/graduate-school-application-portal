<?php echo heading('Education'); ?>
<hr />
<p class="lead">Enter relevant education history here, in no particular order.</p>
<?php
$count = $records ? count($records) : 0;
if (!$count) {
    // create placeholder
    $records = array();
    $records[1] = array();
    $records = array_to_object($records);
}
?>
<div class="panel-group" id="accordion">
    <?php foreach ($records as $i => $record): ?>
        <div data-index="<?php echo $i; ?>" class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-parent="#accordion" style="cursor:pointer"></a>
                </h4>
            </div>
            <div class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    echo form_open('', array(
                        'role' => 'form',
                        'onsubmit' => 'return false',
                        'class' => 'form-user-apply-education'
                    ));
                    ?>
                    <input type="hidden" id="index[<?php echo $i; ?>]" value="<?php echo $i; ?>" />
                    <?php
                    $fields = array(
                        array(
                            'label' => 'Course/Concentration',
                            'required' => true,
                            'type' => 'text',
                            'data' => array(
                                'id' => sprintf('concentration[%1$s]', $i),
                                'class' => 'concentration',
                                'value' => prop('concentration', $record),
                            ),
                        ),
                        array(
                            'label' => 'Degree',
                            'required' => true,
                            'type' => 'select',
                            'data' => array(
                                'id' => sprintf('degree[%1$s]', $i),
                                'options' => array(
                                    'undergraduate' => 'Undergraduate',
                                    'vocational' => 'Technical/Vocational',
                                    'master' => 'Master\'s',
                                    'doctoral' => 'Doctoral',
                                ),
                                'selected' => prop('degree', $record, 'undergraduate'),
                            ),
                        ),
                        array(
                            'label' => 'Institution',
                            'required' => true,
                            'type' => 'text',
                            'data' => array(
                                'id' => sprintf('institution[%1$s]', $i),
                                'value' => prop('institution', $record),
                            ),
                        ),
                        array(
                            'label' => 'Date admitted',
                            'required' => true,
                            'type' => 'date',
                            'data' => array(
                                'id' => sprintf('admitted[%1$s]', $i),
                                'data-date' => prop('admitted', $record, date_nice(), 'date_nice'),
                            ),
                        ),
                        array(
                            'label' => 'Date graduated or expected graduation date',
                            'required' => true,
                            'type' => 'date',
                            'data' => array(
                                'id' => sprintf('graduated[%1$s]', $i),
                                'data-date' => prop('graduated', $record, date_nice(), 'date_nice'),
                            ),
                        ),
                        array(
                            'label' => 'Student ID',
                            'type' => 'text',
                            'data' => array(
                                'id' => sprintf('student_id[%1$s]', $i),
                                'value' => prop('student_id', $record),
                            ),
                        ),
                        array(
                            'label' => 'GPA ' . sprintf('<a data-dynamic="true" data-toggle="modal" data-title="What is GPA?" href="http://en.m.wikipedia.org/wiki/Academic_grading_in_the_Philippines">%1$s</a>', bs_glyph('question-sign')),
                            'type' => 'text',
                            'data' => array(
                                'id' => sprintf('gpa[%1$s]', $i),
                                'value' => prop('gpa', $record),
                            ),
                        ),
                        array(
                            'label' => 'Awards/accolades',
                            'type' => 'textarea',
                            'data' => array(
                                'rows' => 5,
                                'id' => sprintf('awards[%1$s]', $i),
                                'value' => prop('awards', $record),
                            ),
                        ),
                    );
                    echo bs_form_fields($fields);
                    ?>
                    <div class="row form-group">
                        <div class="col-sm-3">
                            <?php
                            $field = array(
                                'type' => 'button',
                                'data' => array(
                                    'class' => 'btn btn-clone btn-block btn-success btn-sm btn-info',
                                    'value' => 'Add new entry'
                                )
                            );
                            echo bs_form_fields($field);
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            $field = array(
                                'type' => 'button',
                                'data' => array(
                                    'class' => 'btn btn-remove btn-block btn-sm btn-danger',
                                    'value' => 'Remove this entry'
                                )
                            );
                            echo bs_form_fields($field);
                            ?>
                        </div>
                    </div>
                    <?php
                    $field = array(
                        'type' => 'button',
                        'data' => array(
                            'class' => 'save btn btn-block btn-success btn-lg btn-success',
                            'value' => 'Save'
                        )
                    );
                    echo bs_form_fields($field);
                    ?>
                    <?php echo form_close(); ?>
                </div><!--.panel-body-->
            </div><!--panel-collapse-->
        </div><!--.panel-->
        <hr />
    <?php endforeach; ?>
</div><!--.panel-group-->
<script type="text/javascript">
    $(document).ready(function() {
        var theBtn, theForm, theData, theID, theVal, theIndex;
        $('.save').click(function() {
            theData = {};
            theBtn = $(this);
            theForm = theBtn.parents('form');
            theForm.find('[id]').filter(function() {
                var elem = $(this);
                var m = elem.prop('id').match(/(.*)\[(\d)+\]/) || [];
                if (m.length === 3) {
                    theID = m[1];
                    theIndex = m[2];
                    theVal = elem.val();

                    if (theID === 'index') {
                        theData[theID] = theIndex;
                    } else {
                        theData[theID] = theVal;
                    }
                }
            });
            var params = {
                url: '<?php echo site_url('user/apply/education/submit'); ?>',
                data: theData,
                success: function(data) {
                    theForm.formAlert(data);
                }
            };
            theForm.postmask(params);
        });
    });

    $(function() {
        var theBtn, theForm;
        $('.btn-clone').click(function() {
            theBtn = $(this);
            theBtn.clonePanel({
                limit: <?php echo $max_index; ?>
            });
        });

        $('.btn-remove').click(function() {
            theBtn = $(this);
            theForm = theBtn.parents('form');
            theBtn.removePanel({
                limit: <?php echo $min_index; ?>
            }, function(idx) {
                $.ajax({
                    method: 'post',
                    url: '<?php echo site_url('user/apply/education/delete'); ?>',
                    data: {
                        index: idx
                    },
                    success: function(data) {
                        if (data === 'false') {
                            alert('There was a problem connecting to the database. Reloading page.');
                            location.reload();
                        } else {
                            theForm.formAlert(data);
                        }
                    }
                });
            });
        });
    });

    $(function() {
        var panel = $('.panel');
        var panelTitle = panel.find('input[id^="concentration"]');

        panelTitle.on('keyup', function() {
            var elem = $(this);
            var val = elem.val() || 'Enter a degree you hold';
            elem.parents('.panel').find('.panel-title a').text(val.substringMore());
        }).trigger('keyup');
    });
</script>