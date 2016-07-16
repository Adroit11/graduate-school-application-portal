<div class="page page-admin page-admin-applications page-admin-applications-home">
    <h1 class="text-center">Manage Applicants</h1>
    <hr />
    <p class="lead"><?php echo bs_glyph('th'); ?> Query generated a total of <?php echo!$total_rows ? 'nothing' : $total_rows . ' record/s'; ?></p>
    <style>
        .table > tbody > tr > td {
            vertical-align: middle;
        }
        .table > thead > tr > td {
            font-weight: bold;
        }
    </style>
    <?php echo form_open('', array('method' => 'get')); ?>
    <div class="basic-search">
        <div class="row">
            <div class="col-sm-7">
                <?php
                $field = array(
                    'type' => 'text',
                    'data' => array(
                        'name' => 'name',
                        'value' => isset($params['name']) && array_key_exists('name', $params) ? $params['name'] : null,
                    )
                );
                echo bs_form_fields($field);
                ?>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <button id="search" type="submit" class="btn btn-block btn-success"><?php echo bs_glyph('search', 'Search'); ?></button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <button type="button" id="advanced-search-toggle" class="btn btn-block btn-info" data-toggle="button"><?php echo bs_glyph('screenshot', 'Toggle Advanced Search'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="advanced-search" style="display:none">
        <div class="well">
            <div class="row">
                <div class="col-sm-3">
                    <?php echo heading('Application status', 4); ?>
                    <?php
                    $statuses = array(
                        array(
                            'key' => 'review',
                            'label' => 'Pending review',
                        ),
                        array(
                            'key' => 'test',
                            'label' => 'Examination scheduled',
                        ),
                        array(
                            'key' => 'test_reschedule',
                            'label' => 'Examination rescheduled',
                        ),
                        array(
                            'key' => 'test_fail',
                            'label' => 'Examination failed',
                        ),
                        array(
                            'key' => 'interview',
                            'label' => 'Pending interview',
                        ),
                        array(
                            'key' => 'interview_fail',
                            'label' => 'Interview failed',
                        ),
                        array(
                            'key' => 'interview_passed',
                            'label' => 'Interview passed',
                        ),
                        array(
                            'key' => 'application_declined',
                            'label' => 'Application declined',
                        ),
                        array(
                            'key' => 'withdraw',
                            'label' => 'Application withdrawn',
                        ),
                        array(
                            'key' => 'revision',
                            'label' => 'Revisions requested',
                        ),
                    );
                    $fields = array();
                    foreach ($statuses as $status) {
                        $fields[] = array(
                            'type' => 'checkbox',
                            'label' => $status['label'],
                            'data' => array(
                                'name' => 'status[]',
                                'value' => $status['key'],
                                'checked' => isset($params['status']) && in_array($status['key'], $params['status']) ? true : false,
                            ),
                        );
                    }
                    echo bs_form_fields($fields);
                    ?>
                    <div class="clearfix"></div>
                </div>
                <div class="col-sm-3">
                    <?php echo heading('Program', 4); ?>
                    <?php if (!$programs): ?>
                        <?php echo '<p>Nothing to filter</p>'; ?>
                        <?php
                    else:
                        $fields = array();
                        foreach ($programs as $program) {
                            $fields[] = array(
                                'type' => 'checkbox',
                                'label' => $program->program_title,
                                'data' => array(
                                    'name' => 'program[]',
                                    'value' => $program->program_id,
                                    'checked' => isset($params['program']) && in_array($program->program_id, $params['program']) ? true : false,
                                ),
                            );
                        }
                        echo bs_form_fields($fields);
                        ?>
                    <?php endif; ?>
                    <?php
                    echo heading('Program type', 4);
                    $types = array(
                        array(
                            'key' => 1,
                            'label' => 'Master\'s Degree',
                        ),
                        array(
                            'key' => 2,
                            'label' => 'Doctoral Degree',
                        ),
                    );
                    $fields = array();
                    foreach ($types as $type) {
                        $fields[] = array(
                            'type' => 'checkbox',
                            'label' => $type['label'],
                            'data' => array(
                                'name' => 'type[]',
                                'value' => $type['key'],
                                'checked' => isset($params['type']) && in_array($type['key'], $params['type']) ? true : false,
                            ),
                        );
                    }
                    echo bs_form_fields($fields);
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php echo heading('Gender', 4); ?>
                    <?php
                    $genders = array(
                        array(
                            'key' => 'mr',
                            'label' => 'Male',
                        ),
                        array(
                            'key' => 'ms',
                            'label' => 'Female',
                        ),
                    );
                    $fields = array();
                    foreach ($genders as $gender) {
                        $fields[] = array(
                            'type' => 'checkbox',
                            'label' => $gender['label'],
                            'data' => array(
                                'name' => 'gender[]',
                                'value' => $gender['key'],
                                'checked' => isset($params['gender']) && in_array($gender['key'], $params['gender']) ? true : false,
                            ),
                        );
                    }
                    echo bs_form_fields($fields);
                    ?>
                    <?php echo heading('Citizenship', 4); ?>
                    <?php
                    $citizenships = array(
                        array(
                            'key' => 'filipino',
                            'label' => 'Filipino',
                        ),
                        array(
                            'key' => 'foreign',
                            'label' => 'Foreign',
                        ),
                        array(
                            'key' => 'naturalized',
                            'label' => 'Naturalized',
                        ),
                    );
                    $fields = array();
                    foreach ($citizenships as $citizenship) {
                        $fields[] = array(
                            'type' => 'checkbox',
                            'label' => $citizenship['label'],
                            'data' => array(
                                'name' => 'citizenship[]',
                                'value' => $citizenship['key'],
                                'checked' => isset($params['citizenship']) && in_array($citizenship['key'], $params['citizenship']) ? true : false,
                            ),
                        );
                    }
                    echo bs_form_fields($fields);
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php echo heading('Additional filters', 4); ?>
                    <?php
                    $fields = array(
                        array(
                            'label' => 'Show results from',
                            'type' => 'select',
                            'data' => array(
                                'name' => 'date_applied',
                                'options' => array(
                                    '' => 'All time',
                                    'week' => 'Past week',
                                    'month' => 'Past month',
                                    'month_3' => 'Past three months',
                                    'month_6' => 'Past six months',
                                    'year' => 'Past year',
                                    'year_2' => 'Past two years',
                                ),
                                'selected' => isset($params['date_applied']) ? $params['date_applied'] : null,
                            ),
                        ),
                        array(
                            'label' => 'Show applicants residing in',
                            'type' => 'country',
                            'data' => array(
                                'name' => 'country',
                                'data-country' => isset($params['country']) ? $params['country'] : null,
                                'blank' => true
                            ),
                        ),
                        array(
                            'label' => 'Show applicants working as',
                            'type' => 'text',
                            'data' => array(
                                'name' => 'profession',
                                'value' => isset($params['profession']) ? $params['profession'] : null,
                                'placeholder' => 'Enter a job title',
                            ),
                        ),
                    );
                    echo bs_form_fields($fields);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var advancedSearch = $('#advanced-search-toggle').parents('.container').find('.advanced-search')

            if ($.cookie('applicants-advanced-search-on')) {
                // advancedSearch.finish().slideToggle();
            }

            $('#advanced-search-toggle').click(function() {
                advancedSearch.finish().slideToggle();
                if (advancedSearch.is(':visible')) {
                    $.cookie('applicants-advanced-search-on', true);
                } else {
                    $.removeCookie('applicants-advanced-search-on');
                }
            });
        });
    </script>
    <?php if (!empty($users)): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <td>Name <?php echo anchor(order_by('name'), '&darr;'); ?> <?php echo anchor(order_by('name', 'asc'), '&uarr;'); ?></td>
                    <td>Status <?php echo anchor(order_by('status'), '&darr;'); ?> <?php echo anchor(order_by('status', 'asc'), '&uarr;'); ?></td>
                    <td>Type <?php echo anchor(order_by('type'), '&darr;'); ?> <?php echo anchor(order_by('type', 'asc'), '&uarr;'); ?></td>
                    <td>Course <?php echo anchor(order_by('program'), '&darr;'); ?> <?php echo anchor(order_by('program', 'asc'), '&uarr;'); ?></td>
                    <td>Applied on <?php echo anchor(order_by('date'), '&darr;'); ?> <?php echo anchor(order_by('date', 'asc'), '&uarr;'); ?></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                <input type="hidden" class="studapp_id" value="<?php echo $user->id; ?>"/>
                <td class="studapp_name"><?php echo $user->name; ?></td>
                <td class="studapp_status"><?php echo $user->status; ?></td>
                <td class="studapp_type"><?php echo (int) $user->type === 1 ? 'MA' : 'PhD'; ?></td>
                <td class="studapp_course"><?php echo $user->program; ?></td>
                <td class="studapp_apply_date"><?php echo $user->date; ?></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            Select action <span class="caret"></span>
                        </button>
                    </div>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="results" class="text-center">
            <?php echo $pagination; ?>
            <script type="text/javascript">
                $('.pagination a').wrap($('<li />'));
                $('.pagination strong').wrap($('<li />').addClass('active').append($('<a />')));
            </script>
        </div>
        <div class="modal fade" id="theModal" tabIndex="-1" role="dialog" aria-labelledby="theModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="theModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <?php echo form_open('', array('role' => 'form')); ?>
                        <?php echo form_close(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="save" class="btn btn-success" data-status="">Update status & send email</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function() {
                var theModal = $('#theModal'), theBtn, theForm, params, $options, $id, $fieldID, $item, $text, $target, $url;

                // set the available modal elements
                var $elem = {
                    hidden: $('<input />').attr('type', 'hidden'),
                    text: $('<input />').attr('type', 'text'),
                    password: $('<input />').attr('type', 'password'),
                    textarea: $('<textarea />').attr('rows', 5),
                    button: $('<button />').attr('type', 'button')
                };

                // add essential classes to each elem
                $.each($elem, function(type, v) {
                    switch (type) {
                        case 'button':
                            $elem[type].addClass('btn btn-default');
                            break;
                        case 'label':
                            $elem[type].addClass('form-label');
                            break;
                        case 'text':
                        case 'textarea':
                        case 'password':
                            $elem[type].addClass('form-control');
                    }
                });

                // format (and spawn) a modal element
                var spawnElem = function(type, id, placeholder, lbl, val, atts) {
                    // cloning is important
                    var el = $elem[type].clone(true);

                    var defaultAtts = {
                        placeholder: placeholder ? placeholder : '',
                        id: id ? id : ''
                    };

                    // set the value
                    switch (type) {
                        case 'textarea':
                            el.text(val);
                            break;
                        default:
                            defaultAtts.value = val;
                    }

                    // set the atts
                    atts = $.extend(defaultAtts, atts);
                    el.attr(atts);

                    // add the wrap
                    if ($.inArray(type, ['hidden']) === -1)
                        el = $('<div />').addClass('form-group ').wrapInner(el);

                    // add the label
                    if (lbl)
                        el.prepend($('<label />').text(lbl));

                    // return the formatted jQuery object
                    return el;
                };

                $options = {
                    view: {
                        status: 'view',
                        text: 'View Details'
                    },
                    test: {
                        status: 'test',
                        text: 'Schedule Entrance Examination'
                    },
                    test_reschedule: {
                        status: 'test_reschedule',
                        text: 'Reschedule Entrance Examination'
                    },
                    test_fail: {
                        status: 'test_fail',
                        text: 'Decline (Failed Entrance Examination)'
                    },
                    interview: {
                        status: 'interview',
                        text: 'Schedule Interview',
                    },
                    interview_reschedule: {
                        status: 'interview_reschedule',
                        text: 'Reschedule Interview'
                    },
                    interview_pass: {
                        status: 'interview_pass',
                        text: 'Approve (Passed Interview)'
                    },
                    interview_fail: {
                        status: 'interview_fail',
                        text: 'Decline (Failed Interview)'
                    },
                    interview_decline: {
                        status: 'interview_decline',
                        text: 'Decline Application/Interview',
                    },
                    enroll: {
                        status: 'enroll',
                        text: 'Enroll (Requirements Complete)'
                    },
                    withdraw: {
                        status: 'withdraw',
                        text: 'Withdrew Application'
                    },
                    revision: {
                        status: 'revision',
                        text: 'Request Revision'
                    }
                };

                // extend jQuery
                $.fn.extend({
                    invokeDropdown: function() {
                        theBtn = $(this);
                        $id = theBtn.parents('tr').find('.studapp_id').val();

                        $.ajax({
                            type: 'get',
                            cache: false,
                            data: {id: $id}, // check if the user ID is valid
                            url: '<?php echo site_url('/admin/applications/home/get_status'); ?>',
                            success: function($status) {
                                // if the URL returned false, show an alert and exit
                                if (parseInt($status) === 0) {
                                    alert('The user ID you entered is invalid.');
                                    return false;
                                } else {
                                    // reset the params
                                    params = [];

                                    // set up defaults
                                    params.push($options.view);

                                    // every status has a different set of menu items
                                    switch ($status) {
                                        case 'view':
                                            break;
                                        case 'test':
                                        case 'test_reschedule':
                                            params.push(
                                                    $options.interview,
                                                    $options.test_reschedule,
                                                    $options.test_fail,
                                                    $options.revision,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'test_fail':
                                            params.push(
                                                    $options.test_reschedule,
                                                    $options.interview,
                                                    $options.revision,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'review':
                                        case 'interview_fail':
                                            params.push(
                                                    $options.test,
                                                    $options.interview,
                                                    $options.interview_decline,
                                                    $options.revision,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'interview':
                                            params.push(
                                                    $options.interview_pass,
                                                    $options.interview_fail,
                                                    $options.interview_decline,
                                                    $options.revision,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'interview_pass':
                                            params.push(
                                                    $options.enroll,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'interview_reschedule':
                                            params.push(
                                                    $options.interview_fail,
                                                    $options.interview_decline,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'interview_decline':
                                            params.push(
                                                    $options.interview,
                                                    $options.withdraw
                                                    );
                                            break;
                                        case 'enroll':
                                            params.push($options.withdraw);
                                            break;
                                        case 'withdraw':
                                            params.push($options.interview);
                                            break;
                                        case 'revision':
                                            params.push($options.test);
                                            params.push($options.interview);
                                            params.push($options.withdraw);
                                            break;
                                    }

                                    // the dropdown menu
                                    var theMenu = $('<ul />').addClass('dropdown-menu');

                                    // the params contain the links and text
                                    if (params && typeof params === 'object') {
                                        // build the menu items
                                        $.each(params, function(k, v) {
                                            // create the line item
                                            $item = $('<a />');
                                            // build the attributes
                                            $status = v.status;
                                            $text = v.text;

                                            // add attributes to the line item
                                            $item.text($text);
                                            // not really required, but this adds some uniqueness to the href property
                                            $item.attr('href', '#' + $status);
                                            // bind a function to an event (using the "on" principle)
                                            $item.on('click', function(e) {
                                                e.preventDefault();
                                                if (v.status === 'view') {
                                                    // if user clicks view, redirect to the view page
                                                    $url = '<?php echo site_url('admin/applications'); ?>' + '/view/index/' + $id;
                                                    window.open($url, 'Applicant Data', 'height=500,width=700');
                                                } else {
                                                    // invoke the appropriate modal based on $action
                                                    // pass the status, and the user ID
                                                    $item.invokeModal(v.status, $id);
                                                }
                                            });
                                            theMenu.append($('<li />').append($item));
                                        });

                                        // purge existing dropdown options (if there are any)
                                        theBtn.nextAll('ul').remove();
                                        // append the dropdown menu
                                        theBtn.after(theMenu);
                                    }
                                }
                            }
                        });
                    }, invokeModal: function($status, $id) {
                        var $user = $(this).parents('tr').find('.studapp_name').text();
                        var $subj, $msg, $date, $time;
                        var $title, $body;

                        // set the default fields
                        // create the body
                        $fieldID = spawnElem('hidden', 'id', null, null, $id);
                        $subj = spawnElem('text', 'subject', 'Enter a subject ', 'Subject');
                        $msg = spawnElem('textarea', 'message', 'Enter a message', 'Message');

                        $body = $fieldID
                                .add($subj)
                                .add($msg);

                        // elements are based on what status we're trying to set
                        switch ($status) {
                            case 'test':
                            case 'test_reschedule':
                                $title = 'Schedule/reschedule an entrance examination for ' + $user + '.';

                                $date = spawnElem('text', 'date', 'Date (E.g. Saturday 01/Feb/14)', 'Date');
                                $time = spawnElem('text', 'time', 'Time (E.g. 9:00 AM)', 'Time');

                                $body = $body.add($time).add($date);
                                break;
                            case 'interview':
                            case 'interview_reschedule':
                                $title = 'Schedule/reschedule an interview with ' + $user + '.';

                                $date = spawnElem('text', 'date', 'Date (E.g. Saturday 01/Feb/14)', 'Date');
                                $time = spawnElem('text', 'time', 'Time (E.g. 9:00 AM)', 'Time');

                                $body = $body.add($time).add($date);
                                break;
                            case 'interview_pass':
                                $title = 'Notify ' + $user + ' that he/she passed the interview.';
                                break;
                            case 'interview_fail':
                                $title = 'Notify ' + $user + ' that he/she failed the interview.';
                                break;
                            case 'interview_decline':
                                $title = 'Notify ' + $user + ' that he he/she is not eligible for an interview.';
                                break;
                            case 'enroll':
                                $title = 'Notify ' + $user + ' that he/she is now enrolled in the Graduate School.';
                                break;
                            case 'withdraw':
                                $title = 'Notify ' + $user + ' that you acknowledge withdrawal of his/her application.';
                                break;
                            case 'revision':
                                $title = 'Notify ' + $user + ' that he/she needs to revise her information.';
                                break;
                            default:
                                alert('Invalid action!');
                                return false;
                        }

                        // clear the modal first
                        theModal.setModal({
                            title: $title,
                            body: $body,
                            status: $status
                        });

                        theModal.modal('show');

                        theBtn = $('.modal').find('#save');
                        theBtn.on('click', function() {
                            theModal.saveModal($status, $id); // pass the status and user id
                        });
                    }, saveModal: function($status, $id) {
                        theForm = $(this).find('form');
                        var $data = {};

                        theForm.find('input[id], textarea[id]').filter(function() {
                            $fieldID = $(this).attr('id');
                            $data[$fieldID] = $(this).val();
                        });

                        // set the new status
                        $data['status_new'] = $status;

                        params = {
                            loadingText: 'Updating the DB & processing your message',
                            url: '<?php echo site_url('/admin/applications/home/update_status'); ?>',
                            data: $data,
                            success: function(data) {
                                theForm.formAlert(data);
                                // update the status column
                                if ($.parseJSON(data).type === 'success') {
                                    $.ajax({
                                        type: 'get',
                                        cache: 'false',
                                        url: '<?php echo site_url('/admin/applications/home/get_status_friendly'); ?>' + '/' + $status + '/echo',
                                        success: function(status_friendly) {
                                            $('[class="studapp_id"][value="' + $id + '"]').fadeIn('slow', function() {
                                                $(this).nextAll('[class="studapp_status"]').text(status_friendly);
                                            });
                                        }
                                    });
                                }
                            }
                        };
                        theForm.postmask(params);
                    }, setModal: function(part, content) {
                        $target = $(this);

                        if (typeof part === 'object') {
                            $.each(part, function(k, v) {
                                $target.setModal(k, v);
                            });
                        }

                        switch (part) {
                            case 'title':
                                $target.find('.modal-title').html(content);
                                break;
                            case 'body':
                                $target.find('.modal-body form').prevAll('*').remove();
                                $target.find('.modal-body form').html(content);
                                break;
                            case 'status':
                                $target.find('.modal-footer #save').attr('data-status', content);
                                break;
                            default:
                        }

                    }
                });
            });

            // call the dropdown
            $(function() {
                $('table [data-toggle="dropdown"]').click(function() {
                    $(this).invokeDropdown();
                });
            });
        </script>
    <?php else: ?>
        <div class="alert alert-info">
            No records found. Please try broadening your search.
        </div>
    <?php endif; ?>
</div>