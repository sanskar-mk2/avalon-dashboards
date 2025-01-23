<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { onUpdated, ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

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
    sales: {
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
                Sales
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="sale in sales.data" :key="sale.id">
                                    <td>{{ sale.order_number }}</td>
                                    <td>{{ sale.customer_name }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="100%">
                                        <Pagination :links="sales.links" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
