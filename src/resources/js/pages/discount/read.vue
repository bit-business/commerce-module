<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action">
        <vs-button
          color="warning"
          type="relief"
          :to="{ name: 'DiscountEdit', params: { id: $route.params.id } }"
          v-if="$helper.isAllowed('edit_discounts')"
          ><vs-icon icon="edit"></vs-icon> {{ $t("action.edit") }}</vs-button
        >
      </template>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('read_discounts')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("discounts.detail.title") }}</h3>
          </div>
          <table class="skijasi-table">
            <tr>
              <th>{{ $t("discounts.detail.header.name") }}</th>
              <td>{{ discount.name }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.desc") }}</th>
              <td>{{ discount.desc }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.discountType") }}</th>
              <td style="text-transform: capitalize;">{{ discount.discountType }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.discountPercent") }}</th>
              <td>{{ discount.discountPercent + '%' }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.discountFixed") }}</th>
              <td>{{ toCurrency(discount.discountFixed) }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.createdAt") }}</th>
              <td>{{ getDate(discount.createdAt) }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.updatedAt") }}</th>
              <td>{{ getDate(discount.updatedAt) }}</td>
            </tr>
            <tr>
              <th>{{ $t("discounts.detail.header.deletedAt") }}</th>
              <td>{{ discount.deletedAt ? getDate(discount.deletedAt) : 'None' }}</td>
            </tr>
          </table>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import moment from 'moment'
import currency from 'currency.js';
export default {
  name: "DiscountRead",
  components: {},
  data: () => ({
    discount: {
      name: "",
      desc: "",
      discountType: "",
      discountPercent: "",
      discountFixed: "",
      createdAt: "",
      updatedAt: ""
    }
  }),
  mounted() {
    this.getDiscountDetail();
  },
  filters: {
    toCurrency: function (value) {
      var formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
      });
      return formatter.format(value);
    }
  },
  methods: {
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
      return moment(date).format('dddd, DD MMMM YYYY')
    },
    getDiscountDetail() {
      this.$openLoader();
      this.$api.skijasiDiscount
      .read({ id: this.$route.params.id, })
      .then((response) => {
        this.$closeLoader();
        this.discount = response.data.discount;
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
  },
};
</script>
