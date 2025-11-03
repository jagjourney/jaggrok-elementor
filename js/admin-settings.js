// ============================================================================
// AiMentor ADMIN SETTINGS JS v1.4.x
// ============================================================================

(function(window) {
    var selectorTransforms = [
        { pattern: /jaggrok_/g, replacement: 'aimentor_' },
        { pattern: /jaggrok-/g, replacement: 'aimentor-' }
    ];

    function mapSelector(selector) {
        if (typeof selector !== 'string') {
            return selector;
        }

        var mapped = selector;
        selectorTransforms.forEach(function(entry) {
            mapped = mapped.replace(entry.pattern, entry.replacement);
        });
        return mapped;
    }

    if (window.jQuery) {
        var $ = window.jQuery;
        var originalInit = $.fn.init;
        $.fn.init = function(selector, context, root) {
            return new originalInit(mapSelector(selector), context, root);
        };
        $.fn.init.prototype = originalInit.prototype;
    }

    if (window.document) {
        var doc = window.document;
        var originalGetElementById = doc.getElementById.bind(doc);
        doc.getElementById = function(id) {
            return originalGetElementById(mapSelector(id));
        };

        var originalQuerySelector = doc.querySelector.bind(doc);
        doc.querySelector = function(selector) {
            return originalQuerySelector(mapSelector(selector));
        };

        var originalQuerySelectorAll = doc.querySelectorAll.bind(doc);
        doc.querySelectorAll = function(selector) {
            return originalQuerySelectorAll(mapSelector(selector));
        };
    }
})(window);

jQuery(document).ready(function($) {
    var strings = (typeof aimentorAjax !== 'undefined' && aimentorAjax.strings) ? aimentorAjax.strings : {};
    var adminAjaxUrl = (typeof aimentorAjax !== 'undefined' && aimentorAjax.ajaxurl) ? aimentorAjax.ajaxurl : (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '');
    var statusStates = ['success', 'error', 'idle', 'pending'];
    var usageNonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.usageNonce) ? aimentorAjax.usageNonce : '';
    var logNonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.logNonce) ? aimentorAjax.logNonce : '';
    var usageRefreshInterval = (typeof aimentorAjax !== 'undefined' && aimentorAjax.usageRefreshInterval) ? parseInt(aimentorAjax.usageRefreshInterval, 10) : 0;
    var usageTimer = null;
    var logAction = 'aimentor_get_error_logs';
    var logDownloadAction = 'aimentor_download_error_log';
    var logClearAction = 'aimentor_clear_error_log';
    var logErrorMessage = '';
    var logDownloadErrorMessage = '';
    var logDownloadReadyMessage = '';
    var logClearConfirmMessage = '';
    var logClearSuccessMessage = '';
    var logClearErrorMessage = '';
    var $logForm = null;
    var $logFeedback = null;
    var $logActions = null;
    var $logDownloadButton = null;
    var $logClearButton = null;

    function getString(key, fallback) {
        if (strings && Object.prototype.hasOwnProperty.call(strings, key) && strings[key]) {
            return strings[key];
        }
        return fallback;
    }

    logErrorMessage = getString('logFetchError', 'Unable to load error logs. Please try again.');
    logDownloadErrorMessage = getString('logDownloadError', 'Unable to download the error log. Please try again.');
    logDownloadReadyMessage = getString('logDownloadReady', 'Log download will begin shortly.');
    logClearConfirmMessage = getString('logClearConfirm', 'Are you sure you want to clear the error log? This cannot be undone.');
    logClearSuccessMessage = getString('logClearSuccess', 'Error log cleared.');
    logClearErrorMessage = getString('logClearError', 'Unable to clear the error log. Please try again.');

    function buildClassList() {
        return statusStates.map(function(state) {
            return 'aimentor-status-badge--' + state;
        }).join(' ');
    }

    var badgeClassList = buildClassList();
    var wpAjax = (typeof window.wp !== 'undefined' && window.wp.ajax && typeof window.wp.ajax.post === 'function') ? window.wp.ajax : null;

    function formatNumber(value) {
        if (typeof window.Intl !== 'undefined' && typeof Intl.NumberFormat === 'function') {
            try {
                return new Intl.NumberFormat().format(value);
            } catch (error) {
                return String(value);
            }
        }

        return String(value);
    }

    function buildUsageEventSummary(entry) {
        if (!entry || typeof entry !== 'object') {
            return '';
        }

        var parts = [];

        if (entry.last_event_human) {
            parts.push(entry.last_event_human);
        }

        if (entry.origin_label) {
            parts.push(entry.origin_label);
        }

        if (!parts.length && entry.total_requests > 0) {
            parts.push(getString('usageJustNow', 'Just now'));
        }

        return parts.join(' — ');
    }

    function renderUsageMetrics(metrics) {
        var $container = jQuery('#aimentor-usage-metrics');

        if (!$container.length || !metrics || typeof metrics !== 'object') {
            return;
        }

        if (typeof metrics.generated_at !== 'undefined') {
            $container.attr('data-generated-at', metrics.generated_at);
        }

        if (metrics.generated_at_human) {
            var updatedLabel = getString('usageUpdated', 'Updated %s');
            $container.find('[data-metric="generated_at"]').text(updatedLabel.replace('%s', metrics.generated_at_human));
        }

        if (!metrics.providers || typeof metrics.providers !== 'object') {
            return;
        }

        Object.keys(metrics.providers).forEach(function(provider) {
            var entry = metrics.providers[provider];
            var $provider = $container.find('.aimentor-usage-provider[data-provider="' + provider + '"]');

            if (!$provider.length || !entry) {
                return;
            }

            var totals = {
                total_requests: entry.total_requests,
                success_total: entry.success_total,
                error_total: entry.error_total
            };

            Object.keys(totals).forEach(function(metricKey) {
                var $target = $provider.find('[data-metric="' + metricKey + '"]');

                if (!$target.length || typeof totals[metricKey] === 'undefined') {
                    return;
                }

                $target.text(formatNumber(totals[metricKey] || 0));
            });

            var eventSummary = buildUsageEventSummary(Object.assign({}, entry, { total_requests: totals.total_requests || 0 }));
            var $eventTarget = $provider.find('[data-metric="last_event_summary"]');

            if ($eventTarget.length) {
                if (eventSummary) {
                    $eventTarget.text(eventSummary);
                } else {
                    $eventTarget.text(getString('usageNoActivity', 'No activity yet'));
                }
            }

            var $contextTarget = $provider.find('[data-metric="context_summary"]');

            if ($contextTarget.length) {
                if (entry.context_summary) {
                    $contextTarget.text(entry.context_summary);
                } else {
                    $contextTarget.text(getString('usageNoContext', 'Most recent context unavailable.'));
                }
            }
        });
    }

    function requestUsageMetrics() {
        if (!usageNonce) {
            return jQuery.Deferred().reject().promise();
        }

        if (wpAjax) {
            return wpAjax.post('aimentor_get_usage_metrics', { nonce: usageNonce });
        }

        var ajaxUrl = (typeof aimentorAjax !== 'undefined' && aimentorAjax.ajaxurl) ? aimentorAjax.ajaxurl : (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '');

        if (!ajaxUrl) {
            return jQuery.Deferred().reject().promise();
        }

        return jQuery.post(ajaxUrl, { action: 'aimentor_get_usage_metrics', nonce: usageNonce });
    }

    function handleUsageResponse(response) {
        if (!response) {
            return;
        }

        if (response.metrics) {
            renderUsageMetrics(response.metrics);
            return;
        }

        if (response.success && response.data && response.data.metrics) {
            renderUsageMetrics(response.data.metrics);
        }
    }

    function refreshUsageMetrics() {
        var $container = jQuery('#aimentor-usage-metrics');

        if (!$container.length) {
            return;
        }

        requestUsageMetrics().done(handleUsageResponse);
    }

    function scheduleUsageRefresh() {
        if (usageTimer) {
            window.clearInterval(usageTimer);
        }

        if (!usageRefreshInterval || usageRefreshInterval < 15000) {
            usageRefreshInterval = 60000;
        }

        usageTimer = window.setInterval(refreshUsageMetrics, usageRefreshInterval);
    }

    function renderLogRows(rows) {
        var $tbody = $('#aimentor-error-log-rows');

        if (!$tbody.length) {
            return;
        }

        $tbody.html(rows);
    }

    function renderLogError(message) {
        var $tbody = $('#aimentor-error-log-rows');

        if (!$tbody.length) {
            return;
        }

        var fallback = message || logErrorMessage;
        var safeMessage = $('<div>').text(fallback).html();
        $tbody.html('<tr><td colspan="3">' + safeMessage + '</td></tr>');
    }

    function updateLogNonce(newNonce) {
        if (!newNonce) {
            return;
        }

        logNonce = String(newNonce);

        if ($logForm && $logForm.length) {
            $logForm.attr('data-nonce', logNonce);
        }
    }

    function setLogFeedback(message, state) {
        if (!$logFeedback || !$logFeedback.length) {
            return;
        }

        $logFeedback.removeClass('is-success is-error');

        if (!message) {
            $logFeedback.attr('hidden', 'hidden');
            $logFeedback.text('');
            return;
        }

        if (state === 'success') {
            $logFeedback.addClass('is-success');
        } else if (state === 'error') {
            $logFeedback.addClass('is-error');
        }

        $logFeedback.text(message);
        $logFeedback.removeAttr('hidden');
    }

    function toggleLogActionsBusy(isBusy) {
        if (!$logActions || !$logActions.length) {
            return;
        }

        var $buttons = $logActions.find('button');

        if ($buttons.length) {
            $buttons.prop('disabled', !!isBusy);
        }

        $logActions.toggleClass('is-busy', !!isBusy);
    }

    function parseDispositionFilename(disposition) {
        if (typeof disposition !== 'string' || !disposition) {
            return '';
        }

        var match = disposition.match(/filename\*=UTF-8''([^;]+)/i);

        if (match && match[1]) {
            try {
                return decodeURIComponent(match[1]);
            } catch (error) {
                return match[1];
            }
        }

        match = disposition.match(/filename="?([^";]+)"?/i);

        if (match && match[1]) {
            return match[1];
        }

        return '';
    }

    function setLogLoading(isLoading) {
        if (!$logForm || !$logForm.length) {
            return;
        }

        var $submit = $logForm.find('button[type="submit"]');

        if ($submit.length) {
            $submit.prop('disabled', !!isLoading);
        }

        $logForm.toggleClass('is-loading', !!isLoading);
    }

    function updateProviderStatus(provider, data) {
        var $container = $('.aimentor-provider-status[data-provider="' + provider + '"]');
        var $badge = $('.aimentor-status-badge[data-provider="' + provider + '"]');
        var $description = $('.aimentor-status-description[data-provider="' + provider + '"]');

        if ($container.length && typeof data.timestamp !== 'undefined') {
            $container.attr('data-timestamp', data.timestamp);
        }

        if ($badge.length) {
            if (data.badge_state) {
                $badge.removeClass(badgeClassList).addClass('aimentor-status-badge--' + data.badge_state);
            }

            if (data.badge_label) {
                $badge.text(data.badge_label);
            }
        }

        if ($description.length && typeof data.description !== 'undefined') {
            $description.text(data.description);
        }
    }

    $('.aimentor-test-provider').on('click', function() {
        var $button = $(this);
        var provider = String($button.data('provider') || '');

        if (!provider) {
            return;
        }

        var $group = $('.aimentor-provider-group[data-provider="' + provider + '"]');
        var $input = $group.find('.aimentor-api-input');
        var apiKey = $.trim($input.val());
        var badgeError = getString('errorBadge', 'Error');
        var badgeTesting = getString('testingBadge', 'Testing');
        var testingDescription = getString('testingDescription', 'Testing connection…');
        var missingKey = getString('missingKey', 'Enter an API key before testing.');
        var unknownError = getString('unknownError', 'Unknown error');

        if (!apiKey) {
            updateProviderStatus(provider, {
                badge_state: 'error',
                badge_label: badgeError,
                description: missingKey
            });

            if ($input.length) {
                $input.trigger('focus');
            }

            return;
        }

        $button.prop('disabled', true);

        updateProviderStatus(provider, {
            badge_state: 'pending',
            badge_label: badgeTesting,
            description: testingDescription
        });

        $.post(aimentorAjax.ajaxurl, {
            action: 'aimentor_test_api',
            nonce: aimentorAjax.nonce,
            provider: provider,
            api_key: apiKey
        }).done(function(response) {
            var payload = (response && typeof response.data !== 'undefined') ? response.data : {};
            var payloadObject = (payload && typeof payload === 'object') ? payload : {};

            if (response && response.success) {
                if (!payloadObject.badge_state) {
                    payloadObject.badge_state = 'success';
                }

                updateProviderStatus(provider, payloadObject);
                return;
            }

            var badgeState = payloadObject.badge_state || 'error';
            var badgeLabel = payloadObject.badge_label || badgeError;
            var description = payloadObject.description;

            if (typeof description === 'undefined' || !description) {
                var message = '';

                if (payloadObject.message) {
                    message = payloadObject.message;
                } else if (typeof payload === 'string') {
                    message = payload;
                }

                description = message || unknownError;
            }

            updateProviderStatus(provider, {
                badge_state: badgeState,
                badge_label: badgeLabel,
                description: description,
                timestamp: payloadObject.timestamp
            });
        }).fail(function() {
            updateProviderStatus(provider, {
                badge_state: 'error',
                badge_label: badgeError,
                description: unknownError
            });
        }).always(function() {
            $button.prop('disabled', false);
        });
    });

    $(document).on('click', '.aimentor-onboarding-card .notice-dismiss', function(event) {
        event.preventDefault();

        var $dismiss = $(this);
        var $card = $dismiss.closest('.aimentor-onboarding-card');

        if (!$card.length || $dismiss.data('aimentorProcessing')) {
            return;
        }

        $dismiss.data('aimentorProcessing', true);
        $card.addClass('is-dismissing');

        var nonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.dismissNonce) ? aimentorAjax.dismissNonce : '';
        if (!nonce) {
            $dismiss.data('aimentorProcessing', false);
            $card.removeClass('is-dismissing');
            if (typeof window.alert === 'function') {
                window.alert(getString('onboardingDismissError', 'Unable to dismiss onboarding right now. Please try again.'));
            }
            return;
        }
        var request;

        if (wpAjax) {
            request = wpAjax.post('aimentor_dismiss_onboarding', { nonce: nonce });
        } else {
            var ajaxUrl = (typeof aimentorAjax !== 'undefined' && aimentorAjax.ajaxurl) ? aimentorAjax.ajaxurl : (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '');
            request = $.post(ajaxUrl, { action: 'aimentor_dismiss_onboarding', nonce: nonce });
        }

        request.done(function() {
            $card.fadeOut(200, function() {
                $(this).remove();
            });
        }).fail(function() {
            $dismiss.data('aimentorProcessing', false);
            $card.removeClass('is-dismissing');
            if (typeof window.alert === 'function') {
                window.alert(getString('onboardingDismissError', 'Unable to dismiss onboarding right now. Please try again.'));
            }
        });
    });

    $logForm = $('#aimentor-error-log-form');
    $logActions = $('.aimentor-error-log-actions');
    $logFeedback = $('#aimentor-error-log-feedback');
    $logDownloadButton = $('#aimentor-download-log');
    $logClearButton = $('#aimentor-clear-log');

    if ($logForm.length) {
        if (!logNonce) {
            var formNonce = $logForm.data('nonce');

            if (formNonce) {
                logNonce = String(formNonce);
            }
        }

        $logForm.on('submit', function(event) {
            event.preventDefault();

            setLogFeedback('');

            if (!logNonce) {
                renderLogError();
                return;
            }

            var provider = $.trim($logForm.find('[name="provider"]').val() || '');
            var keyword = $.trim($logForm.find('[name="keyword"]').val() || '');
            var payload = {
                nonce: logNonce,
                provider: provider,
                keyword: keyword
            };
            var request;

            setLogLoading(true);

            if (wpAjax) {
                request = wpAjax.post(logAction, payload);
            } else if (adminAjaxUrl) {
                request = $.post(adminAjaxUrl, $.extend({ action: logAction }, payload));
            } else {
                setLogLoading(false);
                renderLogError();
                return;
            }

            request.done(function(response) {
                if (response && response.success && response.data && typeof response.data.rows !== 'undefined') {
                    renderLogRows(response.data.rows);

                    if (response.data.nonce) {
                        updateLogNonce(response.data.nonce);
                    }

                    return;
                }

                var message = '';

                if (response && response.data && response.data.message) {
                    message = response.data.message;
                }

                if (response && response.data && response.data.nonce) {
                    updateLogNonce(response.data.nonce);
                }

                renderLogError(message);
            }).fail(function(xhr) {
                var message = '';

                if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    message = xhr.responseJSON.data.message;
                }

                if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.nonce) {
                    updateLogNonce(xhr.responseJSON.data.nonce);
                }

                renderLogError(message);
            }).always(function() {
                setLogLoading(false);
            });
        });
    }

    if ($logDownloadButton && $logDownloadButton.length && adminAjaxUrl) {
        $logDownloadButton.on('click', function(event) {
            event.preventDefault();

            if (!logNonce) {
                setLogFeedback(logDownloadErrorMessage, 'error');
                return;
            }

            setLogFeedback('');
            toggleLogActionsBusy(true);

            $.ajax({
                url: adminAjaxUrl,
                method: 'POST',
                data: {
                    action: logDownloadAction,
                    nonce: logNonce
                },
                xhrFields: {
                    responseType: 'blob'
                }
            }).done(function(data, textStatus, jqXHR) {
                var nonceHeader = jqXHR.getResponseHeader('X-AiMentor-Log-Nonce');

                if (nonceHeader) {
                    updateLogNonce(nonceHeader);
                }

                var contentType = (jqXHR.getResponseHeader('Content-Type') || '').toLowerCase();

                if (contentType.indexOf('application/json') === 0) {
                    var reader = new window.FileReader();

                    reader.onload = function() {
                        var message = logDownloadErrorMessage;

                        try {
                            var payload = JSON.parse(reader.result || '{}');

                            if (payload && payload.data) {
                                if (payload.data.message) {
                                    message = payload.data.message;
                                }

                                if (payload.data.nonce) {
                                    updateLogNonce(payload.data.nonce);
                                }
                            }
                        } catch (error) {
                            // Ignore parse errors and fall back to default message.
                        }

                        setLogFeedback(message, 'error');
                    };

                    reader.onerror = function() {
                        setLogFeedback(logDownloadErrorMessage, 'error');
                    };

                    reader.readAsText(data);
                    return;
                }

                var filename = parseDispositionFilename(jqXHR.getResponseHeader('Content-Disposition')) || 'aimentor-error-log.log';
                var blob = data instanceof Blob ? data : new Blob([data], { type: 'text/plain;charset=utf-8' });
                var downloadUrl = window.URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = downloadUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                window.setTimeout(function() {
                    window.URL.revokeObjectURL(downloadUrl);
                }, 1000);

                setLogFeedback(logDownloadReadyMessage, 'success');
            }).fail(function(jqXHR) {
                var message = logDownloadErrorMessage;

                if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.data) {
                    if (jqXHR.responseJSON.data.message) {
                        message = jqXHR.responseJSON.data.message;
                    }

                    if (jqXHR.responseJSON.data.nonce) {
                        updateLogNonce(jqXHR.responseJSON.data.nonce);
                    }
                } else if (jqXHR && jqXHR.responseText) {
                    try {
                        var payload = JSON.parse(jqXHR.responseText);

                        if (payload && payload.data) {
                            if (payload.data.message) {
                                message = payload.data.message;
                            }

                            if (payload.data.nonce) {
                                updateLogNonce(payload.data.nonce);
                            }
                        }
                    } catch (error) {
                        // Ignore parse errors.
                    }
                }

                setLogFeedback(message, 'error');
            }).always(function() {
                toggleLogActionsBusy(false);
            });
        });
    }

    if ($logClearButton && $logClearButton.length) {
        $logClearButton.on('click', function(event) {
            event.preventDefault();

            if (!logNonce) {
                setLogFeedback(logClearErrorMessage, 'error');
                return;
            }

            if (typeof window.confirm === 'function' && !window.confirm(logClearConfirmMessage)) {
                return;
            }

            setLogFeedback('');
            toggleLogActionsBusy(true);

            var payload = { nonce: logNonce };
            var request;

            if (wpAjax) {
                request = wpAjax.post(logClearAction, payload);
            } else if (adminAjaxUrl) {
                request = $.post(adminAjaxUrl, $.extend({ action: logClearAction }, payload));
            } else {
                toggleLogActionsBusy(false);
                setLogFeedback(logClearErrorMessage, 'error');
                return;
            }

            request.done(function(response) {
                if (response && response.success) {
                    if (response.data && response.data.nonce) {
                        updateLogNonce(response.data.nonce);
                    }

                    var successMessage = (response.data && response.data.message) ? response.data.message : logClearSuccessMessage;

                    setLogFeedback(successMessage, 'success');

                    if ($logForm && $logForm.length) {
                        $logForm.trigger('submit');
                    }

                    return;
                }

                var message = logClearErrorMessage;

                if (response && response.data) {
                    if (response.data.message) {
                        message = response.data.message;
                    }

                    if (response.data.nonce) {
                        updateLogNonce(response.data.nonce);
                    }
                }

                setLogFeedback(message, 'error');
            }).fail(function(xhr) {
                var message = logClearErrorMessage;

                if (xhr && xhr.responseJSON && xhr.responseJSON.data) {
                    if (xhr.responseJSON.data.message) {
                        message = xhr.responseJSON.data.message;
                    }

                    if (xhr.responseJSON.data.nonce) {
                        updateLogNonce(xhr.responseJSON.data.nonce);
                    }
                }

                setLogFeedback(message, 'error');
            }).always(function() {
                toggleLogActionsBusy(false);
            });
        });
    }

    if ($('#aimentor-usage-metrics').length && usageNonce) {
        refreshUsageMetrics();
        scheduleUsageRefresh();
    }
});
