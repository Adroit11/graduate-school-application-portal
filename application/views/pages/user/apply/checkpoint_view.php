<div class="page page-user page-user-apply page-user-apply-checkpoint">
    <?php
    // dummy $_GET
    $status = isset($_GET['status']) ? $_GET['status'] : $status;
    $msg = new stdClass();
    switch ($status):
        default:
        case 'review':
            $msg->icon = 'send';
            $msg->title = 'Application Pending Review';
            $msg->content = <<<EOT
Your application is currently pending review from the Graduate School office.
You will be notified via email for any updates on your progress.</a>
EOT;
        case 'test':
            $msg->icon = 'bell';
            $msg->title = 'Entrance Examination Scheduled';
            $msg->content = <<<EOT
You have been scheduled for an entrance examination at the University testing center.
Should you have any questions, contact the Graduate School Secretary immediately.
EOT;
            break;
        case 'test_reschedule':
            $msg->icon = 'bell';
            $msg->title = ' Entrance Examination Rescheduled';
            $msg->content = <<<EOT
You previously scheduled entrance examination has been moved to a different date.
Should you have any questions, contact the Graduate School Secretary immediately.
EOT;
            break;
        case 'test_fail':
            $msg->icon = 'exclamation-sign';
            $msg->title = 'Entrance Examination Failed';
            $msg->content = <<<EOT
We regret to send news that you have failed the entrance examination.
Should you have any questions/appeals, contact the Graduate School secretary immediately.
EOT;
            break;
        case 'interview':
            $msg->icon = 'bell';
            $msg->title = 'Interview Request';
            $msg->content = <<<EOT
You have been scheduled for an interview with a designated program coordinator.
Please check your inbox for pertinent details (schedule, requirements, etc.).
EOT;
            break;
        case 'interview_reschedule':
            $msg->icon = 'bell';
            $msg->title = 'Interview Rescheduled';
            $msg->content = <<<EOT
Your previously arranged interview with a designated program coordinator has been rescheduled.
Please check your inbox for pertinent details (schedule, requirements, etc.).                
EOT;
            break;
        case 'interview_pass':
            $msg->icon = 'ok';
            $msg->title = 'Interview Passed';
            $msg->content = <<<EOT
You passed the interview, Congratulations!
You may now coordinate with the Graduate School Secretary for more information.
EOT;
            break;
        case 'interview_fail':
            $msg->icon = 'exclamation-sign';
            $msg->title = 'Interview Failed';
            $msg->failed = true;
            $msg->content = <<<EOT
Unfortunately, you did not pass the interview with your designated program coordinator.
To appeal your case, please visit the Graduate School office.
EOT;
            break;
        case 'interview_decline':
            $msg->icon = 'ban-circle';
            $msg->title = 'Interview Denied';
            $msg->failed = true;
            $msg->content = <<<EOT
The Graduate School office has found that the information you sent is grossly lacking merit.
For this reason, we have cancelled your application.

To appeal your case, please do a personal visit to the Graduate School office.
EOT;
            break;
        case 'enroll':
            $msg->icon = 'flag';
            $msg->title = 'You Are Now Enrolled';
            $msg->content = <<<EOT
You are already enrolled at Holy Angel University's Graduate School department.
We wish the best of luck in all your academic endeavors.
EOT;
            break;
        case 'withdraw':
            $msg->icon = 'info-sign';
            $msg->title = 'Application Withdrawn';
            $msg->content = <<<EOT
We are sorry that things could not work out with your application.
We wish the best of luck in all your academic endeavors.
EOT;
            break;
    endswitch;
    ?>
    <?php echo heading(bs_glyph($msg->icon, $msg->title), 1, 'class="text-center"'); ?>
    <hr />
    <div class="alert alert-<?php echo isset($msg->failed) && $msg->failed ? 'danger' : 'success'; ?>" style="max-width: 600px; margin:0 auto">
        <?php echo autop($msg->content); ?>
    </div>
    <?php if ($last_email): ?>
        <hr />
        <h4 class="text-center">Last email from administrator:</h4>
        <div class="well" style="max-width: 600px; margin:0 auto">
            <?php echo autop($last_email); ?>
        </div>
    <?php endif; ?>
</div>