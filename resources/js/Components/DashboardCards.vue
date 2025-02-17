<script setup>
import ArrowDown from "./ArrowDown.vue";
import Dash from "./Dash.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import dayjs from "dayjs";
const props = defineProps({
    cards_data: Object,
    month: String,
    except: {
        type: Array,
        default: () => [],
    },
});

const sales_cards = computed(() => {
    const last_sales_month = props.cards_data.sales[0]?.period
        ? props.cards_data.sales[0].period === "YTD"
            ? "YTD " + new Date().getFullYear()
            : dayjs(props.cards_data.sales[0].period).format("MMM YYYY")
        : "No Data";
    const last_month_sales = props.cards_data.sales[0]?.total_amount
        ? (props.cards_data.sales[0].total_amount / 1000000).toFixed(2) + "M"
        : "0M";
    const percent_change = props.cards_data.sales[1]?.total_amount
        ? ((props.cards_data.sales[0].total_amount -
              props.cards_data.sales[1].total_amount) /
              props.cards_data.sales[1].total_amount) *
          100
        : 0;

    return {
        last_sales_month,
        last_month_sales,
        percent_change,
    };
});

const open_orders_cards = computed(() => {
    const last_open_orders_month = props.cards_data.open_orders[0]?.period
        ? dayjs(props.cards_data.open_orders[0].period).format("MMM YYYY")
        : "No Data";
    const last_month_open_orders = props.cards_data.open_orders[0]?.total_amount
        ? (props.cards_data.open_orders[0].total_amount / 1000000).toFixed(2) +
          "M"
        : "0M";
    const percent_change = props.cards_data.open_orders[1]?.total_amount
        ? ((props.cards_data.open_orders[0].total_amount -
              props.cards_data.open_orders[1].total_amount) /
              props.cards_data.open_orders[1].total_amount) *
          100
        : 0;

    return {
        last_open_orders_month,
        last_month_open_orders,
        percent_change,
    };
});

const inventory_cards = computed(() => {
    if (props.except.includes("inventory")) {
        return null;
    }
    const last_month = props.cards_data.total_inventory[0]?.period
        ? dayjs(props.cards_data.total_inventory[0].period).format("MMM YYYY")
        : "No Data";
    const total = props.cards_data.total_inventory[0]?.total_amount
        ? (props.cards_data.total_inventory[0].total_amount / 1000000).toFixed(
              2
          ) + "M"
        : "0M";
    const percent_change = props.cards_data.total_inventory[1]?.total_amount
        ? ((props.cards_data.total_inventory[0].total_amount -
              props.cards_data.total_inventory[1].total_amount) /
              props.cards_data.total_inventory[1].total_amount) *
          100
        : 0;

    return {
        last_month,
        total,
        percent_change,
    };
});

const receivables_cards = computed(() => {
    const last_month = props.cards_data.total_receivables[0]?.period
        ? dayjs(props.cards_data.total_receivables[0].period).format("MMM YYYY")
        : "No Data";
    const total = props.cards_data.total_receivables[0]?.total_amount
        ? (
              props.cards_data.total_receivables[0].total_amount / 1000000
          ).toFixed(2) + "M"
        : "0M";
    const percent_change = props.cards_data.total_receivables[1]?.total_amount
        ? ((props.cards_data.total_receivables[0].total_amount -
              props.cards_data.total_receivables[1].total_amount) /
              props.cards_data.total_receivables[1].total_amount) *
          100
        : 0;

    return {
        last_month,
        total,
        percent_change,
    };
});
</script>

<template>
    <div
        class="grid grid-cols-1 mx-4 sm:mx-0 sm:grid-cols-4 gap-4"
        :class="{
            'sm:grid-cols-3': except.length === 1,
            'sm:grid-cols-2': except.length === 2,
            'sm:grid-cols-1': except.length === 3,
        }"
    >
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title text-[11px] sm:text-sm">
                    <Link
                        :href="
                            route('sales.index', {
                                'filter[period]': month,
                            })
                        "
                        class="link"
                        >Total Sales for
                        {{ sales_cards.last_sales_month }}</Link
                    >
                </div>
                <div class="stat-value text-primary text-xl sm:text-3xl">
                    {{ sales_cards.last_month_sales }}
                </div>
                <div
                    class="stat-desc flex items-center gap-1 text-[10px] sm:text-xs"
                >
                    <div
                        class="badge"
                        :class="{
                            'bg-success/10 text-success':
                                sales_cards.percent_change > 0,
                            'bg-error/10 text-error':
                                sales_cards.percent_change < 0,
                            'bg-warning/10 text-warning':
                                sales_cards.percent_change === 0,
                        }"
                    >
                        <ArrowDown
                            v-if="sales_cards.percent_change < 0"
                            class="w-4 h-4 stroke-error"
                        />
                        <ArrowDown
                            v-else-if="sales_cards.percent_change > 0"
                            class="w-4 h-4 stroke-success rotate-180"
                        />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{ Math.abs(sales_cards.percent_change).toFixed(2) }}%
                    </div>
                    <span>
                        {{
                            month === "YTD"
                                ? "Compared to last year"
                                : "Compared to last month"
                        }}
                    </span>
                </div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title text-[11px] sm:text-sm">
                    <Link
                        :href="
                            route('open_orders.index', {
                                month: month,
                            })
                        "
                        class="link"
                        >Total Open Orders as of
                        {{ open_orders_cards.last_open_orders_month }}</Link
                    >
                </div>
                <div class="stat-value text-primary text-xl sm:text-3xl">
                    {{ open_orders_cards.last_month_open_orders }}
                </div>
                <div
                    class="stat-desc flex items-center gap-1 text-[10px] sm:text-xs"
                >
                    <div
                        class="badge"
                        :class="{
                            'bg-success/10 text-success':
                                open_orders_cards.percent_change > 0,
                            'bg-error/10 text-error':
                                open_orders_cards.percent_change < 0,
                            'bg-warning/10 text-warning':
                                open_orders_cards.percent_change === 0,
                        }"
                    >
                        <ArrowDown
                            v-if="open_orders_cards.percent_change < 0"
                            class="w-4 h-4 stroke-error"
                        />
                        <ArrowDown
                            v-else-if="open_orders_cards.percent_change > 0"
                            class="w-4 h-4 stroke-success rotate-180"
                        />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{
                            Math.abs(open_orders_cards.percent_change).toFixed(
                                2
                            )
                        }}%
                    </div>
                    <span> Compared to last month </span>
                </div>
            </div>
        </div>

        <div class="stats shadow" v-if="inventory_cards">
            <div class="stat">
                <div class="stat-title text-[11px] sm:text-sm">
                    <Link
                        :href="
                            route('inventories.index', {
                                month: month,
                            })
                        "
                        class="link"
                        >Total Inventory as of
                        {{ inventory_cards.last_month }}</Link
                    >
                </div>
                <div class="stat-value text-primary text-xl sm:text-3xl">
                    {{ inventory_cards.total }}
                </div>
                <div
                    class="stat-desc flex items-center gap-1 text-[10px] sm:text-xs"
                >
                    <div
                        class="badge"
                        :class="{
                            'bg-success/10 text-success':
                                inventory_cards.percent_change > 0,
                            'bg-error/10 text-error':
                                inventory_cards.percent_change < 0,
                            'bg-warning/10 text-warning':
                                inventory_cards.percent_change === 0,
                        }"
                    >
                        <ArrowDown
                            v-if="inventory_cards.percent_change < 0"
                            class="w-4 h-4 stroke-error"
                        />
                        <ArrowDown
                            v-else-if="inventory_cards.percent_change > 0"
                            class="w-4 h-4 stroke-success rotate-180"
                        />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{
                            Math.abs(inventory_cards.percent_change).toFixed(2)
                        }}%
                    </div>
                    <span> Compared to last month </span>
                </div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title text-[11px] sm:text-sm">
                    <Link
                        :href="
                            route('account_receivables.index', {
                                month: month,
                            })
                        "
                        class="link"
                        >Total Receivables as of
                        {{ receivables_cards.last_month }}</Link
                    >
                </div>
                <div class="stat-value text-primary text-xl sm:text-3xl">
                    {{ receivables_cards.total }}
                </div>
                <div
                    class="stat-desc flex items-center gap-1 text-[10px] sm:text-xs"
                >
                    <div
                        class="badge"
                        :class="{
                            'bg-success/10 text-success':
                                receivables_cards.percent_change > 0,
                            'bg-error/10 text-error':
                                receivables_cards.percent_change < 0,
                            'bg-warning/10 text-warning':
                                receivables_cards.percent_change === 0,
                        }"
                    >
                        <ArrowDown
                            v-if="receivables_cards.percent_change < 0"
                            class="w-4 h-4 stroke-error"
                        />
                        <ArrowDown
                            v-else-if="receivables_cards.percent_change > 0"
                            class="w-4 h-4 stroke-success rotate-180"
                        />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{
                            Math.abs(receivables_cards.percent_change).toFixed(
                                2
                            )
                        }}%
                    </div>
                    <span> Compared to last month </span>
                </div>
            </div>
        </div>
    </div>
</template>
