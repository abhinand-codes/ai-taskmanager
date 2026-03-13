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
    title:       props.task?.title           ?? '',
    description: props.task?.description     ?? '',
    priority:    props.task?.priority?.value ?? 'medium',
    status:      props.task?.status?.value   ?? 'pending',
    due_date:    props.task?.due_date        ?? '',
    assigned_to: props.task?.assigned_to?.id ?? '',
});

const today = new Date().toISOString().split('T')[0];

const dateLabel = computed(() => {
    return form.status === 'completed' ? 'Completed On' : 'Due Date';
});

const dateMin = computed(() => {
    return form.status === 'completed' ? null : today;
});

const dateMax = computed(() => {
    return form.status === 'completed' ? today : null;
});

const datePlaceholder = computed(() => {
    if (form.status === 'completed') return 'When was it completed?';
    if (form.status === 'in_progress') return 'When is it due?';
    return 'Set a deadline';
});

const dateHint = computed(() => {
    if (form.status === 'completed') return 'Cannot be a future date.';
    if (form.status === 'in_progress') return 'Task is in progress — set a target deadline.';
    return 'Pick a future deadline for this task.';
});

// Clear date if it becomes invalid after status change
const onStatusChange = () => {
    if (!form.due_date) return;
    if (form.status === 'completed' && form.due_date > today) {
        form.due_date = today;
    }
    if ((form.status === 'pending' || form.status === 'in_progress') && form.due_date < today) {
        form.due_date = '';
    }
};

const submit = () => {
    if (isEdit.value) form.put(route('tasks.update', props.task.id));
    else form.post(route('tasks.store'));
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Task' : 'Create Task'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('tasks.index')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-xl font-semibold text-gray-800">{{ isEdit ? 'Edit Task' : 'Create Task' }}</h1>
            </div>
        </template>

        <div class="max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.title" type="text" placeholder="Enter task title"
                            :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition', form.errors.title ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1.5">{{ form.errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea v-model="form.description" rows="4" placeholder="Describe the task in detail..."
                            :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none', form.errors.description ? 'border-red-400 bg-red-50' : 'border-gray-300']"></textarea>
                        <p class="text-xs text-gray-400 mt-1">AI will generate a summary based on title and description.</p>
                    </div>

                    <!-- Priority + Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
                            <select v-model="form.priority" :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white', form.errors.priority ? 'border-red-400' : 'border-gray-300']">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select v-model="form.status" @change="onStatusChange" :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white', form.errors.status ? 'border-red-400' : 'border-gray-300']">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date + Assign — date label/constraint is dynamic -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ dateLabel }}
                                <span v-if="form.status === 'completed'" class="ml-1 text-xs font-normal text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">past or today</span>
                                <span v-else class="ml-1 text-xs font-normal text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">future</span>
                            </label>
                            <input
                                v-model="form.due_date"
                                type="date"
                                :min="dateMin"
                                :max="dateMax"
                                :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition', form.errors.due_date ? 'border-red-400 bg-red-50' : 'border-gray-300']"
                            />
                            <p class="text-xs mt-1" :class="form.status === 'completed' ? 'text-emerald-600' : 'text-gray-400'">
                                {{ dateHint }}
                            </p>
                            <p v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">{{ form.errors.due_date }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign To</label>
                            <select v-model="form.assigned_to" :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white', form.errors.assigned_to ? 'border-red-400' : 'border-gray-300']">
                                <option value="">Unassigned</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status context banner -->
                    <div :class="['rounded-lg px-4 py-3 text-sm flex items-center gap-2 border', {
                        'bg-amber-50 border-amber-200 text-amber-700': form.status === 'pending',
                        'bg-blue-50 border-blue-200 text-blue-700':   form.status === 'in_progress',
                        'bg-emerald-50 border-emerald-200 text-emerald-700': form.status === 'completed',
                    }]">
                        <svg v-if="form.status === 'pending'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg v-else-if="form.status === 'in_progress'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>
                            <span v-if="form.status === 'pending'">Task is <strong>pending</strong> — set a future deadline.</span>
                            <span v-else-if="form.status === 'in_progress'">Task is <strong>in progress</strong> — set the target due date.</span>
                            <span v-else>Task is <strong>completed</strong> — record when it was finished.</span>
                        </span>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('tasks.index')" class="px-5 py-2.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                        {{ form.processing ? 'Saving...' : (isEdit ? 'Update Task' : 'Create Task') }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
