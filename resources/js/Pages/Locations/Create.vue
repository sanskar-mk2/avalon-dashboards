<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Locations", route: "locations.index" },
    { label: "Create" },
];
</script>

<template>
    <PageLayout title="Locations" :breadcrumbs="breadcrumbs">
        <FileUpload
            label="Locations"
            store-route="locations.store"
            :showDateSelectors="false"
        />
    </PageLayout>
</template>
