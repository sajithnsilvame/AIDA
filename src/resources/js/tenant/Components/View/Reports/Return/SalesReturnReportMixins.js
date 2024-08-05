import DatatableHelperMixin from "../../../../../common/Mixin/Global/DatatableHelperMixin";
import {
    SALES_RETURN_REPORT, RETURN_ORDER_MAX_MIN_PRICE, ORDER_MAX_MIN_PRICE, SALES_RETURN_SUMMARY
} from "../../../../Config/ApiUrl-CP";
import {axiosGet, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import SelectableStatusMixin from "../../../../Helper/SelectableOptions/SelectableStatusMixin";
import {mapGetters} from "vuex";
import {formatDateToLocal, numberWithCurrencySymbol} from "../../../../Helper/Helper";

export default {
    mixins: [DatatableHelperMixin, SelectableStatusMixin],
    data() {
        return {
            numberWithCurrencySymbol,
            options: {
                name: this.$t('sales_report'),
                url: null,
                showCount: true,
                showClearFilter: true,
                showHeader: true,
                columns: [
                    {
                        title: this.$t('invoice_id'),
                        type: 'text',
                        key: 'invoice_number',
                    },
                    {
                        title: this.$t('date'),
                        type: 'custom-html',
                        key: 'returned_at',
                        modifier: value => value ? `<span>${formatDateToLocal(value.split('-').reverse().join('-'))}</span>` : ''
                    },
                    {
                        title: this.$t('branch_or_warehouse'),
                        type: 'custom-html',
                        key: 'branch_or_warehouse',
                        modifier: value => value ? `<span>${value.name} <span class="text-${value.type.toLowerCase() === 'branch' ? 'warning' : 'info'}">(${value.type[0].toUpperCase()})</span></span>` : ''
                    },
                    {
                        title: this.$t('return_by'),
                        type: 'object',
                        key: 'created_by',
                        modifier: (created_by) => (created_by?.full_name)
                    },
                    {
                        title: this.$t('customer'),
                        type: 'object',
                        key: 'customer',
                        modifier: (customer) => (customer?.full_name)
                    },
                    {
                        title: this.$t('discount'),
                        type: 'custom-html',
                        key: 'discount',
                        titleAlignment: 'right',
                        modifier: value => `<p class="text-right mb-0">${numberWithCurrencySymbol(value)}</p>`
                    },

                    {
                        title: this.$t('sub_total'),
                        type: 'custom-html',
                        key: 'sub_total',
                        titleAlignment: 'right',
                        modifier: value => `<p class="text-right mb-0">${numberWithCurrencySymbol(value)}</p>`
                    }
                ],
                actionType: "dropdown",
                actions: [],
                filters: [
                    {
                        title: this.$t('issue_date'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    }, 
                    {
                        title: this.$t('customer'),
                        type: 'search-and-select-filter',
                        key: 'customers',
                        settings: {
                            url: urlGenerator('app/selectable-customers'),
                            modifire: ({id, full_name}) => ({id, full_name}),
                            params: {},
                            per_page: 10,
                            queryName: 'search',
                            loader: 'app-pre-loader'
                        },
                        listValueField: 'full_name'
                    },
                    {
                        title: this.$t('return_by'),
                        type: 'search-and-select-filter',
                        key: 'created_by',
                        settings: {
                            url: urlGenerator('app/selectable/users'),
                            modifire: ({id, full_name}) => ({id, full_name}),
                            params: {},
                            per_page: 10,
                            queryName: 'search',
                            loader: 'app-pre-loader'
                        },
                        listValueField: 'full_name'
                    },
                    {
                        title: "Price Range",
                        type: "range-filter",
                        key: "range_filter",
                        maxTitle: "Max usd",
                        minTitle: "Min usd",
                        sign: "$",
                        minRange: 0,
                        maxRange: 0,
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                search: true,
            },
            orderMaxMinPriceUrl : RETURN_ORDER_MAX_MIN_PRICE
        }
    },
    methods: {

        getMaxMinOrderPrice() {
            axiosGet(this.orderMaxMinPriceUrl).then((res) => {
                this.options?.filters.find(({key}) => {
                    if (key === 'range_filter') {
                        Object.assign(this.options.filters[3], res.data)
                    }
                })
            })
        },
        getInvoiceIdFilter() {
            this.options.filters.push({
                title: this.$t('invoice_id'),
                type: 'search-and-select-filter',
                key: 'invoice_number',
                settings: {
                    url: urlGenerator('app/selectable-invoice'),
                    modifire: ({id, invoice_number: value}) => ({id, value}),
                    params: {
                        branch_or_warehouse_id: this.getBranchOrWarehouseId,
                    },
                    per_page: 10,
                    queryName: 'search',
                    loader: 'app-pre-loader'
                }
            })
        },


        updateMinMaxPriceUrl() {
            this.orderMaxMinPriceUrl = `${RETURN_ORDER_MAX_MIN_PRICE}?branch_or_warehouse_id=${this.getBranchOrWarehouseId}`
        }, 
    },
    computed: {
        ...mapGetters(['getBranchOrWarehouseId']),
    },
    mounted() {
        this.updateUrl(SALES_RETURN_REPORT, this.updateMinMaxPriceUrl);
        this.getInvoiceIdFilter();
        this.getMaxMinOrderPrice();
    },
    watch: {
        getBranchOrWarehouseId() {
            this.updateUrl(SALES_RETURN_REPORT, this.updateMinMaxPriceUrl);
            this.$hub.$emit(`reload-${this.table_id}`)
        },
    },
    created() {
        this.updateMinMaxPriceUrl();
    }
}