<template>
    <aside class="page-aside" :class="{ hide: !showAside }">
        <!-- accordion menu -->
        <!-- aside brand -->
        <div class="aside-brand">
            <inertia-link :href="route('dashboard')" class="text-decoration-none">
                <h2>{{ lang.company }}</h2>
            </inertia-link>
        </div>
        <!-- End aside-brand -->

        <ul class="accordion" id="asideAccordion">
            <h6 class="py-1 ps-3">{{ lang.basic }}</h6>
            <li v-if="permissions.includes('dashboard-view')" class="accordion-item">
                <Link :href="route('dashboard')" class="single-nav-link">
                    <i class="bi bi-house"></i>
                    <span>{{ lang.dashboard }}</span>
                </Link>
            </li>
            <aside-link
                v-if="permissions.includes('customer-view') || permissions.includes('supplier-view')"
                dataTarget="#parties"
                ariaControl="parties"
                id="parties"
                icon="bi bi-people"
                :data="lang.parties"
                :showUrl="[
                    'supplier.create',
                    'supplier.index',
                    'customer.create',
                    'customer.index',
                    'mokam.index',
                    ]"
            >
                <li v-if="permissions.includes('supplier-view')">
                    <Link :href="route('supplier.index')" class="nav-link">
                        {{ lang.supplier }}
                    </Link>
                </li>

                <li v-if="permissions.includes('customer-view')">
                    <Link :href="route('customer.index')" class="nav-link">
                        {{ lang.customer }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('mokam.index')" class="nav-link">
                        {{ lang.mokam }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('purchase-view') || permissions.includes('purchase-create')"
                dataTarget="#purchase"
                ariaControl="purchase"
                id="purchase"
                icon="bi bi-bag"
                :data="lang.purchase"
                :showUrl="[
                    'purchase.index',
                    'purchase.create',
                    'purchase.show',
                    'purchase.edit',
                    ]"
            >
                <li v-if="permissions.includes('purchase-view')">
                    <Link :href="route('purchase.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('purchase-create')">
                    <Link :href="route('purchase.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('purchase_return-view') || permissions.includes('purchase_return-create')"
                dataTarget="#purchase-return"
                ariaControl="purchase-return"
                id="purchase-return"
                icon="bi bi-cart-x"
                :data="lang.purchase_return"
                :showUrl="[
                    'purchase-return.index',
                    'purchase-return.create',
                    'purchase-return.show',
                    'purchase-return.edit',
                    ]"
            >
                <li  v-if="permissions.includes('purchase_return-view')">
                    <Link :href="route('purchase-return.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li  v-if="permissions.includes('purchase_return-create')">
                    <Link :href="route('purchase-return.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('sale-view') || permissions.includes('sale-create')"
                dataTarget="#sale"
                ariaControl="sale"
                id="sale"
                icon="bi bi-cart3"
                :data="lang.sales"
                :showUrl="[
                    'sale.index',
                    'sale.create',
                    'sale.show',
                    'sale.edit',
                    ]"
            >
                <li v-if="permissions.includes('sale-view')">
                    <Link :href="route('sale.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('sale-create')">
                    <Link :href="route('sale.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('sale_return-view') || permissions.includes('sale_return-create')"
                dataTarget="#sale-return"
                ariaControl="sale-return"
                id="sale-return"
                icon="bi bi-cart-x"
                :data="lang.sale_return"
                :showUrl="[
                    'sale-return.index',
                    'sale-return.create',
                    'sale-return.show',
                    'sale-return.edit',
                    ]"
            >
                <li v-if="permissions.includes('sale_return-view')">
                    <Link :href="route('sale-return.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('sale_return-create')">
                    <Link :href="route('sale-return.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <li v-if="permissions.includes('current_stock-view')" class="accordion-item">
                <Link :href="route('stock.index')" class="single-nav-link">
                    <i class="bi bi-house"></i>
                    <span>{{ lang.stock }}</span>
                </Link>
            </li>

            <aside-link
                v-if="permissions.includes('damage_stock-view') || permissions.includes('damage_stock-create')"
                dataTarget="#damage-stock"
                ariaControl="damage-stock"
                id="damage-stock"
                icon="bi bi-cart-x"
                :data="lang.damage_stock"
                :showUrl="[
                    'damage-stock.index',
                    'damage-stock.create',
                    'damage-stock.show',
                    'damage-stock.edit',
                    ]"
            >
                <li v-if="permissions.includes('damage_stock-view')">
                    <Link :href="route('damage-stock.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('damage_stock-create')">
                    <Link :href="route('damage-stock.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('damage_return-view') || permissions.includes('damage_return-create')"
                dataTarget="#damage-return"
                ariaControl="damage-return"
                id="damage-return"
                icon="bi bi-arrow-counterclockwise"
                :data="lang.damage+' '+lang.return"
                :showUrl="[
                    'damage-return.index',
                    'damage-return.create',
                    'damage-return.show',
                    'damage-return.edit',
                    ]"
            >
                <li v-if="permissions.includes('damage_return-view')">
                    <Link :href="route('damage-return.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('damage_return-create')">
                    <Link :href="route('damage-return.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('production-view') || permissions.includes('production-create')"
                dataTarget="#production"
                ariaControl="production"
                id="production"
                icon="bi bi-bag"
                :data="lang.production"
                :showUrl="[
                    'production.index',
                    'production.create',
                    'production.show',
                    'production.edit',
                    ]"
            >
                <li v-if="permissions.includes('production-view')">
                    <Link :href="route('production.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('production-create')">
                    <Link :href="route('production.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('product_transfer-view') || permissions.includes('product_transfer-create')"
                dataTarget="#product-transfer"
                ariaControl="product-transfer"
                id="product-transfer"
                icon="bi bi-bag"
                :data="lang.product_transfer"
                :showUrl="[
                    'product-transfer.index',
                    'product-transfer.create',
                    'product-transfer.show',
                    'product-transfer.edit',
                    ]"
            >
                <li v-if="permissions.includes('product_transfer-view')">
                    <Link :href="route('product-transfer.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li v-if="permissions.includes('product_transfer-create')">
                    <Link :href="route('product-transfer.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('due_management-view') || permissions.includes('due_management-create')"
                dataTarget="#due-manage"
                ariaControl="due-manage"
                id="due-manage"
                icon="bi bi-wallet2"
                :data="lang.due_manage"
                :showUrl="[
                    'customer-due-manage.index',
                    'customer-due-manage.create',
                    'supplier-due-manage.index',
                    'supplier-due-manage.create'
                    ]"
            >
                <li v-if="permissions.includes('due_management-view')">
                    <Link :href="route('customer-due-manage.index')" class="nav-link">
                        {{ lang.customer }}
                    </Link>
                </li>

                <li v-if="permissions.includes('due_management-create')">
                    <Link :href="route('supplier-due-manage.index')" class="nav-link">
                        {{ lang.supplier }}
                    </Link>
                </li>
            </aside-link>

            <li v-if="permissions.includes('barcode-view')" class="accordion-item">
                <Link :href="route('barcode.single')" class="single-nav-link">
                    <i class="bi bi-upc-scan"></i>
                    <span>{{ lang.barcode }}</span>
                </Link>
            </li>

<!--            <aside-link-->
<!--                v-if="permissions.includes('barcode-view')"-->
<!--                dataTarget="#barcode"-->
<!--                ariaControl="barcode"-->
<!--                id="barcode"-->
<!--                icon="bi bi-wallet2"-->
<!--                :data="lang.barcode"-->
<!--                :showUrl="[-->
<!--                    'barcode.batch',-->
<!--                    'barcode.single',-->
<!--                    ]"-->
<!--            >-->
<!--                <li v-if="permissions.includes('barcode-view')">-->
<!--                    <Link :href="route('barcode.batch')" class="nav-link">-->
<!--                        {{ lang.batch }} {{ lang.barcode }}-->
<!--                    </Link>-->
<!--                </li>-->

<!--                <li v-if="permissions.includes('barcode-view')">-->
<!--                    <Link :href="route('barcode.single')" class="nav-link">-->
<!--                        {{ lang.single }} {{ lang.barcode }}-->
<!--                    </Link>-->
<!--                </li>-->
<!--            </aside-link>-->

            <h6 class="py-1 ps-3">{{ lang.transaction }}</h6>
            <!-- transaction start-->
            <aside-link
                v-if="permissions.includes('transaction-view')"
                dataTarget="#transaction-menu"
                ariaControl="transaction-menu"
                id="transaction-menu"
                icon="bi bi-cash-coin"
                :data="lang.balance + ' ' + lang.transfer"
                :showUrl="[
                    'transaction.index',
                    'transaction.create'
                    ]"
            >
                <li>
                    <Link :href="route('transaction.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('transaction.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>
            <!-- transaction start-->

            <li v-if="permissions.includes('advanced_salary-view')" class="accordion-item">
                <Link :href="route('advanced-salary.index')" class="single-nav-link">
                    <i class="bi bi-cash"></i>
                    <span>{{ lang.advanced }} {{ lang.salary }}</span>
                </Link>
            </li>
            <aside-link
                v-if="permissions.includes('salary-view')"
                dataTarget="#salary-menu"
                ariaControl="salary-menu"
                id="salary-menu"
                icon="bi bi-cash-coin"
                :data="lang.salary"
                :showUrl="[
                    'salary.index',
                    'salary.create'
                    ]"
            >
                <li>
                    <Link :href="route('salary.index')" class="nav-link">
                        {{ lang.all_records }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('salary.create')" class="nav-link">
                        {{ lang.new_entry }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('loan-view')"
                dataTarget="#load-menu"
                ariaControl="load-menu"
                id="load-menu"
                icon="bi bi-calendar3-range-fill"
                :data="lang.loan"
                :showUrl="[
                    'loan-account.index',
                    'loan.index'
                    ]"
            >
                <li>
                    <Link :href="route('loan-account.index')" class="nav-link">
                        {{ lang.loan_account }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('loan.index')" class="nav-link">
                        {{ lang.loan }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('income_record-view') || permissions.includes('income_sector-view')"
                dataTarget="#load-menu-income"
                ariaControl="load-menu-income"
                id="load-menu-income"
                icon="bi bi-calendar3-range-fill"
                :data="lang.income"
                :showUrl="[
                    'income-sector.index',
                    'income-record.index'
                    ]"
            >
                <li v-if="permissions.includes('income_sector-view')">
                    <Link :href="route('income-sector.index')" class="nav-link">
                        {{ lang.income_sector }}
                    </Link>
                </li>

                <li v-if="permissions.includes('income_record-view')">
                    <Link :href="route('income-record.index')" class="nav-link">
                        {{ lang.income_record }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('expense-view')"
                dataTarget="#load-menu-expense"
                ariaControl="load-menu-expense"
                id="load-menu-expense"
                icon="bi bi-bar-chart-steps"
                :data="lang.expense"
                :showUrl="[
                    'cost-category.index',
                    'cost-subcategory.index',
                    'expense.index'
                    ]"
            >
                <li>
                    <Link :href="route('cost-category.index')" class="nav-link">
                        {{ lang.cost_category }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('cost-subcategory.index')" class="nav-link">
                        {{ lang.cost_subcategory }}
                    </Link>
                </li>

                <li>
                    <Link :href="route('expense.index')" class="nav-link">
                        {{ lang.expense }}
                    </Link>
                </li>
            </aside-link>

            <h6 class="py-1 ps-3">{{ lang.report }}</h6>
            <!-- sms -->
            <aside-link
                v-if="permissions.includes('sms-view') || permissions.includes('sms-group_sms') || permissions.includes('sms-custom_sms')"
                dataTarget="#sms"
                ariaControl="sms"
                id="sms"
                icon="bi bi-bag"
                :data="lang.sms"
                :showUrl="[
                    'sms.index',
                    'sms.groupSms',
                    'sms.customSms',
                    ]"
            >
                <li v-if="permissions.includes('sms-view')">
                    <Link :href="route('sms.index')" class="nav-link">
                        {{ lang.sms }} {{ lang.report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('sms-group_sms')">
                    <Link :href="route('sms.groupSms')" class="nav-link">
                        {{ lang.group_sms }}
                    </Link>
                </li>

                <li v-if="permissions.includes('sms-custom_sms')">
                    <Link :href="route('sms.customSms')" class="nav-link">
                        {{ lang.custom_sms }}
                    </Link>
                </li>
            </aside-link>
            <!-- sms -->

            <li v-if="permissions.includes('cash_book-view')" class="accordion-item">
                <Link :href="route('report.cash-book')" class="single-nav-link">
                    <i class="bi bi-book-half"></i>
                    <span>{{ lang.cash_book }}</span>
                </Link>
            </li>

            <aside-link
                v-if="permissions.includes('ledger-supplier_ledger') || permissions.includes('ledger-customer_ledger')"
                dataTarget="#ledger"
                ariaControl="ledger"
                id="ledger"
                icon="bi bi-bag"
                :data="lang.ledger"
                :showUrl="[
                    'report.customer-ledger',
                    'report.supplier-ledger'
                    ]"
            >
                <li v-if="permissions.includes('ledger-customer_ledger')">
                    <Link :href="route('report.customer-ledger')" class="nav-link">
                        {{ lang.customer_ledger }}
                    </Link>
                </li>

                <li v-if="permissions.includes('ledger-supplier_ledger')">
                    <Link :href="route('report.supplier-ledger')" class="nav-link">
                        {{ lang.supplier_ledger }}
                    </Link>
                </li>
            </aside-link>

            <aside-link
                v-if="permissions.includes('report-sale_report')
                || permissions.includes('report-purchase_report')
                || permissions.includes('report-production_report')
                || permissions.includes('report-overall_report')
                || permissions.includes('report-profit_loss_report')
                || permissions.includes('report-expense_report')
                || permissions.includes('report-income_report')"
                dataTarget="#report"
                ariaControl="report"
                id="report"
                icon="bi bi-list-check"
                :data="lang.report"
                :showUrl="[
                    'report.income-report',
                    'report.expense-report',
                    'report.sale-report',
                    'report.purchase-report',
                    'report.production-report',
                    'report.overall-report',
                    'report.profit-loss',
                    'report.stock-report'
                    ]"
            >
                <li v-if="permissions.includes('report-income_report')">
                    <Link :href="route('report.income-report')" class="nav-link">
                        {{ lang.income_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-expense_report')">
                    <Link :href="route('report.expense-report')" class="nav-link">
                        {{ lang.expense_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-sale_report')">
                    <Link :href="route('report.sale-report')" class="nav-link">
                        {{ lang.sale_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-purchase_report')">
                    <Link :href="route('report.purchase-report')" class="nav-link">
                        {{ lang.purchase_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-product_report')">
                    <Link :href="route('report.product-report')" class="nav-link">
                        {{ lang.product }} {{ lang.report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-production_report')">
                    <Link :href="route('report.production-report')" class="nav-link">
                        {{ lang.production_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-overall_report')">
                    <Link :href="route('report.overall-report')" class="nav-link">
                        {{ lang.overall_report }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-profit_loss_report')">
                    <Link :href="route('report.profit-loss')" class="nav-link">
                        {{ lang.profit_loss }}
                    </Link>
                </li>

                <li v-if="permissions.includes('report-stock_report')">
                    <Link :href="route('report.stock-report')" class="nav-link">
                        {{ lang.stock }} {{ lang.report }}
                    </Link>
                </li>
            </aside-link>
        </ul>
    </aside>
</template>
<script>
import AsideLink from "@/Components/AsideLink";
import Link from '@/Components/Link'
export default {
    props: [
        "showAside",
    ],
    components: {
        Link,
        AsideLink,
    },
    data() {
        return {
            lang: this.$page.props.lang,
            permissions: this.$page.props.permissionNames
        }
    },
};
</script>
