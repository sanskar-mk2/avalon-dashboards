<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Inventories", route: "inventories.index" },
    { label: "Create" },
];
</script>

<template>
    <PageLayout title="Inventories" :breadcrumbs="breadcrumbs">
        <FileUpload
            label="Inventories"
            store-route="inventories.store"
            check-existing-route="inventories.check-existing"
        />
    </PageLayout>
</template>
