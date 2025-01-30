<script setup>
import { onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { themeChange } from 'theme-change'
import Toast from '@/Components/Toast.vue';

const showingNavigationDropdown = ref(false);

onMounted(() => {
    themeChange(false);
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-base-200">
            <nav class="border-b border-base-200 bg-base-100">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-base-content" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-base-100 px-3 py-2 text-sm font-medium leading-4 text-base-content transition duration-150 ease-in-out hover:text-base-content focus:outline-none">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="
                                showingNavigationDropdown =
                                !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-base-content/60 transition duration-150 ease-in-out hover:bg-base-200 hover:text-base-content focus:bg-base-200 focus:text-base-content focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-base-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-base-content">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-base-content/60">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-base-100 shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between">
                    <slot name="header" />
                    <select class="self-center select select-primary max-w-xs" data-choose-theme>
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="cupcake">Cupcake</option>
                        <option value="bumblebee">Bumblebee</option>
                        <option value="emerald">Emerald</option>
                        <option value="corporate">Corporate</option>
                        <option value="synthwave">Synthwave</option>
                        <option value="retro">Retro</option>
                        <option value="cyberpunk">Cyberpunk</option>
                        <option value="valentine">Valentine</option>
                        <option value="halloween">Halloween</option>
                        <option value="garden">Garden</option>
                        <option value="forest">Forest</option>
                        <option value="aqua">Aqua</option>
                        <option value="lofi">Lo-Fi</option>
                        <option value="pastel">Pastel</option>
                        <option value="fantasy">Fantasy</option>
                        <option value="wireframe">Wireframe</option>
                        <option value="black">Black</option>
                        <option value="luxury">Luxury</option>
                        <option value="dracula">Dracula</option>
                        <option value="cmyk">CMYK</option>
                        <option value="autumn">Autumn</option>
                        <option value="business">Business</option>
                        <option value="acid">Acid</option>
                        <option value="lemonade">Lemonade</option>
                        <option value="night">Night</option>
                        <option value="coffee">Coffee</option>
                        <option value="winter">Winter</option>
                        <option value="dim">Dim</option>
                        <option value="nord">Nord</option>
                        <option value="sunset">Sunset</option>
                    </select>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <Toast />
                <slot />
            </main>
        </div>
    </div>
</template>
