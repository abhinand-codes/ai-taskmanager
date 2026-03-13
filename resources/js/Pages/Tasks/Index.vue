<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    tasks: { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();
const isAdmin = page.props.auth?.user?.role === 'admin';

const search   = ref(props.filters?.search ?? '');
const status   = ref(props.filters?.status ?? '');
const priority = ref(props.filters?.priority ?? '');

watch([search, status, priority], () => {
    router.get(route('tasks.index'), {
        search:   search.value,
        status:   status.value,
        priority: priority.value,
    }, { preserveState: true, replace: true });
});

const priorityBadge = (p) => {
    const map = {
        high:   'bg-red-100 text-red-700',
        medium: 'bg-yellow-100 text-yellow-700',
        low:    'bg-green-100 text-green-700',
    };
    return map[p?.value] ?? 'bg-gray-100 text-gray-700';
};

const statusBadge = (s) => {
    const map = {
        completed:   'bg-green-100 text-green-700',
        in_progress: 'bg-blue-100 text-blue-700',
        pending:     'bg-yellow-100 text-yellow-700',
    };
    return map[s?.value] ?? 'bg-gray-100 text-gray-700';
};

const deleteTask = (id) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('tasks.destroy', { task: id }));
    }
};
</script>

<template>
    <Head title="Tasks" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Tasks</h2>
                <Link
                    v-if="isAdmin"
                    :href="route('tasks.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition"
                >
                    + New Task
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-3">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search tasks..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <select v-model="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select v-model="priority" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <!-- Tasks Table -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Title</th>
                                <th class="px-6 py-3 text-left">Priority</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Assigned To</th>
                                <th class="px-6 py-3 text-left">Due Date</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="!tasks?.data || tasks.data.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                    No tasks found.
                                </td>
                            </tr>
                            <tr
                                v-for="task in (tasks?.data ?? [])"
                                :key="task.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ task.title ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', priorityBadge(task.priority)]">
                                        {{ task.priority?.label ?? task.priority?.value ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusBadge(task.status)]">
                                        {{ task.status?.label ?? task.status?.value ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ task.assigned_to?.name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ task.due_date ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('tasks.show', { task: task.id })"
                                            class="text-blue-600 hover:underline text-xs"
                                        >View</Link>
                                        <Link
                                            v-if="isAdmin"
                                            :href="route('tasks.edit', { task: task.id })"
                                            class="text-yellow-600 hover:underline text-xs"
                                        >Edit</Link>
                                        <button
                                            v-if="isAdmin"
                                            @click="deleteTask(task.id)"
                                            class="text-red-600 hover:underline text-xs"
                                        >Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="tasks?.links" class="px-6 py-4 border-t border-gray-100 flex gap-1 flex-wrap">
                        <template v-for="link in (tasks?.links ?? [])" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 rounded text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                ]"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>