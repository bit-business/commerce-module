<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action">
        <vs-button
          color="success"
          type="relief"
          v-if="selected.length > 0 && $helper.isAllowed('restore_discounts')"
          @click.stop
          @click="confirmRestoreMultiple"
          ><vs-icon icon="restore"></vs-icon> {{ $t("action.bulkRestore") }}</vs-button
        >
        <vs-button
          color="danger"
          type="relief"
          v-if="selected.length > 0 && $helper.isAllowed('delete_permanent_discounts')"
          @click.stop
          @click="confirmDeleteMultiple"
          ><vs-icon icon="delete_sweep"></vs-icon>
          {{ $t("action.bulkDelete") }}</vs-button
        >
      </template>
    </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('browse_discounts_bin')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("discounts.browseBin.title") }}</h3>
          </div>
          <div>
            <skijasi-table
              multiple
              v-model="selected"
              pagination
              max-items="10"
              search
              :data="discounts"
              stripe
              description
              :description-items="descriptionItems"
              :description-title="$t('discounts.browseBin.footer.descriptionTitle')"
              :description-connector="$t('discounts.browseBin.footer.descriptionConnector')"
              :description-body="$t('discounts.browseBin.footer.descriptionBody')"
            >
              <template slot="thead">
                <skijasi-th sort-key="name"> {{ $t("discounts.browse.header.name") }} </skijasi-th>
                <skijasi-th sort-key="discountType"> {{ $t("discounts.browse.header.discountType") }} </skijasi-th>
                <skijasi-th sort-key="discountPercent"> {{ $t("discounts.browse.header.discountPercent") }} </skijasi-th>
                <skijasi-th sort-key="discountFixed"> {{ $t("discounts.browse.header.discountFixed") }} </skijasi-th>
                <skijasi-th sort-key="active"> {{ $t("discounts.browse.header.active") }} </skijasi-th>
                <skijasi-th sort-key="updatedAt"> {{ $t("discounts.browse.header.updatedAt") }} </skijasi-th>
                <vs-th> {{ $t("discounts.browse.header.action") }} </vs-th>
              </template>

              <template slot-scope="{ data }">
                <vs-tr :data="discount" :key="index" v-for="(discount, index) in data">
                  <vs-td :data="discount.name">
                    {{ discount.name }}
                  </vs-td>
                  <vs-td :data="discount.discountType">
                    <span style="text-transform: capitalize;">
                      {{ discount.discountType }}
                    </span>
                  </vs-td>
                  <vs-td :data="discount.discountPercent">
                    {{ discount.discountPercent ? discount.discountPercent + '%' : 'None' }}
                  </vs-td>
                  <vs-td :data="discount.discountFixed">
                    {{ discount.discountFixed | toCurrency }}
                  </vs-td>
                  <vs-td :data="discount.active">
                    {{ discount.active === 1 ? 'Yes' : 'No' }}
                  </vs-td>
                  <vs-td :data="discount.deletedAt">
                    {{ getDate(discount.deletedAt) }}
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
                          icon="restore"
                          @click="confirmRestore(discount.id)"
                          v-if="$helper.isAllowed('restore_discounts')"
                        >
                          Restore
                        </skijasi-dropdown-item>
                        <skijasi-dropdown-item
                          icon="edit"
                          @click="confirmDelete(discount.id)"
                          v-if="$helper.isAllowed('delete_permanent_discounts')"
                        >
                          Delete Permanent
                        </skijasi-dropdown-item>
                      </vs-dropdown-menu>
                    </skijasi-dropdown>
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-table>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import moment from 'moment'
export default {
  name: "DiscountBrowseBin",
  components: {},
  data() {
    return {
      selected: [],
      discounts: [],
      descriptionItems: [10, 50, 100],
      totalItem: 0,
      filter: "",
      page: 1,
      limit: 10,
      orderField: "deletedAt",
      orderDirection: "desc",
      willDeleteId: null,
      willRestoreId: null,
    }
  },
  mounted() {
    this.getDiscountList()
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
    getDate(date) {
      return moment(date).format('dddd, DD MMMM YYYY')
    },
    confirmRestore(id) {
      this.willRestoreId = id;
      this.$vs.dialog({
        type: "confirm",
        color: "success",
        title: this.$t("action.restore.title"),
        text: this.$t("action.restore.text"),
        accept: this.restoreDiscount,
        acceptText: this.$t("action.restore.accept"),
        cancelText: this.$t("action.restore.cancel"),
        cancel: () => {
          this.willRestoreId = null;
        },
      });
    },
    confirmDelete(id) {
      this.willDeleteId = id;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.deleteDiscount,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {
          this.willDeleteId = null;
        },
      });
    },
    confirmRestoreMultiple() {
      this.$vs.dialog({
        type: "confirm",
        color: "success",
        title: this.$t("action.restore.title"),
        text: this.$t("action.restore.text"),
        accept: this.bulkRestoreDiscount,
        acceptText: this.$t("action.restore.accept"),
        cancelText: this.$t("action.restore.cancel"),
        cancel: () => {},
      });
    },
    confirmDeleteMultiple() {
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.bulkDeleteDiscount,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {},
      });
    },
    deleteDiscount() {
      this.$openLoader();
      this.$api.skijasiDiscount
        .forceDelete({ id: this.willDeleteId })
        .then((response) => {
          this.$closeLoader();
          this.getDiscountList();
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
    restoreDiscount() {
      this.$openLoader();
      this.$api.skijasiDiscount
        .restore({ id: this.willRestoreId })
        .then((response) => {
          this.$closeLoader();
          this.getDiscountList();
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
    bulkDeleteDiscount() {
      const ids = this.selected.map((item) => item.id);
      this.$openLoader();
      this.$api.skijasiDiscount
      .forceDeleteMultiple({
        ids: ids.join(","),
      })
      .then((response) => {
        this.$closeLoader();
        this.getDiscountList();
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
    bulkRestoreDiscount() {
      const ids = this.selected.map((item) => item.id);
      this.$openLoader();
      this.$api.skijasiDiscount
      .restoreMultiple({
        ids: ids.join(","),
      })
      .then((response) => {
        this.$closeLoader();
        this.getDiscountList();
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
    getDiscountList() {
      this.$openLoader();
      this.$api.skijasiDiscount
      .browseBin()
      .then((response) => {
        this.$closeLoader();
        this.selected = [];
        this.discounts = response.data.discounts;
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
