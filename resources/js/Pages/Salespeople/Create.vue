<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    file: null,
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("salespeople.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>

    <Head title="Salespeople" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Salespeople
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid w-full max-w-sm items-center gap-1.5">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Salespeople CSV File</span>
                                    </div>
                                    <input type="file" @input="handleFileUpload" accept=".csv"
                                        class="file-input file-input-bordered w-full max-w-xs" />
                                    <div class="label">
                                        <span class="label-text-alt">Please upload a CSV file</span>
                                    </div>
                                </label>
                                <button type="submit" :disabled="!form.file" class="btn btn-primary">
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
