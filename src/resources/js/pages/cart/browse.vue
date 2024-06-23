<template>
  <div>
    <skijasi-breadcrumb-row>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('browse_carts')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("cart.browse.title") }}</h3>
          </div>
          <div>
            <skijasi-server-side-table
              v-model="selected"
              :data="carts.data"
              stripe
              :pagination-data="carts"
              :description-items="descriptionItems"
              :description-title="$t('cart.browse.footer.descriptionTitle')"
              :description-connector="$t('cart.browse.footer.descriptionConnector')"
              :description-body="$t('cart.browse.footer.descriptionBody')"
              @search="handleSearch"
              @changePage="handleChangePage"
              @changeLimit="handleChangeLimit"
              @select="handleSelect"
              @sort="handleSort"
            >
              <template slot="thead" style="text-align: center;">
                <vs-th> </vs-th>
                <skijasi-th sort-key="id"> {{ $t("cart.browse.header.id") }} </skijasi-th>
                <skijasi-th sort-key="name"> {{ $t("cart.browse.header.name") }} </skijasi-th>
                <skijasi-th sort-key="productName"> {{ $t("cart.browse.header.productName") }} </skijasi-th>
                <skijasi-th sort-key="quantity"> {{ $t("cart.browse.header.quantity") }} </skijasi-th>
                <skijasi-th sort-key="createdAt"> {{ $t("cart.browse.header.createdAt") }} </skijasi-th>
                <skijasi-th sort-key="updatedAt"> {{ $t("cart.browse.header.updatedAt") }} </skijasi-th>
           
              </template>

              <template slot="tbody">
                <vs-tr :data="cart" :key="index" v-for="(cart, index) in carts.data" style="text-align: center;">
                  <vs-td class="skijasi-table__td">
                    <vs-button
                        size="large"
                        icon="visibility"
                          :to="{
                            name: 'CartRead',
                            params: { id: cart.id },
                          }"
                          v-if="$helper.isAllowed('read_carts')"
                      >Detalji</vs-button>
                  </vs-td>
                  <vs-td :data="cart.id">
                    {{cart.id }}
                  </vs-td>
                  <vs-td :data="cart.user.name">
                    {{ `${cart.user.name} ${cart.user.username} --- ${cart.user.email}` }}
                  </vs-td>
                  <vs-td :data="cart.productDetail.product.name">
                    {{ `${cart.productDetail.product.name} - ${cart.productDetail.name}` }}
                  </vs-td>
                  <vs-td :data="cart.quantity">
                    {{ cart.quantity }}
                  </vs-td>
                  <vs-td :data="cart.createdAt">
                    {{ getDate(cart.createdAt) }}
                  </vs-td>
                  <vs-td :data="cart.updatedAt">
                    {{ getDate(cart.updatedAt) }}
                  </vs-td>
                  <vs-td class="skijasi-table__td">
                      <vs-button
    size="large"
    color="danger"
    icon="delete"
    @click="confirmDelete(cart.id)"
    v-if="$helper.isAllowed('delete_carts')"
  >Izbriši</vs-button>
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
  name: "CartBrowse",
  components: {},
  data() {
    return {
      selected: [],
      carts: {
        data: []
      },
      descriptionItems: [10, 50, 100],
      totalItem: 0,
      filter: "",
      page: 1,
      limit: 10,
      orderField: "updated_at",
      orderDirection: "desc",
    }
  },
  mounted() {
    this.getCartList()
  },
  methods: {
    getDate(date) {
      return moment(date).format('DD MMMM YYYY')
    },
    getCartList() {
      this.$openLoader();
      this.$api.skijasiCart
      .browse({ limit: this.limit, page: this.page, relation: ['productDetail.product', 'user'] })
      .then((response) => {
        this.$closeLoader();
        this.selected = [];
        this.carts = response.data.carts;
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
      this.getCartList();
    },
    handleChangePage(page) {
      this.page = page;
      this.getCartList();
    },
    handleChangeLimit(limit) {
      this.page = 1;
      this.limit = limit;
      this.getCartList();
    },
    handleSort(field, direction) {
      this.orderField = field;
      this.orderDirection = direction;
      this.getCartList();
    },
    handleSelect(data) {
      this.selected = data;
    },


    

    confirmDelete(cartId) {
    this.$vs.dialog({
      type: 'confirm',
      color: 'danger',
      title: 'Brisanje Zaduženja',
      text: 'Da li ste sigurni da želite obrisati? Ne bi trebalo brisati osim u slučaju ako je greškom dodano ili je član platio.',
      acceptText: 'Potvrdi brisanje',
      cancelText: 'Odustani',
      accept: () => this.deleteCart(cartId)
    })
  },

  deleteCart(cartId) {
    this.$openLoader();
    this.$api.skijasiCart.delete({ id: cartId })
      .then((response) => {
        this.$closeLoader();
        this.$vs.notify({
          title: this.$t('alert.success'),
          text: this.$t('cart.browse.deleteSuccess'),
          color: 'success'
        });
        this.getCartList();
      })
      .catch((error) => {
        this.$closeLoader();
        this.$vs.notify({
          title: this.$t('alert.danger'),
          text: error.message,
          color: 'danger'
        });
      });
  },



  },
};
</script>
