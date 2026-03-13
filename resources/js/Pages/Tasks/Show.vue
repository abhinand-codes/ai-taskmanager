<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: { type: Object, default: null },
});

const page    = usePage();
const isAdmin = page.props.auth?.user?.role === 'admin';

const selectedStatus = ref(props.task?.status?.value ?? 'pending');
const updating       = ref(false);

const statusOptions = [
    { value: 'pending',     label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed',   label: 'Completed' },
];

const updateStatus = () => {
    if (!props.task?.id) return;
    updating.value = true;
    router.patch(route('tasks.status', { id: props.task.id }), {
        status: selectedStatus.value,
    }, {
        preserveState: true,
        only: ['task'],
        onFinish: () => {
            updating.value = false;
        },
    });
};

const priorityColor = (v) => ({
    high:   'bg-red-100 text-red-700',
    medium: 'bg-yellow-100 text-yellow-700',
    low:    'bg-green-100 text-green-700',
}[v] ?? 'bg-gray-100 text-gray-600');

const statusColor = (v) => ({
    completed:   'bg-green-100 text-green-700',
    in_progress: 'bg-blue-100 text-blue-700',
    pending:     'bg-yellow-100 text-yellow-700',
}[v] ?? 'bg-gray-100 text-gray-600');
</script>

<template>
    <Head :title="task?.title ?? 'Task'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Task Detail</h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('tasks.index')"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        ← Back
                    </Link>
                    <Link
                        v-if="task?.id && isAdmin"
                        :href="`/tasks/${task.id}/edit`"
                        class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div v-if="!task" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 py-12">
                Task not found.
            </div>
            <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Main Info -->
                <div class="bg-white rounded-xl shadow p-8">
                    <div class="flex items-start justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">{{ task?.title ?? '—' }}</h1>
                        <div class="flex gap-2">
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium', priorityColor(task?.priority?.value)]">
                                {{ task?.priority?.label ?? task?.priority?.value ?? '—' }}
                            </span>
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium', statusColor(task?.status?.value)]">
                                {{ task?.status?.label ?? task?.status?.value ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ task?.description ?? 'No description provided.' }}
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Assigned To</p>
                            <p class="text-gray-700 font-medium mt-1">{{ task?.assigned_to?.name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Created By</p>
                            <p class="text-gray-700 font-medium mt-1">{{ task?.created_by?.name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Due Date</p>
                            <p class="text-gray-700 font-medium mt-1">{{ task?.due_date ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Update Status -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Update Status</h3>
                    <div class="flex items-center gap-3">
                        <select
                            v-model="selectedStatus"
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                        <button
                            @click="updateStatus"
                            :disabled="updating || !task?.id"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                        >
                            {{ updating ? 'Updating...' : 'Update' }}
                        </button>
                    </div>
                </div>

                <!-- AI Summary -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">🤖 AI Analysis</h3>
                    <div v-if="task?.ai_summary" class="space-y-3">
                        <p class="text-gray-600 leading-relaxed">{{ task.ai_summary }}</p>
                        <div v-if="task?.ai_priority" class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">AI Suggested Priority:</span>
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', priorityColor(task?.ai_priority?.value)]">
                                {{ task?.ai_priority?.label ?? task?.ai_priority?.value ?? '—' }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="text-gray-400 text-sm">
                        AI summary is being generated. Please check back shortly.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
