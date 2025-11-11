# Quick Actions Developer Guide

AiMentor quick actions let developers expose reusable rewrite or generation recipes inside the Elementor editor and the settings dashboard. This guide documents how to register actions, hook into the processing pipeline, and tailor the requests sent to providers.

## Registry configuration

Quick actions are described by associative arrays returned from `aimentor_get_quick_action_registry()`. Each entry is keyed by the action slug and supports the following keys:

- `slug` *(string)* – Optional explicit slug used when the registry array key is not already sanitized.
- `labels` *(array)* – Copy surfaced throughout the UI. The defaults expect `name`, `menu_label`, and `description` strings.
- `defaults` *(array)* – Baseline settings stored for the action. Recognized keys include:
  - `enabled` *(bool)* – Toggles whether the action is available to editors by default.
  - `prompt` *(string)* – Default prompt instructions applied to the user-supplied content.
  - `system` *(string)* – Default system instructions that set the model behavior.
- `processors` *(array)* – Callbacks used to fulfill the action. The core dispatcher expects a callable at the `dispatch` index.

```php
add_filter( 'aimentor_quick_actions', function ( $actions ) {
        $actions['summarize'] = [
                'labels'   => [
                        'name'        => __( 'Summarize Selection', 'my-plugin' ),
                        'menu_label'  => __( 'Summarize', 'my-plugin' ),
                        'description' => __( 'Reduce highlighted copy into a short abstract.', 'my-plugin' ),
                ],
                'defaults' => [
                        'enabled' => true,
                        'prompt'  => __( 'Write a concise summary of the source content in 2–3 sentences.', 'my-plugin' ),
                        'system'  => __( 'You produce direct, factual summaries that keep key context intact.', 'my-plugin' ),
                ],
                'processors' => [
                        'dispatch' => 'aimentor_dispatch_quick_action',
                ],
        ];

        return $actions;
} );
```

After filtering, AiMentor normalizes each definition and fires `aimentor_quick_actions_registered`, passing the sanitized registry for post-processing or dependency bootstrapping.

## Relevant filters and actions

- `aimentor_quick_actions` – Register, modify, or remove action definitions before they are normalized.
- `aimentor_quick_actions_registered` – Inspect the normalized registry or initialize companion services once actions are available.
- `aimentor_quick_action_response` – Run synchronous processors. Return a non-null value to short-circuit the default handler.
- `aimentor_quick_action_dispatch` – Observe every dispatch request and trigger asynchronous or side-effect driven integrations.
- `aimentor_quick_action_prompt` – Adjust the compiled prompt constructed from the stored instructions and selected content before the generation request runs.

## Expected processors and callbacks

The default dispatcher stored in each bundled action delegates to `aimentor_handle_quick_action_response()` via the `aimentor_quick_action_response` filter. Custom implementations can:

1. Provide an alternate callable in the `processors.dispatch` slot to bypass the core dispatcher entirely, or
2. Hook `aimentor_quick_action_response` to process specific slugs. Returning a `WP_Error` instance triggers error messaging in the UI, while returning an associative array sends structured data back to the caller.

When the AJAX entry point (`wp_ajax_aimentor_execute_quick_action`) runs, the plugin passes a context array that includes the origin (`ajax`) and the triggering `user_id`. Reuse this context if your processor needs auditing or additional permission checks.

## Payload shape

The REST API consolidates quick action data with `aimentor_get_quick_actions_payload()`, returning an object shaped as:

```json
{
  "registry": {
    "rewrite_tone": { "labels": { ... }, "defaults": { ... }, "processors": { ... } },
    "outline_steps": { ... }
  },
  "settings": {
    "rewrite_tone": { "enabled": true, "prompt": "...", "system": "..." },
    "outline_steps": { "enabled": false, "prompt": "...", "system": "..." }
  }
}
```

Dispatch payloads submitted from the Elementor toolbar or REST clients should include:

- `prompt` *(string, required)* – The selected content or seed text.
- `prompt_override` *(string, optional)* – Alternate action instructions to merge with the prompt.
- `system_override` *(string, optional)* – System message overrides for the generation request.
- `provider` *(string, optional)* – Provider slug to route the request through. Defaults to the active provider when omitted.
- `task` *(string, optional)* – Generation task identifier. Non-content tasks are normalized back to `content`.
- `tier` *(string, optional)* – Provider performance tier, sanitized by the generation pipeline.
- `knowledge_ids` *(array<string>, optional)* – Knowledge pack identifiers to ground the request.

The dispatcher returns a structured array containing the provider response plus the `quick_action` slug. Any `WP_Error` bubbles back to the caller with its message and status code.

## Crafting high-quality defaults

- Keep prompts specific to the transformation you expect while leaving space for editors to supply rich source content. Explicitly note tone, length, or structural constraints when they are critical.
- Use system instructions to reinforce brand guardrails (“You are a tone specialist…”) or role framing (“You are a project manager…”). Avoid leaking tenant-specific details that would be better stored in Knowledge Packs.
- Default both `prompt` and `system` to production-ready language so teams who never tweak settings still ship high-quality results.
- Consider providing short, scannable descriptions—these appear in toggles and help administrators understand the value of each action at a glance.

## Surfacing actions in the UI

Quick actions appear in two primary places:

1. **Settings → AiMentor → Brand tab** – Administrators toggle availability, edit default prompts, and update system guidance per action. Definitions with `defaults.enabled` set to `true` appear enabled on first install.
2. **Elementor editor toolbar** – Enabled actions render as buttons in the AiMentor modal. Editors select content, choose an action, and dispatch it without leaving the editor.

Ensure that any custom action provides meaningful labels so it is recognizable in both locations. If the action requires asynchronous processing or external integrations, register a processor via the hooks above and return user-facing messages that explain what happens after dispatch.
