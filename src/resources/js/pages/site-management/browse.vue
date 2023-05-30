<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action">
        <vs-button
          color="primary"
          type="relief"
          :to="{ name: 'CommerceConfigurationAdd' }"
          v-if="$helper.isAllowed('add_configurations')"
          ><vs-icon icon="add"></vs-icon> {{ $t("action.add") }}</vs-button
        >
      </template>
    </skijasi-breadcrumb-row>
    <vs-row
      v-if="$helper.isAllowed('browse_commerce_configurations') && groupList.length > 0"
    >
      <vs-col vs-lg="12">
        <vs-card>
          <vs-tabs :color="adminPanelHeaderFontColor">
            <vs-tab
              v-for="(group, index) in groupList"
              :key="index"
              :label="group.label"
            >
              <vs-row
                class="site-management__container"
                v-for="(config, index) in filterConfigurations(group.value)"
                :key="index"
              >
                <skijasi-text
                  v-if="config.type === 'text'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-text>
                <skijasi-email
                  v-if="config.type === 'email'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-email>
                <skijasi-password
                  v-if="config.type === 'password'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-password>
                <skijasi-textarea
                  v-if="config.type === 'textarea'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-textarea>
                <skijasi-checkbox
                  v-if="config.type === 'checkbox'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                  :items="config.details.items"
                ></skijasi-checkbox>
                <skijasi-search
                  v-if="config.type === 'search'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-search>
                <skijasi-number
                  v-if="config.type === 'number'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-number>
                <skijasi-url
                  v-if="config.type === 'url'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-url>
                <skijasi-time
                  v-if="config.type === 'time'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-time>
                <skijasi-date
                  v-if="config.type === 'date'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-date>
                <skijasi-datetime
                  v-if="config.type === 'datetime'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                ></skijasi-datetime>
                <skijasi-select
                  v-if="config.type === 'select'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                  :items="config.details.items"
                ></skijasi-select>
                <skijasi-radio
                  v-if="config.type === 'radio'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                  :items="config.details.items"
                ></skijasi-radio>
                <skijasi-switch
                  v-if="config.type === 'switch'"
                  :label="config.displayName"
                  size="10"
                  v-model="config.value"
                ></skijasi-switch>
                <skijasi-slider
                  v-if="config.type === 'slider'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  v-model="config.value"
                ></skijasi-slider>
                <skijasi-editor
                  v-if="config.type === 'editor'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  v-model="config.value"
                ></skijasi-editor>
                <skijasi-tags
                  v-if="config.type === 'tags'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  v-model="config.value"
                ></skijasi-tags>
                <skijasi-hidden
                  v-if="config.type === 'hidden'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                ></skijasi-hidden>

                <skijasi-select-multiple
                  v-if="config.type === 'select_multiple'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  v-model="config.value"
                  size="10"
                  :items="config.details.items"
                ></skijasi-select-multiple>
                <skijasi-upload-image
                  v-if="config.type === 'upload_image'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  :private-only="config.details !== null && config.details.type === 'private-only'"
                  :shares-only="config.details !== null && config.details.type === 'shares-only'"
                  v-model="config.value"
                ></skijasi-upload-image>
                <skijasi-upload-file
                  v-if="config.type === 'upload_file'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  :private-only="config.details !== null && config.details.type === 'private-only'"
                  :shares-only="config.details !== null && config.details.type === 'shares-only'"
                  v-model="config.value"
                ></skijasi-upload-file>
                <skijasi-color-picker
                  v-if="config.type === 'color_picker'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  v-model="config.value"
                ></skijasi-color-picker>

                <skijasi-upload-image-multiple
                  v-if="config.type === 'upload_image_multiple'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  :private-only="config.details !== null && config.details.type === 'private-only'"
                  :shares-only="config.details !== null && config.details.type === 'shares-only'"
                  v-model="config.value"
                ></skijasi-upload-image-multiple>
                <skijasi-upload-file-multiple
                  v-if="config.type === 'upload_file_multiple'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  :private-only="config.details !== null && config.details.type === 'private-only'"
                  :shares-only="config.details !== null && config.details.type === 'shares-only'"
                  size="10"
                  v-model="config.value"
                ></skijasi-upload-file-multiple>
                <skijasi-code-editor
                  v-if="config.type === 'code'"
                  :label="config.displayName"
                  :placeholder="config.value"
                  size="10"
                  v-model="config.value"
                ></skijasi-code-editor>

                <vs-col vs-lg="2">
                  <br />
                  <vs-button
                    color="danger"
                    type="relief"
                    @click.stop
                    @click="openConfirm(config.id)"
                    v-if="
                      $helper.isAllowed('delete_configurations') &&
                        config.canDelete
                    "
                    ><vs-icon icon="delete"></vs-icon>
                  </vs-button>
                </vs-col>
              </vs-row>
            </vs-tab>
          </vs-tabs>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button
                color="primary"
                type="relief"
                @click="submitMultipleEdit"
              >
                <vs-icon icon="save"></vs-icon> {{ $t("site.edit.multiple") }}
              </vs-button>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
import _ from "lodash";

export default {
  name: "SiteManagementBrowse",
  components: {},
  data: () => ({
    configurations: [],
    willDeleteConfigurationId: null,
  }),
  computed: {
    groupList: {
      get() {
        return [
          {
            label: 'Commerce Configuration',
            value: 'commercePanel'
          }
        ];
      },
    },
    adminPanelHeaderFontColor: {
      get() {
        return "#06bbd3";
      },
    },
  },
  mounted() {
    this.getConfigurationList();
  },
  methods: {
    openConfirm(id) {
      this.willDeleteConfigurationId = id;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.deleteConfiguration,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {
          this.willDeleteConfigurationId = null;
        },
      });
    },
    acceptAlert(color) {
      this.$vs.notify({
        color: "danger",
        title: this.$t("site.deletedImage.title"),
        text: this.$t("site.deletedImage.text"),
      });
    },
    getConfigurationList() {
      this.$openLoader();
      this.$api.skijasiCommerceConfiguration
        .browse()
        .then((response) => {
          this.$closeLoader();
          let configurations = response.data.configurations.map((data) => {
            try {
              data.details = JSON.parse(data.details);
              if (data.type === "hidden") {
                data.value = data.details.value ? data.details.value : "";
              }
              if (data.type === "switch") {
                data.value = data.value == "1" ? true : false;
              }
              const typeRequiredItems = [
                "checkbox",
                "radio",
                "select",
                "select_multiple",
              ];
              if (typeRequiredItems.includes(data.type)) {
                if (!data.details || !data.details.items) {
                  data.details = {};
                  data.details.items = [];
                  this.$vs.notify({
                    title: this.$t("alert.danger"),
                    text:
                      "Invalid options for Checkbox, Radio, Select, Select-multiple.",
                    color: "danger",
                  });
                }
              }
            } catch (error) {
              console.log(error);
            }
            return data;
          });
          this.configurations = JSON.parse(JSON.stringify(configurations));
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
    filterConfigurations(group) {
      return _.filter(this.configurations, ["group", group]);
    },
    deleteConfiguration() {
      this.$openLoader();
      this.$api.skijasiCommerceConfiguration
        .delete({
          id: this.willDeleteConfigurationId,
        })
        .then((response) => {
          this.$closeLoader();
          this.getConfigurationList();
          this.$store.commit("skijasi/FETCH_MENU");
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
    submitForm(config) {
      this.$openLoader();
      this.$api.skijasiCommerceConfiguration
        .edit(this.$caseConvert.snake(config))
        .then((response) => {
          this.$closeLoader();
          this.getConfigurationList();
          this.$store.commit("skijasi/FETCH_CONFIGURATION");
          this.$vs.notify({
            title: this.$t("alert.success"),
            text: this.$t("site.configUpdated"),
            color: "success",
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
    submitMultipleEdit() {
      this.$openLoader();
      this.$api.skijasiCommerceConfiguration
        .editMultiple({ configurations: this.configurations })
        .then((response) => {
          this.$closeLoader();
          this.getConfigurationList();
          this.$store.commit("skijasi/FETCH_CONFIGURATION");
          this.$vs.notify({
            title: this.$t("alert.success"),
            text: this.$t("site.configUpdated"),
            color: "success",
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
  },
};
</script>
