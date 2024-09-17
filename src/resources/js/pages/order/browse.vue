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
      
  <!-- Updated status filter buttons -->
  <div class="mb-4">
              <vs-button
                @click="filterOrders('all')"
                :color="activeFilter === 'all' ? 'primary' : 'rgba(var(--vs-primary), 0.5)'"
                :style="{ opacity: activeFilter === 'all' ? 1 : 0.7 }"
                class="mr-2"
              >
                Sve narudžbe
              </vs-button>
              <vs-button
        @click="filterOrders(['waitingSellerConfirmation', 'waitingBuyerPayment'])"
        :color="activeFilter.includes('waitingSellerConfirmation') || activeFilter.includes('waitingBuyerPayment') ? 'warning' : 'rgba(var(--vs-warning), 0.5)'"
        :style="{ opacity: activeFilter.includes('waitingSellerConfirmation') || activeFilter.includes('waitingBuyerPayment') ? 1 : 0.7 }"
        class="mr-2"
      >
        Čeka Potvrdu
              </vs-button>
              <vs-button
                @click="filterOrders('cancel')"
                :color="activeFilter === 'cancel' ? 'danger' : 'rgba(var(--vs-danger), 0.5)'"
                :style="{ opacity: activeFilter === 'cancel' ? 1 : 0.7 }"
                class="mr-2"
              >
                Otkazane
              </vs-button>
              <vs-button
                @click="filterOrders('done')"
                :color="activeFilter === 'done' ? 'success' : 'rgba(var(--vs-success), 0.5)'"
                :style="{ opacity: activeFilter === 'done' ? 1 : 0.7 }"
                class="mr-2"
              >
                Završene
              </vs-button>
            </div>


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
              <template slot="thead" style="text-align: center;">
                <vs-th> 
                  <!-- {{ $t("orders.browse.header.action") }} -->
                   </vs-th>
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
                
              </template>

              <template slot="tbody">
                <vs-tr
                  :data="order"
                  :key="index"
                  v-for="(order, index) in orders.data"
                  style="text-align: center;"
                >
                <vs-td style="width: 1%; white-space: nowrap">

<vs-button
    size="large"

icon="visibility"
    :to="{
        name: 'OrderConfirm',
        params: { id: order.id },
      }"
        v-if="$helper.isAllowed('confirm_orders')"
  >Detalji</vs-button>
</vs-td>
                  <vs-td :data="order.id">
                    {{ order.id }}
                  </vs-td>
                  <vs-td :data="order.user.name">
                    {{ order.user.name }}   {{ order.user.username }}
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
                  <vs-td :data="order.status" style="display: flex; justify-content: center; align-items: center;">
  <vs-chip :style="getStatusStyle(order.status)">{{ getOrderStatus(order.status) }}</vs-chip>
</vs-td>
                  <vs-td :data="order.createdAt">
                    {{ getDate(order.createdAt) }}
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

      activeFilter: 'all',
    };
  },
  mounted() {
    const filterParam = this.$route.query.filter;
    if (filterParam) {
      this.activeFilter = filterParam.split(',');
      this.getOrderList();
    } else {
      this.getOrderList();
    }
   
  },
  methods: {
  getStatusStyle(status) {
    switch (status) {
      case 'waitingSellerConfirmation':
        return 'background-color: #ffb74d; font-weight: bold;'; // Light Orange, bold
      case 'canceled':
        return 'background-color: #ef5350; font-weight: bold;'; // Light Red, bold
      case 'done':
        return 'background-color: #66bb6a; font-weight: bold;'; // Light Green, bold
      default:
        return '';
    }
  },



  toCurrency(value) {
  // Parse the input value as a float
  const floatValue = parseFloat(value);

  // Check if the parsed value is a valid number
  if (isNaN(floatValue)) {
    return ''; // Return empty string or some default value for invalid input
  }

  // Format the value using the currency.js library
  return currency(floatValue, {
    precision: 2, // Set to 2 decimal places
    decimal: ',', // Use comma as decimal separator
    separator: '.', // Use dot as thousands separator
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
        case "cancel":
        return this.$t("orders.status.-1");
        default:
          return this.$t("orders.status.-1");
      }
    },

    filterOrders(status) {
      this.activeFilter = status;
      this.page = 1;
      this.getOrderList();
    },
    getOrderList() {
      this.$openLoader();
      const params = {
        page: this.page,
        limit: this.limit,
        relation: ["user"],
        filterValue: this.filter,
        orderField: this.$caseConvert.snake(this.orderField),
        orderDirection: this.$caseConvert.snake(this.orderDirection),
        search: this.search,
      };

      // Handle multiple statuses for "Čeka Potvrdu"
      if (Array.isArray(this.activeFilter)) {
        params.status = this.activeFilter.join(',');
      } else if (this.activeFilter !== 'all') {
        params.status = this.activeFilter;
      }

      this.$api.skijasiOrder
        .browse(params)
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
