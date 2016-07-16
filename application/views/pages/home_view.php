<div id="home-page-carousel" class="carousel slide" data-ride="carousel" style="display: none">
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var urls = [
            '<?php echo site_url('img/slider/hau-slider-1.jpg'); ?>',
            '<?php echo site_url('img/slider/hau-slider-2.jpg'); ?>',
            '<?php echo site_url('img/slider/hau-slider-3.jpg'); ?>'
        ];

        if (!urls.length)
            return;

        var loadingImg = $('<img />').attr({
        });

        var slider = $('#home-page-carousel');

        // on-image controls
        var sliderIndicators = $('<ol />').addClass('carousel-indicators').appendTo(slider);

        // images
        var sliderInner = $('<div />').addClass('carousel-inner').appendTo(slider);

        // left and right controls
        var sliderControlLeft = $('<a />').addClass('left carousel-control').attr({
            'href': '#' + $(slider).attr('id'),
            'data-slide': 'prev'
        }).append($('<span />')
                .addClass('glyphicon glyphicon-chevron-left')).appendTo(slider);

        var sliderControlRight = $('<a />').addClass('right carousel-control').attr({
            'href': '#' + $(slider).attr('id'),
            'data-slide': 'next'
        }).append($('<span />').addClass('glyphicon glyphicon-chevron-right')).appendTo(slider);

        $.each(urls, function(k) {
            $('<li />')
                    .attr({
                'data-target': '#' + $(slider).attr('id'),
                'data-slide-to': k,
                'class': (k === 0) ? 'active' : null
            })
                    .appendTo(sliderIndicators);

            var sliderItem = $('<div />')
                    .attr({
                'class': (k === 0) ? 'active' : null
            })
                    .addClass('item').appendTo(sliderInner)

            var sliderItemPlaceholder =
                    $('<div/>')
                    .css({
                'background-color': '#ccc',
                'width': 960,
                'height': 400
            })
                    .addClass('carousel-missing').appendTo(sliderItem);

            var sliderItemPlaceholderImg =
                    $('<img />')
                    .attr({
                'src': 'img/loading.gif'
            })
                    .css({
                'position': 'absolute',
                'top': (sliderItemPlaceholder.outerHeight() / 2) - 32,
                'left': ($('.container').outerWidth() / 2) - 32
            }).appendTo(sliderItemPlaceholder);
        });

        slider
                .css({
            'margin-bottom': '20px'
        })
                .slideToggle('slow');

        $.ajaxSetup({
            'type': 'GET'
        });

        $.each(urls, function(k, v) {
            $.ajax({
                'url': v,
                'success': function(msg) {
                    console.log(msg);
                    slider.find('.item:eq(' + k + ')')
                            .find('img').fadeOut('slow', function() {
                        $(this).parent()
                                .append($('<img />').attr('src', v).hide().fadeIn('slow'));
                    })
                },
            });
        })
    });
</script>

<div class="jumbotron">
    <h1><?php echo prop('jumbotron_lead', $content); ?></h1>
    <p class="lead"><?php echo prop('jumbotron_cta', $content); ?></p>
    <?php echo anchor('register', '<button type="button" class="btn btn-primary btn-lg">Apply now</button>'); ?>
</div>
<hr />

<div class="explain">
    <div class="row">
        <div class="col-sm-8">
            <div class="spiel">
                <h1><?php echo prop('main_title', $content); ?></h1>
                <?php echo auto_typography(prop('main_content', $content)); ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="announcements">
                <h2 class="text-center"><?php echo $content->sidebar_title; ?></h2>
            </div>
            <script type="text/javascript">
                $(document).on('ready resize', function() {
                    var target = $('.announcements');
                    var loading = $('<img />')
                            .attr({
                        'src': 'img/loading.gif',
                        'style': 'margin-top:' + (target.parents('.row').outerHeight() / 2 - 15) + 'px;margin-left:' + (target.outerWidth() / 2 - 15) + 'px'
                    })
                            .appendTo(target);
                    target.ready(function() {
                        $.ajax({
                            type: 'GET',
                            url: 'home/calendar',
                            dataType: 'xml',
                            success: function(xml) {
                                loading.hide();
                                var listGroup = ($('<div />').attr({'class': 'list-group'}));
                                var itemDOM, itemTitle, itemContent, itemStart, itemEnd;
                                var dateRgx = /^(\d{4}\-\d{1,2}\-\d{1,2})/gm;
                                $(xml).find('entry').each(function() {
                                    itemDOM = $(this)[0],
                                            itemTitle = $(this).find('title').text();
                                    itemContent = $(this).find('content').text();
                                    itemStart = itemDOM.getElementsByTagNameNS('http://schemas.google.com/g/2005', 'when')[0].getAttribute('startTime')
                                            .match(dateRgx);
                                    itemEnd = itemDOM.getElementsByTagNameNS('http://schemas.google.com/g/2005', 'when')[0].getAttribute('endTime')
                                            .match(dateRgx);
                                    $('<a />')
                                            .attr(
                                            {
                                                'class': 'list-group-item',
                                                'style': 'cursor:pointer',
                                                'data-toggle': 'modal',
                                                'data-dynamic': 'true',
                                                'data-title': itemTitle + ' | (' + itemStart + ' to ' + itemEnd + ')',
                                                'data-content': itemContent
                                            })
                                            .on('click', function() {
                                        modalHelper.create($(this));
                                    })
                                            .append($('<h4 />').attr({
                                        'class': 'list-group-item-heading'
                                    }).text(itemTitle))
                                            .append($('<p />').attr({
                                        'class': 'list-group-item-text'
                                    }).text(itemContent.substr(0, 160))
                                            .append($('<span />').attr({'style': 'font-size: smaller; color: #2F97E8'}).text(' (Learn more)'))).appendTo(listGroup);
                                });
                                target.append(listGroup);
                            },
                            error: function(xhr, status) {
                                loading.hide();
                                target.append($('<div />').attr({'class': 'alert alert-info'}).text('Google\'s Calendar API has gone bonkers. Try again later.'));
                            }
                        });
                    });
                })
            </script>
        </div>
    </div>
    <hr />
</div>

<div class="marketing">
    <h2>Why Apply Online?</h2>
    <div class="row">
        <div class="col-sm-4">
            <?php echo img('img/frontend/home/marketing_convenient.png'); ?>
            <h3><?php echo prop('trifecta_title_1', $content); ?></h3>
            <?php echo auto_typography(prop('trifecta_content_1', $content)); ?>
        </div>
        <div class="col-sm-4">
            <?php echo img('img/frontend/home/marketing_secure.png'); ?>
            <h3><?php echo prop('trifecta_title_2', $content); ?></h3>
            <?php echo auto_typography(prop('trifecta_content_2', $content)); ?>
        </div>
        <div class="col-sm-4">
            <?php echo img('img/frontend/home/marketing_responsive_design.png'); ?>
            <h3><?php echo prop('trifecta_title_3', $content); ?></h3>
            <?php echo auto_typography(prop('trifecta_content_3', $content)); ?>
        </div>
    </div>
</div>