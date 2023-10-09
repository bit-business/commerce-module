<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_products')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("product.add.title") }}</h3>
          </div>
          <vs-row>
            <vs-col vs-w="6" class="mb-3">
              <skijasi-text
                v-model="product.name"
                size="12"
                :label="$t('product.add.field.name.title')"
                :placeholder="$t('product.add.field.name.placeholder')"
                :alert="errors.name"
                style="margin-bottom: 8px !important;"
              ></skijasi-text>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.name.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>
            <vs-col vs-w="6" class="mb-3">
              <skijasi-text
                v-model="product.slug"
                size="12"
                :label="$t('product.add.field.slug.title')"
                :placeholder="$t('product.add.field.slug.placeholder')"
                :alert="errors.slug"
                style="margin-bottom: 8px !important;"
              ></skijasi-text>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.slug.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>
            <vs-col vs-w="6" class="mb-3">
              <skijasi-select
                v-model="product.productCategoryId"
                size="12"
                :label="$t('product.add.field.productCategoryId.title')"
                :placeholder="$t('product.add.field.productCategoryId.placeholder')"
                :alert="errors.productCategoryId"
                :items="categories"
                @input="updateCategoryName"
                style="margin-bottom: 8px !important;"
              ></skijasi-select>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.productCategoryId.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>
            <vs-col vs-w="6" class="mb-3">
              <skijasi-upload-image
                v-model="product.productImage"
                size="12"
                :label="$t('product.add.field.productImage.title')"
                :placeholder="$t('product.add.field.productImage.placeholder')"
                :alert="errors.productImage"
                style="margin-bottom: 8px !important;"
              ></skijasi-upload-image>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>



            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-date
                v-model="product.datum_pocetka"
                value-zone="local"
                size="12"
                :label="$t('Datum početka događaja')"    
                :alert="errors.datum_pocetka"
                style="margin-bottom: 8px !important;"
              ></skijasi-date>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.datum_pocetka.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>

            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-date
                v-model="product.datum_kraja"
                value-zone="local"
                size="12"
                :label="$t('Datum kraja događaja')"
                :alert="errors.datum_kraja"
                style="margin-bottom: 8px !important;"
              ></skijasi-date>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.datum_kraja.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>



            <vs-col v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-editor
              v-model="product.desc"
              size="12"
              :label="$t('product.add.field.desc.title')"
              :placeholder="$t('product.add.field.desc.placeholder')"
              :alert="errors.desc"
            ></skijasi-editor>
          </vs-col>


          <vs-col v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-editor
              v-model="product.desc2"
              size="12"
              :label="$t('product.add.field.desc2.title')"
              :placeholder="$t('product.add.field.desc2.placeholder')"
              :alert="errors.desc2"
            ></skijasi-editor>
          </vs-col>

          <vs-col v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-editor
              v-model="product.desc3"
              size="12"
              :label="$t('product.add.field.desc3.title')"
              :placeholder="$t('product.add.field.desc3.placeholder')"
              :alert="errors.desc3"
            ></skijasi-editor>
          </vs-col>

          <vs-col v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-editor
              v-model="product.desc4"
              size="12"
              :label="$t('product.add.field.desc4.title')"
              :placeholder="$t('product.add.field.desc4.placeholder')"
              :alert="errors.desc4"
            ></skijasi-editor>
          </vs-col>
          <vs-col v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-editor
              v-model="product.desc5"
              size="12"
              :label="$t('product.add.field.desc5.title')"
              :placeholder="$t('product.add.field.desc5.placeholder')"
              :alert="errors.desc5"
            ></skijasi-editor>
          </vs-col>


          <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-text
                v-model="product.mjesto"
                size="12"
                :label="('Mjesto događaja')"
                :placeholder="$t('Unesi mjesto događaja (obavezno)')"
                :alert="errors.mjesto"
                style="margin-bottom: 8px !important;"
              ></skijasi-text>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.mjesto.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>




          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("product.add.detail.title") }}</h3>
          </div>
          <skijasi-table :data="items" stripe noDataText="">
            <template slot="thead">
              <skijasi-th sort-key="productImage"> {{ $t("product.add.header.productImage") }} </skijasi-th>
              <skijasi-th sort-key="name"> {{ $t("product.add.header.name") }} </skijasi-th>
              <skijasi-th sort-key="quantity"> {{ $t("product.add.header.quantity") }} </skijasi-th>
              <skijasi-th sort-key="price"> {{ $t("product.add.header.price") }} </skijasi-th>
              <skijasi-th sort-key="discountId"> {{ $t("product.add.header.discount") }} </skijasi-th>
              <skijasi-th sort-key="SKU"> {{ $t("product.add.header.SKU") }} </skijasi-th>
              <vs-th> {{ $t("product.add.header.action") }} </vs-th>
            </template>

            <template slot-scope="{ data }">
              <vs-tr :data="product" :key="index" v-for="(product, index) in data">
                <vs-td :data="product.productImage">
                  <img width="150" :src="product.productImage" loading="lazy">
                </vs-td>
                <vs-td :data="product.name">
                  {{ product.name }}
                </vs-td>
                <vs-td :data="product.quantity">
                  {{ product.quantity }}
                </vs-td>
                <vs-td :data="product.price">
                  {{ toCurrency(product.price) }}
                </vs-td>
                <vs-td :data="product.discountId">
                  {{ getDiscountName(product.discountId) }}
                </vs-td>
                <vs-td :data="product.SKU">
                  {{ product.SKU }}
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
                        icon="edit"
                        @click="openEditDialog(product, index)"
                      >
                        Edit
                      </skijasi-dropdown-item>
                      <skijasi-dropdown-item
                        icon="delete"
                        @click="openDeleteDialog(index)"
                      >
                        Delete
                      </skijasi-dropdown-item>
                    </vs-dropdown-menu>
                  </skijasi-dropdown>
                </vs-td>
              </vs-tr>
              <vs-tr>
                <vs-td colspan="8" class="product-detail__button--add">
                  <vs-button type="relief" icon="add" color="primary" @click="openAddProductDetail">
                    Dodaj novu cijenu/opciju
                  </vs-button>
                </vs-td>
              </vs-tr>
            </template>
          </skijasi-table>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> {{ $t("product.add.button") }}
              </vs-button>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>

      <vs-popup :title="$t('product.add.detail.add.title')" :active.sync="productDetailDialog">
        <vs-row>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="addProductDetail.name"
              size="12"
              :label="$t('product.add.detail.add.field.name.title')"
              :placeholder="$t('product.add.detail.add.field.name.placeholder')"
              :alert="errors.name"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.name.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="addProductDetail.quantity"
              size="12"
              :label="$t('product.add.detail.add.field.quantity.title')"
              :placeholder="$t('product.add.detail.add.field.quantity.placeholder')"
              :alert="errors.quantity"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.quantity.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="addProductDetail.price"
              size="12"
              :label="$t('product.add.detail.add.field.price.title')"
              :placeholder="$t('product.add.detail.add.field.price.placeholder')"
              :alert="errors.price"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.price.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="addProductDetail.SKU"
              size="12"
              :label="$t('product.add.detail.add.field.SKU.title')"
              :placeholder="$t('product.add.detail.add.field.SKU.placeholder')"
              :alert="errors.SKU"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.SKU.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <skijasi-select
            v-model="addProductDetail.discountId"
            size="6"
            :label="$t('product.add.detail.add.field.discount.title')"
            :placeholder="$t('product.add.detail.add.field.discount.placeholder')"
            :alert="errors.discountId"
            :items="discounts"
          ></skijasi-select>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-upload-image
              v-model="addProductDetail.productImage"
              size="12"
              :label="$t('product.add.detail.add.field.productImage.title')"
              :placeholder="$t('product.add.detail.add.field.productImage.placeholder')"
              :alert="errors.productImage"
              style="margin-bottom: 8px !important;"
            ></skijasi-upload-image>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
            <vs-button type="relief" color="primary" class="ml-2" @click="addProductDetailToProduct">
              {{ $t('product.add.detail.add.button.save') }}
            </vs-button>
          </vs-col>
        </vs-row>
      </vs-popup>

      <vs-popup :title="$t('product.add.detail.edit.title')" :active.sync="editProductDetailDialog">
        <vs-row>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="editProductDetail.name"
              size="12"
              :label="$t('product.add.detail.edit.field.name.title')"
              :placeholder="$t('product.add.detail.edit.field.name.placeholder')"
              :alert="errors.name"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.name.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="editProductDetail.quantity"
              size="12"
              :label="$t('product.add.detail.edit.field.quantity.title')"
              :placeholder="$t('product.add.detail.edit.field.quantity.placeholder')"
              :alert="errors.quantity"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.quantity.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="editProductDetail.price"
              size="12"
              :label="$t('product.add.detail.edit.field.price.title')"
              :placeholder="$t('product.add.detail.edit.field.price.placeholder')"
              :alert="errors.price"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.price.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-text
              v-model="editProductDetail.SKU"
              size="12"
              :label="$t('product.add.detail.edit.field.SKU.title')"
              :placeholder="$t('product.add.detail.edit.field.SKU.placeholder')"
              :alert="errors.SKU"
              style="margin-bottom: 8px !important;"
            ></skijasi-text>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.SKU.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <skijasi-select
            v-model="editProductDetail.discountId"
            size="6"
            :label="$t('product.add.detail.edit.field.discount.title')"
            :placeholder="$t('product.add.detail.edit.field.discount.placeholder')"
            :alert="errors.discountId"
            :items="discounts"
          ></skijasi-select>
          <vs-col vs-w="6" class="mb-3">
            <skijasi-upload-image
              v-model="editProductDetail.productImage"
              size="12"
              :label="$t('product.add.detail.edit.field.productImage.title')"
              :placeholder="$t('product.add.detail.edit.field.productImage.placeholder')"
              :alert="errors.productImage"
              style="margin-bottom: 8px !important;"
            ></skijasi-upload-image>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col>
          <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
            <vs-button type="relief" color="primary" class="ml-2" @click="editProductDetailToProduct">
              {{ $t('product.add.detail.edit.button.save') }}
            </vs-button>
          </vs-col>
        </vs-row>
      </vs-popup>
    </vs-row>
  </div>
</template>

<script>
import { required, minValue, maxValue, integer } from "vuelidate/lib/validators";
import currency from "currency.js"
import moment from "moment";
export default {
  name: "ProductAdd",
  components: {},
  data: () => ({
    selectedCategoryName: '',
    
    errors: {},
    product: {
      productCategoryId: "",
      name: "",
      slug: "",
      productImage: "",
      desc: "",
      desc2: "",
      desc3: "",
      desc4: "",
      desc5: "",
      datum_pocetka: "",
      datum_kraja: "",
    },
    addProductDetail: {
      discountId: '',
      name: '',
      quantity: '0',
      price: '0',
      SKU: null,
      productImage: ''
    },
    editProductDetail: {
      discountId: '',
      name: '',
      quantity: '0',
      price: '0',
      SKU: null,
      productImage: ''
    },
    categories: [],
    discounts: [],
    items: [],
    productDetailDialog: false,
    editProductDetailDialog: false,
    deleteProductDetailDialog: false,
    willEditId: null,
    willDeleteId: null,
  }),
  validations() {
    return {
      product: {
        productCategoryId: {
          required
        },
        name: {
          required
        },
        slug: {
          required
        },
        productImage: {
          required
        },
      },
      editProductDetail: {
        name: {
          required,
        },
        quantity: {
          required,
          minValue: minValue(0),
          integer
        },
        price: {
          required,
          minValue: minValue(0),
          integer
        },
        SKU: {
          required
        },
        productImage: {
          required
        }
      },
      addProductDetail: {
        name: {
          required,
        },
        quantity: {
          required,
          minValue: minValue(0),
          integer
        },
        price: {
          required,
          minValue: minValue(0),
          integer
        },
        SKU: {
          required
        },
        productImage: {
          required
        }
      }
    };
  },
  watch: {
    'product.name': {
      handler(val) {
        this.product.slug = this.$helper.generateSlug(val)
      }
    }
  },
  mounted() {
    this.getProductCategoryList()
    this.getProductDiscounts()
  },
  methods: {

    updateCategoryName() {
    const selectedCategory = this.categories.find(cat => cat.value == this.product.productCategoryId);
    this.selectedCategoryName = selectedCategory ? selectedCategory.label : '';
},



    toCurrency(value) {
      return currency(value, {
        precision: this.$store.state.skijasi.config.currencyPrecision,
        decimal: this.$store.state.skijasi.config.currencyDecimal,
        separator: this.$store.state.skijasi.config.currencySeparator,
        symbol: this.$store.state.skijasi.config.currencySymbol,
      }).format()
    },
    getProductCategoryList() {
      this.$openLoader();
      this.$api.skijasiProductCategory
      .browse()
      .then((response) => {
        this.$closeLoader();
        this.categories = response.data.productCategories.map((category, index) => {
          return {
            label: category.name,
            value: category.id
          }
        });
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
    getProductDiscounts() {
      this.$openLoader();
      this.$api.skijasiDiscount
      .browse()
      .then((response) => {
        this.$closeLoader();
        this.discounts = response.data.discounts.map((discount, index) => {
          return {
            label: `${discount.name} - ${discount.discountType === 'fixed' ? this.toCurrency(discount.discountFixed) : discount.discountPercent + '%' }`,
            value: discount.id
          }
        });
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
    openAddProductDetail() {
      this.productDetailDialog = true
      this.clearSelection()
    },
    submitForm() {
      this.errors = {};
      this.$v.product.$touch()

      if (!this.$v.product.$invalid) {
        this.$v.product.$reset()
        try {
          this.$openLoader();
  
              // Remove datum_pocetka from the product if selectedCategoryName is "Licenca"
              if (this.selectedCategoryName == "Licenca") {
                delete this.product.datum_pocetka;
                delete this.product.datum_kraja;
            } else{
                 // Format the datum_pocetka and datum_kraja using moment
      if (this.product.datum_pocetka) {
         this.product.datum_pocetka = moment(this.product.datum_pocetka).format('YYYY-MM-DD HH:mm:ss');
      }
      if (this.product.datum_kraja) {
         this.product.datum_kraja = moment(this.product.datum_kraja).format('YYYY-MM-DD HH:mm:ss');
      } }


          this.$api.skijasiProduct
            .add({ ...this.product, items: this.items })
            .then((response) => {
              this.$closeLoader();
              this.$router.push({ name: "ProductBrowse" });
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
        } catch (error) {
          this.errors = error.data
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        }
      }
    },
    addProductDetailToProduct() {
      this.$v.addProductDetail.$touch()

      if (!this.$v.addProductDetail.$invalid) {
        this.items.push({ ...this.addProductDetail })
        this.productDetailDialog = false
      }
    },
    editProductDetailToProduct() {
      this.$v.editProductDetail.$touch()

      if (!this.$v.editProductDetail.$invalid) {
        this.$v.editProductDetail.$reset()
        this.$set(this.items, this.willEditId, this.editProductDetail)
        this.editProductDetailDialog = false
      }
    },
    clearSelection() {
      this.$v.addProductDetail.$reset()
      this.addProductDetail.discountId = ''
      this.addProductDetail.name = ''
      this.addProductDetail.quantity = ''
      this.addProductDetail.price = ''
      this.addProductDetail.SKU = null
      this.addProductDetail.productImage = ''
    },
    openEditDialog(item, index) {
      this.editProductDetailDialog = true
      this.editProductDetail = { ...item }
      this.willEditId = index
    },
    openDeleteDialog(index) {
      this.items.splice(index, 1)
    },
    getDiscountName(discountId) {
      if (!discountId || discountId === '') return ''

      var discount = this.discounts.find(discount => discount.value === discountId)
      if (!discount) return ''
      return discount.label
    }
  },
};
</script>

<style lang="scss">
.product-detail {
  &__button {
    &--add {
      & > span {
        display: flex;
        justify-content: center;
        align-items: center;
      }
    }
  }
}

.danger {
  color: rgba(var(--vs-danger), 1);
  display: inline-block;
}
</style>
