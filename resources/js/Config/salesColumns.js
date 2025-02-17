import { Link } from "@inertiajs/vue3";
import dayjs from "dayjs";

export const getSalesColumns = () => [
    {
        key: "location",
        label: "Location",
        custom_value: (model) => {
            if (model.location_model != null) {
                return {
                    component: Link,
                    props: {
                        href: route("locations.show", model.location_model.id),
                        class: "link",
                    },
                    children: model.location_model.location_abbreviation,
                };
            }
            return model.location;
        },
    },
    { key: "order_no", label: "Order No" },
    {
        key: "order_date",
        label: "Order Date",
        custom_value: (model) => model.order_date.replace(/-/g, "\u2011"),
    },
    {
        key: "customer_name",
        label: "Customer Name",
        custom_value: (model) => {
            return {
                component: Link,
                props: {
                    href: route("customers.show", model.customer_name),
                    class: "link",
                },
                children: model.customer_name,
            };
        },
    },
    { key: "invoice_no", label: "Invoice No" },
    {
        key: "invoice_date",
        label: "Invoice Date",
        custom_value: (model) => model.invoice_date.replace(/-/g, "\u2011"),
    },
    {
        key: "salesperson",
        label: "Salesperson",
        custom_value: (model) => {
            if (model.salesperson_model != null) {
                return {
                    component: Link,
                    props: {
                        href: route(
                            "salespeople.show",
                            model.salesperson_model.id
                        ),
                        class: "link",
                    },
                    children: model.salesperson_model.salesman_name,
                };
            }
            return model.salesperson;
        },
    },
    { key: "item_no", label: "Item No" },
    { key: "item_desc", label: "Item Desc", ellipsis_after: 10 },
    { key: "qty", label: "Qty", remove_decimals: true },
    { key: "ext_sales", label: "Ext Sales", to_format: true },
    { key: "ext_cost", label: "Ext Cost", to_format: true },
    {
        key: "ext_profit",
        label: "GP %",
        custom_value: (model) => {
            const gp =
                ((model.ext_sales - model.ext_cost) / model.ext_sales) * 100;
            return isNaN(gp) ? "-" : gp.toFixed(2) + "%";
        },
    },
    {
        key: "period",
        label: "Period",
        custom_value: (model) => {
            return dayjs(model.period).format("MMM\u2011YY");
        },
    },
    { key: "requested_ship_date", label: "Requested Ship Date" },
    { key: "mfg_code", label: "Mfg Code" },
];
