{{--
    Shared form partial used by both create.blade.php and edit.blade.php.

    Expected variables in scope:
      $notification  — the SystemNotification model instance (new or existing)
      $types         — array from SystemNotification::types(), e.g. ['info' => 'Info', ...]
      $templates     — array from SystemNotification::templates()
      $action        — the form action URL (route string)
      $method        — 'POST' for create, 'PATCH' for update
      $editing       — (optional) truthy when editing an existing notification

    Type selector and template picker both use Alpine x-data. The template
    picker pre-fills the title, message, and type fields when a template is
    chosen; the admin can edit any field after applying it.
--}}

{{--
    Templates panel — shown above the form fields so it's the first thing the
    admin sees. We wrap the entire form (including the template picker) in a
    single x-data scope so the template buttons can directly mutate the same
    reactive properties that drive the title/message inputs and type selector.

    applyTemplate(t) sets:
      - title   → drives the title <input> value via x-model
      - message → drives the message <textarea> value via x-model
      - selectedType → drives the type pill selector and hidden type input

    showTemplates toggles the panel open/closed. It starts open on create
    (no existing content to protect) and closed on edit (less disruptive).
--}}
<div x-data="{
         showTemplates: {{ isset($editing) ? 'false' : 'true' }},
         title:         {{ json_encode(old('title', $notification->title ?? '')) }},
         message:       {{ json_encode(old('message', $notification->message ?? '')) }},
         selectedType:  {{ json_encode(old('type', $notification->type ?? 'info')) }},
         applyTemplate(t) {
             this.title        = t.title;
             this.message      = t.message;
             this.selectedType = t.type;
             this.showTemplates = false;
         }
     }"
     class="space-y-6">

    {{-- Template picker --}}
    @if (!empty($templates))
        <div class="rounded-md border border-border bg-surface">

            {{-- Toggle header --}}
            <button type="button"
                    @click="showTemplates = !showTemplates"
                    :aria-expanded="showTemplates.toString()"
                    aria-controls="template-panel"
                    class="flex w-full items-center justify-between px-4 py-3 text-sm font-medium
                           text-text hover:bg-bg rounded-md transition-colors duration-150
                           focus:outline-none focus:ring-2 focus:ring-accent focus:ring-inset">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Use a template
                </span>
                <svg class="w-4 h-4 text-muted transition-transform duration-150"
                     :class="showTemplates ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Template grid --}}
            <div id="template-panel"
                 x-show="showTemplates"
                 x-transition:enter="transition-all duration-150 ease-out"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-96"
                 x-transition:leave="transition-all duration-100 ease-in"
                 x-transition:leave-start="opacity-100 max-h-96"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="overflow-hidden border-t border-border px-4 py-3">

                <p class="text-xs text-muted mb-3">
                    Select a template to pre-fill the form. You can edit any field afterwards.
                </p>

                <div class="flex flex-wrap gap-2">
                    @foreach ($templates as $template)
                        @php
                            $pillColor = match ($template['type']) {
                                'info'    => 'border-blue-200  dark:border-blue-800  text-blue-700  dark:text-blue-300  hover:bg-blue-50  dark:hover:bg-blue-950/40',
                                'warning' => 'border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-950/40',
                                'success' => 'border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 hover:bg-green-50 dark:hover:bg-green-950/40',
                                'danger'  => 'border-red-200   dark:border-red-800   text-red-700   dark:text-red-300   hover:bg-red-50   dark:hover:bg-red-950/40',
                                default   => 'border-border text-muted hover:bg-bg',
                            };
                        @endphp
                        <button type="button"
                                @click="applyTemplate({{ json_encode($template) }})"
                                class="inline-flex items-center gap-1.5 rounded-md border px-3 py-1.5
                                       text-sm font-medium transition-colors duration-150
                                       focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1
                                       {{ $pillColor }}">
                            {{ $template['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

        </div>
    @endif

<form method="POST"
      action="{{ $action }}"
      class="space-y-6"
      aria-label="{{ isset($editing) ? 'Edit notification' : 'Create notification' }}">
    @csrf
    @if ($method === 'PATCH')
        @method('PATCH')
    @endif

    {{-- Title --}}
    <div>
        <label for="title" class="block text-sm font-medium text-text mb-1">
            Title
            <span class="text-red-500 dark:text-red-400 ml-0.5" aria-hidden="true">*</span>
        </label>
        <input type="text"
               id="title"
               name="title"
               x-model="title"
               required
               maxlength="255"
               class="w-full rounded-md border border-border bg-surface px-3 py-2 text-sm text-text
                      placeholder-muted shadow-sm
                      focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                      dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                      @error('title') border-red-500 dark:border-red-400 @enderror"
               placeholder="e.g. Scheduled maintenance on Friday">
        @error('title')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
        @enderror
    </div>

    {{-- Message --}}
    <div>
        <label for="message" class="block text-sm font-medium text-text mb-1">
            Message
            <span class="text-red-500 dark:text-red-400 ml-0.5" aria-hidden="true">*</span>
        </label>
        <textarea id="message"
                  name="message"
                  required
                  maxlength="2000"
                  rows="4"
                  x-model="message"
                  class="w-full rounded-md border border-border bg-surface px-3 py-2 text-sm text-text
                         placeholder-muted shadow-sm resize-y
                         focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                         dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                         @error('message') border-red-500 dark:border-red-400 @enderror"
                  placeholder="The message users will see in the banner…"></textarea>
        @error('message')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-muted">Maximum 2,000 characters.</p>
    </div>

    {{--
        Type selector.

        Alpine tracks `selectedType` in x-data. When a pill is clicked it:
          1. Updates selectedType (reactive — all :class bindings re-evaluate)
          2. Sets the value of the real hidden <input type="hidden"> that submits with the form

        The hidden input is the source of truth on submit. The pills are
        purely presentational wrappers around that value.

        We seed x-data with the PHP value (old() falls back to the model's
        current type, or 'info' for a brand-new notification) so the correct
        pill is highlighted on first render and after a validation failure.
    --}}
    {{--
        No separate x-data here — selectedType lives in the outer x-data scope
        so the template picker can update it and the pill buttons can read it.
    --}}
    <div>
        <input type="hidden" name="type" :value="selectedType">

        <fieldset>
            <legend class="block text-sm font-medium text-text mb-2">
                Type
                <span class="text-red-500 dark:text-red-400 ml-0.5" aria-hidden="true">*</span>
            </legend>

            <div class="flex flex-wrap gap-2" role="group">

                {{-- Info --}}
                <button type="button"
                        @click="selectedType = 'info'"
                        :aria-pressed="(selectedType === 'info').toString()"
                        :class="selectedType === 'info'
                            ? 'border-blue-400 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300'
                            : 'border-border bg-surface text-muted hover:border-accent hover:text-text'"
                        class="flex items-center gap-2 px-4 py-2 rounded-md border-2 text-sm font-medium
                               transition-colors duration-150 focus:outline-none focus:ring-2
                               focus:ring-accent focus:ring-offset-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400 flex-shrink-0" aria-hidden="true"></span>
                    Info
                </button>

                {{-- Warning --}}
                <button type="button"
                        @click="selectedType = 'warning'"
                        :aria-pressed="(selectedType === 'warning').toString()"
                        :class="selectedType === 'warning'
                            ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300'
                            : 'border-border bg-surface text-muted hover:border-accent hover:text-text'"
                        class="flex items-center gap-2 px-4 py-2 rounded-md border-2 text-sm font-medium
                               transition-colors duration-150 focus:outline-none focus:ring-2
                               focus:ring-accent focus:ring-offset-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0" aria-hidden="true"></span>
                    Warning
                </button>

                {{-- Success --}}
                <button type="button"
                        @click="selectedType = 'success'"
                        :aria-pressed="(selectedType === 'success').toString()"
                        :class="selectedType === 'success'
                            ? 'border-green-400 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-300'
                            : 'border-border bg-surface text-muted hover:border-accent hover:text-text'"
                        class="flex items-center gap-2 px-4 py-2 rounded-md border-2 text-sm font-medium
                               transition-colors duration-150 focus:outline-none focus:ring-2
                               focus:ring-accent focus:ring-offset-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 flex-shrink-0" aria-hidden="true"></span>
                    Success
                </button>

                {{-- Danger --}}
                <button type="button"
                        @click="selectedType = 'danger'"
                        :aria-pressed="(selectedType === 'danger').toString()"
                        :class="selectedType === 'danger'
                            ? 'border-red-400 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300'
                            : 'border-border bg-surface text-muted hover:border-accent hover:text-text'"
                        class="flex items-center gap-2 px-4 py-2 rounded-md border-2 text-sm font-medium
                               transition-colors duration-150 focus:outline-none focus:ring-2
                               focus:ring-accent focus:ring-offset-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0" aria-hidden="true"></span>
                    Danger
                </button>

            </div>
        </fieldset>

        @error('type')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
        @enderror
    </div>

    {{-- Two-column row: Active toggle + Expiry --}}
    <div class="flex flex-col sm:flex-row gap-6">

        {{-- Active toggle --}}
        <div class="flex items-start gap-3">
            {{--
                Hidden input trick: browsers don't submit unchecked checkboxes,
                so a hidden input with value="0" immediately before the checkbox
                ensures the controller always receives an is_active value.
                $request->boolean('is_active') then correctly returns false when
                the checkbox is unchecked (the hidden 0 is sent) and true when
                checked (the checkbox 1 overwrites the hidden 0).
            --}}
            <div class="flex items-center h-5 mt-0.5">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox"
                       id="is_active"
                       name="is_active"
                       value="1"
                       {{ old('is_active', $notification->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-border text-accent
                              focus:ring-accent focus:ring-offset-surface">
            </div>
            <div>
                <label for="is_active" class="text-sm font-medium text-text cursor-pointer">
                    Active
                </label>
                <p class="text-xs text-muted mt-0.5">
                    Inactive notifications are hidden from all users immediately.
                </p>
            </div>
        </div>

        {{-- Expiry date/time --}}
        <div class="flex-1">
            <label for="expires_at" class="block text-sm font-medium text-text mb-1">
                Expires at
                <span class="text-muted font-normal">(optional)</span>
            </label>
            <input type="datetime-local"
                   id="expires_at"
                   name="expires_at"
                   value="{{ old('expires_at', $notification->expires_at?->format('Y-m-d\TH:i')) }}"
                   class="rounded-md border border-border bg-surface px-3 py-2 text-sm text-text
                          shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                          dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                          @error('expires_at') border-red-500 dark:border-red-400 @enderror">
            @error('expires_at')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-muted">
                Leave blank to keep the notification active indefinitely.
            </p>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="flex items-center gap-3 pt-2 border-t border-border">
        <button type="submit"
                class="rounded-md bg-accent px-4 py-2 text-sm font-medium text-white shadow-sm
                       hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent
                       focus:ring-offset-2 transition-colors duration-150
                       dark:bg-purple-700 dark:hover:bg-purple-600">
            {{ isset($editing) ? 'Save Changes' : 'Create Notification' }}
        </button>
        <a href="{{ route('admin.notifications.index') }}"
           class="rounded-md px-4 py-2 text-sm font-medium text-muted border border-border
                  hover:text-text hover:border-accent transition-colors duration-150">
            Cancel
        </a>
    </div>

</form>

</div>{{-- /x-data outer wrapper --}}
