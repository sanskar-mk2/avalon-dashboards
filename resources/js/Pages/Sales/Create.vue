<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Sales", route: "sales.index" },
    { label: "Create" },
];
</script>

<template>
    <PageLayout title="Sales" :breadcrumbs="breadcrumbs">
        <FileUpload
            label="Sales"
            store-route="sales.store"
            check-existing-route="sales.check-existing"
        />
    </PageLayout>
</template>
