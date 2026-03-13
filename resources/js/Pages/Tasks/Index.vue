<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ tasks: Object, filters: Object });
const page    = usePage();
const isAdmin = page.props.auth?.user?.role === 'admin';

const search   = ref(props.filters?.search   ?? '');
const status   = ref(props.filters?.status   ?? '');
const priority = ref(props.filters?.priority ?? '');

watch([search, status, priority], () => {
    router.get(route('tasks.index'), { search: search.value, status: status.value, priority: priority.value }, { preserveState: true, replace: true });
});

const priorityClass = (v) => ({ high: 'bg-red-50 text-red-700 ring-red-200', medium: 'bg-amber-50 text-amber-700 ring-amber-200', low: 'bg-green-50 text-green-700 ring-green-200' }[v] ?? 'bg-gray-100 text-gray-600 ring-gray-200');
const statusClass   = (v) => ({ completed: 'bg-emerald-50 text-emerald-700 ring-emerald-200', in_progress: 'bg-blue-50 text-blue-700 ring-blue-200', pending: 'bg-amber-50 text-amber-700 ring-amber-200' }[v] ?? 'bg-gray-100 text-gray-600 ring-gray-200');

const showConfirm  = ref(false);
const pendingDelete = ref(null);

const confirmDelete = (id) => {
    pendingDelete.value = id;
    showConfirm.value   = true;
};

const handleConfirm = () => {
    router.delete(route('tasks.destroy', pendingDelete.value));
    showConfirm.value   = false;
    pendingDelete.value = null;
};

const handleCancel = () => {
    showConfirm.value   = false;
    pendingDelete.value = null;
};
</script>

<template>
    <Head title="Tasks" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">Tasks</h1>
                <Link v-if="isAdmin" :href="route('tasks.create')" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Task
                </Link>
            </div>
        </template>

        <ConfirmModal
            :show="showConfirm"
            title="Delete Task"
            message="This action cannot be undone. The task will be permanently removed."
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />

        <div class="space-y-5">
            <!-- Filters -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-3">
                <div class="relative flex-1 min-w-48">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input v-model="search" type="text" placeholder="Search tasks..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                </div>
                <select v-model="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                <select v-model="priority" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!tasks?.data?.length">
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-gray-400 text-sm">No tasks found</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="task in tasks?.data" :key="task.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ task.title }}</td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1', priorityClass(task.priority?.value)]">{{ task.priority?.label ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1', statusClass(task.status?.value)]">{{ task.status?.label ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ task.assigned_to?.name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ task.due_date ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <Link :href="route('tasks.show', task.id)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</Link>
                                    <Link v-if="isAdmin" :href="route('tasks.edit', task.id)" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Edit</Link>
                                    <button v-if="isAdmin" @click="confirmDelete(task.id)" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="tasks?.links?.length" class="px-6 py-4 border-t border-gray-100 flex gap-1 flex-wrap">
                    <template v-for="link in tasks.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" v-html="link.label"
                            :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition', link.active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
