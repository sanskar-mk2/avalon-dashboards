<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    file: null,
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("sales.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Sales" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-lg sm:text-xl font-semibold leading-tight">
                <div class="breadcrumbs text-xs sm:text-sm text-primary-content-content">
                    <ul>
                        <li><Link :href="route('dashboard')">Home</Link></li>
                        <li><Link :href="route('sales.index')">Sales</Link></li>
                        <li>Create</li>
                    </ul>
                </div>
            </h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <form @submit.prevent="submit" class="space-y-3 sm:space-y-4">
                            <div class="grid w-full max-w-sm items-center gap-1.5">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text text-sm sm:text-primary-content">Sales CSV File</span>
                                    </div>
                                    <input type="file" @input="handleFileUpload" accept=".csv"
                                        class="file-input file-input-bordered w-full max-w-xs text-sm sm:text-primary-content" />
                                    <div class="label">
                                        <span class="label-text-alt text-xs sm:text-sm">Please upload a CSV file</span>
                                    </div>
                                </label>
                                <button type="submit" :disabled="!form.file" class="btn btn-primary text-sm sm:text-primary-content">
                                    Upload CSV
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
