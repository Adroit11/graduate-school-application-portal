<?php echo heading('Email Settings', 1, 'class="text-center"'); ?>
<hr />
<?php echo heading('Select an action', 2, 'class="text-center"'); ?>
<div class="well" style="max-width: 400px; margin: 0 auto">
    <div class="form-group">
        <?php
            echo anchor('developer/emails/smtp', bs_glyph('cog', 'Edit SMTP settings'), array('class' => 'btn btn-default btn-block btn-lg'));
        ?>
    </div>
    <div class="form-group">
        <?php
            echo anchor('developer/emails/templates', bs_glyph('list-alt', 'Manage email templates'), array('class' => 'btn btn-default btn-block btn-lg'));
        ?>
    </div>
</div>
<?php ?>