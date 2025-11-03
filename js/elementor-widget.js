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
        var modelPresets = aimentorData.modelPresets || {};
        var isProActive = !!aimentorData.isProActive;
        var savedPromptsData = aimentorData.savedPrompts || {};
        var savedPromptsStore = normalizeSavedPrompts(savedPromptsData);
        var savedPromptSelectNodes = [];
        var promptPresetData = buildPromptPresetData(aimentorData.promptPresets || {});
        var promptPresetLookup = promptPresetData.presets;
        var promptCategoryLookup = promptPresetData.categories;
        var promptCategoryPresetMap = promptPresetData.categoryPresets;
        var savedPromptCollator = (typeof window.Intl !== 'undefined' && typeof window.Intl.Collator === 'function') ? new window.Intl.Collator(undefined, { sensitivity: 'base' }) : null;

        ensureBadgeStyles();

        function normalizeSavedPromptEntry(entry, scope) {
            if (!entry || typeof entry !== 'object') {
                return null;
            }

            var promptValue = typeof entry.prompt === 'string' ? entry.prompt.trim() : '';
            var idValue = typeof entry.id === 'string' ? entry.id : '';
            var labelValue = typeof entry.label === 'string' ? entry.label.trim() : '';
            var entryScope = scope === 'global' ? 'global' : 'user';

            if (!promptValue || !idValue) {
                return null;
            }

            if (!labelValue) {
                labelValue = promptValue.length > 60 ? promptValue.slice(0, 57) + '…' : promptValue;
            }

            return {
                id: idValue,
                label: labelValue,
                prompt: promptValue,
                scope: entryScope
            };
        }

        function sortSavedPromptEntries(list) {
            if (!Array.isArray(list)) {
                return [];
            }

            return list.slice().sort(function(a, b) {
                var labelA = a && typeof a.label === 'string' ? a.label : '';
                var labelB = b && typeof b.label === 'string' ? b.label : '';

                if (savedPromptCollator) {
                    return savedPromptCollator.compare(labelA, labelB);
                }

                return labelA.localeCompare(labelB);
            });
        }

        function normalizeSavedPrompts(data) {
            var normalized = { global: [], user: [] };

            if (!data || typeof data !== 'object') {
                return normalized;
            }

            if (Array.isArray(data.user)) {
                normalized.user = sortSavedPromptEntries(data.user.map(function(entry) {
                    return normalizeSavedPromptEntry(entry, 'user');
                }).filter(Boolean));
            }

            if (Array.isArray(data.global)) {
                normalized.global = sortSavedPromptEntries(data.global.map(function(entry) {
                    return normalizeSavedPromptEntry(entry, 'global');
                }).filter(Boolean));
            }

            return normalized;
        }

        function cloneSavedPromptEntry(entry) {
            return {
                id: entry.id,
                label: entry.label,
                prompt: entry.prompt,
                scope: entry.scope
            };
        }

        function cloneSavedPrompts() {
            return {
                global: savedPromptsStore.global.map(cloneSavedPromptEntry),
                user: savedPromptsStore.user.map(cloneSavedPromptEntry)
            };
        }

        function getAllSavedPrompts() {
            return savedPromptsStore.user.concat(savedPromptsStore.global);
        }

        function applySavedPromptToField(entry, $field) {
            if (!entry || !$field || !$field.length) {
                return;
            }

            $field.val(entry.prompt).trigger('input');
        }

        function bindSavedPromptSelect($select, $field) {
            if (!$select || !$select.length) {
                return;
            }

            registerSavedPromptSelect($select);

            $select.off('change.aimentor').on('change.aimentor', function() {
                var selectedPrompt = findSavedPromptById($(this).val());
                applySavedPromptToField(selectedPrompt, $field);
            });
        }

        function findSavedPromptById(id) {
            if (!id) {
                return null;
            }

            var prompts = getAllSavedPrompts();
            for (var i = 0; i < prompts.length; i++) {
                if (prompts[i] && prompts[i].id === id) {
                    return prompts[i];
                }
            }

            return null;
        }

        function getActiveDocumentConfig() {
            var doc = null;
            if (window.elementor && elementor.documents && typeof elementor.documents.getCurrent === 'function') {
                try {
                    var current = elementor.documents.getCurrent();
                    if (current) {
                        if (current.config) {
                            doc = current.config;
                        } else if (current.document && current.document.config) {
                            doc = current.document.config;
                        } else {
                            doc = current;
                        }
                    }
                } catch (err) {
                    doc = null;
                }
            }

            if (!doc && window.elementor && elementor.config && elementor.config.document) {
                doc = elementor.config.document;
            }

            if (!doc && window.elementorCommon && elementorCommon.config && elementorCommon.config.document) {
                doc = elementorCommon.config.document;
            }

            if (!doc) {
                return {};
            }

            var base;
            try {
                base = JSON.parse(JSON.stringify(doc));
            } catch (err) {
                base = Object.assign({}, doc);
            }

            if (!base.page_settings) {
                if (doc.page_settings && typeof doc.page_settings === 'object') {
                    base.page_settings = doc.page_settings;
                } else if (doc.settings && typeof doc.settings === 'object') {
                    base.page_settings = doc.settings;
                } else if (doc.config && doc.config.page_settings && typeof doc.config.page_settings === 'object') {
                    base.page_settings = doc.config.page_settings;
                }
            }

            return base;
        }

        function extractTemplateFromConfig(docConfig) {
            if (!docConfig || typeof docConfig !== 'object') {
                return '';
            }

            var sources = [];
            if (docConfig.page_settings && typeof docConfig.page_settings === 'object') {
                sources.push(docConfig.page_settings);
            }
            if (docConfig.settings && typeof docConfig.settings === 'object') {
                sources.push(docConfig.settings);
            }
            if (docConfig.config && docConfig.config.page_settings && typeof docConfig.config.page_settings === 'object') {
                sources.push(docConfig.config.page_settings);
            }

            for (var i = 0; i < sources.length; i++) {
                var candidate = sources[i];
                if (!candidate) {
                    continue;
                }
                var templateValue = '';
                if (typeof candidate.template === 'string') {
                    templateValue = candidate.template;
                } else if (typeof candidate.post_template === 'string') {
                    templateValue = candidate.post_template;
                }
                if (templateValue && templateValue !== 'default') {
                    return templateValue;
                }
            }

            return '';
        }

        function extractPostTypesFromConfig(docConfig) {
            if (!docConfig || typeof docConfig !== 'object') {
                return [];
            }

            var values = [];
            var seen = {};
            var candidates = [docConfig];

            if (docConfig.config && typeof docConfig.config === 'object') {
                candidates.push(docConfig.config);
            }
            if (docConfig.panel && typeof docConfig.panel === 'object') {
                candidates.push(docConfig.panel);
            }
            if (docConfig.page_settings && typeof docConfig.page_settings === 'object') {
                candidates.push(docConfig.page_settings);
            }

            var fields = ['post_type', 'type', 'postType', 'post_type_slug', 'postTypeSlug', 'main_post_type', 'source', 'sub_type', 'subType'];

            candidates.forEach(function(candidate) {
                if (!candidate || typeof candidate !== 'object') {
                    return;
                }
                fields.forEach(function(field) {
                    var value = candidate[field];
                    if (typeof value === 'string' && value) {
                        var normalized = value.toLowerCase();
                        if (!seen[normalized]) {
                            seen[normalized] = true;
                            values.push(normalized);
                        }
                    }
                });
            });

            return values;
        }

        function buildDocumentContextCandidates() {
            var docConfig = getActiveDocumentConfig();
            return {
                template: extractTemplateFromConfig(docConfig),
                postTypes: extractPostTypesFromConfig(docConfig)
            };
        }

        function resolveDocumentDefaults(defaultsConfig) {
            var contexts = defaultsConfig && typeof defaultsConfig.contexts === 'object' ? defaultsConfig.contexts : {};
            var pageTypes = contexts.page_types && typeof contexts.page_types === 'object' ? contexts.page_types : {};
            var globalDefault = contexts.default && typeof contexts.default === 'object' ? contexts.default : {};
            var fallbackProvider = typeof globalDefault.provider === 'string' ? globalDefault.provider : '';
            var fallbackModel = typeof globalDefault.model === 'string' ? globalDefault.model : '';
            var candidates = buildDocumentContextCandidates();
            var template = candidates.template;
            var postTypes = Array.isArray(candidates.postTypes) ? candidates.postTypes : [];

            if (template) {
                var prioritized = postTypes.slice();
                prioritized.push('__global__');
                var inspected = {};

                for (var i = 0; i < prioritized.length; i++) {
                    var slug = prioritized[i];
                    if (!slug || inspected[slug]) {
                        continue;
                    }
                    inspected[slug] = true;

                    var typeEntry = pageTypes[slug];
                    if (!typeEntry || typeof typeEntry !== 'object') {
                        continue;
                    }

                    var templateMap = typeEntry.templates && typeof typeEntry.templates === 'object' ? typeEntry.templates : {};
                    var templateEntry = templateMap[template];
                    if (!templateEntry || typeof templateEntry !== 'object') {
                        continue;
                    }

                    var baseProvider = typeof typeEntry.provider === 'string' ? typeEntry.provider : fallbackProvider;
                    var baseModel = typeof typeEntry.model === 'string' ? typeEntry.model : fallbackModel;
                    var provider = typeof templateEntry.provider === 'string' && templateEntry.provider ? templateEntry.provider : baseProvider;
                    var model = typeof templateEntry.model === 'string' && templateEntry.model ? templateEntry.model : baseModel;

                    return {
                        key: 'template:' + template,
                        provider: provider || '',
                        model: model || ''
                    };
                }

                var pageTypeKeys = Object.keys(pageTypes);
                for (var j = 0; j < pageTypeKeys.length; j++) {
                    var fallbackEntry = pageTypes[pageTypeKeys[j]];
                    if (!fallbackEntry || typeof fallbackEntry !== 'object') {
                        continue;
                    }
                    var fallbackTemplates = fallbackEntry.templates && typeof fallbackEntry.templates === 'object' ? fallbackEntry.templates : {};
                    var fallbackTemplateEntry = fallbackTemplates[template];
                    if (!fallbackTemplateEntry || typeof fallbackTemplateEntry !== 'object') {
                        continue;
                    }
                    var fallbackBaseProvider = typeof fallbackEntry.provider === 'string' ? fallbackEntry.provider : fallbackProvider;
                    var fallbackBaseModel = typeof fallbackEntry.model === 'string' ? fallbackEntry.model : fallbackModel;
                    var fallbackProviderValue = typeof fallbackTemplateEntry.provider === 'string' && fallbackTemplateEntry.provider ? fallbackTemplateEntry.provider : fallbackBaseProvider;
                    var fallbackModelValue = typeof fallbackTemplateEntry.model === 'string' && fallbackTemplateEntry.model ? fallbackTemplateEntry.model : fallbackBaseModel;
                    return {
                        key: 'template:' + template,
                        provider: fallbackProviderValue || '',
                        model: fallbackModelValue || ''
                    };
                }
            }

            if (postTypes.length) {
                for (var k = 0; k < postTypes.length; k++) {
                    var postTypeSlug = postTypes[k];
                    if (!postTypeSlug) {
                        continue;
                    }
                    var postTypeEntry = pageTypes[postTypeSlug];
                    if (!postTypeEntry || typeof postTypeEntry !== 'object') {
                        continue;
                    }
                    var providerValue = typeof postTypeEntry.provider === 'string' && postTypeEntry.provider ? postTypeEntry.provider : fallbackProvider;
                    var modelValue = typeof postTypeEntry.model === 'string' && postTypeEntry.model ? postTypeEntry.model : fallbackModel;
                    if (providerValue || modelValue) {
                        return {
                            key: 'post_type:' + postTypeSlug,
                            provider: providerValue || '',
                            model: modelValue || ''
                        };
                    }
                }
            }

            if (contexts.default && typeof contexts.default === 'object') {
                return {
                    key: 'default',
                    provider: fallbackProvider || '',
                    model: fallbackModel || ''
                };
            }

            return { key: 'default', provider: '', model: '' };
        }

        function deriveModelPreset(providerKey, modelKey) {
            if (!providerKey || !modelKey) {
                return null;
            }

            var providerEntry = modelPresets[providerKey];
            if (!providerEntry || typeof providerEntry !== 'object') {
                return null;
            }

            var preferredTasks = ['content', 'canvas'];
            var preferredTiers = ['fast', 'quality'];

            for (var i = 0; i < preferredTasks.length; i++) {
                var task = preferredTasks[i];
                var taskEntry = providerEntry[task];
                if (!taskEntry || typeof taskEntry !== 'object') {
                    continue;
                }
                for (var j = 0; j < preferredTiers.length; j++) {
                    var tier = preferredTiers[j];
                    if (taskEntry[tier] === modelKey) {
                        return { task: task, tier: tier };
                    }
                }

                var tierKeys = Object.keys(taskEntry);
                for (var k = 0; k < tierKeys.length; k++) {
                    var tierKey = tierKeys[k];
                    if (taskEntry[tierKey] === modelKey) {
                        return { task: task, tier: tierKey };
                    }
                }
            }

            var fallbackTasks = Object.keys(providerEntry);
            for (var index = 0; index < fallbackTasks.length; index++) {
                var fallbackTask = fallbackTasks[index];
                var fallbackEntry = providerEntry[fallbackTask];
                if (!fallbackEntry || typeof fallbackEntry !== 'object') {
                    continue;
                }
                var fallbackTiers = Object.keys(fallbackEntry);
                for (var t = 0; t < fallbackTiers.length; t++) {
                    var fallbackTier = fallbackTiers[t];
                    if (fallbackEntry[fallbackTier] === modelKey) {
                        return { task: fallbackTask, tier: fallbackTier };
                    }
                }
            }

            return null;
        }

        function populateSavedPromptSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            var placeholder = strings.savedPromptPlaceholder || 'Select a saved prompt…';
            var emptyLabel = strings.savedPromptEmpty || 'No saved prompts found.';
            var userLabel = strings.savedPromptGroupUser || 'My Prompts';
            var globalLabel = strings.savedPromptGroupGlobal || 'Shared Prompts';
            var currentValue = $select.val();
            var allPrompts = getAllSavedPrompts();
            var html = '<option value="">' + escapeHtml(placeholder) + '</option>';

            if (!allPrompts.length) {
                html += '<option value="" disabled>' + escapeHtml(emptyLabel) + '</option>';
                $select.html(html).val('');
                return;
            }

            var grouped = { user: [], global: [] };
            allPrompts.forEach(function(entry) {
                if (!entry) {
                    return;
                }
                var target = entry.scope === 'global' ? 'global' : 'user';
                grouped[target].push(entry);
            });

            if (grouped.user.length) {
                html += '<optgroup label="' + escapeHtml(userLabel) + '">';
                grouped.user.forEach(function(entry) {
                    html += '<option value="' + escapeHtml(entry.id) + '">' + escapeHtml(entry.label) + '</option>';
                });
                html += '</optgroup>';
            }

            if (grouped.global.length) {
                html += '<optgroup label="' + escapeHtml(globalLabel) + '">';
                grouped.global.forEach(function(entry) {
                    html += '<option value="' + escapeHtml(entry.id) + '">' + escapeHtml(entry.label) + '</option>';
                });
                html += '</optgroup>';
            }

            $select.html(html);

            if (currentValue && findSavedPromptById(currentValue)) {
                $select.val(currentValue);
            } else {
                $select.val('');
            }
        }

        function refreshRegisteredSavedPromptSelects() {
            savedPromptSelectNodes = savedPromptSelectNodes.filter(function(node) {
                return node && node.parentNode;
            });

            savedPromptSelectNodes.forEach(function(node) {
                populateSavedPromptSelect($(node));
            });
        }

        function registerSavedPromptSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            $select.each(function() {
                if (savedPromptSelectNodes.indexOf(this) === -1) {
                    savedPromptSelectNodes.push(this);
                }
            });

            populateSavedPromptSelect($select);
        }

        function setSavedPrompts(data) {
            savedPromptsStore = normalizeSavedPrompts(data);
            refreshRegisteredSavedPromptSelects();
            $(document).trigger('aimentor:saved-prompts-refreshed', { prompts: cloneSavedPrompts() });
        }

        function refreshSavedPrompts() {
            var endpoint = aimentorData.promptsEndpoint;

            if (!endpoint || !aimentorData.restNonce || typeof window.fetch !== 'function') {
                return Promise.resolve(cloneSavedPrompts());
            }

            return window.fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-WP-Nonce': aimentorData.restNonce
                },
                credentials: 'same-origin'
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            }).then(function(body) {
                if (body && body.prompts) {
                    setSavedPrompts(body.prompts);
                }
                return cloneSavedPrompts();
            }).catch(function() {
                return cloneSavedPrompts();
            });
        }

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
        var documentDefaults = resolveDocumentDefaults(defaultsData);
        var contextPreset = deriveModelPreset(documentDefaults.provider, documentDefaults.model);
        var defaultProviderCandidate = (existingState.defaults && existingState.defaults.provider) || documentDefaults.provider || defaultsData.provider || aimentorData.provider;
        var defaultProvider = sanitizeProvider(defaultProviderCandidate);
        var defaultTaskCandidate = (existingState.defaults && existingState.defaults.task) || (contextPreset && contextPreset.task) || (defaultsData && defaultsData.task) || 'content';
        var defaultTierCandidate = (existingState.defaults && existingState.defaults.tier) || (contextPreset && contextPreset.tier) || (defaultsData && defaultsData.tier) || 'fast';
        var defaultTask = sanitizeTask(defaultTaskCandidate, isProActive);
        var defaultTier = sanitizeTier(defaultTierCandidate);

        var api = window.AiMentorElementorUI || {};
        api.state = {
            defaults: {
                task: defaultTask,
                tier: defaultTier,
                provider: defaultProvider,
                model: documentDefaults.model || '',
                contextKey: documentDefaults.key || 'default'
            },
            contextMap: (defaultsData && defaultsData.contexts && typeof defaultsData.contexts === 'object') ? defaultsData.contexts : {},
            documentDefaults: documentDefaults,
            widgets: existingState.widgets || {},
            modal: existingState.modal ? {
                task: sanitizeTask(existingState.modal.task, isProActive),
                tier: sanitizeTier(existingState.modal.tier),
                provider: sanitizeProvider(existingState.modal.provider || defaultProvider)
            } : {
                task: defaultTask,
                tier: defaultTier,
                provider: defaultProvider
            }
        };

        api.buildSummary = buildSummary;
        api.getProviderMeta = getProviderMeta;
        api.getSavedPrompts = function() {
            return cloneSavedPrompts();
        };
        api.refreshSavedPrompts = refreshSavedPrompts;

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
            var $savedPromptSelect = config.savedPromptSelector ? $(config.savedPromptSelector) : $();
            var $presetSelect = config.presetSelector ? $(config.presetSelector) : $();
            var $presetDescription = config.presetDescriptionSelector ? $(config.presetDescriptionSelector) : $();
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

            if ($savedPromptSelect.length) {
                bindSavedPromptSelect($savedPromptSelect, $prompt);
            }

            if ($presetSelect.length) {
                var initialPresetValue = $presetSelect.val() || '';
                $presetSelect.data('aimentorLastPreset', initialPresetValue);
                applyPresetChoice($prompt, initialPresetValue, false, $presetDescription);
                $presetSelect.off('change.aimentor').on('change.aimentor', function() {
                    var selectedValue = $(this).val() || '';
                    var previousValue = $presetSelect.data('aimentorLastPreset') || '';
                    $presetSelect.data('aimentorLastPreset', selectedValue);
                    applyPresetChoice($prompt, selectedValue, !!selectedValue && selectedValue !== previousValue, $presetDescription);
                });
            } else if ($presetDescription.length) {
                $presetDescription.hide();
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
                var savedPromptLabel = escapeHtml(strings.savedPromptLabel || 'Saved Prompts');
                var savedPromptPlaceholder = escapeHtml(strings.savedPromptPlaceholder || 'Select a saved prompt…');
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
                    '      <label for="aimentor-saved-prompts" class="aimentor-modal__label">' + savedPromptLabel + '</label>' +
                    '      <select id="aimentor-saved-prompts" class="aimentor-modal__select">' +
                    '        <option value="">' + savedPromptPlaceholder + '</option>' +
                    '      </select>' +
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
            var $savedPromptSelect = $('#aimentor-saved-prompts');
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

            bindSavedPromptSelect($savedPromptSelect, $prompt);
            $savedPromptSelect.val('');
            refreshSavedPrompts().then(function() {
                populateSavedPromptSelect($savedPromptSelect);
            });

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

        $(document).on('aimentor:saved-prompts-updated', function(event, payload) {
            if (payload && payload.prompts) {
                setSavedPrompts(payload.prompts);
                return;
            }

            refreshSavedPrompts();
        });

        window.AiMentorElementorUI = api;

        if (window.elementor && window.elementor.hooks && typeof window.elementor.hooks.addAction === 'function') {
            var widgetSlugs = ['aimentor-ai-generator', 'jaggrok-ai-generator'];

            [
                'panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/aimentor-ai-generator/controls/write_with_jaggrok/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event'
            ].forEach(function(hookName) {
                window.elementor.hooks.addAction(hookName, api.openModal);
            });

            widgetSlugs.forEach(function(slug) {
                window.elementor.hooks.addAction('panel/open_editor/widget/' + slug, function(panel, model, view) {
                    if (!view || !view.$el) {
                        return;
                    }
                    setTimeout(function() {
                        setupPresetControlBindings(view);
                    }, 0);
                });
            });
        }

        function buildPromptPresetData(catalog) {
            var data = {
                presets: {},
                categories: {},
                categoryPresets: {}
            };
            if (!catalog || typeof catalog !== 'object') {
                return data;
            }
            Object.keys(catalog).forEach(function(providerKey) {
                var providerEntry = catalog[providerKey];
                if (!providerEntry || typeof providerEntry !== 'object') {
                    return;
                }
                Object.keys(providerEntry).forEach(function(categoryKey) {
                    var categoryMeta = providerEntry[categoryKey];
                    if (!categoryMeta || typeof categoryMeta !== 'object') {
                        return;
                    }
                    var categoryId = providerKey + '::' + categoryKey;
                    var categoryLabel = typeof categoryMeta.label === 'string' ? categoryMeta.label : categoryKey;
                    var categoryDescription = typeof categoryMeta.description === 'string' ? categoryMeta.description : '';
                    data.categories[categoryId] = {
                        id: categoryId,
                        key: categoryKey,
                        provider: providerKey,
                        label: categoryLabel,
                        description: categoryDescription
                    };
                    data.categoryPresets[categoryId] = [];

                    var presets = categoryMeta.presets;
                    if (!presets || typeof presets !== 'object') {
                        return;
                    }

                    Object.keys(presets).forEach(function(presetKey) {
                        var presetMeta = presets[presetKey];
                        if (!presetMeta || typeof presetMeta !== 'object') {
                            return;
                        }
                        var presetId = providerKey + '::' + categoryKey + '::' + presetKey;
                        data.presets[presetId] = {
                            id: presetId,
                            key: presetKey,
                            provider: providerKey,
                            categoryId: categoryId,
                            categoryKey: categoryKey,
                            label: typeof presetMeta.label === 'string' ? presetMeta.label : presetKey,
                            description: typeof presetMeta.description === 'string' ? presetMeta.description : '',
                            prompt: typeof presetMeta.prompt === 'string' ? presetMeta.prompt : '',
                            task: typeof presetMeta.task === 'string' ? presetMeta.task : ''
                        };
                        data.categoryPresets[categoryId].push(presetId);
                    });
                });
            });
            return data;
        }

        function getPromptPreset(value) {
            if (!value) {
                return null;
            }
            return promptPresetLookup[value] || null;
        }

        function getPromptCategory(value) {
            if (!value) {
                return null;
            }
            return promptCategoryLookup[value] || null;
        }

        function mergePromptText($field, presetPrompt) {
            if (!$field || !$field.length) {
                return;
            }
            var presetText = (presetPrompt || '').trim();
            if (!presetText) {
                return;
            }
            var currentValue = String($field.val() || '');
            var normalizedCurrent = currentValue.trim();
            if (!normalizedCurrent) {
                $field.val(presetText);
                $field.trigger('input');
                return;
            }
            if (normalizedCurrent.indexOf(presetText) !== -1) {
                return;
            }
            var separator = /\n\s*$/.test(currentValue) ? '' : '\n\n';
            $field.val(currentValue + separator + presetText);
            $field.trigger('input');
        }

        function renderPresetDescription($target, preset) {
            if (!$target || !$target.length) {
                return;
            }
            if (!preset) {
                $target.text('').hide();
                return;
            }
            var providerName = providerLabels[preset.provider] || preset.provider || '';
            var category = preset.categoryId ? getPromptCategory(preset.categoryId) : null;
            var pieces = [];
            if (providerName) {
                pieces.push(providerName);
            }
            if (category && category.label) {
                pieces.push(category.label);
            }
            if (preset.label) {
                pieces.push(preset.label);
            }
            var headline = pieces.join(' • ');
            var descriptionText = preset.description || (category && category.description ? category.description : '');
            var message = headline;
            if (descriptionText) {
                message = message ? headline + ' — ' + descriptionText : descriptionText;
            }
            if (!message) {
                message = preset.key || '';
            }
            if (message) {
                $target.text(message).show();
            } else {
                $target.text('').hide();
            }
        }

        function renderCategoryDescription($target, category) {
            if (!$target || !$target.length) {
                return;
            }
            if (!category) {
                $target.text('').hide();
                return;
            }
            var providerName = providerLabels[category.provider] || category.provider || '';
            var pieces = [];
            if (providerName) {
                pieces.push(providerName);
            }
            if (category.label) {
                pieces.push(category.label);
            }
            var headline = pieces.join(' • ');
            var descriptionText = category.description || '';
            var message = headline;
            if (descriptionText) {
                message = message ? headline + ' — ' + descriptionText : descriptionText;
            }
            if (!message) {
                message = category.key || '';
            }
            if (message) {
                $target.text(message).show();
            } else {
                $target.text('').hide();
            }
        }

        function applyPresetChoice($field, presetValue, shouldMerge, $descriptionEl) {
            var preset = getPromptPreset(presetValue);
            if ($descriptionEl && $descriptionEl.length) {
                renderPresetDescription($descriptionEl, preset);
            }
            if (!shouldMerge || !$field || !$field.length) {
                return;
            }
            if (!preset || !preset.prompt) {
                return;
            }
            mergePromptText($field, preset.prompt);
        }

        function setupPresetControlBindings(view) {
            var $panel = view && view.$el ? view.$el : null;
            if (!$panel || !$panel.length) {
                return;
            }
            var $categorySelect = $panel.find('select[data-setting="aimentor_prompt_category"]');
            var $presetSelect = $panel.find('select[data-setting="aimentor_prompt_preset"]');
            var $promptField = $panel.find('textarea[data-setting="aimentor_prompt_text"]');
            if (!$presetSelect.length || !$promptField.length) {
                return;
            }

            var $control = $presetSelect.closest('.elementor-control');
            var $description = $control.find('.aimentor-preset-description');
            if (!$description.length) {
                $description = $('<p class="aimentor-preset-description" style="margin-top:8px;font-size:12px;color:#4b5563;display:none;"></p>');
                $control.find('.elementor-control-input-wrapper').append($description);
            }

            var presetOptionCache = [];
            $presetSelect.find('option').each(function() {
                var optionValue = typeof this.value === 'string' ? this.value : '';
                presetOptionCache.push({
                    value: optionValue,
                    label: $(this).text()
                });
            });

            function buildAllowedPresetLookup(categoryId) {
                if (!categoryId) {
                    return null;
                }
                var presetIds = promptCategoryPresetMap[categoryId];
                if (!Array.isArray(presetIds) || !presetIds.length) {
                    return {};
                }
                var lookup = {};
                presetIds.forEach(function(id) {
                    lookup[id] = true;
                });
                return lookup;
            }

            function restorePresetOptions(categoryId, preserveSelection) {
                var allowedLookup = buildAllowedPresetLookup(categoryId);
                var previousValue = preserveSelection ? ($presetSelect.val() || '') : '';
                var preservedMatch = false;

                $presetSelect.empty();

                presetOptionCache.forEach(function(option) {
                    if (!option || typeof option.value === 'undefined') {
                        return;
                    }
                    if (option.value && allowedLookup && !allowedLookup[option.value]) {
                        return;
                    }
                    var $option = $('<option></option>').val(option.value).text(option.label);
                    $presetSelect.append($option);
                    if (option.value === previousValue) {
                        preservedMatch = true;
                    }
                });

                var finalValue = preservedMatch ? previousValue : '';
                $presetSelect.val(finalValue);
                $presetSelect.data('aimentorLastPreset', finalValue);
                applyPresetChoice($promptField, finalValue, false, $description);
            }

            var $categoryDescription = $();
            if ($categorySelect.length) {
                var $categoryControl = $categorySelect.closest('.elementor-control');
                $categoryDescription = $categoryControl.find('.aimentor-preset-category-description');
                if (!$categoryDescription.length) {
                    $categoryDescription = $('<p class="aimentor-preset-category-description" style="margin-top:8px;font-size:12px;color:#4b5563;display:none;"></p>');
                    $categoryControl.find('.elementor-control-input-wrapper').append($categoryDescription);
                }
                var initialCategoryValue = $categorySelect.val() || '';
                renderCategoryDescription($categoryDescription, getPromptCategory(initialCategoryValue));
                restorePresetOptions(initialCategoryValue, true);

                $categorySelect.off('change.aimentorPresetCategory').on('change.aimentorPresetCategory', function() {
                    var value = $(this).val() || '';
                    renderCategoryDescription($categoryDescription, getPromptCategory(value));
                    restorePresetOptions(value, false);
                });
            } else {
                restorePresetOptions('', true);
            }

            $presetSelect.off('change.aimentorPreset').on('change.aimentorPreset', function() {
                var value = $(this).val() || '';
                var previous = $presetSelect.data('aimentorLastPreset') || '';
                $presetSelect.data('aimentorLastPreset', value);
                applyPresetChoice($promptField, value, !!value && value !== previous, $description);

                if ($categorySelect.length) {
                    var preset = getPromptPreset(value);
                    var presetCategoryId = preset && preset.categoryId ? preset.categoryId : '';
                    if (presetCategoryId && presetCategoryId !== ($categorySelect.val() || '')) {
                        $categorySelect.val(presetCategoryId);
                        renderCategoryDescription($categoryDescription, getPromptCategory(presetCategoryId));
                        restorePresetOptions(presetCategoryId, true);
                    }
                }
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
            if (api && api.state && api.state.defaults && api.state.defaults.provider) {
                return sanitizeProvider(api.state.defaults.provider);
            }
            if (defaultsData && typeof defaultsData.provider === 'string' && defaultsData.provider) {
                return sanitizeProvider(defaultsData.provider);
            }
            if (typeof aimentorData.provider === 'string' && aimentorData.provider) {
                return sanitizeProvider(aimentorData.provider);
            }
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : 'grok';
        }
    });
})(jQuery, window);
