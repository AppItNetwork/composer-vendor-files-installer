/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

window.cvfi = (function ($) {
    var pub = {
        /**
         * @return string|undefined the CSRF parameter name. Undefined is returned if CSRF validation is not enabled.
         */
        getCsrfParam: function () {
            return $('meta[name=csrf-param]').attr('content');
        },

        /**
         * @return string|undefined the CSRF token. Undefined is returned if CSRF validation is not enabled.
         */
        getCsrfToken: function () {
            return $('meta[name=csrf-token]').attr('content');
        },

        /**
         * Updates all form CSRF input fields with the latest CSRF token.
         * This method is provided to avoid cached forms containing outdated CSRF tokens.
         */
        refreshCsrfToken: function () {
            var token = pub.getCsrfToken();
            if (token) {
                $('form input[name="' + pub.getCsrfParam() + '"]').val(token);
            }
        },

        init: function () {
            initCsrfHandler();
        },
        
        /**
         * @return string application API endpoint.
         */
        getApiEndpoint: function () {
            return $('meta[name=api-endpoint]').attr('content');
        },

        ajax: function (type, params, callback, failureCallback) {
            if (type=='get') {
                $.get(cvfi.getApiEndpoint(), params, callback, 'json')
                  .fail(function (jqXHR, textStatus, errorThrown) {
                    cvfi.ajaxFail(jqXHR, textStatus, errorThrown, failureCallback);
                  }).done(cvfi.ajaxDone());
            } else if (type=='post') {
                $.post(cvfi.getApiEndpoint(), params, callback, 'json')
                  .fail(function (jqXHR, textStatus, errorThrown) {
                    cvfi.ajaxFail(jqXHR, textStatus, errorThrown, failureCallback);
                  }).done(cvfi.ajaxDone());
            } else {
                $.ajax({
                    url: cvfi.getApiEndpoint(),
                    type: type,
                    data: params,
                    dataType: 'json',
                    contentType: 'application/x-www-form-urlencoded;',
                    success: function (response, textStatus, jqXhr) {
                        callback(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        cvfi.ajaxFail(jqXHR, textStatus, errorThrown, failureCallback);
                    },
                    done: cvfi.ajaxDone()
                });
            }
        },
        ajaxDone: function () {
            
        },
        ajaxFail: function (jqXHR, status, text, failureCallback) {
            text = (jqXHR.hasOwnProperty('responseJSON') && jqXHR.responseJSON!==null) ? jqXHR.responseJSON.message : text;
            if (typeof failureCallback === 'function') {
                failureCallback(jqXHR, status, text);
            }
        }
    };

    function initCsrfHandler() {
        // automatically send CSRF token for all AJAX requests
        $.ajaxPrefilter(function (options, originalOptions, xhr) {
            if (!options.crossDomain && pub.getCsrfParam()) {
                xhr.setRequestHeader('X-CSRF-Token', pub.getCsrfToken());
            }
        });
        pub.refreshCsrfToken();
    }

    return pub;
})(window.jQuery);
    
window.jQuery(function () {
    window.cvfi.init();

    var failureCallback = function(jqXHR, status, text) {
        $('#terminal').append('<br><em class="error-output">Error '+jqXHR.status+': '+text+'</em>');
        $('#terminal').append('<br>Installation cycle has ended.<br>');
    };

    var reiterate = function(response) {
        $('#terminal').append(response.output);
        if (response.nextIndex) {
            var params = {'nextIndex': response.nextIndex};
            var command = response.command;
            setTimeout(function() {
                $('#terminal').append('$ <i class="command">'+command+'</i><br>');
            }, 1000);
            setTimeout(function() {
                cvfi.ajax('post', params, reiterate, failureCallback);
            }, 3000);
        } else {
            $('#terminal').append('<br>Installation cycle has ended.<br>');
        }
    };
    
    $('.start').on('click', function(e) {
        $('#terminal').append($(this).data('output'));

        // https://stackoverflow.com/a/37801316/1100697
        // sending post requests without any post params will trigger `$HTTP_RAW_POST_DATA is deprecated` warning on certain server.
        // 'start' attribute is just a placeholder to avoid this warning and halt the installation process.
        cvfi.ajax('post', {'start': true}, reiterate, failureCallback);
    });
});
