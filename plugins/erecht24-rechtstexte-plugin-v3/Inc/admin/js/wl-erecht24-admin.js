(function ($) {
    'use strict';

    $(function () {
        var $syncButton = $('.wl-synchronize-button');
        var $syncAllButton = $('.syncAllLegalTexts');
        const syncAllButtonCache = $syncAllButton.prop('checked')
        var $optionToggle = $('input.erecht24-ajax-option-toggle');
        var $localSource = $('.wl_source__local');
        var $remoteSource = $('.wl_source__server');
        var $syncToLocalButton = $('.importSyncToLocal');
        var $permanentHideShortcodeWarningButton = $('#banOldShortcodeWarning');
        var $googleAnalyticsSwitch = $('#google_analytics_enabled');
        var $googleAnalyticsUsercentrics = $('#google_analytics_usercentricsHidden');
        let $usercentricsRow = $googleAnalyticsUsercentrics.parent().parent();

        const $form = $('#api_key_form');
        const $apiKeyInput = $('#api_key');
        const akiKeyCache = $apiKeyInput.val();
        const $debugInfoBtn = $('#debugInfoButton');
        const debugInfoData = document.getElementById("debugInfoData");

        function showLoader() {
            // remove all previous errors
            $('.erecht24Error').remove();
            $('body').addClass('showAjaxLoader')

        }
        function hideLoader() {
            $('body').removeClass('showAjaxLoader')

        }
        function handleRestError(response) {
            if (!response || !response.hasOwnProperty('responseJSON') || !response.responseJSON.hasOwnProperty('message'))
                return;
            const code = response.status;
            const message = '"' + code + '": ' + response.responseJSON.message;
            wlDisplayAdminNotice(message, 'red')
        }
        function handleRestDisabledError() {
            const message = 'Die WordPress REST-API ist deaktiviert. Bitte überprüfen Sie Ihre Plugins. Weitere Informationen finden Sie im Tab "Pluginstatus".';
            wlDisplayAdminNotice(message, 'red')
        }
        function handleRestSuccess(response) {
            if (!response || !response.hasOwnProperty('responseJSON') || !response.responseJSON.hasOwnProperty('message'))
                return;
            const message = response.responseJSON.message;
            wlDisplayAdminNotice(message, 'green')
        }
        function wlLocalTextArea() {
            $remoteSource.hide();
            $localSource.show();
            $localSource.parent().find('.leftLabel').css('fontWeight', 'normal');
            $localSource.parent().find('.rightLabel').css('fontWeight', 'bold');
            $('.wl-readonly-on-server textarea').prop('readonly', false);
            $syncButton.closest('tr').hide();
            $syncToLocalButton.closest('tr').show();
        }
        function wlRemoteTextArea() {
            $remoteSource.show();
            $localSource.hide();
            $localSource.parent().find('.leftLabel').css('fontWeight', 'bold');
            $localSource.parent().find('.rightLabel').css('fontWeight', 'normal');
            $('.wl-readonly-on-server textarea').prop('readonly', true);
            $syncButton.closest('tr').show()
            $syncToLocalButton.closest('tr').hide();
        }

        if ($optionToggle.length > 0) {
            if ($optionToggle.prop('checked')) {
                wlLocalTextArea();
            } else {
                wlRemoteTextArea();
            }
        }

        $(document).ready(function () {
            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/runStatusChecks',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    $debugInfoBtn.prop('disabled', false)
                    switch (response.status) {
                        case 200:
                            $('#statusTableContainer').html(response.responseText)
                            break;
                        default: // any error
                            handleRestDisabledError();
                            $('#statusTableContainer').prepend(
                                '<div class="notice notice-error" style="background: #f8e5e7; border-color: #e3413f">' +
                                    '<p><strong>Die WordPress REST-API ist deaktiviert.</strong></p>' +
                                    '<p>Bitte aktivieren Sie die gesamte WordPress REST-API oder fügen Sie folgende Routen zu Ihrer Whitelist hinzu:</p>' +
                                    '<ul style="list-style: disc inside; margin-left: 5px">' +
                                        '<li>Namespace: <strong>/erecht24/v1</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/push</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/ajaxUpdateSubOption</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/checkApiKey</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/getDebugInformation</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/updateAllDocuments</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/updateSingleDocument</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/refreshApiConnection</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/runStatusChecks</strong></li>' +
                                        '<li>Route GET <strong>/erecht24/v1/syncLocalText</strong></li>' +
                                    '</ul>' +
                                '</div>')
                            $('#statusCheckTable td').html('<p class="erecht24-statusrow erecht24-row-error">REST-API deaktiviert</p>')
                            $('#statusCheckTable').hide()
                            break;
                    }
                }
            });
        });

        $permanentHideShortcodeWarningButton.on('click', function(event){
            event.preventDefault();
            var data = {
                'action': 'permanentHideShortcodeWarnings',
            };

            $.post(ajaxurl, data, function (response) {
            });

            var $notice = $(".deprecatedShortCodeWarning");
            $notice.fadeTo(100, 0, function () {
                $notice.slideUp(100, function () {
                    $notice.remove();
                });
            });

        })

        // switch local / remote
        $optionToggle.on('change', function () {
            showLoader()

            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/ajaxUpdateSubOption',
                data: {
                    'option': $(this).data('option'),
                    'subOption': $(this).data('sub-option'),
                    'value': ($(this).prop('checked')) ? 1 : 0
                },
                beforeSend: function ( xhr ) {
                    showLoader();
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader()
                    switch (response.status) {
                        case 200: // imported
                            handleRestSuccess(response)
                            break;
                        default: // any error
                            handleRestError(response)
                    }
                }
            });

            if ($(this).prop('checked')) {
                wlLocalTextArea();
            } else {
                wlRemoteTextArea();
            }
        });

        // sync local texts
        $syncToLocalButton.on('click', function (event) {
            showLoader()
            // Stop the normal event
            event.preventDefault();
            var $data_type = $(this).data('action');

            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/syncLocalText',
                data: {
                    'data_type': $data_type
                },
                beforeSend: function ( xhr ) {
                    showLoader();
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader()
                    switch (response.status) {
                        case 200: // imported
                            handleRestSuccess(response)
                            $('textarea[name="erecht24_' + $data_type + '_settings[document_de_local]"]').html(response.responseJSON.html_de);
                            $('textarea[name="erecht24_' + $data_type + '_settings[document_en_local]"]').html(response.responseJSON.html_en);
                            $('input[name="erecht24_' + $data_type + '_settings[document_de_last_update_local]"]').val(response.responseJSON.timestamp_de);
                            $('input[name="erecht24_' + $data_type + '_settings[document_en_last_update_local]"]').val(response.responseJSON.timestamp_en);
                            break;
                        default: // any error
                            handleRestError(response)
                    }
                }
            });
        });

        // import single document
        $syncButton.on('click', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/updateSingleDocument',
                data: {
                    'data_type': $(this).data('action')
                },
                beforeSend: function ( xhr ) {
                    showLoader();
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader()

                    switch (response.status) {
                        case 200: // imported
                            location.reload();
                            break;
                        default: // any error
                            handleRestError(response)

                    }
                }
            });
        });

        // import all documents
        $syncAllButton.on('click', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/updateAllDocuments',
                beforeSend: function ( xhr ) {
                    showLoader();
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader()
                    for (let message of response.responseJSON.success) {
                        wlDisplayAdminNotice(message, 'green');
                    }

                    for (let message of response.responseJSON.failed) {
                        wlDisplayAdminNotice(message, 'red');
                    }
                }
            });
        });

        // save api key
        $form.on('submit',function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/checkApiKey',
                data: {
                    'apiKey': $apiKeyInput.val()
                },
                beforeSend: function ( xhr ) {
                    showLoader()
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader();

                    switch (response.status) {
                        case 200: // updated
                            $('.NoAPI-KEY').remove();
                            $syncAllButton.prop("disabled", false);
                            handleRestSuccess(response);
                            break;
                        case 201: // no changes detected
                            $syncAllButton.prop("disabled", syncAllButtonCache);
                            handleRestSuccess(response);
                            break;
                        case 202: // key deleted
                            $apiKeyInput.val('')
                            $syncAllButton.prop("disabled", true);
                            handleRestSuccess(response);
                            break;
                        case 203: // key force deleted
                            $apiKeyInput.val('')
                            $syncAllButton.prop("disabled", true);
                            wlDisplayAdminNotice(response.responseJSON.message, 'yellow')
                            break;
                        case 400: // to many clients
                            $apiKeyInput.val(akiKeyCache)
                            $syncAllButton.prop("disabled", true);
                            if (response.responseJSON.message !== "Es gab ein Problem. Zu viele Clients wurden für dieses Projekt angemeldet.") {
                                wlDisplayAdminNotice(response.responseJSON.message, 'red');
                            } else {
                                wlDisplayAdminNotice('Der API-Schlüssel konnte nicht gespeichert werden. Die Höchstanzahl an zulässigen API-Clients ist erreicht. Bitte löschen Sie einen Client. Eine Anleitung finden Sie in unserem <a href="https://www.e-recht24.de/mitglieder/tools/erecht24-rechtstexte-plugin/#1603102250539-d1f7c879-0d82" target="_blank">Hilfebereich</a>.', 'red');
                            }
                            break;
                        default: //  rest disabled error
                            $apiKeyInput.val(akiKeyCache)
                            $syncAllButton.prop("disabled", true);
                            handleRestError(response)
                            break;
                    }
                }
            });
        });

        $(document).on('click', '#refreshConnection', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                url: eRecht24ApiSettings.root + 'erecht24/v1/refreshApiConnection',
                beforeSend: function ( xhr ) {
                    showLoader()
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader();
                    switch (response.status) {
                        case 200: // updated
                            $('#statusTableContainer').html(response.responseText)
                            break;
                        default: // any error
                            handleRestError(response)
                            wlDisplayAdminNotice(response.responseJSON.additionalMessage, 'red')
                            break;
                    }
                }
            });
        })

        $debugInfoBtn.on('click', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'GET',
                async: false,
                url: eRecht24ApiSettings.root + 'erecht24/v1/getDebugInformation',
                beforeSend: function ( xhr ) {
                    showLoader();
                    xhr.setRequestHeader( 'X-WP-Nonce', eRecht24ApiSettings.nonce );
                },
                complete: function (response) {
                    hideLoader();

                    const data = response.responseJSON;
                    data.wp_remote_check = $('#statusCheckTable tbody tr:nth-child(1) .erecht24-statusrow').text();
                    data.connection_check = $('#statusCheckTable tbody tr:nth-child(2) .erecht24-statusrow').text();
                    data.config_check = $('#statusCheckTable tbody tr:nth-child(3) .erecht24-statusrow').text();
                    data.push_check = $('#statusCheckTable tbody tr:nth-child(4) .erecht24-statusrow').text();
                    data.wp_url_changed_check = $('#statusCheckTable tbody tr:nth-child(5) .erecht24-statusrow').text();

                    debugInfoData.value = '';
                    logDebugData('Debug Data', data, 0)
                    const $debugData = $('#debugInfoData');
                    $debugData.prop('disabled', false).show();

                    // $debugData.prop('disabled',true);
                    if (copyToClipboard(debugInfoData))
                        $('.textCopied').show();

                    $debugData.prop('disabled', true)
                }
            });
        })

        function logDebugData(key, val, depth) {
            if (depth > 0) {
                debugInfoData.value += '\t'.repeat(depth - 1) + key + ': ';
            }

            if (typeof val === "object" ) {
                if (depth > 0) {
                    debugInfoData.value += '\r\n'
                }
                for (const index in val) {
                    logDebugData(index, val[index], depth+1)
                }

            } else {
                debugInfoData.value += val;
                debugInfoData.value += '\r\n'
            }
        }

        function copyToClipboard(elem) {
            var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
            var origSelectionStart, origSelectionEnd;
            if (isInput) {
                target = elem;
                origSelectionStart = elem.selectionStart;
                origSelectionEnd = elem.selectionEnd;
            } else {
                // create hidden text element, if it doesn't already exist
                // must use a temporary form element for the selection and copy
                var targetId = "_hiddenCopyText_";
                target = document.getElementById(targetId);
                if (!target) {
                    var target = document.createElement("textarea");
                    target.style.position = "absolute";
                    target.style.left = "-9999px";
                    target.style.top = "0";
                    target.id = targetId;
                    document.body.appendChild(target);
                }
                target.textContent = elem.textContent;
            }
            // select the content
            var currentFocus = document.activeElement;
            target.focus();
            target.setSelectionRange(0, target.value.length);

            // copy the selection
            var succeed;
            try {
                succeed = document.execCommand("copy");
            } catch(e) {
                succeed = false;
            }
            // restore original focus
            if (currentFocus && typeof currentFocus.focus === "function") {
                currentFocus.focus();
            }

            if (isInput) {
                // restore prior selection
                elem.setSelectionRange(origSelectionStart, origSelectionEnd);
            } else {
                // clear temporary content
                target.textContent = "";
            }
            return succeed;
        }

        toggleUsercentricsRow();
        $googleAnalyticsSwitch.on('change', function (event) {
            toggleUsercentricsRow();
        });

        /**
         * Display usercentrics option for google analytics only if google analytics is activated
         */
        function toggleUsercentricsRow() {
            if ($usercentricsRow.is(':visible') && $googleAnalyticsSwitch.prop('checked') !== true) {
                $usercentricsRow.hide();
            } else {
                $usercentricsRow.show();
            }
        }
    });

    function wlDisplayAdminNotice(adminMessage, adminMessageColor) {
        var $settingsTitle = $('#erecht24-settings-title');

        $settingsTitle.after('<div class="error erecht24Error notice is-dismissible"><p>' +
            erecht24Data.plugin_title + ': ' + adminMessage +
            '</p><button id="erecht24-dismiss-admin-message" class="notice-dismiss" type="button">' +
            '<span class="screen-reader-text">Dismiss this notice.</span></button></div>');
        $('#erecht24-dismiss-admin-message').click(function (event) {
            event.preventDefault();
            $('.' + 'error').fadeTo(100, 0, function () {
                $('.' + 'error').slideUp(100, function () {
                    $('.' + 'error').remove();
                });
            });
        });
        var $errorDiv = $('div.error:first');
        switch (adminMessageColor) {
            case 'yellow':
                $errorDiv.css('border-left', '4px solid #ffba00');
                break;
            case 'red':
                $errorDiv.css('border-left', '4px solid #dd3d36');
                break;
            default:
                $errorDiv.css('border-left', '4px solid #7ad03a');
        }
    }

    $(window).on('load',function () {
        var $noticeDismissButton = $('.erecht24-notice.is-dismissible .notice-dismiss');

        $noticeDismissButton.on('click', function () {
            var temporaryDisableOldShortcodeWarning = $(this).parent().hasClass("deprecatedShortCodeWarning");
            var data = {
                'action': 'dismiss_api_message',
                "temporaryDisableOldShortcodeWarning":temporaryDisableOldShortcodeWarning
            };
            $.post(ajaxurl, data, function () {
            });
        });
    });

})(jQuery);
