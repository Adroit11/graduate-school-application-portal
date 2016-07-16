<div class="page page-user-account page-user-account-notifications">
    <?php echo heading('Notifications'); ?>
    <hr />
    <p class="lead">What type/s of emails do you want to receive?</p>
    <?php echo form_open('', array('role' => 'form', 'id' => 'form-user-account-notifications', 'onsubmit' => 'return false')); ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="general">General notifications</label><br />
                <?php
                $data = array(
                    'id' => 'general',
                );
                echo form_checkbox($data, '', prop('general', $record));
                ?>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="admin">Admin messages</label><br />
                <?php
                $data = array(
                    'id' => 'admin',
                );
                echo form_checkbox($data, '', prop('admin', $record));
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="account">Account modifications</label><br />
                <?php
                $data = array(
                    'id' => 'account',
                );
                echo form_checkbox($data, '', prop('account', $record));
                ?>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="newsletters">Newsletters</label><br />
                <?php
                $data = array(
                    'id' => 'newsletters',
                );
                echo form_checkbox($data, '', prop('newsletters', $record));
                ?>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(function() {
            var theBtn = $('[type="checkbox"]');
            var theForm = theBtn.parents('form');
            var theData = {}, theAlert;
            theBtn.on('switch-change', function() {
                $(this).parents(theForm).find('[type="checkbox"]').filter(function() {
                    theData[$(this).attr('id')] = $(this).prop('checked') ? 1 : 0;
                });
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('user/account/notifications/submit'); ?>',
                    data: {
                        notifications: theData
                    },
                    success: function(data) {
                        theForm.formAlert(data);
                    },
                    error: function() {
                        theAlert = $('<div />').addClass('alert alert-danger').text('Your preferences were not saved. Please try toggling the buttons again.');
                        theForm.prevAll('.alert').remove();
                        theForm.before(theAlert.hide().fadeIn('slow'));
                    }
                });
            });
            theBtn.bootstrapSwitch('setSizeClass', 'switch-large');
            theBtn.bootstrapSwitch('setOnClass', 'success');
            theBtn.bootstrapSwitch('setOffClass', 'default');
        });
    </script>
</div>