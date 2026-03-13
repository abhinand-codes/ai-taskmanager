<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page   = usePage();
const flash  = computed(() => page.props.flash);
const toasts = ref([]);
const shown  = ref(new Set());

const addToast = (message, type = 'success') => {
    const key = `${type}:${message}`;
    if (shown.value.has(key)) return;
    shown.value.add(key);
    const id = Date.now();
    toasts.value.push({ id, message, type });
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id);
        shown.value.delete(key);
    }, 3500);
};

const processFlash = (f) => {
    if (f?.success) addToast(f.success, 'success');
    if (f?.error)   addToast(f.error,   'error');
    if (f?.warning) addToast(f.warning,  'warning');
};

onMounted(() => processFlash(flash.value));

watch(flash, (val) => processFlash(val), { deep: true });

const typeConfig = {
    success: { bg: 'bg-emerald-50 border border-emerald-200', icon: 'text-emerald-500', text: 'text-emerald-800', bar: 'bg-emerald-400' },
    error:   { bg: 'bg-red-50 border border-red-200',         icon: 'text-red-500',     text: 'text-red-800',     bar: 'bg-red-400'     },
    warning: { bg: 'bg-amber-50 border border-amber-200',     icon: 'text-amber-500',   text: 'text-amber-800',   bar: 'bg-amber-400'   },
};
</script>

<template>
    <div class="fixed top-5 right-5 z-[999] flex flex-col gap-3 w-80 pointer-events-none">
        <transition-group name="toast">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="['relative rounded-xl p-4 overflow-hidden shadow-lg pointer-events-auto', typeConfig[toast.type]?.bg]"
            >
                <div class="flex items-start gap-3">
                    <div :class="['mt-0.5 shrink-0', typeConfig[toast.type]?.icon]">
                        <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg v-else-if="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p :class="['text-sm font-medium flex-1 leading-snug', typeConfig[toast.type]?.text]">{{ toast.message }}</p>
                    <button @click="toasts = toasts.filter(t => t.id !== toast.id)" :class="['shrink-0 opacity-40 hover:opacity-100 transition', typeConfig[toast.type]?.icon]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div :class="['absolute bottom-0 left-0 h-1 toast-bar', typeConfig[toast.type]?.bar]"></div>
            </div>
        </transition-group>
    </div>
</template>

<style scoped>
.toast-enter-active { transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1); }
.toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from   { opacity: 0; transform: translateX(110%); }
.toast-leave-to     { opacity: 0; transform: translateX(110%); }
.toast-bar { animation: shrink 3.5s linear forwards; }
@keyframes shrink { from { width: 100%; } to { width: 0%; } }
</style>
