<script setup>
import { Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import { useFloating, offset, shift } from "@floating-ui/vue";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    routeName: {
        type: String,
        default: null,
    },
    linkColumn: {
        type: String,
        default: null,
    },
});

// Tooltip refs and state
const tooltipRef = ref(null);
const tooltipContent = ref(null);
const isTooltipVisible = ref(false);
const activeText = ref("");

const { floatingStyles } = useFloating(tooltipRef, tooltipContent, {
    middleware: [offset(10), shift()],
    placement: "top",
});

const numberFormatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const formatValue = (value) => {
    if (!value) return "-";
    if (value == 0) return "-";
    return value;
};

const formatNumberCurrency = (value) => {
    value = formatValue(value);
    if (value == "-") return "-";
    return numberFormatter.format(value);
};

const removeDecimals = (value) => {
    value = formatValue(value);
    if (value == "-") return "-";
    return Math.floor(value);
};

const truncateText = (text, length) => {
    if (!text) return "-";
    text = String(text);
    if (text.length <= length) return text;
    return text.slice(0, length) + "...";
};

const showTooltip = (event, text) => {
    if (!text || text === "-") return;
    tooltipRef.value = event.target;
    activeText.value = text;
    isTooltipVisible.value = true;
};

const hideTooltip = () => {
    isTooltipVisible.value = false;
    activeText.value = "";
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-zebra text-xs sm:text-sm">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column.key">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data.data" :key="item.id">
                    <td v-for="column in columns" :key="column.key">
                        <template v-if="column.key === linkColumn && routeName">
                            <Link
                                class="link"
                                :href="route(routeName, item.id)"
                                @mouseenter="
                                    column.ellipsis_after &&
                                        showTooltip($event, item[column.key])
                                "
                                @mouseleave="hideTooltip"
                            >
                                {{
                                    column.ellipsis_after
                                        ? truncateText(
                                              item[column.key],
                                              column.ellipsis_after
                                          )
                                        : item[column.key] || "-"
                                }}
                            </Link>
                        </template>
                        <template v-else-if="column.custom_value">
                            <component
                                v-if="
                                    typeof column.custom_value(item) ===
                                    'object'
                                "
                                :is="column.custom_value(item).component"
                                v-bind="column.custom_value(item).props"
                                @mouseenter="
                                    column.ellipsis_after &&
                                        showTooltip(
                                            $event,
                                            column.custom_value(item).children
                                        )
                                "
                                @mouseleave="hideTooltip"
                            >
                                {{ column.custom_value(item).children }}
                            </component>
                            <span
                                v-else
                                @mouseenter="
                                    column.ellipsis_after &&
                                        showTooltip(
                                            $event,
                                            column.custom_value(item)
                                        )
                                "
                                @mouseleave="hideTooltip"
                            >
                                {{
                                    column.ellipsis_after
                                        ? truncateText(
                                              column.custom_value(item),
                                              column.ellipsis_after
                                          )
                                        : column.custom_value(item)
                                }}
                            </span>
                        </template>
                        <template v-else>
                            <span
                                @mouseenter="
                                    column.ellipsis_after &&
                                        showTooltip($event, item[column.key])
                                "
                                @mouseleave="hideTooltip"
                            >
                                <template v-if="column.to_format">
                                    {{ formatNumberCurrency(item[column.key]) }}
                                </template>
                                <template v-else-if="column.remove_decimals">
                                    {{ removeDecimals(item[column.key]) }}
                                </template>
                                <template v-else>
                                    {{
                                        column.ellipsis_after
                                            ? truncateText(
                                                  item[column.key],
                                                  column.ellipsis_after
                                              )
                                            : formatValue(item[column.key])
                                    }}
                                </template>
                            </span>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Floating Tooltip -->
        <div
            v-if="isTooltipVisible && activeText"
            ref="tooltipContent"
            :style="floatingStyles"
            class="bg-gray-800 text-white px-2 py-1 rounded text-sm max-w-xs z-50"
        >
            {{ activeText }}
        </div>
    </div>
    <div class="mt-2 flex justify-between w-full items-center">
        <div class="text-base-content text-xs sm:text-sm">
            {{ data.total === 0 ? 'No results found' : `Showing ${data.from} to ${data.to} of ${data.total} results` }}
        </div>
        <Pagination :links="data.links" />
    </div>
</template>
