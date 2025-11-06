// ============================================================================
// AiMentor ADMIN SETTINGS JS v1.3.18
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
    var tabAction = (typeof aimentorAjax !== 'undefined' && aimentorAjax.tabAction) ? aimentorAjax.tabAction : 'aimentor_load_settings_tab';
    var tabNonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.tabNonce) ? aimentorAjax.tabNonce : '';
    var EVENT_NAMESPACE = '.aimentorSettings';

    function resolveContext(context) {
        if (!context) {
            return null;
        }

        if (context.jquery) {
            return context;
        }

        return $(context);
    }

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
    var tabLoadingMessage = getString('tabLoading', 'Loading…');
    var tabErrorMessage = getString('tabLoadError', 'Unable to load tab content. Please try again.');
    var savedPromptsEndpoint = (typeof aimentorAjax !== 'undefined' && aimentorAjax.promptsEndpoint) ? String(aimentorAjax.promptsEndpoint) : '';
    var savedPromptsRestNonce = (typeof aimentorAjax !== 'undefined' && aimentorAjax.restNonce) ? String(aimentorAjax.restNonce) : '';
    var savedPromptsStore = normalizeSavedPrompts((typeof aimentorAjax !== 'undefined' && aimentorAjax.savedPrompts) ? aimentorAjax.savedPrompts : {});
    var savedPromptCreateSuccessMessage = getString('savedPromptCreateSuccess', 'Prompt saved.');
    var savedPromptCreateErrorMessage = getString('savedPromptCreateError', 'Unable to save the prompt. Please try again.');
    var savedPromptDeleteConfirmMessage = getString('savedPromptDeleteConfirm', 'Delete this prompt? This cannot be undone.');
    var savedPromptDeleteSuccessMessage = getString('savedPromptDeleteSuccess', 'Prompt deleted.');
    var savedPromptDeleteErrorMessage = getString('savedPromptDeleteError', 'Unable to delete the prompt. Please try again.');
    var savedPromptPermissionMessage = getString('savedPromptPermissionError', 'You do not have permission to manage saved prompts.');
    var savedPromptListEmptyMessage = getString('savedPromptListEmpty', 'No prompts saved yet.');
    var savedPromptColumnLabel = getString('savedPromptColumnLabel', 'Label');
    var savedPromptColumnPrompt = getString('savedPromptColumnPrompt', 'Prompt');
    var savedPromptColumnActions = getString('savedPromptColumnActions', 'Actions');
    var savedPromptDeleteLabel = getString('savedPromptDeleteLabel', 'Delete');

    function buildClassList() {
        return statusStates.map(function(state) {
            return 'aimentor-status-badge--' + state;
        }).join(' ');
    }

    var badgeClassList = buildClassList();
    var wpAjax = (typeof window.wp !== 'undefined' && window.wp.ajax && typeof window.wp.ajax.post === 'function') ? window.wp.ajax : null;
    var $tabsRoot = $('.aimentor-settings-tabs');
    var $tabContainer = $('#aimentor-settings-tab-content');
    var $tabFallback = $('#aimentor-settings-tab-fallback');
    var $tabButtons = $('.aimentor-settings-tabs__tab');
    var loadedTabs = {};
    var activeTab = $tabContainer.length ? String($tabContainer.data('active-tab') || '') : '';
    var rootDefaultTab = '';

    if ($tabsRoot.length) {
        if (typeof $tabsRoot.data('defaultTab') !== 'undefined') {
            rootDefaultTab = $tabsRoot.data('defaultTab');
        } else if (typeof $tabsRoot.data('default-tab') !== 'undefined') {
            rootDefaultTab = $tabsRoot.data('default-tab');
        }
    }

    var defaultTab = rootDefaultTab ? String(rootDefaultTab) : activeTab;
    var suppressHashChange = false;

    if ($tabsRoot.length) {
        var dataAction = $tabsRoot.data('tabAction');
        var dataNonce = $tabsRoot.data('tabNonce');

        if (dataAction) {
            tabAction = String(dataAction);
        }

        if (dataNonce) {
            tabNonce = String(dataNonce);
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function renderTabLoading() {
        return '<div class="aimentor-settings-tab-loading" role="status" aria-live="polite">'
            + '<span class="aimentor-settings-tab-loading__spinner" aria-hidden="true"></span>'
            + '<span>' + escapeHtml(tabLoadingMessage) + '</span>'
            + '</div>';
    }

    function renderTabError(message) {
        var text = message ? escapeHtml(message) : escapeHtml(tabErrorMessage);
        return '<div class="aimentor-settings-error" role="alert">' + text + '</div>';
    }

    function fetchTabContent(tabSlug) {
        if (!adminAjaxUrl) {
            return $.Deferred().reject().promise();
        }

        return $.ajax({
            url: adminAjaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: tabAction,
                tab: tabSlug,
                nonce: tabNonce
            }
        });
    }

    function getTabFromHash() {
        var hash = window.location.hash || '';
        var prefix = '#aimentor-tab=';

        if (hash.indexOf(prefix) === 0) {
            return hash.substring(prefix.length);
        }

        return '';
    }

    function updateTabHash(tabSlug) {
        if (!tabSlug) {
            return;
        }

        var hash = '#aimentor-tab=' + tabSlug;

        if (window.location.hash === hash) {
            return;
        }

        suppressHashChange = true;
        window.location.hash = hash;
        window.setTimeout(function() {
            suppressHashChange = false;
        }, 0);
    }

    function toggleProviderSections(provider) {
        var key = String(provider || '');
        $('.aimentor-provider-group, .jaggrok-provider-group').each(function() {
            var $group = $(this);
            var groupProvider = String($group.data('provider') || '');
            var isMatch = !groupProvider || groupProvider === key;
            $group.toggle(isMatch).attr('aria-hidden', isMatch ? 'false' : 'true');
        });

        $('.aimentor-provider-help, .jaggrok-provider-help').each(function() {
            var $help = $(this);
            var helpProvider = String($help.data('provider') || '');
            var isMatch = !helpProvider || helpProvider === key;
            $help.toggle(isMatch).attr('aria-hidden', isMatch ? 'false' : 'true');
        });
    }

    function updateContextRow(row) {
        var $row = (row && row.jquery) ? row : $(row);

        if (!$row.length) {
            return;
        }

        var $provider = $row.find('.aimentor-context-provider');
        var $model = $row.find('.aimentor-context-model');

        if (!$provider.length || !$model.length) {
            return;
        }

        var providerValue = String($provider.val() || '');
        var $options = $model.find('option[data-provider]');
        var hasEnabled = false;

        $options.each(function() {
            var $option = $(this);
            var optionProvider = String($option.data('provider') || '');
            var isMatch = !providerValue || optionProvider === providerValue;
            $option.prop('disabled', !isMatch);
            if (isMatch) {
                hasEnabled = true;
            }
        });

        var $selected = $model.find('option:selected');

        if (!$selected.length || $selected.prop('disabled')) {
            var $replacement = $options.filter(function() {
                return String($(this).data('provider') || '') === providerValue;
            }).first();

            if ($replacement.length) {
                $model.val($replacement.val());
            } else if (hasEnabled) {
                var $fallback = $options.filter(function() {
                    return !$(this).prop('disabled');
                }).first();

                if ($fallback.length) {
                    $model.val($fallback.val());
                }
            } else {
                $model.val('');
            }
        }
    }

    function initializeProviderControls(context) {
        var $context = resolveContext(context);
        var $inputs = ($context && $context.length) ? $context.find('input[name="aimentor_provider"]') : $('input[name="aimentor_provider"]');

        if (!$inputs.length) {
            return;
        }

        var $checked = $inputs.filter(':checked').first();
        var provider = $checked.length ? String($checked.val()) : String($inputs.first().val() || '');
        toggleProviderSections(provider);
    }

    function initializeContextControls(context) {
        var $context = resolveContext(context);
        var $providers = ($context && $context.length) ? $context.find('.aimentor-context-provider') : $('.aimentor-context-provider');

        if (!$providers.length) {
            return;
        }

        $providers.each(function() {
            updateContextRow($(this).closest('tr'));
        });
    }

    function initializeUsageMetricsInContext(context) {
        var $context = resolveContext(context);
        var $metrics = ($context && $context.length) ? $context.find('#aimentor-usage-metrics') : $('#aimentor-usage-metrics');

        if (!$metrics.length || !usageNonce) {
            return;
        }

        refreshUsageMetrics();
        scheduleUsageRefresh();
    }

    function syncLegacyModelInputs(context) {
        var $context = resolveContext(context);
        var $grokSelect = ($context && $context.length) ? $context.find('#aimentor_provider_models_grok') : $('#aimentor_provider_models_grok');
        var $anthropicSelect = ($context && $context.length) ? $context.find('#aimentor_provider_models_anthropic') : $('#aimentor_provider_models_anthropic');
        var $openaiSelect = ($context && $context.length) ? $context.find('#aimentor_provider_models_openai') : $('#aimentor_provider_models_openai');

        if ($grokSelect.length) {
            $('#aimentor_model_legacy').val($grokSelect.val());
        }

        if ($anthropicSelect.length) {
            $('#aimentor_anthropic_model_legacy').val($anthropicSelect.val());
        }

        if ($openaiSelect.length) {
            $('#aimentor_openai_model_legacy').val($openaiSelect.val());
        }
    }

    function initializeLogNonce(context) {
        var $context = resolveContext(context);
        var $form = ($context && $context.length) ? $context.find('#aimentor-error-log-form') : $('#aimentor-error-log-form');

        if ($form.length) {
            var formNonce = $form.data('nonce');

            if (formNonce) {
                logNonce = String(formNonce);
            }
        }
    }

    function normalizeSavedPromptEntry(entry, scope) {
        if (!entry || typeof entry !== 'object') {
            return null;
        }

        var normalizedScope = scope === 'global' ? 'global' : 'user';
        if (entry.scope === 'global') {
            normalizedScope = 'global';
        } else if (entry.scope === 'user') {
            normalizedScope = 'user';
        }

        var id = String(entry.id || '').trim();
        var prompt = String(entry.prompt || '').trim();
        var label = String(entry.label || '').trim();

        if (!id || !prompt) {
            return null;
        }

        if (!label) {
            label = prompt.length > 60 ? prompt.substring(0, 57) + '…' : prompt;
        }

        return {
            id: id,
            label: label,
            prompt: prompt,
            scope: normalizedScope
        };
    }

    function normalizeSavedPrompts(data) {
        var normalized = { global: [], user: [] };

        if (!data || typeof data !== 'object') {
            return normalized;
        }

        ['user', 'global'].forEach(function(scope) {
            var entries = data[scope];

            if (!Array.isArray(entries)) {
                return;
            }

            entries.forEach(function(entry) {
                var normalizedEntry = normalizeSavedPromptEntry(entry, scope);

                if (normalizedEntry) {
                    normalized[scope].push(normalizedEntry);
                }
            });
        });

        return normalized;
    }

    function cloneSavedPromptEntry(entry) {
        if (!entry || typeof entry !== 'object') {
            return null;
        }

        return {
            id: String(entry.id || ''),
            label: String(entry.label || ''),
            prompt: String(entry.prompt || ''),
            scope: entry.scope === 'global' ? 'global' : 'user'
        };
    }

    function cloneSavedPromptsStore() {
        return {
            global: savedPromptsStore.global.map(cloneSavedPromptEntry).filter(Boolean),
            user: savedPromptsStore.user.map(cloneSavedPromptEntry).filter(Boolean)
        };
    }

    function setSavedPromptsStore(data, options) {
        var settings = $.extend({ silent: false }, options);
        savedPromptsStore = normalizeSavedPrompts(data);

        if (typeof window.aimentorAjax !== 'undefined') {
            window.aimentorAjax.savedPrompts = cloneSavedPromptsStore();
        }

        if (!settings.silent) {
            $(document).trigger('aimentor:saved-prompts-refreshed', { prompts: cloneSavedPromptsStore() });
        }
    }

    function resolveSavedPromptEndpoint(container) {
        var $container = container && container.jquery ? container : $(container);
        var endpoint = savedPromptsEndpoint;

        if ($container && $container.length) {
            var dataEndpoint = $container.data('restEndpoint');
            if (dataEndpoint) {
                endpoint = String(dataEndpoint);
            }
        }

        return endpoint;
    }

    function resolveSavedPromptNonce(container) {
        var $container = container && container.jquery ? container : $(container);
        var nonce = savedPromptsRestNonce;

        if ($container && $container.length) {
            var dataNonce = $container.data('restNonce');

            if (dataNonce) {
                nonce = String(dataNonce);
            } else {
                var $field = $container.find('input[name="aimentor_rest_nonce"]');

                if ($field.length && $field.val()) {
                    nonce = String($field.val());
                    $container.data('restNonce', nonce);
                }
            }
        }

        return nonce;
    }

    function updateSavedPromptNonce(container, nonce) {
        if (!nonce) {
            return;
        }

        var value = String(nonce);
        savedPromptsRestNonce = value;

        if (typeof window.aimentorAjax !== 'undefined') {
            window.aimentorAjax.restNonce = value;
        }

        var $container = container && container.jquery ? container : $(container);

        if ($container && $container.length) {
            $container.data('restNonce', value);
            var $field = $container.find('input[name="aimentor_rest_nonce"]');
            if ($field.length) {
                $field.val(value);
            }
        }
    }

    function buildSavedPromptUrl(endpoint, path, query) {
        var base = String(endpoint || '').replace(/\/?$/, '');
        var suffix = path ? '/' + String(path).replace(/^\//, '') : '';
        var queryString = '';

        if (query && typeof query === 'object') {
            queryString = $.param(query);
            if (queryString) {
                queryString = '?' + queryString;
            }
        }

        return base + suffix + queryString;
    }

    function buildSavedPromptError(status, data) {
        var error = new Error((data && data.message) ? data.message : '');
        error.status = status || 0;
        error.data = data || {};
        error.code = (data && data.code) ? data.code : '';
        return error;
    }

    function resolveSavedPromptErrorMessage(error, fallback) {
        if (!error) {
            return fallback;
        }

        if (error.data && typeof error.data.message === 'string' && error.data.message.trim()) {
            return error.data.message.trim();
        }

        if (error.status === 403) {
            return savedPromptPermissionMessage;
        }

        if (typeof error.message === 'string' && error.message.trim()) {
            return error.message.trim();
        }

        return fallback;
    }

    function showSavedPromptNotice(container, message, type) {
        var $container = container && container.jquery ? container : $(container);

        if (!$container || !$container.length) {
            return;
        }

        var $notice = $container.find('.aimentor-saved-prompts__notice');

        if (!$notice.length) {
            return;
        }

        if (!message) {
            $notice.attr('hidden', 'hidden').text('').removeClass('notice notice-success notice-error');
            return;
        }

        var isError = type === 'error';
        $notice.removeClass('notice-success notice-error').addClass('notice');
        $notice.addClass(isError ? 'notice-error' : 'notice-success');
        $notice.text(message).removeAttr('hidden');
    }

    function renderSavedPromptTable(entries, scope) {
        var safeScope = scope === 'global' ? 'global' : 'user';
        var rows = Array.isArray(entries) ? entries : [];

        if (!rows.length) {
            return '<p class="description aimentor-saved-prompts__empty" data-scope="' + escapeHtml(safeScope) + '">' + escapeHtml(savedPromptListEmptyMessage) + '</p>';
        }

        var html = '';
        html += '<table class="widefat striped aimentor-saved-prompts__table" data-scope="' + escapeHtml(safeScope) + '">';
        html += '<thead><tr>'
            + '<th scope="col">' + escapeHtml(savedPromptColumnLabel) + '</th>'
            + '<th scope="col">' + escapeHtml(savedPromptColumnPrompt) + '</th>'
            + '<th scope="col" class="aimentor-saved-prompts__actions-header">' + escapeHtml(savedPromptColumnActions) + '</th>'
            + '</tr></thead>';
        html += '<tbody>';

        rows.forEach(function(entry) {
            if (!entry) {
                return;
            }

            var id = escapeHtml(String(entry.id || ''));
            var label = escapeHtml(String(entry.label || ''));
            var excerptSource = String(entry.prompt || '');
            var excerpt = excerptSource;

            if (excerpt.length > 0) {
                excerpt = excerpt.replace(/\s+/g, ' ');
                if (excerpt.length > 180) {
                    excerpt = excerpt.substring(0, 177) + '…';
                }
            }

            html += '<tr data-id="' + id + '" data-scope="' + escapeHtml(entry.scope === 'global' ? 'global' : 'user') + '">';
            html += '<td><strong>' + label + '</strong></td>';
            html += '<td><div class="aimentor-saved-prompts__excerpt"><span>' + escapeHtml(excerpt) + '</span></div></td>';
            html += '<td class="aimentor-saved-prompts__actions">'
                + '<button type="button" class="button button-link-delete aimentor-saved-prompts__delete" data-id="' + id + '" data-scope="' + escapeHtml(entry.scope === 'global' ? 'global' : 'user') + '" data-label="' + label + '">' + escapeHtml(savedPromptDeleteLabel) + '</button>'
                + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';

        return html;
    }

    function updateSavedPromptLists(container) {
        var $containers;

        if (container && container.jquery) {
            $containers = container;
        } else if (container) {
            $containers = $(container);
        }

        if (!$containers || !$containers.length) {
            $containers = $('.aimentor-saved-prompts');
        }

        if (!$containers.length) {
            return;
        }

        $containers.each(function() {
            var $root = $(this);
            var $userList = $root.find('.aimentor-saved-prompts__list[data-scope="user"]');
            var $globalList = $root.find('.aimentor-saved-prompts__list[data-scope="global"]');

            if ($userList.length) {
                $userList.html(renderSavedPromptTable(savedPromptsStore.user, 'user'));
            }

            if ($globalList.length) {
                $globalList.html(renderSavedPromptTable(savedPromptsStore.global, 'global'));
            }

            $root.data('initialPrompts', cloneSavedPromptsStore());
        });
    }

    function performSavedPromptRequest(container, method, path, body, query) {
        var $container = container && container.jquery ? container : $(container);
        var endpoint = resolveSavedPromptEndpoint($container);
        var nonce = resolveSavedPromptNonce($container);

        if (!endpoint || !nonce) {
            return $.Deferred().reject(buildSavedPromptError(0, { message: savedPromptCreateErrorMessage })).promise();
        }

        var url = buildSavedPromptUrl(endpoint, path, query);
        var headers = {
            'X-WP-Nonce': nonce,
            'Accept': 'application/json'
        };
        var supportsFetch = typeof window.fetch === 'function';

        if (supportsFetch) {
            var fetchOptions = {
                method: method,
                headers: headers,
                credentials: 'same-origin'
            };

            if (body && method !== 'DELETE') {
                fetchOptions.headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(body);
            }

            return window.fetch(url, fetchOptions).then(function(response) {
                var newNonce = response && response.headers ? response.headers.get('X-WP-Nonce') : null;

                if (newNonce) {
                    updateSavedPromptNonce($container, newNonce);
                }

                return response.json().catch(function() {
                    return {};
                }).then(function(data) {
                    if (!response.ok) {
                        throw buildSavedPromptError(response.status, data);
                    }
                    return data;
                });
            });
        }

        var ajaxOptions = {
            url: url,
            method: method,
            headers: headers,
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            }
        };

        if (body && method !== 'DELETE') {
            ajaxOptions.data = JSON.stringify(body);
            ajaxOptions.processData = false;
            ajaxOptions.contentType = 'application/json';
        } else {
            ajaxOptions.processData = false;
        }

        var deferred = $.Deferred();

        $.ajax(ajaxOptions).done(function(data, textStatus, jqXHR) {
            var newNonce = jqXHR.getResponseHeader('X-WP-Nonce');

            if (newNonce) {
                updateSavedPromptNonce($container, newNonce);
            }

            deferred.resolve(data || {});
        }).fail(function(jqXHR) {
            var responseJSON = jqXHR && jqXHR.responseJSON ? jqXHR.responseJSON : {};
            deferred.reject(buildSavedPromptError(jqXHR ? jqXHR.status : 0, responseJSON));
        });

        return deferred.promise();
    }

    function initializeSavedPrompts(context) {
        var $context = resolveContext(context);
        var $containers = ($context && $context.length) ? $context.find('.aimentor-saved-prompts') : $('.aimentor-saved-prompts');

        if (!$containers.length) {
            return;
        }

        $containers.each(function() {
            var $container = $(this);
            var containerEndpoint = $container.data('restEndpoint');
            var containerNonce = $container.data('restNonce');
            var initialPrompts = $container.data('initialPrompts');

            if (containerEndpoint) {
                savedPromptsEndpoint = String(containerEndpoint);
            }

            if (containerNonce) {
                savedPromptsRestNonce = String(containerNonce);
            } else {
                savedPromptsRestNonce = resolveSavedPromptNonce($container);
            }

            if (initialPrompts) {
                if (typeof initialPrompts === 'string') {
                    try {
                        initialPrompts = JSON.parse(initialPrompts);
                    } catch (error) {
                        initialPrompts = null;
                    }
                }

                if (initialPrompts) {
                    setSavedPromptsStore(initialPrompts, { silent: true });
                }
            }

            updateSavedPromptNonce($container, savedPromptsRestNonce);
            updateSavedPromptLists($container);
        });
    }

    function initializeDynamicContent(context) {
        var $context = resolveContext(context);

        initializeStatusMetrics();
        initializeProviderControls($context);
        initializeContextControls($context);
        initializeUsageMetricsInContext($context);
        syncLegacyModelInputs($context);
        initializeLogNonce($context);
        initializeSavedPrompts($context);
    }

    $(document)
        .off('change' + EVENT_NAMESPACE, 'input[name="aimentor_provider"]')
        .on('change' + EVENT_NAMESPACE, 'input[name="aimentor_provider"]', function() {
            var provider = String($(this).val() || '');
            toggleProviderSections(provider);
            syncLegacyModelInputs();
        });

    $(document)
        .off('change' + EVENT_NAMESPACE, '.aimentor-context-provider')
        .on('change' + EVENT_NAMESPACE, '.aimentor-context-provider', function() {
            var $row = $(this).closest('tr');
            updateContextRow($row);
            syncLegacyModelInputs();
        });

    $(document)
        .off('change' + EVENT_NAMESPACE, '.aimentor-context-model')
        .on('change' + EVENT_NAMESPACE, '.aimentor-context-model', function() {
            syncLegacyModelInputs();
        });

    $(document)
        .off('submit' + EVENT_NAMESPACE, '.aimentor-saved-prompts__form')
        .on('submit' + EVENT_NAMESPACE, '.aimentor-saved-prompts__form', function(event) {
            event.preventDefault();

            var $form = $(this);
            var $container = $form.closest('.aimentor-saved-prompts');

            if (!$container.length) {
                return;
            }

            var labelValue = $.trim($form.find('input[name="label"]').val() || '');
            var promptValue = $.trim($form.find('textarea[name="prompt"]').val() || '');
            var scopeValue = String($form.find('[name="scope"]').val() || 'user');

            if (!promptValue) {
                showSavedPromptNotice($container, getString('promptRequired', 'Please enter a prompt!'), 'error');
                $form.find('textarea[name="prompt"]').focus();
                return;
            }

            var $submit = $form.find('button[type="submit"]');
            var wasDisabled = $submit.prop('disabled');
            $submit.prop('disabled', true).addClass('is-busy');
            showSavedPromptNotice($container, '', '');

            var request = performSavedPromptRequest($container, 'POST', '', {
                label: labelValue,
                prompt: promptValue,
                scope: scopeValue
            });

            if (!request || typeof request.then !== 'function') {
                showSavedPromptNotice($container, savedPromptCreateErrorMessage, 'error');
                $submit.prop('disabled', wasDisabled).removeClass('is-busy');
                return;
            }

            request.then(function(response) {
                if (response && response.prompts) {
                    setSavedPromptsStore(response.prompts);
                    updateSavedPromptLists($container);
                }

                showSavedPromptNotice($container, savedPromptCreateSuccessMessage, 'success');
                if ($form.length && $form[0] && typeof $form[0].reset === 'function') {
                    $form[0].reset();
                }

                if ($form.find('[name="scope"]').is('select')) {
                    $form.find('[name="scope"]').val(scopeValue);
                }
            }, function(error) {
                var message = resolveSavedPromptErrorMessage(error, savedPromptCreateErrorMessage);
                showSavedPromptNotice($container, message, 'error');
            }).then(function() {
                $submit.prop('disabled', wasDisabled).removeClass('is-busy');
            }, function() {
                $submit.prop('disabled', wasDisabled).removeClass('is-busy');
            });
        });

    $(document)
        .off('click' + EVENT_NAMESPACE, '.aimentor-saved-prompts__delete')
        .on('click' + EVENT_NAMESPACE, '.aimentor-saved-prompts__delete', function(event) {
            event.preventDefault();

            var $button = $(this);
            var $container = $button.closest('.aimentor-saved-prompts');

            if (!$container.length) {
                return;
            }

            var id = String($button.data('id') || '').trim();
            var scope = String($button.data('scope') || 'user');
            var label = String($button.data('label') || '');

            if (!id) {
                return;
            }

            var confirmMessage = savedPromptDeleteConfirmMessage;

            if (confirmMessage.indexOf('%s') !== -1) {
                confirmMessage = confirmMessage.replace('%s', label || savedPromptColumnLabel);
            } else if (label) {
                confirmMessage = confirmMessage + ' ' + label;
            }

            if (!window.confirm(confirmMessage)) {
                return;
            }

            var wasDisabled = $button.prop('disabled');
            $button.prop('disabled', true).addClass('is-busy');
            showSavedPromptNotice($container, '', '');

            var request = performSavedPromptRequest($container, 'DELETE', encodeURIComponent(id), null, { scope: scope });

            if (!request || typeof request.then !== 'function') {
                showSavedPromptNotice($container, savedPromptDeleteErrorMessage, 'error');
                $button.prop('disabled', wasDisabled).removeClass('is-busy');
                return;
            }

            request.then(function(response) {
                if (response && response.prompts) {
                    setSavedPromptsStore(response.prompts);
                    updateSavedPromptLists($container);
                }

                showSavedPromptNotice($container, savedPromptDeleteSuccessMessage, 'success');
            }, function(error) {
                var message = resolveSavedPromptErrorMessage(error, savedPromptDeleteErrorMessage);
                showSavedPromptNotice($container, message, 'error');
            }).then(function() {
                $button.prop('disabled', wasDisabled).removeClass('is-busy');
            }, function() {
                $button.prop('disabled', wasDisabled).removeClass('is-busy');
            });
        });

    $(document).on('aimentor:saved-prompts-refreshed' + EVENT_NAMESPACE, function(event, payload) {
        if (payload && payload.prompts) {
            setSavedPromptsStore(payload.prompts, { silent: true });
            updateSavedPromptLists();
        }
    });

    function setActiveTabState(tabSlug) {
        var slug = String(tabSlug || '');

        var labelledBy = '';

        $tabButtons.each(function() {
            var $button = $(this);
            var buttonSlug = String($button.data('tab') || '');
            var isActive = buttonSlug === slug;
            $button.toggleClass('nav-tab-active', isActive)
                .attr('aria-selected', isActive ? 'true' : 'false')
                .attr('tabindex', isActive ? '0' : '-1');

            if (isActive && !labelledBy) {
                labelledBy = $button.attr('id') || '';
            }
        });

        if ($tabContainer.length) {
            $tabContainer.attr('data-active-tab', slug);
            if (labelledBy) {
                $tabContainer.attr('aria-labelledby', labelledBy);
            }
        }

        activeTab = slug;
    }

    function activateTab(tabSlug, options) {
        var settings = $.extend({ updateHash: false, focus: false, force: false }, options);
        var targetSlug = String(tabSlug || '');

        if (!targetSlug) {
            targetSlug = defaultTab || ($tabButtons.length ? String($tabButtons.first().data('tab') || '') : '');
        }

        if (!targetSlug) {
            return;
        }

        var $button = $tabButtons.filter('[data-tab="' + targetSlug + '"]');

        if (!$button.length && defaultTab && targetSlug !== defaultTab) {
            targetSlug = defaultTab;
            $button = $tabButtons.filter('[data-tab="' + targetSlug + '"]');
        }

        if (!$button.length && $tabButtons.length) {
            $button = $tabButtons.first();
            targetSlug = String($button.data('tab') || targetSlug);
        }

        if (!settings.force && activeTab === targetSlug && loadedTabs.hasOwnProperty(targetSlug)) {
            if (settings.updateHash) {
                updateTabHash(targetSlug);
            }
            return;
        }

        setActiveTabState(targetSlug);

        if (settings.updateHash) {
            updateTabHash(targetSlug);
        }

        if (loadedTabs.hasOwnProperty(targetSlug)) {
            if ($tabContainer.length) {
                $tabContainer.attr('aria-busy', 'false').html(loadedTabs[targetSlug]);
                initializeDynamicContent($tabContainer);
                if (settings.focus) {
                    $tabContainer.attr('tabindex', '0').focus();
                }
            }
            return;
        }

        if ($tabContainer.length) {
            $tabContainer.attr('aria-busy', 'true').html(renderTabLoading());
        }

        fetchTabContent(targetSlug).done(function(response) {
            var html = '';

            if (response && response.success && response.data && typeof response.data.html !== 'undefined') {
                html = response.data.html;
            } else if (response && response.data && typeof response.data === 'string') {
                html = response.data;
            } else if (response && typeof response.html === 'string') {
                html = response.html;
            }

            if (!html) {
                if ($tabContainer.length) {
                    $tabContainer.attr('aria-busy', 'false').html(renderTabError());
                }
                return;
            }

            loadedTabs[targetSlug] = html;

            if ($tabContainer.length) {
                $tabContainer.attr('aria-busy', 'false').html(html);
                initializeDynamicContent($tabContainer);

                if (settings.focus) {
                    $tabContainer.attr('tabindex', '0').focus();
                }
            }
        }).fail(function(xhr) {
            var message = tabErrorMessage;

            if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                message = xhr.responseJSON.data.message;
            }

            if ($tabContainer.length) {
                $tabContainer.attr('aria-busy', 'false').html(renderTabError(message));
            }
        });
    }

    if (!defaultTab && $tabButtons.length) {
        defaultTab = String($tabButtons.first().data('tab') || '');
    }

    if (!activeTab) {
        activeTab = defaultTab;
    }

    if ($tabFallback.length && $tabContainer.length && activeTab) {
        loadedTabs[activeTab] = $tabFallback.html();
        $tabContainer.attr('aria-busy', 'false').html(loadedTabs[activeTab]);
        $tabFallback.remove();
        initializeDynamicContent($tabContainer);
    }

    if (activeTab) {
        setActiveTabState(activeTab);
    }

    var initialTab = getTabFromHash();

    if (initialTab && initialTab !== activeTab) {
        activateTab(initialTab, { updateHash: false, force: true });
    } else {
        if ($tabContainer.length && activeTab && !loadedTabs.hasOwnProperty(activeTab)) {
            activateTab(activeTab, { updateHash: false, focus: false, force: true });
        }

        if (initialTab && initialTab !== defaultTab) {
            updateTabHash(initialTab);
        }
    }

    $tabButtons.off('click' + EVENT_NAMESPACE).on('click' + EVENT_NAMESPACE, function(event) {
        event.preventDefault();
        var slug = String($(this).data('tab') || '');
        activateTab(slug, { updateHash: true, focus: true });
    });

    $tabButtons.off('keydown' + EVENT_NAMESPACE).on('keydown' + EVENT_NAMESPACE, function(event) {
        var key = event.key || event.which;
        var index = $tabButtons.index(this);

        if (key === 'ArrowRight' || key === 'ArrowDown' || key === 39 || key === 40) {
            event.preventDefault();
            var nextIndex = (index + 1) % $tabButtons.length;
            $tabButtons.eq(nextIndex).trigger('focus');
            return;
        }

        if (key === 'ArrowLeft' || key === 'ArrowUp' || key === 37 || key === 38) {
            event.preventDefault();
            var prevIndex = (index - 1 + $tabButtons.length) % $tabButtons.length;
            $tabButtons.eq(prevIndex).trigger('focus');
            return;
        }

        if (key === 'Home') {
            event.preventDefault();
            $tabButtons.eq(0).trigger('focus');
        }

        if (key === 'End') {
            event.preventDefault();
            $tabButtons.eq($tabButtons.length - 1).trigger('focus');
        }
    });

    $(window).off('hashchange' + EVENT_NAMESPACE).on('hashchange' + EVENT_NAMESPACE, function() {
        if (suppressHashChange) {
            return;
        }

        var slug = getTabFromHash();

        if (slug && slug !== activeTab) {
            activateTab(slug, { updateHash: false });
        }
    });

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

    function formatPercentage(value) {
        var numeric = typeof value === 'string' ? parseFloat(value) : value;

        if (!isFinite(numeric)) {
            return '';
        }

        var clamped = Math.min(Math.max(numeric, 0), 100);
        var decimals = (clamped % 1 === 0) ? 0 : 1;

        if (typeof window.Intl !== 'undefined' && typeof Intl.NumberFormat === 'function') {
            try {
                return new Intl.NumberFormat(undefined, { maximumFractionDigits: decimals }).format(clamped) + '%';
            } catch (error) {
                // fall through
            }
        }

        return clamped.toFixed(decimals) + '%';
    }

    function sanitizeHistory(history) {
        if (!Array.isArray(history)) {
            return [];
        }

        var allowed = { success: true, error: true };

        return history.reduce(function(list, entry) {
            if (!entry || typeof entry !== 'object') {
                return list;
            }

            var status = entry.status;

            if (!allowed[status]) {
                return list;
            }

            var timestamp = entry.timestamp;

            if (typeof timestamp === 'string') {
                timestamp = parseInt(timestamp, 10);
            }

            if (typeof timestamp !== 'number' || !isFinite(timestamp)) {
                timestamp = 0;
            }

            list.push({
                status: status,
                timestamp: timestamp
            });

            return list;
        }, []);
    }

    function normalizeMetrics(provider, data, container) {
        var dataset = (container && container.dataset) ? container.dataset : {};

        function parseInteger(value) {
            var parsed = parseInt(value, 10);

            if (isNaN(parsed)) {
                return 0;
            }

            return parsed;
        }

        function parseFloatValue(value) {
            var parsed = parseFloat(value);

            return isNaN(parsed) ? NaN : parsed;
        }

        var metrics = {
            success_count: 0,
            failure_count: 0,
            total_count: 0,
            success_rate: NaN,
            history: [],
            metrics_label: '',
            summary_label: '',
            provider_label: dataset.providerLabel || provider
        };

        var successValue = (data && typeof data.success_count !== 'undefined') ? data.success_count : dataset.successCount;
        var failureValue = (data && typeof data.failure_count !== 'undefined') ? data.failure_count : dataset.failureCount;

        metrics.success_count = parseInteger(successValue);
        metrics.failure_count = parseInteger(failureValue);
        metrics.total_count = metrics.success_count + metrics.failure_count;

        var successRateValue = (data && typeof data.success_rate !== 'undefined') ? data.success_rate : dataset.successRate;
        var parsedRate = parseFloatValue(successRateValue);

        if (!isNaN(parsedRate)) {
            metrics.success_rate = parsedRate;
        }

        if (!isFinite(metrics.success_rate) && metrics.total_count > 0) {
            metrics.success_rate = (metrics.success_count / metrics.total_count) * 100;
        }

        var historySource = (data && typeof data.history !== 'undefined') ? data.history : (dataset.history || '[]');

        if (typeof historySource === 'string') {
            try {
                historySource = JSON.parse(historySource);
            } catch (error) {
                historySource = [];
            }
        }

        metrics.history = sanitizeHistory(historySource);

        metrics.metrics_label = (data && typeof data.metrics_label === 'string' && data.metrics_label)
            ? data.metrics_label
            : (dataset.metricsLabel || '');

        metrics.summary_label = (data && typeof data.summary_label === 'string' && data.summary_label)
            ? data.summary_label
            : (dataset.summaryLabel || '');

        return metrics;
    }

    function buildMetricsSummary(metrics) {
        if (!metrics.total_count) {
            return getString('statusSummaryEmpty', 'No tests yet');
        }

        var rate = metrics.success_rate;

        if (!isFinite(rate) && metrics.total_count) {
            rate = (metrics.success_count / metrics.total_count) * 100;
        }

        var rateLabel = formatPercentage(rate);
        return rateLabel + ' • ' + formatNumber(metrics.success_count) + '/' + formatNumber(metrics.total_count);
    }

    function buildMetricsLabel(metrics) {
        if (!metrics.total_count) {
            return getString('statusMetricsNoData', 'No connection tests recorded yet.');
        }

        var rate = metrics.success_rate;

        if (!isFinite(rate) && metrics.total_count) {
            rate = (metrics.success_count / metrics.total_count) * 100;
        }

        var rateLabel = formatPercentage(rate);
        var template = getString('statusMetricsSummary', 'Success rate %rate% across %total% tests (%success% success, %failure% failure).');

        if (template.indexOf('%rate%') !== -1) {
            return template
                .replace('%rate%', rateLabel)
                .replace('%total%', formatNumber(metrics.total_count))
                .replace('%success%', formatNumber(metrics.success_count))
                .replace('%failure%', formatNumber(metrics.failure_count));
        }

        return rateLabel + ' • ' + formatNumber(metrics.success_count) + '/' + formatNumber(metrics.total_count);
    }

    function renderStatusTrend(provider, metrics, $target) {
        var $container = ($target && $target.length) ? $target : jQuery('.aimentor-provider-status[data-provider="' + provider + '"]');

        if (!$container.length) {
            return;
        }

        var $trend = $container.find('.aimentor-status-trend[data-provider="' + provider + '"]');

        if (!$trend.length) {
            return;
        }

        $trend.empty();

        var values = Array.isArray(metrics.history)
            ? metrics.history.map(function(entry) {
                    return entry && entry.status === 'success' ? 1 : 0;
                })
            : [];

        if (!values.length && metrics.total_count > 0) {
            var sampleSize = Math.min(metrics.total_count, 10);
            var successShare = metrics.total_count ? (metrics.success_count / metrics.total_count) : 0;

            for (var i = 0; i < sampleSize; i++) {
                values.push(i / sampleSize < successShare ? 1 : 0);
            }
        }

        if (!values.length) {
            var placeholder = jQuery('<span />').addClass('aimentor-status-trend__empty').text('—');
            $trend.append(placeholder);
            return;
        }

        var width = 72;
        var height = 16;
        var gap = values.length > 1 ? 2 : 0;
        var barWidth = (width - gap * (values.length - 1)) / values.length;

        if (barWidth < 2) {
            barWidth = 2;
        }

        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'aimentor-status-trend__chart');
        svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
        svg.setAttribute('preserveAspectRatio', 'none');
        svg.setAttribute('focusable', 'false');
        svg.setAttribute('aria-hidden', 'true');

        values.forEach(function(value, index) {
            var barHeight = value ? height : Math.max(3, height * 0.35);
            var rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', index * (barWidth + gap));
            rect.setAttribute('y', height - barHeight);
            rect.setAttribute('width', barWidth);
            rect.setAttribute('height', barHeight);
            rect.setAttribute('rx', 1);
            rect.setAttribute('ry', 1);
            rect.setAttribute('fill', value ? '#1DA866' : '#d63638');
            svg.appendChild(rect);
        });

        $trend.append(svg);
    }

    function updateStatusMetrics(provider, data) {
        var $container = jQuery('.aimentor-provider-status[data-provider="' + provider + '"]');

        if (!$container.length) {
            return;
        }

        var container = $container.get(0);
        var metrics = normalizeMetrics(provider, data || {}, container);

        if (container) {
            container.dataset.successCount = String(metrics.success_count);
            container.dataset.failureCount = String(metrics.failure_count);
            container.dataset.totalCount = String(metrics.total_count);

            if (isFinite(metrics.success_rate)) {
                container.dataset.successRate = String(metrics.success_rate);
            } else {
                delete container.dataset.successRate;
            }

            if (metrics.history.length) {
                try {
                    container.dataset.history = JSON.stringify(metrics.history);
                } catch (error) {
                    delete container.dataset.history;
                }
            } else {
                delete container.dataset.history;
            }

            if (metrics.metrics_label) {
                container.dataset.metricsLabel = metrics.metrics_label;
            } else {
                delete container.dataset.metricsLabel;
            }

            if (metrics.summary_label) {
                container.dataset.summaryLabel = metrics.summary_label;
            } else {
                delete container.dataset.summaryLabel;
            }
        }

        var summaryLabel = (data && typeof data.summary_label === 'string' && data.summary_label) ? data.summary_label : metrics.summary_label;

        if (!summaryLabel) {
            summaryLabel = buildMetricsSummary(metrics);
        }

        metrics.summary_label = summaryLabel;

        var metricsLabel = (data && typeof data.metrics_label === 'string' && data.metrics_label) ? data.metrics_label : metrics.metrics_label;

        if (!metricsLabel) {
            metricsLabel = buildMetricsLabel(metrics);
        }

        metrics.metrics_label = metricsLabel;

        var $summary = $container.find('.aimentor-status-metrics-summary[data-provider="' + provider + '"]');

        if ($summary.length) {
            $summary.text(summaryLabel);
        }

        var $srText = $container.find('.aimentor-status-metrics[data-provider="' + provider + '"]');

        if ($srText.length) {
            $srText.text(metricsLabel);
        }

        renderStatusTrend(provider, metrics, $container);
    }

    function initializeStatusMetrics() {
        jQuery('.aimentor-provider-status').each(function() {
            var provider = String(jQuery(this).data('provider') || '');

            if (!provider) {
                return;
            }

            updateStatusMetrics(provider, {});
        });
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

        var $form = $('#aimentor-error-log-form');

        if ($form.length) {
            $form.attr('data-nonce', logNonce);
        }
    }

    function setLogFeedback(message, state) {
        var $feedback = $('#aimentor-error-log-feedback');

        if (!$feedback.length) {
            return;
        }

        $feedback.removeClass('is-success is-error');

        if (!message) {
            $feedback.attr('hidden', 'hidden');
            $feedback.text('');
            return;
        }

        if (state === 'success') {
            $feedback.addClass('is-success');
        } else if (state === 'error') {
            $feedback.addClass('is-error');
        }

        $feedback.text(message);
        $feedback.removeAttr('hidden');
    }

    function toggleLogActionsBusy(isBusy) {
        var $actions = $('.aimentor-error-log-actions');

        if (!$actions.length) {
            return;
        }

        var $buttons = $actions.find('button');

        if ($buttons.length) {
            $buttons.prop('disabled', !!isBusy);
        }

        $actions.toggleClass('is-busy', !!isBusy);
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
        var $form = $('#aimentor-error-log-form');

        if (!$form.length) {
            return;
        }

        var $submit = $form.find('button[type="submit"]');

        if ($submit.length) {
            $submit.prop('disabled', !!isLoading);
        }

        $form.toggleClass('is-loading', !!isLoading);
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

        updateStatusMetrics(provider, data);
    }

    initializeStatusMetrics();

    $(document)
        .off('click' + EVENT_NAMESPACE, '.aimentor-test-provider')
        .on('click' + EVENT_NAMESPACE, '.aimentor-test-provider', function() {
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

    $(document)
        .off('click' + EVENT_NAMESPACE, '.aimentor-onboarding-card .notice-dismiss')
        .on('click' + EVENT_NAMESPACE, '.aimentor-onboarding-card .notice-dismiss', function(event) {
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

    $(document)
        .off('submit' + EVENT_NAMESPACE, '#aimentor-error-log-form')
        .on('submit' + EVENT_NAMESPACE, '#aimentor-error-log-form', function(event) {
            event.preventDefault();

            var $form = $(this);

        if (!logNonce) {
            var existingNonce = $form.data('nonce');

            if (existingNonce) {
                logNonce = String(existingNonce);
            }
        }

        setLogFeedback('');

        if (!logNonce) {
            renderLogError();
            return;
        }

        var provider = $.trim($form.find('[name="provider"]').val() || '');
        var keyword = $.trim($form.find('[name="keyword"]').val() || '');
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

    $(document)
        .off('click' + EVENT_NAMESPACE, '#aimentor-download-log')
        .on('click' + EVENT_NAMESPACE, '#aimentor-download-log', function(event) {
            event.preventDefault();

            var $form = $('#aimentor-error-log-form');

        if (!logNonce && $form.length) {
            var existingNonce = $form.data('nonce');

            if (existingNonce) {
                logNonce = String(existingNonce);
            }
        }

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
    });

    $(document)
        .off('click' + EVENT_NAMESPACE, '#aimentor-clear-log')
        .on('click' + EVENT_NAMESPACE, '#aimentor-clear-log', function(event) {
            event.preventDefault();

            var $form = $('#aimentor-error-log-form');

        if (!logNonce && $form.length) {
            var existingNonce = $form.data('nonce');

            if (existingNonce) {
                logNonce = String(existingNonce);
            }
        }

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

                $('#aimentor-error-log-form').trigger('submit');

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

    window.AiMentorAdminSettings = window.AiMentorAdminSettings || {};
    window.AiMentorAdminSettings.ui = $.extend({}, window.AiMentorAdminSettings.ui || {}, {
        toggleProviderSections: function(provider) {
            toggleProviderSections(provider);
        },
        updateContextRow: function(row) {
            updateContextRow(row);
        },
        initializeProviderControls: function(context) {
            initializeProviderControls(context);
        },
        initializeContextControls: function(context) {
            initializeContextControls(context);
        },
        initializeDynamicContent: function(context) {
            initializeDynamicContent(context);
        }
    });

    if ($('#aimentor-usage-metrics').length && usageNonce) {
        refreshUsageMetrics();
        scheduleUsageRefresh();
    }
});
