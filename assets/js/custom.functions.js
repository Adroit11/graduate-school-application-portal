String.prototype.format = function() {
    var formatted = this;
    for (var arg in arguments) {
        formatted = formatted.replace("{" + arg + "}", arguments[arg]);
    }
    return formatted;
};

String.prototype.substringMore = function(max, append) {
    var val = this;

    var params = {
        max: max || 50,
        append: append || '...'
    };

    if (val.length > params.max) {
        val = val.substring(0, params.max) + params.append;
    }

    return val;
};

// check if string is valid json
$(function() {
    $.extend({
        isJSON: function(str) {
            try {
                $.parseJSON(str);
            } catch (e) {
                return false;
            }
            return true;
        }
    });
});

// extended functions
(function($) {
    $.fn.extend({
        postmask: function(params) {
            var formObj = $(this);
            var paramsDefaults = {
                loadingText: 'Saving...',
                url: document.location.hostname,
                data: {},
                success: function() {
                    console.log($(this).attr('id') + ' operation success');
                },
                error: function() {
                    console.log($(this).attr('id') + ' operation error');
                }
            };

            params = $.extend(paramsDefaults, params);

            $.ajaxSetup({
                'beforeSend': function() {
                    formObj.find(params.invoker).attr('data-loading-text', params.loadingText).button('loading');
                    formObj.mask(params.loadingText);
                },
                'complete': function() {
                    formObj.find(params.invoker).button('reset');
                    formObj.unmask();
                }
            });
            $.post(params.url, params.data, function(result) {
                params.success(result);
            });
        }
    });
})(jQuery);

function getURLParameter(name) {
    return decodeURIComponent(
            (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
            );
}

function closeParent(elem, target) {
    target = (typeof target === 'undefined') ? $(elem).parent().prop('tagName') : target;
    console.log(target);
    $(elem).parents(target).fadeOut('fast');
}

// HANDLE FORM AJAX RESULTS
(function($) {
    var theForm, theTarget, theAlert, field, line, elem, defaults, matches;

    var scrollToAlert = function() {
        var nav = $('.navbar-header');
        if ($.scrollTo) {
            $.scrollTo(theAlert, {
                offset: {
                    top: -(nav.outerHeight())
                },
                duration: 'slow'
            });
        }
    };

    var formInit = function(elem) {
        theForm = elem;
    };

    var elem = function(elemName, att) {
        switch (att) {
            case 'class':
                return $('.' + elemName);
                break;
            case 'name':
                return $('[name="' + elemName + '"]');
                break;
            default:
                return $('#' + elemName);
        }
    };

    $.fn.extend({
        formAlert: function(data, params) {
            theForm = $(this);

            if ($.isJSON(data)) {
                data = $.parseJSON(data);
                if (data.type === 'error') {
                    theForm.formErrors(data.data, params);
                } else if (data.type === 'success') {
                    theForm.formSuccess(data.data, params);
                }
            } else {
                alert('There was an unexpected error. Please try again.');
            }

        },
        removeAlerts: function() {
            $(this).find('.has-error')
                    .removeClass('has-error').end().
                    prevAll('.alert').fadeOut('fast').remove();
        },
        formErrors: function(errors, params) {
            theForm = $(this);

            defaults = {
                att: 'id',
                parent: theForm,
                indexed: false
            };
            params = $.extend(defaults, params);

            theForm.removeAlerts();

            theAlert = $('<div />').addClass('alert alert-danger');
            $.each(errors, function(k, v) {
                // auto-infer if we're doing indices
                if (params.indexed) {
                    matches = k.match(/^(.*)\_(\d+)$/) || [];
                    if (matches.length === 3) {
                        theForm.find('.' + matches[1]).eq(matches[2] - 1).parents('.form-group').addClass('has-error');
                    }
                } else {
                    // colorize the invalid fields
                    field = elem(k, params.att);
                    params.parent.find(field).parents('.form-group').addClass('has-error');
                }

                // build the alert box
                line = $('<p />').html('<span class="glyphicon glyphicon-chevron-right"></span> ' + v);
                theAlert.append(line);
            });

            // show the alert box
            theForm.before(theAlert.hide().fadeIn('slow'));

            scrollToAlert();
        },
        formSuccess: function(data, params) {
            theForm = $(this);

            theAlert = $('<div />').addClass('alert');

            defaults = {
                hideForm: false
            };
            params = $.extend(defaults, params);

            theForm.removeAlerts();

            theAlert = $('<div />').addClass('alert alert-success');
            $.each(data, function(k, v) {
                // build the alert box
                line = $('<p />').html('<span class="glyphicon glyphicon-ok"></span> ' + v);
                theAlert.append(line);
            });

            // show the alert box
            theForm.before(theAlert.hide().fadeIn('slow'));

            // remove the form (if specified)
            if (params.hide_form) {
                theForm.fadeOut('fast');
            }

            scrollToAlert();
        }
    });
}(jQuery));

var modalHelper = (function() {
    var elem = null;

    return {
        create: function(elem) {
            var modalElem = $('<div class="modal fade" tabindex="-1" role="dialog"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

            var modalTitle, modalBody, modalFooter;
            modalTitle = (elem.attr('data-title')) ? this.createTitle(elem.attr('data-title')) : '';
            if (elem.attr('href')) {
                modalBody = this.createIframe({
                    height: elem.attr('height') || Math.floor($(window).height() * .70) + 'px',
                    width: elem.attr('width') || '100%',
                    src: elem.attr('href')
                });
            } else {
                modalBody = this.createBody({
                    content: elem.attr('data-content') || 'No content specified.'
                });
            }
            if (elem.attr('data-footer')) {
                modalBody = this.createBody({
                    content: elem.attr('data-footer') || 'No content specified.'
                });
            }
            modalFooter = (elem.attr('data-footer')) ? elem.attr('data-footer') : $('<button />').attr({
                'type': 'button',
                'class': 'btn btn-default',
                'data-dismiss': 'modal'
            }).text('Close');
            modalFooter = this.createFooter(modalFooter);

            modalElem.find('.modal-content').append(modalTitle).append(modalBody).append(modalFooter);
            modalElem.modal();
        },
        createTitle: function(content) {
            return $('<div />')
                    .attr({
                'class': 'modal-header'
            })
                    .append(
                    $('<button />').attr({
                'type': 'button',
                'class': 'close',
                'data-dismiss': 'modal',
                'aria-hidden': 'true'
            }).html('&times;')
                    )
                    .append(
                    $('<h4 />').append(content)
                    )
        },
        createIframe: function(prop) {
            return $('<div class="modal-body"><iframe src="' + prop.src + '" height="' + prop.height + '" width="' + prop.width + '"></iframe></div>');
        },
        createBody: function(prop) {
            return $('<div class="modal-body">' + prop.content + '</div>');
        },
        createFooter: function(content) {
            return $('<div/>').attr({
                'class': 'modal-footer'
            }).append(content);
        }
    }
}());

$(document).ready(function() {
    $('[data-toggle="modal"]').click(function(e) {
        e.preventDefault();
        if ($(this).attr('data-dynamic'))
            modalHelper.create($(this));
    });
});

// CLONE AND REMOVE PANELS
(function($) {
    var thePanel, newPanel, panelCount, theAccordion, indices, dataIndex, freeIndex = 0, currentNum, detachedElem;
    var defaults, i, elem, val, match;

    $.fn.extend({
        removePanel: function(params, callback) {
            thePanel = $(this).parents('.panel');
            theAccordion = thePanel.parents('.panel-group');
            panelCount = theAccordion.find('.panel').length;
            detachedElem = false;

            defaults = {
                limit: 1,
                limitMsg: 'Deletion limit reached.',
                panelID: 'data-index'
            };

            params = $.extend(defaults, params);

            if (panelCount > params.limit) {
                thePanel.fadeOut('slow', function() {
                    thePanel.prev('hr').remove();
                    if (thePanel.index() === 0) {
                        thePanel.next('hr').remove();
                    }
                    detachedElem = thePanel.detach();
                })
            } else {
                alert(params.limitMsg);
                return;
            }

            if (typeof callback === 'function') {
                callback(thePanel.attr(params.panelID), detachedElem);
            }
        },
        clonePanel: function(params, callback) {
            thePanel = $(this).parents('.panel');

            defaults = {
                limit: 10,
                limitMsg: 'Adding limit reached.',
                panelIdentifier: 'data-index',
                regex: /^(.*)\[(\d)+\]$/,
                matchlength: 3,
                regexAtts: ['name', 'id', 'for'],
                regexElems: 'div, textarea, select, input, label'
            };

            params = $.extend(defaults, params);

            // start with an empty indices array
            indices = [];
            // check for existing data-index values and populate indices array
            theAccordion = thePanel.parents('.panel-group');
            theAccordion
                    .find('.panel')
                    .filter(function() {
                dataIndex = parseInt($(this).attr(params.panelIdentifier));
                indices.push(parseInt(dataIndex));
            });

            if (indices.length >= params.limit) {
                alert(params.limitMsg);
                return;
            }

            indices.sort(function(a, b) {
                if (a < b)
                    return -1;
                if (a > b)
                    return 1;
                return 0;
            });

            for (i in indices) {
                currentNum = parseInt(indices[i]);
                // -1 denotes "not in array"
                if ($.inArray(1, indices) === -1) {
                    freeIndex = 1;
                    break;
                }
                else if (currentNum - 1 > 1 && $.inArray((currentNum - 1), indices) === -1) {
                    freeIndex = currentNum - 1;
                    break;
                }
                freeIndex = currentNum + 1;
            }

            newPanel = thePanel
                    .clone(true)
                    .find(params.regexElems).each(function() {
                elem = $(this);

                for (var i in params.regexAtts) {
                    val = elem.attr(params.regexAtts[i]) || '';
                    match = val.match(params.regex) || [];
                    if (match.length === params.matchlength) {
                        elem.attr(params.regexAtts[i], match[1] + '[' + freeIndex + ']');
                    }
                }
            })
                    .end()
                    .attr(params.panelID, freeIndex)
                    .find('.alert').remove().end()
                    .insertAfter($('<hr />').insertAfter(thePanel))
                    .hide()
                    .fadeIn('slow');

            $('body').scrollTo(newPanel, 'slow', {
                offset: {
                    top: -($('.navbar').outerHeight())
                },
                duration: 'slow'
            });

            if (typeof callback === 'function') {
                callback(freeIndex);
            }
        }
    });
}(jQuery));

// CSRF protection
// from http://jerel.co/blog/2012/03/a-simple-solution-to-codeigniter-csrf-protection-and-ajax
// make sure the cookie plugin is installed
$.support.cors = true
$.ajaxSetup({
    data: {
        'ci_csrf': $.cookie('ci_csrf')
    }
});