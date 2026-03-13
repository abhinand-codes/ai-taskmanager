<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ canResetPassword: Boolean, status: String });

const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Sign In" />

    <div class="min-h-screen bg-gray-50 flex">
        <!-- Left branding panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-blue-600 flex-col justify-between p-12">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-white font-bold text-lg">TaskManager</span>
            </div>

            <div>
                <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                    Manage tasks<br/>smarter with AI.
                </h1>
                <p class="text-blue-100 text-base leading-relaxed mb-8">
                    Assign, track, and prioritize tasks with AI-powered summaries and intelligent priority suggestions.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-blue-100 text-sm">AI-generated task summaries via Gemini</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-blue-100 text-sm">Role-based access for admins and users</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="text-blue-100 text-sm">Real-time dashboard with analytics</span>
                    </div>
                </div>
            </div>

            <p class="text-blue-200 text-xs">Built with Laravel 12 · Vue 3 · Inertia · Gemini AI</p>
        </div>

        <!-- Right login form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-sm">
                <!-- Mobile logo -->
                <div class="flex items-center gap-2 mb-8 lg:hidden">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <span class="font-bold text-gray-800">TaskManager</span>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
                <p class="text-gray-500 text-sm mb-8">Sign in to your account to continue</p>

                <!-- Status message -->
                <div v-if="status" class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            placeholder="you@example.com"
                            :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition', form.errors.email ? 'border-red-400 bg-red-50' : 'border-gray-300']"
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-blue-600 hover:text-blue-800 transition">
                                Forgot password?
                            </Link>
                        </div>
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            :class="['w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition', form.errors.password ? 'border-red-400 bg-red-50' : 'border-gray-300']"
                        />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input v-model="form.remember" type="checkbox" id="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <label for="remember" class="text-sm text-gray-600">Remember me</label>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition disabled:opacity-60">
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                        {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>

                <!-- Test credentials hint -->
                <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-2">Test credentials</p>
                    <div class="space-y-1 text-xs text-gray-600">
                        <div class="flex justify-between"><span class="font-medium">Admin:</span><span>admin@taskmanager.com / password</span></div>
                        <div class="flex justify-between"><span class="font-medium">User:</span><span>john@taskmanager.com / password</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
