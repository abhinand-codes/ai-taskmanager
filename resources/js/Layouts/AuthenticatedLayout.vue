<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Toast from '@/Components/Toast.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const page    = usePage();
const user    = page.props.auth.user;
const showLogoutConfirm = ref(false);

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <Toast />

        <ConfirmModal
            :show="showLogoutConfirm"
            title="Sign Out"
            message="Are you sure you want to log out of your account?"
            confirm-label="Sign Out"
            confirm-class="bg-gray-800 hover:bg-gray-900"
            icon="logout"
            @confirm="handleLogout"
            @cancel="showLogoutConfirm = false"
        />

        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-20">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <ApplicationLogo class="h-8 w-auto text-blue-600" />
                <span class="ml-3 text-lg font-bold text-gray-800">TaskManager</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <Link
                    :href="route('dashboard')"
                    :class="['flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition', route().current('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </Link>
                <Link
                    :href="route('tasks.index')"
                    :class="['flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition', route().current('tasks.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Tasks
                </Link>
            </nav>

            <div class="px-4 py-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ user.role }}</p>
                    </div>
                    <button @click="showLogoutConfirm = true" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center px-8 sticky top-0 z-10">
                <div class="flex-1"><slot name="header" /></div>
            </header>
            <main class="flex-1 p-8"><slot /></main>
        </div>
    </div>
</template>
