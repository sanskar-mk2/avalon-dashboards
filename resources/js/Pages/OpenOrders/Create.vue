<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { onUpdated, ref } from "vue";

const form = useForm({
    file: null,
});

const showSuccess = ref(false);
const showError = ref(false);
const flashMessage = ref('');

const props = defineProps({
    flash: {
        type: Object,
        default: () => ({}),
    },
});

onUpdated(() => {
    if (props.flash.success) {
        showSuccess.value = true;
        flashMessage.value = props.flash.success;
        setTimeout(() => {
            showSuccess.value = false;
        }, 3000);
    }
    if (props.flash.error) {
        showError.value = true;
        flashMessage.value = props.flash.error;
        setTimeout(() => {
            showError.value = false;
        }, 3000);
    }
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route("open_orders.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>

    <Head title="Open Orders" />

    <AuthenticatedLayout>
        <div class="toast toast-top toast-end" v-if="showSuccess || showError">
            <div class="alert alert-success" v-if="showSuccess">
                <span>{{ flashMessage }}</span>
            </div>
            <div class="alert alert-error" v-if="showError">
                <span>{{ flashMessage }}</span>
            </div>
        </div>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Open Orders
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
                                        <span class="label-text">Open Orders CSV File</span>
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
