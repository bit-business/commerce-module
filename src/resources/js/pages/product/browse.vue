<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action">
        <vs-button
          color="primary"
          type="relief"
          :to="{ name: 'ProductAdd' }"
          v-if="$helper.isAllowed('add_products')"
          ><vs-icon icon="add"></vs-icon> {{ $t("action.add") }}</vs-button
        >
        <vs-button
          color="danger"
          type="relief"
          :to="{ name: 'ProductBrowseBin' }"
          v-if="$helper.isAllowed('browse_products_bin')"
          ><vs-icon icon="delete"></vs-icon> {{ $t("action.bin") }}</vs-button
        >
        <vs-button
          color="danger"
          type="relief"
          v-if="selected.length > 0 && $helper.isAllowed('delete_products')"
          @click.stop
          @click="confirmDeleteMultiple"
          ><vs-icon icon="delete_sweep"></vs-icon>
          {{ $t("action.bulkDelete") }}</vs-button
        >
      </template>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('browse_products')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("product.browse.title") }}</h3>
          </div>
          <div>
            <skijasi-server-side-table
              v-model="selected"
              :data="products.data"
              stripe
              :pagination-data="products"
              :description-items="descriptionItems"
              :description-title="$t('product.browse.footer.descriptionTitle')"
              :description-connector="$t('product.browse.footer.descriptionConnector')"
              :description-body="$t('product.browse.footer.descriptionBody')"
              @search="handleSearch"
              @changePage="handleChangePage"
              @changeLimit="handleChangeLimit"
              @select="handleSelect"
              @sort="handleSort"
            >
              <template slot="thead">
                <skijasi-th> {{ $t("product.browse.header.productImage") }} </skijasi-th>
                <skijasi-th sort-key="name"> {{ $t("product.browse.header.name") }} </skijasi-th>
                <skijasi-th sort-key="slug"> {{ $t("product.browse.header.slug") }} </skijasi-th>
                <skijasi-th sort-key="category"> {{ $t("product.browse.header.productCategoryId") }} </skijasi-th>
                <skijasi-th sort-key="createdAt"> {{ $t("product.browse.header.createdAt") }} </skijasi-th>
                <skijasi-th sort-key="updatedAt"> {{ $t("product.browse.header.updatedAt") }} </skijasi-th>
                <vs-th> Opcije </vs-th>
              </template>

              <template slot="tbody">
                <vs-tr v-for="product in sortedProducts" :key="product.id">
                <!-- <vs-tr :data="product" :key="index" v-for="(product, index) in products.data"> -->
                  <vs-td :data="product.productImage">
                    <img width="100" :src="product.productImage" loading="lazy">
                  </vs-td>
                  <vs-td :data="product.name">
                    {{ product.name }}
                  </vs-td>
                  <vs-td :data="product.slug">
                    {{ product.slug }}
                  </vs-td>
                  <vs-td :data="product.productCategory">
                    {{ product.productCategory ? product.productCategory.name : null }}
                  </vs-td>
                  <vs-td :data="product.createdAt">
                    {{ getDate(product.createdAt) }}
                  </vs-td>
                  <vs-td :data="product.updatedAt">
                    {{ getDate(product.updatedAt) }}
                  </vs-td>
                  <vs-td style="width: 1%; white-space: nowrap">
             
                      
                        <vs-button
                   class="button-spacing"
                          icon="visibility"
                          :to="{
                            name: 'ProductRead',
                            params: { id: product.id },
                          }"
                          v-if="$helper.isAllowed('read_products')"
                        >
                        </vs-button>
                        <vs-button
                           class="button-spacing"
                          icon="edit"
                          :to="{
                            name: 'ProductEdit',
                            params: { id: product.id },
                          }"
                          v-if="$helper.isAllowed('edit_products')"
                        >
                        </vs-button>
                        <vs-button
                        class="button-spacing"
                          icon="delete"
                          @click="confirmDelete(product.id)"
                          v-if="$helper.isAllowed('delete_products')"
                        >
                        
                      </vs-button>
                   
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
import moment from 'moment'
export default {
  name: "ProductBrowse",
  components: {},
  data() {
    return {
      selected: [],
      products: {
        data: []
      },
      descriptionItems: [50, 100, 200],
      totalItem: 0,
      filter: "",
      page: 1,
      limit: 50,
      orderField: "createdAt",
      orderDirection: "desc",
    }
  },
  mounted() {
    this.getProductList()
  },
  computed: {
  sortedProducts() {
    return [...this.products.data].sort((a, b) => {
      let modifier = this.orderDirection === 'desc' ? -1 : 1;
      if(a[this.orderField] < b[this.orderField]) return -1 * modifier;
      if(a[this.orderField] > b[this.orderField]) return 1 * modifier;
      return 0;
    });
  }
},
  methods: {
    getDate(date) {
      return moment(date).format('DD MMMM YYYY')
    },
    confirmDelete(id) {
      this.willDeleteId = id;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.deleteProducts,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {
          this.willDeleteId = null;
        },
      });
    },
    deleteProducts() {
      this.$openLoader();
      this.$api.skijasiProduct
      .delete({ id: this.willDeleteId })
      .then((response) => {
        this.$closeLoader();
        this.getProductList();
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
    confirmDeleteMultiple(id) {
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.bulkDeleteProduct,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {},
      });
    },
    bulkDeleteProduct() {
      const ids = this.selected.map((item) => item.id);
      this.$openLoader();
      this.$api.skijasiProduct
        .deleteMultiple({
          ids: ids.join(","),
        })
        .then((response) => {
          this.$closeLoader();
          this.getProductList();
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
    getProductList() {
      this.$openLoader();
      this.$api.skijasiProduct
      .browse({
      limit: this.limit,
      page: this.page,
      relation: ['productCategory'],
      sort: this.orderField,
      order: this.orderDirection
    })
      .then((response) => {
        this.$closeLoader();
        this.selected = [];
        this.products = response.data.products;
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
      this.filter = e.target.value;
      this.page = 1;
      this.getProductList();
    },
    handleChangePage(page) {
      this.page = page;
      this.getProductList();
    },
    handleChangeLimit(limit) {
      this.page = 1;
      this.limit = limit;
      this.getProductList();
    },
    handleSort(field, direction) {
  console.log("handleSort called with:", field, direction);
  this.orderField = field;
  this.orderDirection = direction;
  console.log("New sort:", this.orderField, this.orderDirection);
  this.getProductList();
},
    handleSelect(data) {
      this.selected = data;
    },
  },
};
</script>


<style scoped>
.button-spacing:not(:last-child) {
  margin-bottom: 5px; 
}
</style>