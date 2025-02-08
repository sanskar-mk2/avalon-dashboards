<script setup>
import { ref, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";

const showSuccess = ref(false);
const showError = ref(false);
const flashMessage = ref("");
const page = usePage();

onMounted(() => {
    handleFlash(page.props.flash);
});

const handleFlash = (flash) => {
    if (flash.success) {
        showSuccess.value = true;
        flashMessage.value = flash.success;
        setTimeout(() => {
            showSuccess.value = false;
        }, 3000);
    }
    if (flash.error) {
        showError.value = true;
        flashMessage.value = flash.error;
        setTimeout(() => {
            showError.value = false;
        }, 3000);
    }
};

watch(() => page.props.flash, handleFlash, { deep: true, immediate: true });
</script>
<template>
    <div class="toast toast-top toast-end" v-if="showSuccess || showError">
        <div class="alert alert-success" v-if="showSuccess">
            <span>{{ flashMessage }}</span>
        </div>
        <div class="alert alert-error" v-if="showError">
            <span>{{ flashMessage }}</span>
        </div>
    </div>
</template>
