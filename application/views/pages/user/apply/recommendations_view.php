<?php echo heading('Recommendations'); ?>
<hr />
<p class="lead">What do your instructors think about you?</p>
<div class="alert alert-info">
    <p><?php echo bs_fa('users'); ?> Enter details of <strong>two (2) previous instructors</strong>. They will confirm your eligibility via email.</p>
    <hr />
    <p><strong>Read this</strong> before you proceed:</p>
    <ol>
        <li>Inform both your professors first about your application.</li>
        <li>Clicking the "Save" button will automatically dispatch an email.</li>
        <li>Make sure the email is correct. This system will only send once for each entry.</li>
        <li>Entering a new email will invalidate the link sent to your previous instructor (assuming no recommendation has been given yet).</li>
        <li>Once an instructor responds, you won't be able to edit his/her entry again.</li>
    </ol>
</div>
<?php $prof_count = 2; ?>
<?php for ($i = 1; $i <= $prof_count; $i++): ?>
    <?php
    $records = (array) $records;
    $record = isset($records[$i]) ? $records[$i] : array();
    $recommendation = prop('recommendation', $record);
    ?>
    <?php if ($recommendation): ?>
        <div class="alert alert-success">
            <?php echo bs_glyph('thumbs-up'); ?> <strong>Update:</strong> Recommendation received from your instructor, <strong><?php echo prop('name', $record); ?></strong>.
        </div>
    <?php else: ?>
        <div class="panel-group" id="accordion">
            <div data-index="<?php echo $i; ?>" class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-parent="#accordion" style="cursor:pointer"></a>
                    </h4><!--.panel-title-->
                </div><!--.panel-heading-->
                <div class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="well"><strong>Status</strong>: Awaiting recommendation<small>
                                (<a href="#" onclick="javascript:location.reload()">reload this page</a> to update)</small></div>
                        <?php
                        echo form_open('', array(
                            'role' => 'form',
                            'onsubmit' => 'return false',
                            'id' => sprintf('form-user-apply-recommendations-%1$s', $i)
                        ));
                        ?>
                        <input type="hidden" id="index[<?php echo $i; ?>]" value="<?php echo $i; ?>" />
                        <?php
                        $fields = array(
                            array(
                                'label' => 'Name',
                                'required' => true,
                                'type' => 'text',
                                'data' => array(
                                    'id' => sprintf('name[%1$s]', $i),
                                    'value' => prop('name', $record),
                                )
                            ),
                            array(
                                'label' => 'Position',
                                'required' => true,
                                'type' => 'text',
                                'data' => array(
                                    'id' => sprintf('position[%1$s]', $i),
                                    'value' => prop('position', $record),
                                )
                            ),
                            array(
                                'label' => 'Institution',
                                'required' => true,
                                'type' => 'text',
                                'data' => array(
                                    'id' => sprintf('institution[%1$s]', $i),
                                    'value' => prop('institution', $record),
                                )
                            ),
                            array(
                                'label' => 'Email address',
                                'required' => true,
                                'type' => 'text',
                                'data' => array(
                                    'id' => sprintf('email[%1$s]', $i),
                                    'value' => prop('email', $record),
                                )
                            ),
                            array(
                                'label' => 'Phone',
                                'type' => 'phone',
                                'required' => true,
                                'data' => array(
                                    'id' => sprintf('phone[%1$s]', $i),
                                    'data-format' => '+dd (ddd) ddd-dddd',
                                    'number' => prop('phone', $record),
                                    'value' => prop('phone', $record),
                                ),
                            ),
                            array(
                                'type' => 'button',
                                'data' => array(
                                    'class' => 'save btn btn-block btn-success btn-lg',
                                    'value' => 'Save & request recommendation',
                                ),
                            ),
                        );
                        echo bs_form_fields($fields);
                        ?>
                        <?php echo form_close(); ?>
                    </div><!--.panel-body-->
                </div><!--collapse_%1$s-->
            </div><!--.panel-->
        </div><!--.panel-group-->
    <?php endif; ?>
    <?php echo ($i != $prof_count) ? '<hr />' : null; ?>
<?php endfor; ?>
<script type="text/javascript">
                                    $(function() {
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
                                                console.log(theData);
                                            });
                                            var params = {
                                                url: '<?php echo site_url('user/apply/recommendations/submit'); ?>',
                                                data: theData,
                                                success: function(data) {
                                                    theForm.formAlert(data);
                                                }
                                            };
                                            theForm.postmask(params);
                                        });
                                    });

                                    $(function() {
                                        var panel = $('.panel');
                                        var panelTitle = panel.find('input[id^="name"]');

                                        panelTitle.on('keyup', function() {
                                            var elem = $(this);
                                            var val = elem.val() || 'Enter a professor name';
                                            elem.parents('.panel').find('.panel-title a').text(val.substringMore());
                                        }).trigger('keyup');
                                    });
</script>