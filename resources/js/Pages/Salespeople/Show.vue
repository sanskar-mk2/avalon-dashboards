<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { getSalesColumns } from "@/Config/salesColumns";

const props = defineProps({
    salesperson: {
        type: Object,
        default: () => ({}),
    },
    sales: {
        type: Object,
        default: () => ({}),
    },
});

const columns = getSalesColumns();

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Salespeople", route: "salespeople.index" },
    { label: props.salesperson.salesman_name },
];
</script>

<template>
    <PageLayout :title="salesperson.salesman_name" :breadcrumbs="breadcrumbs">
        <DataTable :data="sales" :columns="columns" />
    </PageLayout>
</template>
