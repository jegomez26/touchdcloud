@props(['name' => 'default-modal', 'maxWidth' => '3xl', 'show' => false, 'closeable' => true])

<div
    x-data="{
        show: @js($show),
        focusables() {
            // All focusable element types not disabled.
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...this.$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] },
        nextFocusableIndex() {
            return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
        },
        prevFocusableIndex() {
            return Math.max(0, this.focusables().indexOf(document.activeElement)) -1;
        },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $closeable ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="
        if ($event.detail.name == '{{ $name }}') {
            show = true;
        }
    "
    x-on:close-modal.window="
        if ($event.detail.name == '{{ $name }}') {
            show = false;
        }
    "
    x-on:keydown.escape.window="{{ $closeable ? 'show = false' : '' }}"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="{{ $closeable ? 'show = false' : '' }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
    </div>

    <div
        x-show="show"
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        @click.away="{{ $closeable ? 'show = false' : '' }}"
    >
        <div class="px-6 py-4">
            <div class="text-lg font-bold text-[#33595a]">
                {{ $title ?? '' }}
            </div>

            <div class="mt-4 text-sm text-gray-600">
                {{ $slot }}
            </div>
        </div>

        @if (isset($footer))
            <div class="px-6 py-4 bg-gray-100 text-right">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>