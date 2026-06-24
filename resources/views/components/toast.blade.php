{{-- Global Toast Notification System --}}
<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message: detail.message, type: detail.type ?? 'success' });
            setTimeout(() => this.remove(id), 4500);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail)"
    class="fixed top-5 right-5 z-[9999] space-y-2.5 pointer-events-none"
    style="min-width:280px;max-width:380px;"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-xl border w-full"
            :class="{
                'bg-white border-green-200': toast.type === 'success',
                'bg-white border-red-200':   toast.type === 'error',
                'bg-white border-amber-200': toast.type === 'warning',
                'bg-white border-blue-200':  toast.type === 'info',
            }"
        >
            {{-- Icon --}}
            <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                  :class="{
                      'bg-green-100': toast.type === 'success',
                      'bg-red-100':   toast.type === 'error',
                      'bg-amber-100': toast.type === 'warning',
                      'bg-blue-100':  toast.type === 'info',
                  }">
                <template x-if="toast.type === 'success'">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </span>

            {{-- Message --}}
            <p class="flex-1 text-sm font-medium leading-snug pt-1"
               :class="{
                   'text-green-900': toast.type === 'success',
                   'text-red-900':   toast.type === 'error',
                   'text-amber-900': toast.type === 'warning',
                   'text-blue-900':  toast.type === 'info',
               }"
               x-text="toast.message"></p>

            {{-- Close --}}
            <button @click="remove(toast.id)"
                    class="shrink-0 text-slate-300 hover:text-slate-500 transition-colors mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>