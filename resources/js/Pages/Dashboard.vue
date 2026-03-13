<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    stats: Object,
});

const chartRef = ref(null);

onMounted(() => {
    if (chartRef.value) {
        new Chart(chartRef.value, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    data: [
                        props.stats.completed,
                        props.stats.in_progress,
                        props.stats.pending,
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                        <div class="bg-blue-100 text-blue-600 rounded-full p-3 text-2xl">📋</div>
                        <div>
                            <p class="text-sm text-gray-500">Total Tasks</p>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.total }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                        <div class="bg-green-100 text-green-600 rounded-full p-3 text-2xl">✅</div>
                        <div>
                            <p class="text-sm text-gray-500">Completed</p>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.completed }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                        <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 text-2xl">⏳</div>
                        <div>
                            <p class="text-sm text-gray-500">Pending</p>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.pending }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                        <div class="bg-red-100 text-red-600 rounded-full p-3 text-2xl">🔥</div>
                        <div>
                            <p class="text-sm text-gray-500">High Priority</p>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.high }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chart + Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Task Status Overview</h3>
                        <div class="h-64">
                            <canvas ref="chartRef"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <Link
                                :href="route('tasks.index')"
                                class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition"
                            >
                                <span class="text-blue-500 text-xl">📋</span>
                                <div>
                                    <p class="font-medium text-gray-700">View All Tasks</p>
                                    <p class="text-sm text-gray-400">See and manage all tasks</p>
                                </div>
                            </Link>
                            <Link
                                :href="route('tasks.create')"
                                class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition"
                            >
                                <span class="text-green-500 text-xl">➕</span>
                                <div>
                                    <p class="font-medium text-gray-700">Create New Task</p>
                                    <p class="text-sm text-gray-400">Add a new task with AI analysis</p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>