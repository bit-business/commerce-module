<template>
  <div>
    <skijasi-breadcrumb-row></skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('edit_payments')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("payments.edit.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="payment.name"
              size="6"
              :label="$t('payments.edit.field.name.title')"
              :placeholder="$t('payments.edit.field.name.placeholder')"
              :alert="errors.name"
            ></skijasi-text>
            <skijasi-text
              v-model="payment.slug"
              size="6"
              :label="$t('payments.edit.field.slug.title')"
              :placeholder="$t('payments.edit.field.slug.placeholder')"
              :alert="errors.slug"
              disabled
            ></skijasi-text>
            <skijasi-switch
              v-model="payment.isActive"
              size="6"
              :label="$t('payments.edit.field.isActive.title')"
              :placeholder="$t('payments.edit.field.isActive.placeholder')"
              :alert="errors.isActive"
            ></skijasi-switch>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> {{ $t("payments.edit.button") }}
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
  name: "PaymentEdit",
  components: {},
  data() {
    return {
      errors: {},
      payment: {
        name: null,
        slug: null,
        isActive: true,
      },
    }
  },
  mounted() {
    this.getPayment()
  },
  methods: {
    submitForm() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiPayment
        .edit(this.payment)
        .then((response) => {
          this.$closeLoader();
          this.$router.push({ name: "PaymentBrowse" });
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
    getPayment() {
      this.$openLoader();
      this.$api.skijasiPayment
      .read({ id: this.$route.params.id, })
      .then((response) => {
        this.$closeLoader();
        this.payment = {...response.data.payment};
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