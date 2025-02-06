<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        default: "Confirm Deletion"
    },
    message: {
        type: String,
        required: true
    },
    deleteRoute: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['close']);
</script>

<template>
    <dialog :open="show" class="modal">
        <div class="modal-box border-2 border-base-300">
            <h3 class="text-base sm:text-lg font-bold">{{ title }}</h3>
            <p class="py-4 text-xs sm:text-sm">{{ message }}</p>
            <div class="modal-action">
                <Link :href="route(deleteRoute)" method="delete" as="button" class="btn btn-error text-xs sm:text-sm"
                    preserve-scroll @click="$emit('close')">
                    Delete All
                </Link>
                <button class="btn text-xs sm:text-sm" @click="$emit('close')">Cancel</button>
            </div>
        </div>
    </dialog>
</template> 