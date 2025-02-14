<script setup>
import { ref, onMounted, computed, watch } from "vue";
import Chart from "chart.js/auto";
import theme from "daisyui/src/theming/themes";
import Color from "colorjs.io";

const props = defineProps({
    us_warehouse_inventory: Object,
});

const inventoryChartRef = ref(null);
let inventoryChart = null;
const current_theme = ref(null);

const getThemeColor = computed(() => {
    const theme_obj = theme[current_theme.value];
    if (!theme_obj) {
        return {};
    }
    const color_keys = Object.keys(theme_obj).filter(
        (key) =>
            !key.startsWith("--") &&
            key !== "color-scheme" &&
            key !== "fontFamily"
    );
    const color_obj = {};
    color_keys.forEach((key) => {
        color_obj[key] = new Color(theme_obj[key])
            .toGamut({ space: "srgb" })
            .to("srgb")
            .toString({ format: "hex" });
    });
    console.log(color_obj);
    return color_obj;
});

const createInventoryChart = () => {
    if (inventoryChart) {
        inventoryChart.destroy();
    }

    const chartData = { ...props.us_warehouse_inventory };
    chartData.datasets = chartData.datasets.map((dataset, index) => {
        let color;
        switch (dataset.label) {
            case "Inventory Value":
                color = getThemeColor.value.primary;
                break;
            case "Sales":
                color = getThemeColor.value.secondary;
                break;
            case "COGS":
                color = getThemeColor.value.accent;
                break;
            case "Inventory Turn":
                color = getThemeColor.value.neutral;
                break;
            default:
                color = getThemeColor.value.primary;
        }

        return {
            ...dataset,
            backgroundColor:
                dataset.label === "Inventory Turn" ? color : color + "80",
            borderColor: color,
            borderWidth: dataset.label === "Inventory Turn" ? 2 : 1,
            type: dataset.label === "Inventory Turn" ? "line" : "bar",
            yAxisID: dataset.label === "Inventory Turn" ? "y1" : "y",
        };
    });

    inventoryChart = new Chart(inventoryChartRef.value, {
        type: "bar",
        data: chartData,
        options: {
            scales: {
                y: {
                    position: "left",
                    ticks: {
                        callback: function (value) {
                            return (value / 1000000).toFixed(1) + "M";
                        },
                    },
                },
                y1: {
                    position: "right",
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function (value) {
                            return value.toFixed(1) + "x";
                        },
                    },
                },
            },
        },
    });
};

// Watch for theme changes
new MutationObserver((mutations) => {
    if (mutations.some((m) => m.attributeName === "data-theme")) {
        current_theme.value =
            document.documentElement.getAttribute("data-theme");
        createInventoryChart();
    }
}).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["data-theme"],
});

// Watch for data changes
watch(
    () => props.us_warehouse_inventory,
    () => {
        createInventoryChart();
    },
    { deep: true }
);

onMounted(() => {
    current_theme.value = document.documentElement.getAttribute("data-theme");
    createInventoryChart();
});
</script>

<template>
    <div class="flex justify-center mt-10">
        <div class="w-full mx-4 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                Inventory Metrics
            </h2>
            <canvas ref="inventoryChartRef"></canvas>
        </div>
    </div>
</template>
