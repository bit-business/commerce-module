<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action">
        <vs-button
          color="warning"
          type="relief"
          :to="{ name: 'ProductEdit', params: { id: $route.params.id } }"
          v-if="$helper.isAllowed('edit_products')"
          ><vs-icon icon="edit"></vs-icon> {{ $t("action.edit") }}</vs-button
        >
      </template>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('read_products')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("product.detail.title") }}</h3>
          </div>
          <table class="skijasi-table">
            <tr>
              <th>{{ $t("product.detail.header.name") }}</th>
              <td>{{ product.name }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.slug") }}</th>
              <td>{{ product.slug }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.desc") }}</th>
              <td>{{ product.desc }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.productImage") }}</th>
              <td><img width="100" v-if="product.productImage" :src="product.productImage" loading="lazy"></td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.productCategory") }}</th>
              <td>{{ product.productCategory.name }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.createdAt") }}</th>
              <td>{{ getDate(product.createdAt) }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.updatedAt") }}</th>
              <td>{{ getDate(product.updatedAt) }}</td>
            </tr>
            <tr>
              <th>{{ $t("product.detail.header.deletedAt") }}</th>
              <td>{{ product.deletedAt ? getDate(product.deletedAt) : 'Nema zapisa' }}</td>
            </tr>
          </table>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12" vs-type="grid" vs-justify="flex-start" vs-align="center" class="product-details">
        <vs-col vs-w="12" v-for="(productDetail, index) in product.productDetails" :key="index" class="product-details__item">
          <vs-card class="mb-0 product-details--card">
            <div slot="media">
              <img width="100" v-if="productDetail.productImage" :src="productDetail.productImage" loading="lazy">
            </div>
            <div>
              <h6 class="mb-0"><strong>{{ productDetail.name }}</strong></h6>
              <small>{{ productDetail.SKU }}</small>
              <h3 class="mb-2 mt-2">{{ toCurrency(productDetail.price) }}</h3>
              <small>{{ productDetail.quantity }} dostupno</small>
              <small v-if="productDetail.discountId" class="d-block">Popust: {{  `${productDetail.discount.name} - ${productDetail.discount.discountType === 'fixed' ? toCurrency(productDetail.discount.discountFixed) : productDetail.discount.discountPercent + '%' }` }}</small>
            </div>
          </vs-card>
        </vs-col>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import moment from 'moment'
import currency from 'currency.js'
export default {
  name: "ProductRead",
  components: {},
  data: () => ({
    product: {
      name: "",
      slug: "",
      desc: "",
      productImage: "",
      productCategory: "",
      createdAt: "",
      updatedAt: ""
    }
  }),
  mounted() {
    this.getProductDetail();
  },
  methods: {
    toCurrency(value) {
      return currency(value, {
        precision: this.$store.state.skijasi.config.currencyPrecision,
        decimal: this.$store.state.skijasi.config.currencyDecimal,
        separator: this.$store.state.skijasi.config.currencySeparator,
        symbol: this.$store.state.skijasi.config.currencySymbol,
      }).format()
    },
    getDate(date) {
      return moment(date).format('dddd, DD MMMM YYYY')
    },
    getProductDetail() {
      this.$openLoader();
      this.$api.skijasiProduct
      .read({ id: this.$route.params.id, relation: [ 'productCategory', 'productDetails.discount' ] })
      .then((response) => {
        this.$closeLoader();
        this.product = response.data.product;
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

<style lang="scss">
.product-details {
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;

  &--card {
    & > .vs-card--content {
      margin-bottom: 0;
    }
  }

  &__discount {
    &--text {
      text-decoration: line-through;
    }
  }

  &__item {
    display: flex;
    height: 100%;
  }
}
</style>