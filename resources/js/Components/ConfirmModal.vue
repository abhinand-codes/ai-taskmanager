<script setup>
defineProps({
    show:          { type: Boolean, default: false },
    title:         { type: String,  default: 'Confirm Action' },
    message:       { type: String,  default: 'Are you sure?' },
    confirmLabel:  { type: String,  default: 'Confirm' },
    confirmClass:  { type: String,  default: 'bg-red-600 hover:bg-red-700' },
    icon:          { type: String,  default: 'delete' },
});
const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <teleport to="body">
        <transition name="modal">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="emit('cancel')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
                    <div class="flex items-start gap-4 mb-5">
                        <!-- Delete icon -->
                        <div v-if="icon === 'delete'" class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <!-- Logout icon -->
                        <div v-else-if="icon === 'logout'" class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ message }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button @click="emit('cancel')" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button @click="emit('confirm')" :class="['px-4 py-2 text-sm text-white rounded-lg transition font-medium', confirmClass]">
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.modal-enter-active { transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1); }
.modal-leave-active { transition: all 0.15s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.92); }
</style>
