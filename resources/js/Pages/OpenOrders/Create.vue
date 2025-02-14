<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Open Orders", route: "open_orders.index" },
    { label: "Create" },
];
</script>

<template>
    <PageLayout title="Open Orders" :breadcrumbs="breadcrumbs">
        <FileUpload
            label="Open Orders"
            store-route="open_orders.store"
            check-existing-route="open_orders.check-existing"
        />
    </PageLayout>
</template>
