<?php echo heading('Programs Page', 1, 'class="text-center"'); ?>
<hr />
<?php echo form_open('', array('role' => 'form', 'id' => 'form-programs')); ?>
<?php
$programs = array(
    array(
        'title' => 'Business',
        'id' => 'business'
    ),
    array(
        'title' => 'Education',
        'id' => 'education'
    ),
    array(
        'title' => 'Engineering & IT',
        'id' => 'engineering_it'
    ),
    array(
        'title' => 'Nursing',
        'id' => 'nursing'
    ),
);
?>
<?php
for ($i = 0; $i < ($program_count = count($programs)); $i++):
    ?>
    <div class="well">
        <?php echo heading($programs[$i]['title'], 2); ?>
        <?php
        $fields = array(
            array(
                'type' => 'text',
                'label' => 'Title',
                'required' => true,
                'data' => array(
                    'id' => $programs[$i]['id'] . '_title',
                ),
            ),
            array(
                'type' => 'textarea',
                'label' => 'Content',
                'required' => true,
                'data' => array(
                    'id' => $programs[$i]['id'] . '_content',
                    'rows' => 10,
                ),
            ),
        );
        echo bs_form_fields($fields);
        ?>
        <?php echo heading('Course offerings', 3, 'class="text-center"'); ?>
        <div class="form-group panel-group" id="accordion">
            <div data-index="1" class="panel panel-info">
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
                                'label' => 'Program',
                                'required' => true,
                                'data' => array(
                                    'id' => sprintf('[%1$s_course_title][1]', $programs[$i]['id']),
                                ),
                            ),
                            array(
                                'type' => 'textarea',
                                'label' => 'Description',
                                'required' => true,
                                'data' => array(
                                    'id' => sprintf('[%1$s_course_desc][1]', $programs[$i]['id']),
                                    'rows' => 5,
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
        </div>
        <?php if ($i < $program_count - 1): ?>
            <hr />
        <?php endif; ?>
    </div>
<?php endfor; ?>

<?php
$field = array(
    'type' => 'button',
    'data' => array(
        'value' => 'Save',
        'class' => 'btn btn-success btn-lg btn-block'
    ),
);
echo bs_form_fields($field);
?>
<?php echo form_close(); ?>
<script type="text/javascript">
    
    (function($) {
        var panel = $('.panel');
        var panelTitle = panel.find('input[type="text"]');
            
        panelTitle.on('keyup', function() {
            var elem = $(this);
            var val = elem.val() || 'Enter a course offering title';
            elem.parents('.panel').find('.panel-title a').text(val.substringMore());
        });
        
        panelTitle.trigger('keyup');
    })(jQuery);
    
    (function($) {
        var elem;
        $('.btn-clone').click(function() {
            elem = $(this);
            elem.clonePanel({limit: 10});
        });
        
        $('.btn-remove').click(function() {
            elem = $(this);
            elem.removePanel();
        })
    })(jQuery);
</script>