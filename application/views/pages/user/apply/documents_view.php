<div class="page-user page-user-apply page-user-documents">
    <?php echo heading('Electronic Documents'); ?>
    <hr />
    <p class="lead">Please upload <strong>only</strong> authenticated and (optionally) scanned documents.</p>
    <?php foreach ($alerts as $alert): ?>
        <?php if (is_array($alert['data'])): ?>
            <div class="alert alert-danger">
                <?php
                if (isset($alert['file'])) {
                    echo sprintf('<p>There were errors with the file <em>%1$s</em>:</p>', $alert['file']);
                } else {
                    echo '<p>There were errors:</p>';
                }
                foreach ($alert['data'] as $err) {
                    echo sprintf('<p>%1$s %2$s</p>', bs_glyph('chevron-right'), $err);
                }
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <p><?php echo $alert['data']; ?></p>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php echo form_open_multipart(current_url() . '/submit'); ?>
    <div class="alert alert-info">
        <p><strong>Restrictions per document:</strong></p><p>
            <?php
            $restrictions = array(
                'File size limit: 2 megabytes (MB)',
                'Allowed file extensions/types: DOC, DOCX, PDF (recommended), and ODF',
                'Employer recommendation is optional'
            );
            echo ul($restrictions);
            ?></p>
    </div>
    <?php
    $fields = array(
        array(
            'type' => 'upload',
            'label' => 'Birth Certificate',
            'data' => array(
                'name' => 'birth',
            )
        ),
        array(
            'type' => 'upload',
            'label' => 'Transcript of Records (from last school attended)',
            'data' => array(
                'name' => 'tor',
            )
        ),
        array(
            'type' => 'upload',
            'label' => 'Letter of Recommendation (from employer)',
            'data' => array(
                'name' => 'employer',
            )
        ),
    );

    foreach ($fields as $field) {
        echo '<div class="well">';
        if (element(($name = $field['data']['name']), $records)) {
            echo '<div class="form-group">';
            echo bs_glyph('thumbs-up', sprintf('<label>%1$s</label><br />', $field['label']), true);
            echo anchor_popup('user/apply/documents/download/' . $name, bs_glyph('download-alt', 'View your recent submission', true));
            echo ' or ' . anchor('user/apply/documents/delete/' . $name, bs_glyph('trash', 'Delete it', true));
            echo '</div>';
        } else {
            echo bs_form_fields($field);
        }
        echo '</div><hr />';
    }
    ?>
    <?php
    $field = array(
        'type' => 'submit',
        'data' => array(
            'name' => 'upload',
            'value' => 'Upload documents',
            'class' => 'btn-success btn-block btn-lg'
        )
    );
    echo bs_form_fields($field);
    ?>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theError = $('.alert-danger');
            if (theError.length) {
                $.scrollTo(theError, {
                    duration: 'slow',
                    offset: {
                        top: -$('.navbar').outerHeight()
                    }
                });
            }
        });
    </script>
</div>