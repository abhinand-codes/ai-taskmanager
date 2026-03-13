<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    task:  { type: Object, default: null },
    users: { type: Array,  default: () => [] },
});

const isEdit = computed(() => !!props.task);

const form = useForm({
    title:       props.task?.title             ?? '',
    description: props.task?.description       ?? '',
    priority:    props.task?.priority?.value   ?? 'medium',
    status:      props.task?.status?.value     ?? 'pending',
    due_date:    props.task?.due_date          ?? '',
    assigned_to: props.task?.assigned_to?.id  ?? '',
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('tasks.update', { task: props.task.id }));
    } else {
        form.post(route('tasks.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Task' : 'Create Task'" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                {{ isEdit ? 'Edit Task' : 'Create Task' }}
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow p-8">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Enter task title"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-400': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Enter task description"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-400': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Priority + Status -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.priority"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-400': form.errors.priority }"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <p v-if="form.errors.priority" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.priority }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-400': form.errors.status }"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.status }}
                                </p>
                            </div>
                        </div>

                        <!-- Due Date + Assigned To -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Due Date
                                </label>
                                <input
                                    v-model="form.due_date"
                                    type="date"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-400': form.errors.due_date }"
                                />
                                <p v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.due_date }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Assign To
                                </label>
                                <select
                                    v-model="form.assigned_to"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-400': form.errors.assigned_to }"
                                >
                                    <option value="">Unassigned</option>
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.assigned_to" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.assigned_to }}
                                </p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <Link
                                :href="route('tasks.index')"
                                class="px-5 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Saving...' : (isEdit ? 'Update Task' : 'Create Task') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>