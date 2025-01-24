<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onUpdated, ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

const showSuccess = ref(false);
const showError = ref(false);
const flashMessage = ref('');

const props = defineProps({
    flash: {
        type: Object,
        default: () => ({}),
    },
    salespeople: {
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
</script>

<template>

    <Head title="Salespeople" />

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
                Salespeople
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg">
                    <div class="p-6 ">
                        <div class="flex justify-end gap-4 mb-4">
                            <Link :href="route('salespeople.create')" class="btn btn-primary">
                            Upload Salesperson CSV
                            </Link>
                            <Link :href="route('salespeople.deleteAll')" method="delete" as="button" class="btn btn-error"
                                preserve-scroll>
                            Delete All Salesperson Records
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Company No</th>
                                        <th>Salesman No</th>
                                        <th>Salesman Name</th>
                                        <th>As Of Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="salesperson in salespeople.data" :key="salesperson.id">
                                        <td>{{ salesperson.company_no }}</td>
                                        <td>{{ salesperson.salesman_no }}</td>
                                        <td>{{ salesperson.salesman_name }}</td>
                                        <td>{{ salesperson.as_of_date }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex justify-between w-full items-center">
                            <div class="text-base-content text-md">
                                Showing {{ salespeople.from }} to {{ salespeople.to }} of {{ salespeople.total }} results
                            </div>
                            <Pagination :links="salespeople.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
