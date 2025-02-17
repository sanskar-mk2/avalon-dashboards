<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    availableMonths: {
        type: Array,
        default: () => [],
    },
    currentMonth: {
        type: String,
        required: true,
    },
    showYTD: {
        type: Boolean,
        default: false,
    },
});

const selectedMonth = ref(props.currentMonth);

const formatMonthDisplay = (month) => {
    if (month === "YTD") {
        return "Year to Date";
    }
    return new Date(month).toLocaleDateString("en-US", {
        month: "long",
        year: "numeric",
    });
};

watch(selectedMonth, (newValue) => {
    // Get current route parameters
    const currentParams = route().params;

    router.get(
        route(route().current(), currentParams),
        { month: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
});
</script>

<template>
    <div class="flex items-center gap-2">
        <select
            v-model="selectedMonth"
            class="select select-secondary select-sm sm:select-md text-xs sm:text-sm"
        >
            <option v-if="showYTD" value="YTD">Year to Date</option>
            <option
                v-for="month in availableMonths"
                :key="month"
                :value="month"
            >
                {{ formatMonthDisplay(month) }}
            </option>
        </select>
    </div>
</template>
