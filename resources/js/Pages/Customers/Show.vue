<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { getSalesColumns } from "@/Config/salesColumns";

const props = defineProps({
    customer_name: {
        type: String,
        required: true,
    },
    sales: {
        type: Object,
        default: () => ({}),
    },
});

const columns = getSalesColumns();

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Sales", route: "sales.index" },
    { label: props.customer_name },
];
</script>

<template>
    <PageLayout :title="customer_name" :breadcrumbs="breadcrumbs">
        <DataTable :data="sales" :columns="columns" />
    </PageLayout>
</template> 