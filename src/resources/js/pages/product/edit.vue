<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_products')">
      <vs-col vs-lg="12">
        <vs-card>
  <div slot="header" class="card-header">
    <h3 class="card-title">Postavke za Plaćanje</h3>
  </div>
  <vs-row class="content-section">

  
           

            <vs-col vs-w="6" class="mb-3">
              <skijasi-upload-image-dogadaji
                v-model="product.productImage"
                size="12"
                   class="custom-label"
                :label="'Slika'"
                :placeholder="'obavezno'"
                :alert="errors.productImage"
                style="margin-bottom: 8px !important;"
              ></skijasi-upload-image-dogadaji>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>


            <vs-col vs-w="6" class="mb-3">
              <skijasi-text
                v-model="product.name"
                size="12"
                   class="custom-label"
                :label="$t('product.add.field.name.title')"
                :placeholder="$t('product.add.field.name.placeholder')"
                :alert="errors.name"
                style="margin-bottom: 8px !important;"
              ></skijasi-text>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.name.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>


            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-select
          v-model="formIdString"
            :items="availableForms"
            size="12"
               class="custom-label"
            :label="'Prijavnica za događaj'"
            :placeholder="'Odaberi prijavnicu za seminar (stvorit prije u meniju pod Prijavnice)'"
            :alert="errors.formId"
            style="margin-bottom: 8px !important;"
          ></skijasi-select>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.mjesto.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>


            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-text
                v-model="product.mjesto"
                size="12"
                   class="custom-label"
                :label="'Mjesto događaja'"
                :placeholder="'Unesi mjesto događaja (obavezno)'"
                :alert="errors.mjesto"
                style="margin-bottom: 8px !important;"
              ></skijasi-text>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.mjesto.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>

            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-date
                v-model="product.datumPocetka"
                value-zone="local"
                size="12"
                   class="custom-label"
                :label="'Datum početka događaja'"    
                :alert="errors.datum_pocetka"
                style="margin-bottom: 8px !important;"
              ></skijasi-date>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.datum_pocetka.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>

            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
              <skijasi-date
                v-model="product.datumKraja"
                value-zone="local"
                size="12"
                   class="custom-label"
                :label="'Datum kraja događaja'"
                :alert="errors.datum_kraja"
                style="margin-bottom: 8px !important;"
              ></skijasi-date>
              <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.datum_kraja.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
            </vs-col>

            <vs-col vs-w="6" class="mb-3" v-if="selectedCategoryName.trim() == 'Događanja'">
            <skijasi-switch
              v-model="product.sakrij"
              size="6"
              class="custom-label"
                :label="'Sakrij događaj?'"
              :alert="errors.sakrij"
            ></skijasi-switch>
            <template v-if="$v.product.$dirty">
                <span class="danger" v-if="$v.product.sakrij.$anyError">{{ $t('vuelidate.error') }}</span>
              </template>
              <skijasi-switch
              v-model="product.zatvoriprijave"
              size="6"
              class="custom-label"
                :label="'Zatvori prijave?'"
              :alert="errors.zatvoriprijave"
            ></skijasi-switch>
            </vs-col>


            <vs-col vs-w="12" class="email-section" v-if="selectedCategoryName.trim() == 'Događanja'">
  <div class="section-divider"></div>
  <skijasi-textarea
    v-model="product.prijaveposebni"
    size="12"
    class="custom-label special-access-field"
    :label="'Posebne dozvole (email adrese odvojene zarezom)'"
    :placeholder="'Unesite email adrese odvojene zarezom (npr. email1@test.com, email2@test.com)'"
    :alert="errors.prijaveposebni"
  ></skijasi-textarea>
  <small class="help-text">
    <i class="material-icons info-icon">info</i>
    Korisnici s navedenim email adresama će moći pristupiti prijavnicama čak i kada su prijave zatvorene.
  </small>
</vs-col>


            <vs-col vs-w="12" class="mb-3"  v-if="selectedCategoryName.trim() == 'Događanja'">   
  <skijasi-select 
    v-model="selectedLanguage" 
    :items="languageOptions"
    label="Odaberi jezik"
    size="12"
    class="custom-label"
    style="margin-bottom: 2rem !important; font-weight: bold;"
  >
  </skijasi-select>

  <vs-col vs-w="6" class="mb-3" v-if="selectedLanguage === 'en'">
  <skijasi-text
    v-model="product.nameEn"
    size="12"
    class="custom-label"
    label="Engleski naziv (EN)"
    placeholder="Upišite engleski prijevod naziva"
    :alert="errors.nameEn"
    style="margin-bottom: 8px !important;"
  ></skijasi-text>
  <template v-if="$v.product.$dirty">
    <span class="danger" v-if="$v.product.nameEn.$anyError">{{ $t('vuelidate.error') }}</span>
  </template>
</vs-col>

<vs-col vs-w="6" class="mb-3" v-if="selectedLanguage === 'it'">
  <skijasi-text
    v-model="product.nameIt"
    size="12"
    class="custom-label"
    label="Talijanski naziv (IT)"
    placeholder="Upišite talijanski prijevod naziva"
    :alert="errors.nameIt"
    style="margin-bottom: 8px !important;"
  ></skijasi-text>
  <template v-if="$v.product.$dirty">
    <span class="danger" v-if="$v.product.nameIt.$anyError">{{ $t('vuelidate.error') }}</span>
  </template>
</vs-col>

  <div v-for="(descField, index) in descFields" :key="descField" class="custom-editor-wrapper">
  <skijasi-editor
    :value="getFieldValue(descField)"
    @input="updateFieldValue(descField, $event)"
    :label="`${descLabels[descField]} (${selectedLanguage.toUpperCase()})`"
    :placeholder="`Enter ${descLabels[descField]} in ${selectedLanguage}`"
    :editorId="`editor-${descField}-${selectedLanguage}`"
  ></skijasi-editor>
</div>
</vs-col>

       

          </vs-row>     <vs-row  style="padding-top: 3rem !important;">
          <vs-col vs-w="12" class="mb-3"  v-if="selectedCategoryName.trim() == 'Događanja'">
  <skijasi-upload-image-dogadaji-galerija
    v-model="product.galleryimages"
    size="12"
       class="custom-label"
    :label="'Galerija slika'"
    :placeholder="'Dodajte slike za galeriju'"
    :alert="errors.galleryimages"
    multiple
    style="margin-bottom: 8px !important;"
  ></skijasi-upload-image-dogadaji-galerija>
</vs-col>     </vs-row>
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
              <skijasi-th sort-key="discountFixed"> {{ $t("product.add.header.discount") }} </skijasi-th>
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
                <vs-td style="width: 1%; white-space: nowrap; align-items: center; ">
              
                   
                      <vs-button
                                  class="button-spacing"
                        icon="edit"
                        v-if="$helper.isAllowed('edit_product_details')"
                        @click="openEditDialog(product, index)"
                      >
                
                      </vs-button>
                      <vs-button
                                  class="button-spacing"
                        icon="delete"
                        v-if="$helper.isAllowed('delete_product_details')"
                        @click="openDeleteDialog(product, index)"
                      >
              
                      </vs-button>
             
               
                </vs-td>
              </vs-tr>
              <vs-tr>
                <vs-td colspan="8" class="product-detail__button--add">
                  <vs-button type="relief" icon="add" v-if="$helper.isAllowed('add_product_details')" color="primary" @click="openAddProductDetail">
                    Dodaj Status, Cijenu i Količinu
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
            <skijasi-select
    v-model="addProductDetail.name"
  size="12"
       :label="$t('product.add.detail.add.field.name.title')"
              :placeholder="$t('product.add.detail.add.field.name.placeholder')"
  :items="productDetailNazivi"
  :alert="errors.name"
  style="margin-bottom: 8px !important;"
></skijasi-select>
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
          <!-- <vs-col vs-w="6" class="mb-3">
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
          </vs-col> -->
          <skijasi-select
            v-model="addProductDetail.discountId"
            size="6"
            :label="$t('product.add.detail.add.field.discount.title')"
            :placeholder="$t('product.add.detail.add.field.discount.placeholder')"
            :alert="errors.discountId"
            :items="discounts"
          ></skijasi-select>
          <!-- <vs-col vs-w="6" class="mb-3">
            <skijasi-upload-image-dogadaji
              v-model="addProductDetail.productImage"
              size="12"
              :label="$t('product.add.detail.add.field.productImage.title')"
              :placeholder="$t('product.add.detail.add.field.productImage.placeholder')"
              :alert="errors.productImage"
              style="margin-bottom: 8px !important;"
            ></skijasi-upload-image-dogadaji>
            <template v-if="$v.addProductDetail.$dirty">
              <span class="danger" v-if="$v.addProductDetail.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col> -->
          <vs-col vs-w="12" vs-type="flex" vs-justify="flex-end">
            <vs-button type="relief" color="primary" class="ml-2" @click="addProductDetailToProduct">
              {{ $t('product.add.detail.add.button.save') }}
            </vs-button>
          </vs-col>
        </vs-row>
      </vs-popup>

      <vs-popup :title="$t('product.add.detail.edit.title')" :active.sync="editProductDetailDialog">
        <vs-row>
          <vs-col vs-w="12" class="mb-3">
            <skijasi-select
  v-model="editProductDetail.name"
  size="12"
      :label="$t('product.add.detail.edit.field.name.title')"
              :placeholder="$t('product.add.detail.edit.field.name.placeholder')"
  :items="productDetailNazivi"
  :alert="errors.name"
  style="margin-bottom: 8px !important;"
></skijasi-select>
            
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
          <!-- <vs-col vs-w="6" class="mb-3">
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
          </vs-col> -->
          <skijasi-select
            v-model="editProductDetail.discountId"
            size="6"
            :label="$t('product.add.detail.edit.field.discount.title')"
            :placeholder="$t('product.add.detail.edit.field.discount.placeholder')"
            :alert="errors.discountId"
            :items="discounts"
          ></skijasi-select>
          <!-- <vs-col vs-w="6" class="mb-3">
            <skijasi-upload-image-dogadaji
              v-model="editProductDetail.productImage"
              size="12"
              :label="$t('product.add.detail.edit.field.productImage.title')"
              :placeholder="$t('product.add.detail.edit.field.productImage.placeholder')"
              :alert="errors.productImage"
              style="margin-bottom: 8px !important;"
            ></skijasi-upload-image-dogadaji>
            <template v-if="$v.editProductDetail.$dirty">
              <span class="danger" v-if="$v.editProductDetail.productImage.$anyError">{{ $t('vuelidate.error') }}</span>
            </template>
          </vs-col> -->
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
import api from "../../../../../../commerce-theme/src/resources/js/api/modules/skijasi-commerce-theme-configuration.js";

export default {
  name: "ProductAdd",
  components: {},
  data: () => ({
    selectedCategoryName: '',
    forms: {
        items: []
      },
    
    errors: {},
    product: {
      productCategoryId: "",
      name: "",
      slug: "",
      productImage: "",
      desc: '', desc2: '', desc3: '', desc4: '', desc5: '', // Croatian
      nameEn: '', descEn: '', desc2En: '', desc3En: '', desc4En: '', desc5En: '', // English
      nameIt: '', descIt: '', desc2It: '', desc3It: '', desc4It: '', desc5It: '', // Italian
   
      formId: "",

      galleryimages: [],
    },

    selectedLanguage: 'hr',
  languageOptions: [
    { label: 'Hrvatski', value: 'hr' },
    { label: 'Engleski', value: 'en' },
    { label: 'Talijanski', value: 'it' }
  ],
  descFields: ['desc', 'desc2', 'desc3', 'desc4', 'desc5'],
  descLabels: {
    desc: 'Tekst -> O Skijalištu',
    desc2: 'Tekst -> Informacije',
    desc3: 'Tekst -> Smještaj',
    desc4: 'Tekst -> Prijevoz',
    desc5: 'Tekst -> Prijava i plaćanje'
  },
    
    addProductDetail: {
      discountId: '',
      name: '',
      quantity: '0',
      price: '0',
      SKU: null,
      // productImage: ''
    },
    editProductDetail: {
      discountId: '',
      name: '',
      quantity: '0',
      price: '0',
      SKU: null,
      // productImage: ''
    },
    categories: [],
    discounts: [],
    items: [],
    productDetailDialog: false,
    editProductDetailDialog: false,
    deleteProductDetailDialog: false,
    willEditId: null,
    willDeleteId: null,

    productDetailNazivi: [
    { label: 'Produženje licence', value: 'Produženje licence' },
    { label: 'HZUTS član', value: 'HZUTS član' },
    // { label: 'Učitelj skijanja', value: 'Učitelj skijanja' },
    // { label: 'ISIA učitelj', value: 'ISIA učitelj' },
    // { label: 'Snowboard učitelj', value: 'Snowboard učitelj' },
    // { label: 'Voditelj skijanja', value: 'Voditelj skijanja' },
    // { label: 'Trener skijanja', value: 'Trener skijanja' },
    // { label: 'Demonstrator skijanja', value: 'Demonstrator skijanja' },
    // { label: 'Snowboard demonstrator', value: 'Snowboard Demonstrator' },
    // { label: 'Počasni član', value: 'Počasni član' },
    // { label: 'Podupirući član', value: 'Podupirući član' },
    // { label: 'Snowboard Trener', value: 'Snowboard Trener' },
    { label: 'Nije član', value: 'Nije član' },
  ],

  }),
  validations() {
    const decimal2Places = (value) => {
      if (value === null || value === undefined || value === '') return true;
      const regex = /^\d+(\.\d{1,2})?$/;
      return regex.test(value.toString());
    };
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
          decimal2Places,
        },
        // SKU: {
        //   required
        // },
        // productImage: {
        //   required
        // }
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
          decimal2Places,
        },
        // SKU: {
        //   required
        // },
        // productImage: {
        //   required
        // }
      }
    };
  },

  async created() {
  await this.getProductCategoryList();
  await this.getProductDetail();
  this.updateCategoryName();
  this.getProductDiscounts();
  this.getForms();
},

  computed: {
  availableForms() {
    return this.forms.items.map(form => ({
      label: form.name,
      value: String(form.id)
    }));
  },

  formIdString: {
      get() {
        // Return empty string if formId is null/undefined
        return this.product.formId ? String(this.product.formId) : '';
      },
      set(value) {
        // Convert empty string to null, otherwise convert to number
        this.product.formId = value === '' ? null : Number(value);
      }
    }
  
},
watch: {
  'product.productCategoryId': {
    handler(newVal) {
      this.updateCategoryName();
    },
    immediate: true
  },

  selectedLanguage(newVal, oldVal) {
    this.handleLanguageChange();
  },
},

  methods: {
    updateCategoryName() {
  const selectedCategory = this.categories.find(cat => cat.value == this.product.productCategoryId);
  this.selectedCategoryName = selectedCategory ? selectedCategory.label : '';
  this.$nextTick(() => {
    // Force component update
    this.$forceUpdate();
  });
},


getDescFieldKey(field) {
  if (this.selectedLanguage === 'hr') {
    return field;
  } else {
    return `${field}${this.selectedLanguage.charAt(0).toUpperCase() + this.selectedLanguage.slice(1)}`;
  }
},

getFieldValue(field) {
  const key = this.getDescFieldKey(field);
  return this.product[key] || '';
},

updateFieldValue(field, value) {
  const key = this.getDescFieldKey(field);
  this.$set(this.product, key, value);
},

handleLanguageChange() {
  this.descFields.forEach(field => {
    const key = this.getDescFieldKey(field);
  });
  this.$forceUpdate();
},




    async getForms() {
          try {
        const response = await api.browseForm();
     
        let formsData;
        if (typeof response.data === 'string') {
          formsData = JSON.parse(response.data);
        } else {
          formsData = response.data;
        }

        if (formsData && formsData.forms && formsData.forms['\u0000*\u0000items']) {
          this.forms = {
            items: formsData.forms['\u0000*\u0000items'].map(item => ({ ...item }))
          };
        } else {
          console.error("Unexpected API response structure:", formsData);
          this.forms = { items: [] };
        }

      } catch (error) {
        console.error('Error fetching forms:', error);
        this.forms = { items: [] };
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
getProductDetail() {
  this.$openLoader();
  this.$api.skijasiProduct
    .read({ id: this.$route.params.id, relation: [ 'productCategory', 'productDetails' ] })
    .then((response) => {
      this.$closeLoader();

      const productData = response.data.product;
      
      // Ensure all language fields are present and reactive
      this.descFields.forEach(field => {
        this.$set(this.product, field, productData[field] || '');
        this.$set(this.product, `${field}En`, productData[`${field}En`] || '');
        this.$set(this.product, `${field}It`, productData[`${field}It`] || '');
      });

      // Set other product properties
      Object.keys(productData).forEach(key => {
        if (!key.startsWith('desc')) {
          this.$set(this.product, key, productData[key]);
        }
      });

      this.items = productData.productDetails;

   
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
    async getProductCategoryList() {
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
      this.$v.product.$touch();

      if (!this.$v.product.$invalid) {
        this.$v.product.$reset();
        try {
          this.$openLoader();

          // Create a clean product data object
          const productData = {
            ...this.product,
            items: this.items,
            id: this.$route.params.id,
            // Ensure galleryimages is null if empty
            galleryimages: this.product.galleryimages || null,
            // Handle formId properly
            formId: this.product.formId || null
          };

          // Remove properties if not in Događanja category
          if (this.selectedCategoryName !== "Događanja") {
            delete productData.datum_pocetka;
            delete productData.datum_kraja;
            delete productData.formId;
            delete productData.galleryimages;
          } else {
            // Format dates if they exist
            if (productData.datum_pocetka) {
              productData.datum_pocetka = moment(productData.datum_pocetka).format('YYYY-MM-DD HH:mm:ss');
            }
            if (productData.datum_kraja) {
              productData.datum_kraja = moment(productData.datum_kraja).format('YYYY-MM-DD HH:mm:ss');
            }
          }

          // Add all language fields
          this.descFields.forEach(field => {
            productData[field] = this.product[field] || '';
            productData[`${field}En`] = this.product[`${field}En`] || '';
            productData[`${field}It`] = this.product[`${field}It`] || '';
          });

          // Submit the data
          this.$api.skijasiProduct.edit(productData)
            .then((response) => {
              // Update form product slug if needed
              if (this.product.formId && 
                  this.selectedCategoryName === "Događanja" && 
                  this.product.slug) {
                return this.$api.skijasiCommerceThemeConfiguration.updateFormProductSlug({
                  id: this.product.formId,
                  productslug: this.product.slug
                });
              }
              return response;
            })
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
          this.errors = error.data;
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
        this.$api.skijasiProductDetail
        .add({ ...this.addProductDetail, productId: this.$route.params.id,  productImage: this.product.productImage  })
        .then((response) => {
          this.$closeLoader();
          this.getProductDetail()
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
        this.productDetailDialog = false
      }
    },
    editProductDetailToProduct() {
      this.$v.editProductDetail.$touch()

      if (!this.$v.editProductDetail.$invalid) {
        this.$api.skijasiProductDetail
        .edit({ ...this.editProductDetail, productId: this.$route.params.id, productImage: this.product.productImage  })
        .then((response) => {
          this.$closeLoader();
          this.getProductDetail()
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
        this.editProductDetailDialog = false
      }
    },
    clearSelection() {
      this.$v.addProductDetail.$reset()
      this.addProductDetail.discount = ''
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
    openDeleteDialog(item, index) {
      this.willDeleteId = item.id
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.deleteProduct,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {},
      });
    },
    deleteProduct() {
      this.$api.skijasiProductDetail
      .delete({ id: this.willDeleteId })
      .then((response) => {
        this.$closeLoader();
        this.getProductDetail()
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

.button-spacing:not(:last-child) {
  margin-bottom: 5px;
}

.gapredovi {
 gap: 1.2rem !important;
 font-weight: bold;
}

.custom-label .skijasi-text__label,
.custom-label .skijasi-select__label,
.custom-label .skijasi-upload-image-dogadaji__label,
.custom-label .skijasi-date__label,
.custom-label .skijasi-editor__label {
  font-size: 1.1rem; /* Adjust as needed */
  font-weight: bold;
}

 .vs-select--label {
  font-size: 1.1rem !important; /* Adjust size as needed */
  font-weight: bold !important; /* Make the font bold */
}


.custom-editor-wrapper {
  margin-bottom: 1rem; 

  .skijasi-editor__label {
    font-weight: bold;
    font-size: 1.2rem; 
    margin-bottom: 0.5rem;
    display: block; 
  }
}




.card-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);

  .card-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
  }
}

.content-section {
  padding: 2rem;
}

.section-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.1);
  margin: 2rem 0;
  width: 100%;
}

.email-section {

  padding: 1.5rem;
  border-radius: 8px;
  margin: 1.5rem 0;
  margin-bottom: 7rem;

  .special-access-field {
    margin-bottom: 0.5rem !important;
  }

  .help-text {
    display: flex;
    align-items: center;
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: #fff;
    border-radius: 4px;
    border-left: 3px solid #17a2b8;

    .info-icon {
      font-size: 1.2rem;
      margin-right: 0.5rem;
      color: #17a2b8;
    }
  }
}

.custom-label {
  .skijasi-text__label,
  .skijasi-select__label,
  .skijasi-upload-image-dogadaji__label,
  .skijasi-date__label,
  .skijasi-editor__label,
  .skijasi-textarea__label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
  }

  input,
  select,
  textarea {
    border: 1px solid #dce0e3;
    border-radius: 4px;
    padding: 0.75rem;
    transition: border-color 0.15s ease-in-out;

    &:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
  }
}

.vs-card {
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;

  &.action-card {
    background: #f8f9fa;
    padding: 1rem;
  }
}

// Style for switches
.vs-switch {
  margin: 1rem 0;
  
  &:not(:last-child) {
    margin-right: 2rem;
  }
}

// Button styling
.vs-button {
  &.vs-button-primary {
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    
    &:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  }
}

// Improve language selector spacing
.language-selector {
  margin: 2rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

// Editor improvements
.custom-editor-wrapper {
  margin: 2rem 0;
  padding: 1rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);

  .skijasi-editor__label {
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
  }

  .ck-editor__main {
    min-height: 200px;
  }
}

// Table improvements
.skijasi-table {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);

  th {
    background: #f8f9fa;
    font-weight: 600;
    padding: 1rem;
  }

  td {
    padding: 1rem;
    vertical-align: middle;
  }
}


</style>