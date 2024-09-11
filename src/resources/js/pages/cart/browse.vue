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
  color="warning"
  icon="edit"
  @click="editCart(cart)"
  v-if="$helper.isAllowed('edit_carts')"
></vs-button>
</vs-td><vs-td class="skijasi-table__td">             <vs-button
    size="large"
    color="danger"
    icon="delete"
    @click="confirmDelete(cart.id)"
    v-if="$helper.isAllowed('delete_carts')"
  ></vs-button>
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-server-side-table>
          </div>
        </vs-card>
      </vs-col>
      
    </vs-row>




<vs-popup :title="'Promjena zaduženja (Promijeniti i u kartici člana pod plaćanje kada se rade ručne izmjene ovdje)'" :active.sync="editModal">
      <vs-row>
        <vs-col vs-w="12" vs-type="flex">
          <skijasi-number
            type="number"
            v-model="editForm.quantity"
            size="12"
            :label="'Količina'"
            :placeholder="'Promijeni količinu'"
            style="margin-bottom: 8px !important;"
          ></skijasi-number>
        </vs-col>

        <vs-col vs-w="12" vs-type="flex">
          <vs-select
  v-model="editForm.product_detail_id"
  :label="'Zaduženje'"
  style="margin-bottom: 8px !important;"
>
  <vs-select-item
    :key="editForm.product_detail_id"
    :value="editForm.product_detail_id"
    :text="editForm.productName"
  />
  <vs-select-item
    v-for="detail in productDetails"
    :key="detail.id"
    :value="detail.id"
    :text="`${detail.product.name} - ${detail.name}`"
  />
</vs-select>
    </vs-col>

        <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
          <vs-button type="relief" color="primary" class="ml-2" @click="updateCart" :disabled="!isFormValid">
            {{ $t('orders.confirm.button.save') }}
          </vs-button>
        </vs-col>
      </vs-row>
    </vs-popup>

  </div>
  
</template>

<script>
import moment from 'moment'
export default {
  name: "CartBrowse",
  components: {},
  data() {
    return {
      editModal: false,
    editForm: {
      id: null,
      quantity: '0',
      product_detail_id: 0,
    },

    productDetails: [],

    search: "",


      selected: [],
      carts: {
      data: [],
    },
      descriptionItems: [10, 50, 100],
      totalItem: 0,
      filter: "",
      page: 1,
      limit: 10,
      orderField: "id",  // Change this from "updated_at" to "id"
      orderDirection: "desc",
    }
  },
        computed: {
        isFormValid() {
          return this.editForm && 
                this.editForm.quantity && 
                this.editForm.quantity > 0 &&
                this.editForm.product_detail_id;
        },
        filteredProductDetails() {
    return this.productDetails.filter(detail => detail.id !== this.editForm.product_detail_id);
  },

  tableData() {
    return this.carts.data || [];
  },


      },
      watch: {
  editModal(newVal) {
    if (newVal) {
      this.getProductDetails();
    }
  }
},
  mounted() {
    this.getCartList();
  },
  methods: {
    getProductDetails() {
  this.$openLoader();
  this.$api.skijasiProductDetail.browse({ relation: 'product' })
    .then((response) => {
      this.$closeLoader();
      this.productDetails = response.data.productDetails;
      console.log('Product details:', this.productDetails);
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


editCart(cart) {
  this.editForm = {
    id: cart.id,
    quantity: cart.quantity || 0,
    product_detail_id: cart.productDetail.id,
    productName: `${cart.productDetail.product.name} - ${cart.productDetail.name}`,
  };
  this.getProductDetails(); // Fetch all product details
  this.editModal = true;
},
              updateCart() {
            if (!this.isFormValid) {
              this.$vs.notify({
                title: this.$t('alert.warning'),
                text: 'Molimo ispunite sva polja',
                color: 'warning'
              });
              return;
            }

            this.$openLoader();
            this.$api.skijasiCart.edit(this.editForm)
              .then((response) => {
                this.$closeLoader();
                this.editModal = false;
                this.$vs.notify({
                  title: 'Uspješno spremljeno',
                  text: '',
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

    getDate(date) {
      return moment(date).format('DD MMMM YYYY')
    },
    getCartList() {
  this.$openLoader();
  this.$api.skijasiCart
    .browse({
      limit: this.limit,
      page: this.page,
      relation: 'productDetail.product,user',
      search: this.search,
      order_field: this.$caseConvert.snake(this.orderField),
      order_direction: this.$caseConvert.snake(this.orderDirection),
    })
    .then((response) => {
      this.$closeLoader();
      this.selected = [];
      if (response.data && response.data.carts) {
        this.carts = response.data.carts;
      } else {
        console.error('Unexpected response structure:', response);
        this.carts = { data: [] };
      }
    })
    .catch((error) => {
      this.$closeLoader();
      console.error('Error fetching cart data:', error);
      this.$vs.notify({
        title: this.$t("alert.danger"),
        text: error.message,
        color: "danger",
      });
    });
},
handleSearch(e) {
  this.search = e.target.value;
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
