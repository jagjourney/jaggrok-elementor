// ============================================================================
// JAGJourney ADMIN SETTINGS JS v1.4.x
// ============================================================================

jQuery(document).ready(function($) {
    var strings = (typeof jaggrokAjax !== 'undefined' && jaggrokAjax.strings) ? jaggrokAjax.strings : {};
    var statusStates = ['success', 'error', 'idle', 'pending'];

    function getString(key, fallback) {
        if (strings && Object.prototype.hasOwnProperty.call(strings, key) && strings[key]) {
            return strings[key];
        }
        return fallback;
    }

    function buildClassList() {
        return statusStates.map(function(state) {
            return 'jaggrok-status-badge--' + state;
        }).join(' ');
    }

    var badgeClassList = buildClassList();

    function updateProviderStatus(provider, data) {
        var $container = $('.jaggrok-provider-status[data-provider="' + provider + '"]');
        var $badge = $('.jaggrok-status-badge[data-provider="' + provider + '"]');
        var $description = $('.jaggrok-status-description[data-provider="' + provider + '"]');

        if ($container.length && typeof data.timestamp !== 'undefined') {
            $container.attr('data-timestamp', data.timestamp);
        }

        if ($badge.length) {
            if (data.badge_state) {
                $badge.removeClass(badgeClassList).addClass('jaggrok-status-badge--' + data.badge_state);
            }

            if (data.badge_label) {
                $badge.text(data.badge_label);
            }
        }

        if ($description.length && typeof data.description !== 'undefined') {
            $description.text(data.description);
        }
    }

    $('.jaggrok-test-provider').on('click', function() {
        var $button = $(this);
        var provider = String($button.data('provider') || '');

        if (!provider) {
            return;
        }

        var $group = $('.jaggrok-provider-group[data-provider="' + provider + '"]');
        var $input = $group.find('.jaggrok-api-input');
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

        $.post(jaggrokAjax.ajaxurl, {
            action: 'jaggrok_test_api',
            nonce: jaggrokAjax.nonce,
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
});
