<div class="page page-developer page-developer-admins">
    <h1 class="text-center">Manage Administrators</h1>
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
                        'placeholder' => 'Enter a name here',
                        'value' => prop('name', $params),
                    ),
                );
                echo bs_form_fields($field);
                ?>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="submit" class="btn btn-block btn-success" value="Search" />
                </div>
            </div>
            <div class="col-sm-2">
                <?php echo anchor('developer/admins/add', 'Add', array('class' => 'btn btn-info btn-block')); ?>
            </div>
        </div>
    </div>
    <?php if (!empty($users)): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <td>Name <?php echo anchor(order_by('name'), '&darr;'); ?> <?php echo anchor(order_by('name'), '&uarr;'); ?></td>
                    <td>Email <?php echo anchor(order_by('email'), '&darr;'); ?> <?php echo anchor(order_by('email', 'asc'), '&uarr;'); ?></td>
                    <td>Status <?php echo anchor(order_by('status'), '&darr;'); ?> <?php echo anchor(order_by('status', 'asc'), '&uarr;'); ?></td>
                    <td>Last updated <?php echo anchor(order_by('udate'), '&darr;'); ?> <?php echo anchor(order_by('udate', 'asc'), '&uarr;'); ?></td>
                    <td>Date created <?php echo anchor(order_by('cdate'), '&darr;'); ?> <?php echo anchor(order_by('cdate', 'asc'), '&uarr;'); ?></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                <input type="hidden" class="user_id" value="<?php echo $user->id; ?>"/>
                <td class="user_name"><?php echo $user->name; ?></td>
                <td class="user_email"><?php echo $user->email; ?></td>
                <td class="user_active"><?php echo $user->status; ?></td>
                <td class="user_udate"><?php echo $user->udate; ?></td>
                <td class="user_cdate"><?php echo $user->cdate; ?></td>
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
        <script type="text/javascript">
            $(function() {
                var theBtn, params, $options, $id, $item, $text;

                $options = {
                    activate: {
                        status: 0,
                        text: 'Activate'
                    },
                    deactivate: {
                        status: 1,
                        text: 'Deactivate'
                    }
                };

                // extend jQuery
                $.fn.extend({
                    invokeDropdown: function() {
                        theBtn = $(this);
                        $id = theBtn.parents('tr').find('.user_id').val();
                        $.ajax({
                            type: 'get',
                            cache: false,
                            data: {id: $id}, // check if the user ID is valid
                            url: '<?php echo site_url('/developer/admins/home/get_status'); ?>',
                            success: function($status) {
                                // if the URL returned false, show an alert and exit
                                if ($status === 'false') {
                                    alert('The user ID you entered is invalid.');
                                    return false;
                                } else {
                                    // reset the params
                                    params = [];

                                    // every status has a different set of menu items
                                    switch (parseInt($status)) {
                                        case 0:
                                            params.push(
                                                    $options.deactivate
                                                    );
                                            break;
                                        case 1:
                                            params.push(
                                                    $options.activate
                                                    );
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
                                                // invoke the appropriate modal based on $action
                                                // pass the status, and the user ID
                                                $.ajax({
                                                    type: 'post',
                                                    url: '<?php echo site_url('/developer/admins/home/update_status'); ?>',
                                                    data: {
                                                        id: $id,
                                                        status: $status
                                                    },
                                                    success: function(data) {
                                                        if (typeof data === 'string' && data === 'true') {
                                                            var status_friendly;
                                                            $('[class="user_id"][value="' + $id + '"]').fadeIn('slow', function() {
                                                                switch ($status) {
                                                                    case 0:
                                                                        status_friendly = 'Active';
                                                                        break;
                                                                    case 1:
                                                                        status_friendly = 'Deactivated';
                                                                }
                                                                $(this).nextAll('[class="user_active"]').text(status_friendly);
                                                            });
                                                        } else {
                                                            var msg = 'There were errors:\n\n';
                                                            $.each($.parseJSON(data).data, function(k, v) {
                                                                msg += '- ' + v + '\n';
                                                            });
                                                            alert(msg);
                                                        }
                                                    }
                                                });
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