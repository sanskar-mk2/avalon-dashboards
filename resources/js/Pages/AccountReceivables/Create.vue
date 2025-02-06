<script setup>
import PageLayout from "@/Components/PageLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";

const breadcrumbs = [
    { label: 'Home', route: 'dashboard' },
    { label: 'Account Receivables', route: 'account_receivables.index' },
    { label: 'Create' }
];
</script>


<template>
    <PageLayout title="Account Receivables" :breadcrumbs="breadcrumbs">
        <FileUpload label="Account Receivables" store-route="account_receivables.store" />
    </PageLayout>
</template>
