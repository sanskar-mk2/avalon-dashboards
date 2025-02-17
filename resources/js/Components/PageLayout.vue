<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import MonthYearSelector from "@/Components/MonthYearSelector.vue";
import Breadcrumbs from "./Breadcrumbs.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    breadcrumbs: {
        type: Array,
        required: true,
    },
    availableMonths: {
        type: Array,
        default: null,
    },
    currentMonth: {
        type: String,
        default: null,
    },
    showMonthSelector: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <Head :title="title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex w-full mr-4 justify-between items-center">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
                <MonthYearSelector
                    v-if="showMonthSelector && availableMonths && currentMonth"
                    :available-months="availableMonths"
                    :current-month="currentMonth"
                />
            </div>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg"
                >
                    <div class="p-4 sm:p-6">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
