<template>
  <div>
    <skijasi-breadcrumb-row></skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_discounts')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("discounts.add.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="discount.name"
              size="6"
              :label="$t('discounts.add.field.name.title')"
              :placeholder="$t('discounts.add.field.name.placeholder')"
              :alert="errors.name"
            ></skijasi-text>
            <skijasi-switch
              v-model="discount.active"
              size="6"
              :label="$t('discounts.add.field.active.title')"
              :placeholder="$t('discounts.add.field.active.placeholder')"
              :alert="errors.active"
            ></skijasi-switch>
            <skijasi-select
              v-model="discount.discountType"
              size="6"
              :label="$t('discounts.add.field.discountType.title')"
              :placeholder="$t('discounts.add.field.discountType.placeholder')"
              :alert="errors.discountType"
              :items="discountType"
              @input="clearDiscount"
            ></skijasi-select>
            <skijasi-number
              v-if="discount.discountType === 'percent'"
              v-model="discount.discountPercent"
              size="6"
              :label="$t('discounts.add.field.discountPercent.title')"
              :placeholder="$t('discounts.add.field.discountPercent.placeholder')"
              :alert="errors.discountPercent"
               :tooltip="$t('discounts.help.discountPercent')"
            ></skijasi-number>
            <skijasi-text
            v-if="discount.discountType === 'fixed'"
              v-model="discount.discountFixed"
              size="6"
              :label="$t('discounts.add.field.discountFixed.title')"
              :placeholder="$t('discounts.add.field.discountFixed.placeholder')"
              :alert="errors.discountFixed"
              :tooltip="$t('discounts.help.discountFixed')"
            ></skijasi-text>
            <skijasi-textarea
              v-model="discount.desc"
              size="12"
              :label="$t('discounts.add.field.desc.title')"
              :placeholder="$t('discounts.add.field.desc.placeholder')"
              :alert="errors.desc"
            ></skijasi-textarea>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> {{ $t("discounts.add.button") }}
              </vs-button>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "DiscountAdd",
  components: {},
  data() {
    return {
      errors: {},
      discount: {
        name: null,
        desc: null,
        discountType: 'fixed',
        discountPercent: '',
        discountFixed: '',
        active: false,
      },
      discountType: [
        { label: this.$t('discounts.discountType.fixed'), value: 'fixed' },
        { label: this.$t('discounts.discountType.percent'), value: 'percent' },
      ]
    }
  },
  methods: {
    submitForm() {
      this.errors = {};
      try {
        this.$openLoader();
        this.$api.skijasiDiscount
          .add(this.discount)
          .then((response) => {
            this.$closeLoader();
            this.$router.push({ name: "DiscountBrowse" });
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
    },
    clearDiscount() {
      this.discount.discountFixed = ''
      this.discount.discountPercent = ''
    }
  },
};
</script>
