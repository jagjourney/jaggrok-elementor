(function($, window) {
    'use strict';

    function ensureBadgeStyles() {
        if (document.getElementById('aimentor-provider-badge-style')) {
            return;
        }
        var style = document.createElement('style');
        style.id = 'aimentor-provider-badge-style';
        style.textContent = '.aimentor-provider-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.05em;}';
        document.head.appendChild(style);
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatString(template, value) {
        if (!template) {
            return value;
        }
        return template.replace('%s', value);
    }

    function recordHistoryEntry(prompt, providerKey) {
        if (!prompt || !providerKey) {
            return;
        }
        var aimentorData = window.aimentorAjax || {};
        if (!aimentorData.historyEndpoint || !aimentorData.restNonce) {
            return;
        }

        var payload = {
            prompt: String(prompt),
            provider: String(providerKey)
        };

        if (window.fetch) {
            window.fetch(aimentorData.historyEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': aimentorData.restNonce
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            }).catch(function() {
                // Silently ignore logging failures.
            });
            return;
        }

        if (window.jQuery && jQuery.ajax) {
            jQuery.ajax({
                url: aimentorData.historyEndpoint,
                method: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                processData: false,
                headers: {
                    'X-WP-Nonce': aimentorData.restNonce
                }
            });
        }
    }

    $(document).on('elementor/init', function() {
        var aimentorData = window.aimentorAjax || {};
        var strings = aimentorData.strings || {};
        var providerLabels = aimentorData.providerLabels || {};
        var providerSummaries = aimentorData.providerSummaries || {};
        var providersMeta = aimentorData.providersMeta || {};
        var defaultsData = aimentorData.defaults || {};
        var modelLabels = aimentorData.modelLabels || {};
        var isProActive = !!aimentorData.isProActive;

        ensureBadgeStyles();

        var providerDefaults = buildProviderDefaults();
        var providerMap = buildProviderMap(providerDefaults);
        var existingProviders = (typeof window.AiMentorProviders === 'object' && window.AiMentorProviders) ? window.AiMentorProviders : {};

        if (!Object.keys(existingProviders).length && typeof window.JagGrokProviders === 'object' && window.JagGrokProviders) {
            existingProviders = Object.assign({}, window.JagGrokProviders);
        }

        window.AiMentorProviders = Object.assign({}, providerDefaults, providerMap, existingProviders);

        if (typeof window.JagGrokProviders !== 'undefined') {
            window.JagGrokProviders = window.AiMentorProviders;
        }

        var existingState = (window.AiMentorElementorUI && window.AiMentorElementorUI.state) ? window.AiMentorElementorUI.state : {};
        var defaultTask = sanitizeTask((defaultsData && defaultsData.task) || (existingState.defaults && existingState.defaults.task) || 'content', isProActive);
        var defaultTier = sanitizeTier((defaultsData && defaultsData.tier) || (existingState.defaults && existingState.defaults.tier) || 'fast');

        var api = window.AiMentorElementorUI || {};
        api.state = {
            defaults: {
                task: defaultTask,
                tier: defaultTier
            },
            widgets: existingState.widgets || {},
            modal: existingState.modal ? {
                task: sanitizeTask(existingState.modal.task, isProActive),
                tier: sanitizeTier(existingState.modal.tier),
                provider: sanitizeProvider(existingState.modal.provider || getDefaultProvider())
            } : {
                task: defaultTask,
                tier: defaultTier,
                provider: sanitizeProvider(getDefaultProvider())
            }
        };

        api.buildSummary = buildSummary;
        api.getProviderMeta = getProviderMeta;

        api.initWidget = function(config) {
            if (!config || !config.widgetId) {
                return;
            }

            var allowCanvas = !!config.allowCanvas && isProActive;
            var $container = config.container ? $(config.container) : $();
            var $provider = config.providerSelector ? $(config.providerSelector) : $();
            var $prompt = config.promptSelector ? $(config.promptSelector) : $();
            var $output = config.outputSelector ? $(config.outputSelector) : $();
            var $button = config.buttonSelector ? $(config.buttonSelector) : $();
            var $task = config.taskSelector ? $(config.taskSelector) : $();
            var $tier = config.tierSelector ? $(config.tierSelector) : $();
            var $summary = config.summarySelector ? $(config.summarySelector) : $();
            var ui = {
                icon: config.providerIconSelector ? $(config.providerIconSelector) : $(),
                label: config.providerLabelSelector ? $(config.providerLabelSelector) : $(),
                summary: config.providerSummarySelector ? $(config.providerSummarySelector) : $(),
                badge: config.providerBadgeSelector ? $(config.providerBadgeSelector) : $(),
                button: $button
            };

            if (!$provider.length || !$button.length || !$prompt.length) {
                return;
            }

            if (!ensureAjaxConfig($container, $button)) {
                return;
            }

            var widgetState = api.state.widgets[config.widgetId] || {};
            widgetState.provider = sanitizeProvider(widgetState.provider || $provider.val() || getDefaultProvider());
            widgetState.task = sanitizeTask(widgetState.task || ($task.length ? $task.val() : api.state.defaults.task), allowCanvas);
            widgetState.tier = sanitizeTier(widgetState.tier || ($tier.length ? $tier.val() : api.state.defaults.tier));
            api.state.widgets[config.widgetId] = widgetState;

            $provider.val(widgetState.provider);
            if ($task.length) {
                $task.val(widgetState.task);
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(widgetState.tier);
            }

            applyProviderMeta(widgetState.provider, ui);
            updateSummaryText($summary, widgetState.provider, widgetState);

            $provider.off('change.aimentor').on('change.aimentor', function() {
                var providerKey = sanitizeProvider($(this).val());
                widgetState.provider = providerKey;
                applyProviderMeta(providerKey, ui);
                updateSummaryText($summary, providerKey, widgetState);
            });

            if ($task.length) {
                $task.off('change.aimentor').on('change.aimentor', function() {
                    widgetState.task = sanitizeTask($(this).val(), allowCanvas);
                    $(this).val(widgetState.task);
                    updateSummaryText($summary, widgetState.provider, widgetState);
                });
            }

            if ($tier.length) {
                $tier.off('change.aimentor').on('change.aimentor', function() {
                    widgetState.tier = sanitizeTier($(this).val());
                    $(this).val(widgetState.tier);
                    updateSummaryText($summary, widgetState.provider, widgetState);
                });
            }

            $button.off('click.aimentor').on('click.aimentor', function() {
                var promptValue = ($prompt.val() || '').trim();
                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    if ($output.length) {
                        $output.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    }
                    return;
                }

                var providerKey = sanitizeProvider($provider.val() || widgetState.provider);
                widgetState.provider = providerKey;
                var providerMeta = applyProviderMeta(providerKey, ui);
                updateSummaryText($summary, providerKey, widgetState);

                var generatingMessage = getGeneratingMessage(providerMeta);
                if ($output.length) {
                    $output.html('<p>' + escapeHtml(generatingMessage) + '</p>');
                }

                $button.prop('disabled', true).text(generatingMessage);

                var requestPayload = {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerKey,
                    nonce: aimentorData.nonce,
                    task: widgetState.task,
                    tier: widgetState.tier
                };

                $.post(aimentorData.ajaxurl, requestPayload)
                    .done(function(response) {
                        $button.prop('disabled', false);
                        applyProviderMeta(providerKey, ui);

                        var responseProvider = response && response.data && response.data.provider ? sanitizeProvider(response.data.provider) : providerKey;
                        if (responseProvider !== widgetState.provider) {
                            widgetState.provider = responseProvider;
                            $provider.val(responseProvider);
                            applyProviderMeta(responseProvider, ui);
                        }

                        updateSummaryText($summary, widgetState.provider, widgetState);

                        if (response && response.success) {
                            var summaryText = extractResponseSummary(response, widgetState.provider, widgetState);
                            if (response.data && response.data.canvas_json && window.elementorFrontend && elementorFrontend.elementsHandler) {
                                elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                            }
                            if ($output.length) {
                                $output.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + '</p>');
                            }
                            recordHistoryEntry(promptValue, widgetState.provider);
                        } else {
                            var message = response && response.data ? response.data : 'Unknown error';
                            if (typeof message === 'object' && message !== null) {
                                message = message.message || message.error || 'Unknown error';
                            }
                            var errorPrefix = strings.errorPrefix || 'Error:';
                            if ($output.length) {
                                $output.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(String(message)) + '</p>');
                            }
                        }
                    })
                    .fail(function() {
                        $button.prop('disabled', false);
                        applyProviderMeta(widgetState.provider, ui);
                        var errorPrefix = strings.errorPrefix || 'Error:';
                        var errorMessage = strings.unknownError || 'Request failed.';
                        if ($output.length) {
                            $output.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(errorMessage) + '</p>');
                        }
                    });
            });
        };

        api.openModal = function() {
            var allowCanvas = isProActive;
            if (!$('#aimentor-modal').length) {
                var defaultProvider = sanitizeProvider(getDefaultProvider());
                var providerOptions = buildProviderOptions(defaultProvider);
                var generationLabel = escapeHtml(strings.generationType || 'Generation Type');
                var performanceLabel = escapeHtml(strings.performanceLabel || 'Performance');
                var pageCopyLabel = escapeHtml(strings.pageCopy || 'Page Copy');
                var pageLayoutLabel = escapeHtml(strings.pageLayout || 'Page Layout');
                var fastLabel = escapeHtml(strings.fastLabel || 'Fast');
                var qualityLabel = escapeHtml(strings.qualityLabel || 'Quality');
                var promptLabel = escapeHtml(strings.promptLabel || 'Prompt');
                var promptPlaceholder = escapeHtml(strings.promptPlaceholder || 'Describe your page (e.g., hero with CTA)');
                var headingMeta = getProviderMeta(defaultProvider);
                var headingText = escapeHtml(strings.writeWith ? strings.writeWith.replace('%s', headingMeta.label || defaultProvider) : 'Write with ' + (headingMeta.label || defaultProvider));
                var closeLabel = escapeHtml(strings.closeModal || 'Close modal');
                var askLabel = escapeHtml(strings.askAiMentor || 'Ask AiMentor');

                var modalHtml = '' +
                    '<div id="aimentor-modal" class="aimentor-modal" role="dialog" aria-modal="true" aria-labelledby="aimentor-modal-heading-text" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">' +
                    '  <div class="aimentor-modal__content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:520px;background:white;border-radius:8px;box-shadow:0 5px 18px rgba(0,0,0,0.25);overflow:hidden;">' +
                    '    <div class="aimentor-modal__header" style="padding:20px;border-bottom:1px solid #e2e4e7;display:flex;align-items:center;justify-content:space-between;gap:12px;">' +
                    '      <h3 id="aimentor-modal-heading-text" class="aimentor-modal__title" style="margin:0;display:flex;align-items:center;gap:8px;"><span class="dashicons dashicons-art" aria-hidden="true"></span><span>' + headingText + '</span></h3>' +
                    '      <button type="button" id="aimentor-modal-close" class="aimentor-modal__close" aria-label="' + closeLabel + '" style="background:none;border:none;font-size:24px;line-height:1;color:#6b7280;cursor:pointer;">&times;</button>' +
                    '    </div>' +
                    '    <div class="aimentor-modal__body" style="padding:20px;display:flex;flex-direction:column;gap:16px;">' +
                    '      <div class="aimentor-modal__providers" role="radiogroup" aria-label="' + escapeHtml(strings.chooseProvider || 'Choose provider') + '">' + providerOptions + '</div>' +
                    '      <div class="aimentor-provider-active" style="display:flex;align-items:center;gap:8px;">' +
                    '        <span id="aimentor-provider-active-icon" aria-hidden="true"></span>' +
                    '        <strong id="aimentor-provider-active-label"></strong>' +
                    '        <span id="aimentor-provider-active-badge" class="aimentor-provider-badge"></span>' +
                    '      </div>' +
                    '      <p id="aimentor-provider-summary" class="aimentor-provider-description" style="margin:0;font-size:13px;color:#4b5563;"></p>' +
                    '      <label for="aimentor-modal-task" class="aimentor-modal__label">' + generationLabel + '</label>' +
                    '      <select id="aimentor-modal-task" class="aimentor-modal__select"' + (allowCanvas ? '' : ' disabled') + '>' +
                    '        <option value="content">' + pageCopyLabel + '</option>' +
                    '        <option value="canvas"' + (allowCanvas ? '' : ' disabled') + '>' + pageLayoutLabel + '</option>' +
                    '      </select>' +
                    '      <label for="aimentor-modal-tier" class="aimentor-modal__label">' + performanceLabel + '</label>' +
                    '      <select id="aimentor-modal-tier" class="aimentor-modal__select">' +
                    '        <option value="fast">' + fastLabel + '</option>' +
                    '        <option value="quality">' + qualityLabel + '</option>' +
                    '      </select>' +
                    '      <p id="aimentor-modal-summary" class="aimentor-context-summary" aria-live="polite" style="margin:0;font-weight:600;color:#111827;"></p>' +
                    '      <label for="aimentor-prompt" class="aimentor-modal__label">' + promptLabel + '</label>' +
                    '      <textarea id="aimentor-prompt" rows="4" placeholder="' + promptPlaceholder + '" style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;"></textarea>' +
                    '      <button type="button" id="aimentor-generate" class="button button-primary" style="width:100%;padding:12px;font-size:16px;font-weight:600;">' + askLabel + '</button>' +
                    '      <div id="aimentor-result" style="min-height:38px;padding:12px;background:#f3f4f6;border-radius:6px;color:#111827;"></div>' +
                    '    </div>' +
                    '  </div>' +
                    '</div>';
                $('body').append(modalHtml);
            }

            var $modal = $('#aimentor-modal');
            var $close = $('#aimentor-modal-close');
            var $providerRadios = $('input[name="aimentor-modal-provider"]');
            var $task = $('#aimentor-modal-task');
            var $tier = $('#aimentor-modal-tier');
            var $summary = $('#aimentor-modal-summary');
            var $prompt = $('#aimentor-prompt');
            var $generate = $('#aimentor-generate');
            var $result = $('#aimentor-result');
            var ui = {
                icon: $('#aimentor-provider-active-icon'),
                label: $('#aimentor-provider-active-label'),
                summary: $('#aimentor-provider-summary'),
                badge: $('#aimentor-provider-active-badge'),
                button: $generate,
                heading: $('#aimentor-modal-heading-text span').last()
            };

            $modal.show();
            $close.off('click.aimentor').on('click.aimentor', function() {
                $modal.hide();
            });

            var modalState = api.state.modal;
            modalState.provider = sanitizeProvider(modalState.provider || getDefaultProvider());
            modalState.task = sanitizeTask(modalState.task, allowCanvas);
            modalState.tier = sanitizeTier(modalState.tier);

            $providerRadios.prop('checked', false);
            $providerRadios.filter('[value="' + modalState.provider + '"]').prop('checked', true);
            if (!$providerRadios.filter(':checked').length && $providerRadios.length) {
                modalState.provider = sanitizeProvider($providerRadios.first().val());
                $providerRadios.first().prop('checked', true);
            }

            if ($task.length) {
                $task.val(modalState.task);
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(modalState.tier);
            }

            applyProviderMeta(modalState.provider, ui);
            updateSummaryText($summary, modalState.provider, modalState);

            if (!ensureAjaxConfig($modal, $generate)) {
                return;
            }

            $providerRadios.off('change.aimentor').on('change.aimentor', function() {
                modalState.provider = sanitizeProvider($(this).val());
                applyProviderMeta(modalState.provider, ui);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            $task.off('change.aimentor').on('change.aimentor', function() {
                modalState.task = sanitizeTask($(this).val(), allowCanvas);
                $(this).val(modalState.task);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            $tier.off('change.aimentor').on('change.aimentor', function() {
                modalState.tier = sanitizeTier($(this).val());
                $(this).val(modalState.tier);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            $prompt.val('').focus();
            $result.empty();

            $generate.off('click.aimentor').on('click.aimentor', function() {
                var promptValue = ($prompt.val() || '').trim();
                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    $result.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    return;
                }

                var selection = {
                    task: sanitizeTask($task.val(), allowCanvas),
                    tier: sanitizeTier($tier.val())
                };
                modalState.task = selection.task;
                modalState.tier = selection.tier;

                var providerValue = modalState.provider;
                var providerMeta = applyProviderMeta(providerValue, ui);
                updateSummaryText($summary, providerValue, selection);

                var generatingMessage = getGeneratingMessage(providerMeta);
                $generate.prop('disabled', true).text(generatingMessage);
                $result.html('<p>' + escapeHtml(generatingMessage) + '</p><p>' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>');

                $.post(aimentorData.ajaxurl, {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerValue,
                    task: selection.task,
                    tier: selection.tier,
                    nonce: aimentorData.nonce
                }).done(function(response) {
                    $generate.prop('disabled', false);
                    applyProviderMeta(providerValue, ui);

                    if (response && response.data && response.data.provider) {
                        providerValue = sanitizeProvider(response.data.provider);
                        modalState.provider = providerValue;
                        $providerRadios.prop('checked', false);
                        $providerRadios.filter('[value="' + providerValue + '"]').prop('checked', true);
                        applyProviderMeta(providerValue, ui);
                    }

                    updateSummaryText($summary, providerValue, selection);

                    if (response && response.success) {
                        var summaryText = extractResponseSummary(response, providerValue, selection);
                        if (response.data && response.data.canvas_json && window.elementorFrontend && elementorFrontend.elementsHandler) {
                            elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                        }
                        if (response.data && response.data.html) {
                            var snippet = response.data.html.substring(0, 160);
                            var snippetHtml = snippet ? '<br><small>' + escapeHtml(snippet + (response.data.html.length > 160 ? '…' : '')) + '</small>' : '';
                            $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + snippetHtml + '</p>');
                        } else {
                            $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + '</p>');
                        }
                        recordHistoryEntry(promptValue, providerValue);
                    } else {
                        var message = response && response.data ? response.data : 'Unknown error';
                        if (typeof message === 'object' && message !== null) {
                            message = message.message || message.error || 'Unknown error';
                        }
                        var errorPrefix = strings.errorPrefix || 'Error:';
                        $result.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(String(message)) + '</p>');
                    }
                }).fail(function() {
                    $generate.prop('disabled', false);
                    applyProviderMeta(modalState.provider, ui);
                    var errorPrefix = strings.errorPrefix || 'Error:';
                    var errorMessage = strings.unknownError || 'Request failed.';
                    $result.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(errorMessage) + '</p>');
                });
            });
        };

        window.AiMentorElementorUI = api;

        if (window.elementor && window.elementor.hooks && typeof window.elementor.hooks.addAction === 'function') {
            [
                'panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/aimentor-ai-generator/controls/write_with_jaggrok/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event'
            ].forEach(function(hookName) {
                window.elementor.hooks.addAction(hookName, api.openModal);
            });
        }

        function buildProviderDefaults() {
            var defaults = {};
            Object.keys(providersMeta || {}).forEach(function(key) {
                var meta = providersMeta[key] || {};
                var label = meta.label || providerLabels[key] || key;
                defaults[key] = Object.assign({
                    label: label,
                    icon: meta.icon || '🤖',
                    summary: meta.summary || providerSummaries[key] || formatString(strings.contentGenerated, label || key),
                    badgeText: meta.badgeText || label,
                    badgeColor: meta.badgeColor || '#444444'
                }, meta);
            });
            if (!Object.keys(defaults).length) {
                defaults = {
                    grok: {
                        label: providerLabels.grok || 'xAI Grok',
                        icon: '🚀',
                        summary: providerSummaries.grok || formatString(strings.contentGenerated, providerLabels.grok || 'xAI Grok'),
                        badgeText: 'xAI',
                        badgeColor: '#1E1E1E'
                    },
                    openai: {
                        label: providerLabels.openai || 'OpenAI',
                        icon: '🔷',
                        summary: providerSummaries.openai || formatString(strings.contentGenerated, providerLabels.openai || 'OpenAI'),
                        badgeText: 'OpenAI',
                        badgeColor: '#2B8CFF'
                    }
                };
            }
            return defaults;
        }

        function buildProviderMap(defaults) {
            var map = Object.assign({}, defaults);
            Object.keys(providerLabels || {}).forEach(function(key) {
                var label = providerLabels[key];
                var meta = map[key] || {};
                meta.label = label;
                meta.summary = providerSummaries[key] || meta.summary || formatString(strings.contentGenerated, label);
                map[key] = meta;
            });
            return map;
        }

        function sanitizeProvider(value) {
            var providers = window.AiMentorProviders || {};
            if (value && providers[value]) {
                return value;
            }
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : (value || 'grok');
        }

        function getProviderMeta(key) {
            var providers = window.AiMentorProviders || {};
            var meta = providers[key];
            if (!meta) {
                var fallbackLabel = key || 'AiMentor';
                return {
                    key: key,
                    label: fallbackLabel,
                    icon: '🤖',
                    summary: formatString(strings.contentGenerated, fallbackLabel),
                    badgeText: fallbackLabel,
                    badgeColor: '#444444'
                };
            }
            var result = Object.assign({}, meta);
            result.key = key;
            if (!result.summary) {
                result.summary = formatString(strings.contentGenerated, result.label || key);
            }
            if (!result.badgeText) {
                result.badgeText = result.label || key;
            }
            if (!result.badgeColor) {
                result.badgeColor = '#444444';
            }
            if (!result.icon) {
                result.icon = '🤖';
            }
            return result;
        }

        function getTaskLabel(task) {
            if (task === 'canvas') {
                return strings.layoutLabel || strings.pageLayout || 'Page Layout';
            }
            return strings.copyLabel || strings.pageCopy || 'Page Copy';
        }

        function getTierLabel(tier) {
            if (tier === 'quality') {
                return strings.qualityLabel || 'Quality';
            }
            return strings.fastLabel || 'Fast';
        }

        function getModelLabel(providerKey, task, tier) {
            var providerEntry = (modelLabels || {})[providerKey];
            if (!providerEntry) {
                return '';
            }
            if (typeof providerEntry === 'string') {
                return providerEntry;
            }
            if (providerEntry && typeof providerEntry === 'object') {
                var taskEntry = providerEntry[task];
                if (typeof taskEntry === 'string') {
                    return taskEntry;
                }
                if (taskEntry && typeof taskEntry === 'object' && taskEntry[tier] && typeof taskEntry[tier] === 'string') {
                    return taskEntry[tier];
                }
                if (providerEntry[tier] && typeof providerEntry[tier] === 'string') {
                    return providerEntry[tier];
                }
            }
            return '';
        }

        function buildSummary(providerKey, task, tier) {
            var taskLabel = getTaskLabel(task || 'content');
            var tierLabel = getTierLabel(tier || 'fast');
            var separator = typeof strings.summarySeparator === 'string' ? strings.summarySeparator : ' • ';
            var summary = taskLabel + separator + tierLabel;
            var modelLabel = getModelLabel(providerKey, task, tier);
            if (modelLabel) {
                var powered = strings.summaryPoweredBy ? strings.summaryPoweredBy.replace('%s', modelLabel) : '– powered by ' + modelLabel;
                summary += ' ' + powered;
            }
            return summary;
        }

        function sanitizeTask(value, allowCanvas) {
            var valid = ['content'];
            if (allowCanvas) {
                valid.push('canvas');
            }
            return valid.indexOf(value) !== -1 ? value : valid[0];
        }

        function sanitizeTier(value) {
            var valid = ['fast', 'quality'];
            return valid.indexOf(value) !== -1 ? value : valid[0];
        }

        function buildProviderOptions(defaultProvider) {
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            if (!keys.length) {
                providers = providerDefaults;
                keys = Object.keys(providers);
            }
            var optionsHtml = '';
            keys.forEach(function(key, index) {
                var meta = providers[key] || {};
                var icon = meta.icon ? '<span class="aimentor-provider-icon" aria-hidden="true">' + escapeHtml(meta.icon) + '</span>' : '';
                var label = escapeHtml(meta.label || key);
                var valueAttr = escapeHtml(key);
                var isChecked = (key === defaultProvider) || (!defaultProvider && index === 0);
                var checkedAttr = isChecked ? ' checked' : '';
                var badge = meta.badgeText ? '<span class="aimentor-provider-badge" style="background-color:' + escapeHtml(meta.badgeColor || '#444444') + '">' + escapeHtml(meta.badgeText) + '</span>' : '';
                optionsHtml += '<label class="aimentor-provider-option"><input type="radio" name="aimentor-modal-provider" value="' + valueAttr + '"' + checkedAttr + '>' + icon + '<span class="aimentor-provider-name">' + label + '</span>' + badge + '</label>';
            });
            return optionsHtml;
        }

        function getGeneratingMessage(meta) {
            if (strings.generatingWith) {
                return strings.generatingWith.replace('%s', meta.label || meta.key || '');
            }
            return 'Generating with ' + (meta.label || 'AiMentor') + '…';
        }

        function ensureAjaxConfig($container, $button) {
            if (aimentorData.ajaxurl && aimentorData.nonce) {
                return true;
            }
            var message = strings.missingConfig || 'AiMentor AJAX configuration is missing. Please ensure the plugin assets are enqueued properly.';
            var $notice = $('<div class="notice notice-error aimentor-missing-config"><p></p></div>');
            $notice.find('p').text(message);
            var $target = $container && $container.length ? $container : $('body');
            $target.prepend($notice);
            if ($button && $button.length) {
                $button.prop('disabled', true);
            }
            console.error('AiMentor AJAX configuration missing: expected window.aimentorAjax.');
            return false;
        }

        function extractResponseSummary(response, providerKey, selection) {
            if (response && response.data) {
                if (typeof response.data.summary === 'string' && response.data.summary.trim()) {
                    return response.data.summary.trim();
                }
                if (typeof response.data.message === 'string' && response.data.message.trim()) {
                    return response.data.message.trim();
                }
            }
            if (selection && selection.task && selection.tier) {
                return buildSummary(providerKey, selection.task, selection.tier);
            }
            var meta = getProviderMeta(providerKey);
            return formatString(strings.contentGenerated, meta.label || providerKey);
        }

        function updateSummaryText($summary, providerKey, selection) {
            if (!$summary || !$summary.length) {
                return;
            }
            var summary = buildSummary(providerKey, selection.task, selection.tier);
            $summary.text(summary);
        }

        function applyProviderMeta(providerKey, ui) {
            var meta = getProviderMeta(providerKey);
            if (ui.icon && ui.icon.length) {
                ui.icon.text(meta.icon || '🤖');
            }
            if (ui.label && ui.label.length) {
                ui.label.text(meta.label || providerKey);
            }
            if (ui.summary && ui.summary.length) {
                ui.summary.text(meta.summary || '');
            }
            if (ui.badge && ui.badge.length) {
                ui.badge.text(meta.badgeText || providerKey);
                ui.badge.css('background-color', meta.badgeColor || '#444444');
            }
            if (ui.button && ui.button.length) {
                var buttonText = strings.askAiMentorWith ? strings.askAiMentorWith.replace('%s', meta.label || providerKey) : (strings.generateWith ? strings.generateWith.replace('%s', meta.label || providerKey) : 'Generate with ' + (meta.label || providerKey));
                ui.button.text(buttonText);
            }
            if (ui.heading && ui.heading.length) {
                var headingText = strings.writeWith ? strings.writeWith.replace('%s', meta.label || providerKey) : 'Write with ' + (meta.label || providerKey);
                ui.heading.text(headingText);
            }
            return meta;
        }

        function getDefaultProvider() {
            if (typeof aimentorData.provider === 'string' && aimentorData.provider) {
                return sanitizeProvider(aimentorData.provider);
            }
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : 'grok';
        }
    });
})(jQuery, window);
