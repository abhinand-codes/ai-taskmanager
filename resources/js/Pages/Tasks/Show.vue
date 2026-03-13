<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ task: { type: Object, required: true } });
const page    = usePage();
const isAdmin = page.props.auth?.user?.role === 'admin';
const selectedStatus = ref(props.task?.status?.value ?? 'pending');
const updating = ref(false);

const statusOptions = [
    { value: 'pending',     label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed',   label: 'Completed' },
];

const updateStatus = () => {
    if (!props.task?.id) return;
    updating.value = true;
    router.patch(route('tasks.status', { id: props.task.id }), { status: selectedStatus.value }, {
        preserveState: true,
        only: ['task'],
        onFinish: () => { updating.value = false; },
    });
};

const priorityClass = (v) => ({ high: 'bg-red-50 text-red-700 ring-1 ring-red-200', medium: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200', low: 'bg-green-50 text-green-700 ring-1 ring-green-200' }[v] ?? 'bg-gray-100 text-gray-600');
const statusClass   = (v) => ({ completed: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200', in_progress: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200', pending: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' }[v] ?? 'bg-gray-100 text-gray-600');
</script>

<template>
    <Head :title="task?.title ?? 'Task'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('tasks.index')" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <h1 class="text-xl font-semibold text-gray-800">Task Detail</h1>
                </div>
                <Link v-if="task?.id && isAdmin" :href="`/tasks/${task.id}/edit`" class="inline-flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Task
                </Link>
            </div>
        </template>

        <div class="max-w-3xl space-y-5">
            <!-- Main Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ task?.title ?? '—' }}</h2>
                    <div class="flex gap-2 ml-4 shrink-0">
                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', priorityClass(task?.priority?.value)]">{{ task?.priority?.label ?? '—' }}</span>
                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusClass(task?.status?.value)]">{{ task?.status?.label ?? '—' }}</span>
                    </div>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ task?.description ?? 'No description provided.' }}</p>
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-1">Assigned To</p>
                        <p class="text-sm font-medium text-gray-700">{{ task?.assigned_to?.name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-1">Created By</p>
                        <p class="text-sm font-medium text-gray-700">{{ task?.created_by?.name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-1">Due Date</p>
                        <p class="text-sm font-medium text-gray-700">{{ task?.due_date ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>
                <div class="flex gap-2">
                    <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                        <button v-for="opt in statusOptions" :key="opt.value" @click="selectedStatus = opt.value"
                            :class="['px-4 py-2 text-sm font-medium transition border-r border-gray-200 last:border-0', selectedStatus === opt.value ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50']">
                            {{ opt.label }}
                        </button>
                    </div>
                    <button @click="updateStatus" :disabled="updating"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 font-medium">
                        {{ updating ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>

            <!-- AI Analysis -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    <h3 class="text-sm font-semibold text-indigo-800">AI Analysis</h3>
                </div>
                <div class="p-6">
                    <div v-if="task?.ai_summary" class="space-y-3">
                        <p class="text-gray-700 text-sm leading-relaxed">{{ task.ai_summary }}</p>
                        <div v-if="task?.ai_priority" class="flex items-center gap-2 pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">AI Suggested Priority:</span>
                            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold', priorityClass(task?.ai_priority?.value)]">{{ task?.ai_priority?.label ?? task?.ai_priority?.value }}</span>
                        </div>
                    </div>
                    <p v-else class="text-gray-400 text-sm">AI summary is being generated. Please check back shortly.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
