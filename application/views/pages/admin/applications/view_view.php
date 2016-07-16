<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?php echo auto_typography($error); ?>
    </div>
<?php else: ?>
    <style>
        .tab-content {
            padding: 1em 0;
        }
    </style>
    <div class="page page-admin page-admin-applications page-admin-applications-view">
        <h2 class="text-center"><?php echo bs_glyph('list'); ?> Dossier: <?php echo $basic->studapp_basic_fname . ' ' . $basic->studapp_basic_lname; ?></h2>
        <hr />
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#basic" data-toggle="tab">Basic</a></li>
            <li><a href="#education" data-toggle="tab">Education</a></li>
            <li><a href="#documents" data-toggle="tab">Documents</a></li>
            <li><a href="#recommendations" data-toggle="tab">Recommendations</a></li>
            <li><a href="#essay" data-toggle="tab">Essay</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="basic">
                <table class="table table-striped">
                    <tr>
                        <td>Name</td>
                        <td><?php
                            echo
                            sprintf('%1$s. %2$s %3$s %4$s%5$s', ucfirst($basic->studapp_basic_title), $basic->studapp_basic_fname, $basic->studapp_basic_mname, $basic->studapp_basic_lname, $basic->studapp_basic_suffix ? ', ' . $basic->studapp_basic_suffix : null);
                            ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $user->user_email; ?></td>
                    </tr>
                    <tr>
                        <td>Profession</td>
                        <td><?php echo $basic->studapp_basic_profession; ?></td>
                    </tr>
                    <tr>
                        <td>Birth date</td>
                        <td><?php echo date_nice($basic->studapp_basic_bdate); ?></td>
                    </tr>
                    <tr>
                        <td>Citizenship</td>
                        <td><?php echo ucfirst($basic->studapp_basic_citizenship); ?></td>
                    </tr>
                    <tr>
                        <td>Mobile number</td>
                        <td><?php echo ucfirst($basic->studapp_basic_phone); ?></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><?php
                            echo ucfirst($basic->studapp_basic_address);
                            if ($basic->studapp_basic_address_2) {
                                echo '<br />' . $basic->studapp_basic_address_2;
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>ZIP</td>
                        <td><?php echo ucfirst($basic->studapp_basic_zip); ?></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><?php echo ucfirst($basic->studapp_basic_city); ?></td>
                    </tr>
                    <tr>
                        <td>Province/State</td>
                        <td><?php echo ucfirst($basic->studapp_basic_state); ?></td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td><?php echo country_code_to_country($basic->studapp_basic_country); ?></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane" id="education">
                <?php $i = 0; ?>
                <?php foreach ($education as $ed): ?>
                    <table class="table table-striped">
                        <thead>
                        <h3>Entry <?php echo++$i; ?></h3>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Concentration</td>
                                <td><?php echo $ed->concentration; ?></td>
                            </tr>
                            <tr>
                                <td>Degree type</td>
                                <td><?php echo ucfirst($ed->degree); ?></td>
                            </tr>
                            <tr>
                                <td>Institution</td>
                                <td><?php echo $ed->institution; ?></td>
                            </tr>
                            <tr>
                                <td>Admitted</td>
                                <td><?php echo date_nice($ed->admitted); ?></td>
                            </tr>
                            <tr>
                                <td>Graduated</td>
                                <td><?php echo date_nice($ed->graduated); ?></td>
                            </tr>
                            <?php if ($ed->student_id): ?>
                                <tr>
                                    <td>Student ID</td>
                                    <td><?php echo $ed->student_id; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($ed->gpa): ?>
                                <tr>
                                    <td>GPA</td>
                                    <td><?php echo $ed->gpa; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($ed->awards): ?>
                                <tr>
                                    <td>Awards</td>
                                    <td><?php echo $ed->awards; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($i !== count($education)): ?>
                        <hr />
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane" id="documents">
                <?php
                $docs = array(
                    'birth' => array(
                        'title' => 'Birth Certificate',
                        'metadata' => $documents_birth,
                    ),
                    'tor' => array(
                        'title' => 'Transcript of Records',
                        'metadata' => $documents_tor,
                    ),
                );
                if ($documents_employer) {
                    $docs['employer'] = array(
                        'title' => 'Employer Recommendation',
                        'metadata' => $documents_employer,
                    );
                }
                $i = 0;
                ?>
                <?php foreach ($docs as $doc_key => $doc_data): ?>
                    <h3><?php echo $doc_data['title']; ?></h3>
                    <div class="well">
                        <?php
                        $user_id = $user->user_id;
                        $action = bs_glyph('save') . ' Download ' . $doc_data['title'];
                        echo anchor_popup('admin/applications/view/document/' . $user_id . '/' . $doc_key, $action);
                        ?>
                    </div>
                    <?php if (++$i !== count($docs)): ?>
                        <hr />
                    <?php endif; ?>
                <?php endforeach;
                ?>
            </div>
            <div class="tab-pane" id="recommendations">
                <?php $recommendations = array(object_pop($recommendations_1), object_pop($recommendations_2)); ?>
                <?php $i = 0; ?>
                <?php foreach ($recommendations as $rec): ?>
                    <h3>Recommendation from <?php echo $rec->name; ?></h3>
                    <table class="table">
                        <tbody>
                            <tr class="success">
                                <th>Name</th>
                                <th>Position</th>
                                <th>Institution</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                            <tr>
                                <td><?php echo $rec->name; ?></td>
                                <td><?php echo $rec->position; ?></td>
                                <td><?php echo $rec->institution; ?></td>
                                <td><?php echo $rec->email; ?></td>
                                <td><?php echo $rec->phone; ?></td>
                            <tr>
                            </tr>
                        </tbody>
                        </tr>
                    </table>
                    <div class="well">
                        <?php echo auto_typography($rec->recommendation); ?>
                    </div>
                    <?php if ($i !== count($recommendations)): ?>
                        <hr />
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane" id="essay">
                <div class="well">
                    <?php echo auto_typography($essay); ?>
                </div>
            </div>
        </div>
    </ul>
    </div>
<?php endif; ?>