<div class="page page-developer page-developer-pages page-developer-pages-programs">
    <?php echo heading('Programs Pages', 1, 'class="text-center"'); ?>
    <hr />
    <?php echo form_open('', array('role' => 'form')); ?>
    <div class="form-group panel-group" id="accordion">
        <?php
        $programs = array(
            'business' => array(
                'title' => 'Business',
            ),
            'education' => array(
                'title' => 'Education',
            ),
            'engineering_it' => array(
                'title' => 'Engineering & IT',
            ),
            'nursing' => array(
                'title' => 'Nursing',
            ),
        );

        // merge db records with $programs array
        $content_rgx = '/(.*)_content/';
        $course_rgx = '/(.*)_course_(\d+)/';
        $records = $records ? $records : array();
        foreach ($records as $key => $record) {
            if (preg_match($content_rgx, $key, $m)) {
                $programs[$m[1]]['content'] = $record;
            }
            if (preg_match($course_rgx, $key, $m)) {
                $programs[$m[1]]['courses'][$m[2]] = $record;
            }
        }
        ?>
        <?php $i = 0; ?>
        <?php foreach ($programs as $program_id => $program): ?>
            <div class="panel panel-success"<?php echo (++$i > 1) ? ' style="margin-top:25px"' : null; ?>>
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $program['title']; ?></h4>
                </div>
                <div class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        $field = array(
                            'type' => 'textarea',
                            'label' => 'Content',
                            'required' => true,
                            'data' => array(
                                'rows' => 10,
                                'data-id' => $program_id,
                                'name' => $program_id . '_content',
                                'value' => $program['content'],
                            ),
                        );
                        echo bs_form_fields($field);
                        ?>
                        <div class="form-group">
                            <label>Programs (<small>displayed as a list</small>)</label>
                        </div>
                        <?php
                        // set a placeholder
                        if (!isset($program['courses'])) {
                            $program['courses'][0] = '';
                        }
                        ?>
                        <?php foreach ($program['courses'] as $course): ?> 
                            <div class="row">
                                <div class="col-sm-8">
                                    <?php
                                    $field = array(
                                        'type' => 'text',
                                        'required' => true,
                                        'data' => array(
                                            'data-id' => $program_id,
                                            'name' => $program_id . '_course',
                                            'value' => $course,
                                        ),
                                    );
                                    echo bs_form_fields($field);
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-clone btn-info btn-block">
                                        <?php echo bs_glyph('plus'); ?>
                                    </button>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-remove btn-danger btn-block">
                                        <?php echo bs_glyph('minus'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
            var theForm, theRow;
            var elem, content, course, clone, data, dataID, idx;

            btnClone.click(function() {
                elem = $(this);
                // get the first row
                theRow = elem.parents('.row:eq(0)');
                clone = theRow.clone(true);
                clone
                        .find('input[type="text"]').val('')
                        .end()
                        .find('.form-group').removeClass('has-error')
                        .end()
                        ;
                theRow.after(clone.fadeIn('slow'));
            });

            btnRemove.click(function() {
                elem = $(this);
                theRow = $(this).parents('.panel').find('.row');
                // prevent deletion if there's only one row left
                if (theRow.length === 1) {
                    alert('Deletion limit reached.');
                    return;
                }
                // get the first row
                theRow = elem.parents('.row:eq(0)');
                theRow.fadeOut('slow', function() {
                    theRow.remove();
                });
            });

            btnSave.click(function() {
                data = {};
                idx = 0;

                elem = $(this);
                theForm = elem.parents('form');

                // find elements that have a name attr ending in '_program'
                theForm.find('textarea[name$="_content"]').each(function() {
                    idx++;
                    content = $(this);
                    dataID = content.attr('data-id');

                    // give a unique key to every field
                    data[dataID + '_content_' + idx] = content.val();
                });

                // find elements that have a name attr ending in '_course'
                theForm.find('input[name$="_course"]').each(function() {
                    idx++;
                    course = $(this);
                    dataID = course.attr('data-id');

                    // give a unique key to every field
                    data[dataID + '_course_' + idx] = course.val();
                });

                var params = {
                    url: '<?php echo site_url('developer/pages/programs/submit'); ?>',
                    data: data,
                    // right now, indexed won't work, as we're using the "name"
                    // att instead of the "class" att
                    success: function(data) {
                        theForm.formAlert(data, {indexed: true});
                    }
                };
                theForm.postmask(params);
            });
        });
    </script>
</div>