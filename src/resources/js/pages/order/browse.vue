<template>
  <div>
    <skijasi-breadcrumb-row />
    <vs-row v-if="$helper.isAllowed('browse_orders')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("orders.browse.title") }}</h3>
          </div>
          <div>
            <skijasi-server-side-table
              v-model="selected"
              :data="orders.data"
              stripe
              :pagination-data="orders"
              :description-items="descriptionItems"
              :description-title="$t('orders.browse.footer.descriptionTitle')"
              :description-connector="
                $t('orders.browse.footer.descriptionConnector')
              "
              :description-body="$t('orders.browse.footer.descriptionBody')"
              @search="handleSearch"
              @changePage="handleChangePage"
              @changeLimit="handleChangeLimit"
              @select="handleSelect"
              @sort="handleSort"
            >
              <template slot="thead">
                <skijasi-th sort-key="id">
                  {{ $t("orders.browse.header.orderId") }}
                </skijasi-th>
                <skijasi-th sort-key="user">
                  {{ $t("orders.browse.header.user") }}
                </skijasi-th>
                <skijasi-th sort-key="total">
                  {{ $t("orders.browse.header.total") }}
                </skijasi-th>
                <skijasi-th sort-key="discounted">
                  {{ $t("orders.browse.header.discounted") }}
                </skijasi-th>
                <skijasi-th sort-key="payed">
                  {{ $t("orders.browse.header.payed") }}
                </skijasi-th>
                <skijasi-th sort-key="status">
                  {{ $t("orders.browse.header.status") }}
                </skijasi-th>
                <skijasi-th sort-key="orderedAt">
                  {{ $t("orders.browse.header.orderedAt") }}
                </skijasi-th>
                <vs-th> {{ $t("orders.browse.header.action") }} </vs-th>
              </template>

              <template slot="tbody">
                <vs-tr
                  :data="order"
                  :key="index"
                  v-for="(order, index) in orders.data"
                >
                  <vs-td :data="order.id">
                    {{ order.id }}
                  </vs-td>
                  <vs-td :data="order.user.name">
                    {{ order.user.name }}
                  </vs-td>
                  <vs-td :data="order.total">
                    {{ toCurrency(order.total) }}
                  </vs-td>
                  <vs-td :data="order.discounted">
                    {{ toCurrency(order.discounted) }}
                  </vs-td>
                  <vs-td :data="order.payed">
                    {{ toCurrency(order.payed) }}
                  </vs-td>
                  <vs-td :data="order.status">
                    {{ getOrderStatus(order.status) }}
                  </vs-td>
                  <vs-td :data="order.createdAt">
                    {{ getDate(order.createdAt) }}
                  </vs-td>
                  <vs-td style="width: 1%; white-space: nowrap">
                    <skijasi-dropdown vs-trigger-click>
                      <vs-button
                        size="large"
                        type="flat"
                        icon="more_vert"
                      ></vs-button>
                      <vs-dropdown-menu>
                        <skijasi-dropdown-item
                          icon="check"
                          :to="{
                            name: 'OrderConfirm',
                            params: { id: order.id },
                          }"
                          v-if="$helper.isAllowed('confirm_orders')"
                        >
                          Confirm
                        </skijasi-dropdown-item>
                      </vs-dropdown-menu>
                    </skijasi-dropdown>
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-server-side-table>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import moment from "moment";
import currency from "currency.js";
export default {
  name: "OrderBrowse",
  components: {},
  data() {
    return {
      selected: [],
      orders: {
        data: [],
      },
      descriptionItems: [10, 50, 100],
      totalItem: 0,
      filter: "",
      page: 1,
      limit: 10,
      orderField: "updatedAt",
      orderDirection: "desc",
      search: "",
      rowPerPage: null,
      willDeleteId: null,
    };
  },
  mounted() {
    this.getOrderList();
  },
  methods: {
    toCurrency(value) {
      return currency(value, {
        precision: this.$store.state.skijasi.config.currencyPrecision,
        decimal: this.$store.state.skijasi.config.currencyDecimal,
        separator: this.$store.state.skijasi.config.currencySeparator,
        symbol: this.$store.state.skijasi.config.currencySymbol,
      }).format();
    },
    getDate(date) {
      return moment(date).format("DD MMMM YYYY HH:mm:ss");
    },
    getOrderStatus(status) {
      switch (status) {
        case "waitingBuyerPayment":
          return this.$t("orders.status.0");
        case "waitingSellerConfirmation":
          return this.$t("orders.status.1");
        case "process":
          return this.$t("orders.status.2");
        case "delivering":
          return this.$t("orders.status.3");
        case "done":
          return this.$t("orders.status.4");
        default:
          return this.$t("orders.status.-1");
      }
    },
    getOrderList() {
      this.$openLoader();
      this.$api.skijasiOrder
        .browse({
          page: this.page,
          limit: this.limit,
          relation: ["user"],
          filterValue: this.filter,
          orderField: this.$caseConvert.snake(this.orderField),
          orderDirection: this.$caseConvert.snake(this.orderDirection),
          search: this.search,
        })
        .then((response) => {
          this.$closeLoader();
          this.selected = [];
          this.orders = response.data.orders;
        })
        .catch((error) => {
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    handleSearch(e) {
      this.search = e.target.value;
      this.filter = e.target.value;
      this.page = 1;
      this.getOrderList();
    },
    handleChangePage(page) {
      this.page = page;
      this.getOrderList();
    },
    handleChangeLimit(limit) {
      this.page = 1;
      this.limit = limit;
      this.getOrderList();
    },
    handleSort(field, direction) {
      this.orderField = field;
      this.orderDirection = direction;
      this.getOrderList();
    },
    handleSelect(data) {
      this.selected = data;
    },
  },
};
</script>
