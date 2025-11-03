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
    var statusStates = ['success', 'error', 'idle', 'pending'];
    var usageNonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.usageNonce) ? aimentorAjax.usageNonce : '';
    var usageRefreshInterval = (typeof aimentorAjax !== 'undefined' && aimentorAjax.usageRefreshInterval) ? parseInt(aimentorAjax.usageRefreshInterval, 10) : 0;
    var usageTimer = null;

    function getString(key, fallback) {
        if (strings && Object.prototype.hasOwnProperty.call(strings, key) && strings[key]) {
            return strings[key];
        }
        return fallback;
    }

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

    if ($('#aimentor-usage-metrics').length && usageNonce) {
        refreshUsageMetrics();
        scheduleUsageRefresh();
    }
});
