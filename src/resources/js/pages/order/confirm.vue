<template>
  <div>
    <skijasi-breadcrumb-row>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('confirm_orders')">
      <vs-col :vs-lg="6" :vs-sm="12" :vs-xs="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("orders.confirm.title.customerInfo") }}</h3>
          </div>
          <table class="skijasi-table">
       
            <tr>
              <th>{{ $t("orders.confirm.header.recipientName") }}</th>
              <td>{{ order.user.name }} {{ order.user.username }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.addressLine1") }}</th>
              <td>{{ order.user.adresa }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.city") }}</th>
              <td>{{ order.user.grad }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.postalCode") }}</th>
              <td>{{ order.user.postanskibroj }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.country") }}</th>
              <td>{{ order.user.drzava }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.phoneNumber") }}</th>
              <td>{{ order.user.brojmobitela }}</td>
            </tr><tr>
              <th>{{ $t("orders.confirm.header.user.email") }}</th>
              <td>{{ order.user.email }}</td>
            </tr>
          </table>
        </vs-card>
        <vs-row>
          <vs-col 
    :vs-sm="12"
    :vs-md="6"
    :vs-lg="3"
    v-for="(orderDetail, index) in order.orderDetails" 
    :key="index"
  >
    <vs-card :class="{ 'deleted-item': orderDetail.deletedAt }">
      <!-- Add delete button at top right -->
      <div class="delete-button-wrapper">
  <vs-button
    color="danger"
    type="flat"
    icon="close"
    size="small"
    class="delete-button-alt"
    @click="confirmDeleteOrderDetail(orderDetail)"
  >
  </vs-button>
</div>

      <!-- Rest of your existing card content -->
      <div slot="media">
        <img v-if="orderDetail.productDetail && orderDetail.productDetail.productImage"
             :src="orderDetail.productDetail.productImage"
             class="w-8 h-8"
             alt="Product Image">
        <span v-else>Nema slike</span>
      </div>

        <div>
          <h2>{{ orderDetail.productDetail.product.name }}</h2>
          <h1>{{ toCurrency(orderDetail.productDetail.price) }}</h1>
          <p>Status člana: {{ orderDetail.productDetail.name }}</p>
          <p>Iznos popusta: {{ toCurrency(orderDetail.discounted) }}</p>
          <p>Količina: {{ orderDetail.quantity }} komada</p>
          <vs-button 
              type="border" 
              color="primary" 
              size="small" 
              @click="copyToShipping(orderDetail)"
              :loading="isLoading && loadingDetailId === orderDetail.id"
            >
              <vs-icon icon="content_copy"></vs-icon>
              Kopiraj u otpremnicu
            </vs-button>
          <p v-if="orderDetail.deletedAt" class="deleted-notice">Ovo je obrisano i nije u narudžbi više, ali je i dalje zabilježeno te služi za kontrolu ukoliko je greška.</p>
        </div>
      </vs-card>
    </vs-col>


  </vs-row>
      </vs-col>
      <vs-col :vs-lg="6" :vs-sm="12" :vs-xs="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("orders.confirm.title.orderInfo") }}</h3>
          </div>
          <table class="skijasi-table">
            <tr>
              <th>{{ $t("orders.confirm.header.status") }}</th>
              <td>
                <vs-chip :color="getOrderStatusColor(order.status)">
                  <b>{{ getOrderStatus(order.status) }}</b>
                </vs-chip>
              </td>
            </tr>
            <tr>
              <th>Broj narudžbe</th>
              <td>{{ order.id }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.user.email") }}</th>
              <td>{{ order.user.email }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.total") }}</th>
              <td>{{ toCurrency(order.total) }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.discounted") }}</th>
              <td>{{ toCurrency(order.discounted) }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.shippingCost") }}</th>
              <td>{{ toCurrency(order.shippingCost) }}</td>
            </tr>
<!--           
            <tr>
              <th>{{ $t("orders.confirm.header.trackingNumber") }}</th>
              <td>
                <span v-if="order.trackingNumber">{{ order.trackingNumber }}</span>
                <span v-else></span>
              </td>
            </tr> -->
         
            <tr>
              <th>{{ $t("orders.confirm.header.expiredAt") }}</th>
              <td>
                <span v-if="order.expiredAt">{{ getDate(order.expiredAt) }}</span>
                <span v-else></span>
              </td>
            </tr>

            <tr>
              <th>{{ "Poruka od kupca" }}</th>
              <td style="font-weight: bolder; color:darkorange">{{ order.message }}</td>
            </tr>

            <tr>
              <th>{{ $t("orders.confirm.header.payed") }}</th>
              <td style="font-weight: bolder; font-size:medium;">{{ toCurrency(order.payed) }}</td>
            </tr>
            <tr v-if="order.status != 'cancel' && order.status != 'done'">
              <!-- <th>{{ $t("orders.confirm.header.action") }}</th> -->
              <td>
                <div class="actions-container">
                  <div class="main-actions">
                <vs-button type="relief" color="success" @click="done" v-if="order.status == 'waitingBuyerPayment' || order.status == 'waitingSellerConfirmation'">
                  <!-- <vs-icon icon="check"></vs-icon> -->
                  <vs-icon icon="done_all"></vs-icon>
                  Potvrdi da je plaćeno
                </vs-button>
                <vs-button type="relief" color="warning" @click="openCancelDialog" v-if="order.status == 'waitingBuyerPayment' || order.status == 'waitingSellerConfirmation'">
                  <vs-icon icon="clear"></vs-icon>
                  Odbij narudžbu
                </vs-button>
                <vs-button type="relief" color="primary" v-if="order.status == 'process'" @click="openTrackingNumber">
                  <vs-icon icon="local_shipping"></vs-icon>
                 Dodaj Broj (šifru)
                </vs-button>
                <vs-button type="relief" color="dark" v-if="order.status == 'delivering'" @click="done">
                  <vs-icon icon="done_all"></vs-icon>
                Završena narudžba 
                </vs-button>
              </div>
                
                <vs-button 
        v-if="canDeleteOrders()"
        type="relief" 
        color="danger" 
        @click="confirmDeleteOrder" 
        class="delete-order-btn"
      >
        <vs-icon icon="delete"></vs-icon>
        Obriši narudžbu
      </vs-button>
    </div>
              </td>
            </tr>
            <tr v-if="order.status == 'cancel'">
              <th>{{ $t("orders.confirm.header.cancelMessage") }}</th>
              <td>{{ order.cancelMessage }}</td>
            </tr>
          </table>
        </vs-card>
        <vs-card v-if="order.orderPayment">
          <div slot="header">
            <h3>{{ $t("orders.confirm.title.orderPayment") }}</h3>
          </div>
          <table class="skijasi-table">
            <!-- <tr>
              <th>{{ $t("orders.confirm.header.orderPayment.sourceBank") }}</th>
              <td>{{ getSourceBank(order.orderPayment.sourceBank) }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.orderPayment.destinationBank") }}</th>
              <td>{{ order.orderPayment.destinationBank }}</td>
            </tr>
            <tr>
              <th>{{ $t("orders.confirm.header.orderPayment.accountNumber") }}</th>
              <td>{{ order.orderPayment.accountNumber }}</td>
            </tr> -->
            <tr>
              <th>{{ $t("orders.confirm.header.orderPayment.totalTransfer") }}</th>
              <td>{{ toCurrency(order.orderPayment.totalTransfered) }}</td>
            </tr>



            <tr>
  <th>{{ $t("orders.confirm.header.orderPayment.proofOfTransaction") }}</th>
  <td>
    <div v-if="order.orderPayment.proofOfTransaction">
      <div v-if="isImageFile(order.orderPayment.proofOfTransaction)">
        <img class="w-100" :src="getFileUrl(order.orderPayment.proofOfTransaction)" alt="Proof of Transaction">
      </div>
      <div v-else-if="isPdfFile(order.orderPayment.proofOfTransaction)">
        <iframe :src="getFileUrl(order.orderPayment.proofOfTransaction)" width="100%" height="500px"   @error="handleIframeError"></iframe>
      </div>
      <div v-else>
        <a :href="getFileUrl(order.orderPayment.proofOfTransaction)" target="_blank">Pogledaj Potvrdu</a>
      </div>
    </div>
    <span v-else>Nema</span>
  </td>
</tr>




          </table>
        </vs-card>
      </vs-col>
    </vs-row>
    <vs-popup :title="$t('orders.confirm.title.trackingNumber')" :active.sync="trackingNumberDialog">
      <vs-row>
        <vs-col vs-w="12" vs-type="flex">
          <skijasi-text
            type="text"
            v-model="trackingNumber"
            size="12"
            :label="$t('orders.confirm.field.trackingNumber.label')"
            :placeholder="$t('orders.confirm.field.trackingNumber.placeholder')"
            style="margin-bottom: 8px !important;"
          ></skijasi-text>
        </vs-col>
        <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
          <vs-button type="relief" color="primary" class="ml-2" @click="setTrackingNumber">
            {{ $t('orders.confirm.button.save') }}
          </vs-button>
        </vs-col>
      </vs-row>
    </vs-popup>
    <vs-popup :title="$t('orders.confirm.title.cancel')" :active.sync="cancelDialog">
      <vs-row>
        <vs-col vs-w="12" vs-type="flex">
          <skijasi-text
            type="text"
            v-model="cancelMessage"
            size="12"
            :label="$t('orders.confirm.field.cancel.label')"
            :placeholder="$t('orders.confirm.field.cancel.placeholder')"
            style="margin-bottom: 8px !important;"
          ></skijasi-text>
        </vs-col>
        <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
          <vs-button type="relief" color="danger" class="ml-2" @click="setCancelMessage">
            {{ $t('orders.confirm.button.save') }}
          </vs-button>
        </vs-col>
      </vs-row>
    </vs-popup>

      <!-- Add delete confirmation dialog -->
  <vs-popup
    :active.sync="deleteConfirmDialog"
    title="Potvrdi brisanje"
    color="danger"
  >
    <div class="confirmation-content">
      <p>Jeste li sigurni da želite izbrisati ovaj proizvod iz narudžbe?</p>
      <p v-if="selectedOrderDetail">
        Proizvod: {{ selectedOrderDetail.productDetail.product.name }}
        <br>
        Cijena: {{ toCurrency(selectedOrderDetail.price) }}
      </p>
    </div>
    <div class="confirmation-actions">
      <vs-button
        type="border"
        color="dark"
        @click="deleteConfirmDialog = false"
      >
        Odustani
      </vs-button>
      <vs-button
        type="filled"
        color="danger"
        @click="deleteOrderDetail"
        :loading="isDeleting"
      >
        Izbriši
      </vs-button>
    </div>
  </vs-popup>


     <!-- Add delete order confirmation dialog -->
     <vs-popup
      :active.sync="deleteOrderDialog"
      title="Potvrdi brisanje narudžbe"
      color="danger"
    >
      <div class="confirmation-content">
        <p>Jeste li sigurni da želite obrisati ovu narudžbu?</p>
        <p>
          Narudžba broj: {{ order.id }}<br>
          Kupac: {{ order.user.name }}<br>
          Ukupno: {{ toCurrency(order.total) }}
        </p>
      </div>
      <div class="confirmation-actions">
        <vs-button
          type="border"
          color="dark"
          @click="deleteOrderDialog = false"
        >
          Odustani
        </vs-button>
        <vs-button
          type="filled"
          color="danger"
          @click="deleteOrder"
          :loading="isDeletingOrder"
        >
          Izbriši narudžbu
        </vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import moment from "moment";
import currency from 'currency.js';
import { or } from "vuelidate/lib/validators";
export default {
  name: "OrderConfirm",
  components: {},
  data: () => ({
    isLoading: false,
    loadingDetailId: null,

    deleteOrderDialog: false,
    isDeletingOrder: false,
    allowedUserIds: [1, 2439, 4417],
    userId: null,

    deleteConfirmDialog: false,
  selectedOrderDetail: null,
  isDeleting: false,

    trackingNumber: null,
    trackingNumberDialog: false,
    cancelDialog: false,
    cancelMessage: null,
    order: {
      recipientName: "",
      addressLine1: "",
      addressLine2: "",
      city: "",
      postalCode: "",
      country: "",
      telephone: "",
      mobile: "",
      discounted: 0,
      total: 0,
      payed: 0,
      status: 0,
      expiredAt: "",
      image: "",
      trackingNumber: null,
      user: {
        email: "",
      },
      orderDetails: [],
      orderPayment: {
        sourceBank: null,
        destinationBank: null,
        accountNumber: null,
        totalTransfered: null,
        proofOfTransaction: null,
      }
    },
  }),
  mounted() {
    this.getOrderDetail();
    this.getUser();
  },
  methods: {
    getUser() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .user({})
        .then((response) => {
          this.$closeLoader();
          // Store user ID instead of role
          this.userId = response.data.user.id;
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    canDeleteOrders() {
      return this.allowedUserIds.includes(Number(this.userId));
    },

    confirmDeleteOrder() {
      this.deleteOrderDialog = true;
    },
    
    async deleteOrder() {
      this.isDeletingOrder = true;
      try {
        const response = await this.$api.skijasiOrder.deleteOrder({
          id: this.order.id
        });

        if (response.data.success) {
          this.$vs.notify({
            title: "Uspješno",
            text: "Narudžba je uspješno obrisana",
            color: "success"
          });
          
          // Redirect to orders list
          this.$router.push({ name: "OrderBrowse" });
        }
      } catch (error) {
        this.$vs.notify({
          title: "Greška",
          text: error.message || "Došlo je do greške prilikom brisanja narudžbe",
          color: "danger"
        });
      } finally {
        this.isDeletingOrder = false;
        this.deleteOrderDialog = false;
      }
    },

    copyToShipping(orderDetail) {
    this.isLoading = true;
    this.loadingDetailId = orderDetail.id;


    this.$api.skijasiOrder
      .copyToShipping({
        orderDetailId: orderDetail.id,
        orderId: this.order.id,
        productDetailId: orderDetail.productDetailId,  
        quantity: orderDetail.quantity,
        price: orderDetail.price,
        discounted: orderDetail.discounted,
        name: this.order.user.name,           
        username: this.order.user.username  
      })
      .then((response) => {
        this.$vs.notify({
          title: "Uspješno",
          text: "Dodano u tablicu za obradu!",
          color: "success"
        });
      })
      .catch((error) => {
        this.$vs.notify({
          title: this.$t("alert.danger"),
          text: error.message,
          color: "danger"
        });
      })
      .finally(() => {
        this.isLoading = false;
        this.loadingDetailId = null;
      });
  },


    handleIframeError() {
    console.error('Error loading iframe, falling back to link');
  },
    done() {
      this.$openLoader();
      this.$api.skijasiOrder
        .done({ id: this.$route.params.id })
        .then((response) => {
          this.$closeLoader();
          this.getOrderDetail()
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

    isImageFile(filename) {
      return /\.(jpg|jpeg|png|gif|heic)$/i.test(filename);
    },

    isPdfFile(filename) {
      return /\.pdf$/i.test(filename);
    },

    getFileUrl(proofOfTransaction) {
  // Check if the proofOfTransaction already has a full URL
  if (proofOfTransaction.startsWith('http')) {
    return proofOfTransaction; // Return the full URL as is
  }
  
  // If it's a relative path, append it to /storage/
  return `/storage/${proofOfTransaction}`;
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
      return moment(date).format("dddd, DD MMMM YYYY HH:mm:ss");
    },
    getOrderDetail() {
      this.$openLoader();
      this.$api.skijasiOrder
        .read({ id: this.$route.params.id })
        .then((response) => {
          this.$closeLoader();
          this.order = response.data.order;

          console.log("TEST ORDER",(this.order));
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
    getOrderStatus(status) {
      switch (status) {
        case 'waitingBuyerPayment':
          return this.$t("orders.status.0")
        case 'waitingSellerConfirmation':
          return this.$t("orders.status.1")
        case 'process':
          return this.$t("orders.status.2")
        case 'delivering':
          return this.$t("orders.status.3")
        case 'done':
          return this.$t("orders.status.4")
        default:
          return this.$t("orders.status.-1")
      }
    },
    getOrderStatusColor(status) {
      switch (status) {
        case 'waitingBuyerPayment':
          return "warning";
        case 'waitingSellerConfirmation':
          return "warning";
        case 'process':
          return "primary";
        case 'delivering':
          return "dark";
        case 'done':
          return "success";
        default:
          return "danger";
      }
    },
    setCancelMessage() {
      this.$openLoader();
      this.$api.skijasiOrder
        .reject({ id: this.$route.params.id, cancelMessage: this.cancelMessage })
        .then((response) => {
          this.$closeLoader();
          this.cancelDialog = false
          this.getOrderDetail()
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
    confirm() {
      this.$openLoader();
      this.$api.skijasiOrder
        .confirm({ id: this.$route.params.id })
        .then((response) => {
          this.$closeLoader();
          this.getOrderDetail()
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
 

    
    openTrackingNumber() {
      this.trackingNumberDialog = true
    },
    setTrackingNumber() {
      this.$openLoader();
      this.trackingNumberDialog = false
      this.$api.skijasiOrder
        .ship({
          id: this.$route.params.id,
          trackingNumber: this.trackingNumber
        })
        .then((response) => {
          this.$closeLoader();
          this.getOrderDetail()
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
    getSourceBank(bank) {
      if (!bank) return ''
      return JSON.parse(this.$store.state.skijasi.config.availableBanks)[bank]
    },
    openCancelDialog() {
      this.cancelDialog = true
      this.cancelMessage = null
    },


    confirmDeleteOrderDetail(orderDetail) {
    this.selectedOrderDetail = orderDetail;
    this.deleteConfirmDialog = true;
  },
  async deleteOrderDetail() {
    if (!this.selectedOrderDetail) return;

    this.isDeleting = true;
    try {
        const response = await this.$api.skijasiOrder.deleteOrderDetail({
            orderId: this.order.id,
            orderDetailId: this.selectedOrderDetail.id
        });

        if (response.data.success) {
            // Update the order with the response data
            this.order = response.data.order;
            
            this.$vs.notify({
                title: 'Uspješno',
                text: response.data.message || 'Proizvod je izbrisan iz narudžbe',
                color: 'success'
            });
        }
    } catch (error) {
        this.$vs.notify({
            title: 'Greška',
            text: error.message || 'Došlo je do greške prilikom brisanja',
            color: 'danger'
        });
    } finally {
        this.isDeleting = false;
        this.deleteConfirmDialog = false;
        this.selectedOrderDetail = null;
        
        // Refresh the order details after everything is done
        await this.getOrderDetail();
    }
},

  },
};
</script>
<style scoped>
.w-100 {
  width: 100%;
  max-width: 500px;
}

iframe {
  border: none;
  width: 100%;
  height: 500px;
}

.deleted-item {
  opacity: 0.5;
}

.deleted-notice {
  color: red;
  font-style: italic;
}


.vs-button--small {
  margin-top: 8px;
  margin-bottom: 8px;
}


/* Responsive table styles */
@media screen and (max-width: 768px) {
  .skijasi-table {
    display: block;
    width: 100%;
  }
  
  .skijasi-table tr {
    display: block;
    margin-bottom: 1rem;
    border-bottom: 1px solid #eee;
  }
  
  .skijasi-table th,
  .skijasi-table td {
    display: block;
    width: 100%;
    text-align: left;
    padding: 0.5rem;
  }
  
  .skijasi-table th {
    font-weight: bold;
    background-color: #f8f8f8;
  }

  /* Card adjustments */
  .vs-card {
    margin-bottom: 1rem;
  }

  /* Image responsiveness */
  .w-100 {
    max-width: 100%;
  }

  /* Button adjustments */
  .vs-button {
    width: 100%;
    margin: 0.5rem 0;
  }

  /* Iframe adjustment */
  iframe {
    max-height: 300px;
  }
}

/* Additional responsive adjustments */
@media screen and (max-width: 480px) {
  .vs-chip {
    width: 100%;
    text-align: center;
  }

  h3 {
    font-size: 1.2rem;
  }
}

/* Fix for product cards */
.vs-row .vs-col {
  margin-bottom: 1rem;
}

/* Make vs-popup responsive */
@media screen and (max-width: 768px) {
  .vs-popup {
    width: 90% !important;
    margin: 5% !important;
  }
}

/* Ensure buttons stack nicely on mobile */
@media screen and (max-width: 768px) {
  td .vs-button {
    display: block;
    margin: 0.5rem 0;
    width: 100%;
  }
}

/* Add some breathing room between sections */
.vs-card {
  margin-bottom: 1rem;
}

/* Ensure table cells don't get too squished */
.skijasi-table td, 
.skijasi-table th {
  word-break: break-word;
  min-width: 100px;
}

/* Make sure images don't overflow */
img {
  max-width: 100%;
  height: auto;
}



.delete-button-container {
  position: absolute;
  top: 5px;
  right: 5px;
  z-index: 100;
}

.delete-btn {
  padding: 0 !important;
  width: 25px !important;
  height: 25px !important;
  min-width: unset !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.delete-btn .vs-icon {
  font-size: 16px !important;
}

/* Add position relative to the card to ensure proper positioning */
.vs-card {
  position: relative !important;
}

.delete-button-container {
  border: 2px solid red !important;
  background: yellow !important;
}

.delete-btn {
  border: 2px solid blue !important;
  background: pink !important;
}


.confirmation-content {
  padding: 1rem;
  margin-bottom: 1rem;
}

.confirmation-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1rem;
}

.actions-container {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.main-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.delete-order-btn {
  margin-left: auto;
}

/* Update your existing media query */
@media screen and (max-width: 768px) {
  .actions-container {
    flex-direction: column;
  }

  .main-actions {
    width: 100%;
  }

  .delete-order-btn {
    width: 100%;
    margin-top: 1rem;
  }

  .main-actions .vs-button {
    width: 100%;
    margin: 0.25rem 0;
  }
}

</style>