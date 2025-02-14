<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Salespeople", route: "salespeople.index" },
    { label: "Create" },
];
</script>

<template>
    <PageLayout title="Salespeople" :breadcrumbs="breadcrumbs">
        <FileUpload
            label="Salespeople"
            store-route="salespeople.store"
            :showDateSelectors="false"
        />
    </PageLayout>
</template>
