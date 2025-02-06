<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    salespeople: {
        type: Object,
        default: () => ({}),
    },
});

const showDeleteModal = ref(false);

const columns = [
    { key: 'company_no', label: 'Company No' },
    { key: 'salesman_no', label: 'Salesman No' },
    { key: 'salesman_name', label: 'Salesman Name' },
    { key: 'sales_count', label: 'Sales' },
    { key: 'open_orders_count', label: 'Open Orders' },
    { key: 'as_of_date', label: 'As Of Date' },
];

const breadcrumbs = [
    { label: 'Home', route: 'dashboard' },
    { label: 'Salespeople' }
];
</script>

<template>
    <PageLayout title="Salespeople" :breadcrumbs="breadcrumbs">
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link :href="route('salespeople.create')" class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm">
            Upload CSV
            </Link>
            <button @click="showDeleteModal = true" class="btn btn-error btn-sm sm:btn-md text-xs sm:text-sm">
                Delete All
            </button>
        </div>

        <DataTable :data="salespeople" :columns="columns" route-name="salespeople.show" link-column="salesman_name" />

        <DeleteModal :show="showDeleteModal"
            message="Are you sure you want to delete all salesperson records? This action cannot be undone."
            delete-route="salespeople.deleteAll" @close="showDeleteModal = false" />
    </PageLayout>
</template>
