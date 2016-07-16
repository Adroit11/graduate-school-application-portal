<div class="page page-admin page-admin-course-offerings">
    <?php echo heading('Course Offerings', 1, 'class="text-center"'); ?>
    <hr />
    <?php if (!$records): ?>
        <div class="well">
            <p>No programs have been set up yet. Contact the developer for support.</p>
        </div>
    <?php else: ?>
        <?php foreach ($records as $record): ?>
            <?php echo heading($record->title, 3, 'class="text-center"'); ?>
            <div class="well">
                <?php echo form_open('', array('role' => 'form', 'id' => 'form-admin-course-offerings')); ?>
                <?php
                // create a placeholder if there are no courses
                $count = count((array) $record->courses);
                if ($count) {
                    $courses = $record->courses;
                } else {
                    $courses = array();
                    $courses[] = array(
                        'title' => '',
                        'type' => 1,
                        'parent' => $record->id,
                    );
                    $courses = array_to_object($courses);
                }
                ?>
                <div class="row">
                    <div class="col-sm-2">
                        <h5 class="text-center"><strong>Type</strong></h5>
                    </div>
                    <div class="col-sm-4">
                        <h5 class="text-center"><strong>Course Name</strong></h5>
                    </div>
                    <div class="col-sm-6">
                        <h5 class="text-center"><strong>Actions</strong></h5>
                    </div>
                </div>
                <hr />
                <?php foreach ($courses as $course): ?>
                    <div class="row">
                        <?php echo form_hidden('orig_program_title', $course->title); ?>
                        <?php echo form_hidden('orig_program_type', $course->type); ?>
                        <?php echo form_hidden('program_parent', $course->parent); ?>
                        <div class="col-sm-2">
                            <?php
                            $fields = array(
                                array(
                                    'type' => 'select',
                                    'data' => array(
                                        'options' => array(
                                            1 => 'MA',
                                            2 => 'PhD',
                                        ),
                                        'selected' => $course->type,
                                        'class' => 'program_type'
                                    ),
                                )
                            );
                            echo bs_form_fields($fields);
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            $fields = array(
                                array(
                                    'type' => 'text',
                                    'data' => array(
                                        'placeholder' => 'Enter a course offering',
                                        'value' => $course->title,
                                        'class' => 'program_title',
                                    ),
                                ),
                            );
                            echo bs_form_fields($fields);
                            ?>
                        </div>
                        <div class="col-sm-2">
                            <?php
                            $field = array(
                                'type' => 'button',
                                'data' => array(
                                    'value' => 'Save',
                                    'class' => 'save btn-success btn-sm btn-block',
                                ),
                            );
                            echo bs_form_fields($field);
                            ?>
                        </div>
                        <div class="col-sm-2">
                            <?php
                            $field = array(
                                'type' => 'button',
                                'data' => array(
                                    'value' => 'Delete',
                                    'class' => 'delete btn-danger btn-sm btn-block',
                                ),
                            );
                            echo bs_form_fields($field);
                            ?>
                        </div>
                        <div class="col-sm-2">
                            <?php
                            $field = array(
                                'type' => 'button',
                                'data' => array(
                                    'value' => 'New',
                                    'class' => 'clone btn-info btn-sm btn-block',
                                ),
                            );
                            echo bs_form_fields($field);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php echo form_close(); ?>
            </div>
            <hr />
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script type="text/javascript">
    $(function() {
        var theForm, theRow, theClone, theBtn;
        var btnClone = $('.clone');
        var btnDelete = $('.delete');
        var btnSave = $('.save');

        $('.btn').click(function() {
            theBtn = $(this);
            theRow = theBtn.parents('.row');
            theForm = theRow.parents('form');
            theClone = theRow.clone(true);
        });

        btnClone.click(function() {
            // remove row clone values
            theClone.find('input[type="text"], [name^="orig_"]').each(function() {
                $(this).val('');
            });
            // insert the new row
            theRow.after(theClone.hide().fadeIn('slow'));
        });

        btnSave.click(function() {
            theData = {
                program_title: theRow.find('.program_title').val(),
                program_type: theRow.find('.program_type').val(),
                program_parent: theRow.find('[name="program_parent"]').val(),
                orig_program_title: theRow.find('[name="orig_program_title"]').val(),
                orig_program_type: theRow.find('[name="orig_program_type"]').val()
            };
            params = {
                data: theData,
                url: '<?php echo site_url('admin/course_offerings/submit'); ?>',
                success: function(data) {
                    // update the orig_program_title if it's a success
                    if (typeof data === 'string')
                        theRow.find('[name="orig_program_title"]').val(theData.program_title);
                    theForm.formAlert(data, {att: 'class', parent: theRow});
                }
            };
            theForm.postmask(params);
        });

        btnDelete.click(function() {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('admin/course_offerings/delete'); ?>',
                data: {
                    program_title: theRow.find('.program_title').val()
                },
                success: function(data) {
                    console.log(data);
                    if (data === 'success') {
                        theRow.fadeOut('slow', function() {
                            $(this).remove();
                            // reload if there are no more rows (the column header is counted)
                            if (theForm.find('.row').length === 1) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(data);
                        location.reload();
                    }
                }
            });
        });
    });
</script>