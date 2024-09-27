<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <template
    v-if="!cloudPlatForms.data.length && !refreshing && $route.name === 'Plan'"
  >
    <div class="text-tiny p-3 rounded-md mb-5 bg-[#EFF6FF] text-[#308DEA]">
      <p>
        <b class="font-medium">Note:</b> You don’t have any cloud providers
        integrated yet. Please add one first, then you’ll be able to set up
        plans.
        <router-link
          class="underline"
          :to="{
            name: 'integrateCloudPlatForms',
          }"
        >
          Integrate Now
        </router-link>
      </p>
    </div>
  </template>
  <div class="xs:flex justify-between items-center gap-5">
    <div
      class="text-xl flex items-center whitespace-nowrap text-[#31363f] font-medium"
    >
      {{ title }}
    </div>

    <div class="flex gap-5 justify-end mt-2 xs:mt-0">
      <Button
        class="flex items-center"
        @click="openModal('create')"
        v-if="$route.name !== 'Plan' && !isAllProviderSet"
      >
        <span class="material-symbols-outlined text-[20px]"> add </span>
        <span>Add</span>
      </Button>
      <div>
        <button
          @click="fetchCloudPlatforms()"
          type="button"
          class="bg-[#F6F6F6] flex border-gray-200 border rounded-lg text-gray-500 p-1.5 text-center"
        >
          <span
            :class="[
              refreshing ? 'fa-spin' : '',
              'material-symbols-outlined text-[22px] ',
            ]"
          >
            refresh
          </span>
        </button>
      </div>
    </div>
  </div>
  <div class="h-full mt-5">
    <Table
      :head="cloudPlatForms.thead"
      v-if="cloudPlatForms.data && cloudPlatForms.data.length > 0"
    >
      <tr
        class="border-b border-primary text-[#2c3138] text-[13px]"
        v-for="providerData in cloudPlatForms.data"
        :key="providerData.id"
      >
        <td class="py-5 px-4 pl-10">
          <img
            v-if="CloudLogos"
            :src="CloudLogos[providerData.provider].logo"
            v-tooltip="CloudLogos[providerData.provider].title"
            class="w-7 h-auto"
          />
        </td>
        <td class="whitespace-nowrap py-5 px-4" v-if="$route.name === 'Plan'">
          {{ providerData.active_plans }}
        </td>
        <td class="whitespace-nowrap py-5 px-4" v-if="$route.name === 'Plan'">
          {{ providerData.plans_count }}
        </td>
        <td
          class="whitespace-nowrap text-center py-5 px-4"
          v-if="$route.name === 'Plan'"
        >
          <router-link
            :to="{
              name: 'setPlan',
              params: {
                id: providerData.id,
              },
            }"
          >
            <span
              :class="[
                'inline-flex text-center rounded-md px-4 py-2 font-medium',
                isLightColor
                  ? 'bg-custom-700 text-white'
                  : 'bg-custom-500 text-white',
              ]"
              >Set Plans</span
            >
          </router-link>
        </td>
        <td
          :class="$route.name !== 'plan' ? '' : 'text-center'"
          class="whitespace-nowrap py-5 px-4"
        >
          <Switch
            @update:modelValue="toggle(providerData.id, $event)"
            v-model="providerData.visible"
            :class="[
              providerData.visible
                ? isLightColor
                  ? 'bg-custom-700'
                  : 'bg-custom-500'
                : 'bg-gray-200',
              'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
            ]"
          >
            <span class="sr-only">Use setting</span>
            <span
              aria-hidden="true"
              :class="[
                providerData.visible ? 'translate-x-5' : 'translate-x-0',
                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
              ]"
            />
          </Switch>
        </td>
        <td class="whitespace-nowrap py-5 px-4" v-if="$route.name !== 'Plan'">
          <div class="flex items-center justify-center gap-3">
            <button
              v-tooltip="'Edit'"
              :class="[
                'text-green-600 bg-green-100 rounded-md p-1.5 items-center flex',
              ]"
              @click="openModal('update', providerData)"
            >
              <span class="material-symbols-outlined text-[20px]">
                edit_square
              </span>
            </button>
            <button
              v-tooltip="'Delete'"
              class="text-red-500 bg-red-50 rounded-md p-1.5 items-center justify-center flex"
              @click="deleteCloud(providerData)"
            >
              <span class="material-symbols-outlined text-[20px]">
                delete
              </span>
            </button>
          </div>
        </td>
      </tr>
    </Table>
    <template v-else>
      <TableSkeleton :heads="$route.name == 'Plan' ? 6 : 3" v-if="refreshing" />
      <Table :head="cloudPlatForms.thead" v-else>
        <tr>
          <td
            :colspan="$route.name == 'Plan' ? 6 : 3"
            class="text-center whitespace-nowrap py-4 px-4"
          >
            {{ refreshing ? "Please Wait" : "No Data Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>

  <Modal
    :show="isOpenModal"
    :modelIcon="'cloud_download'"
    :modalTitle="modelTitle"
    :titleClass="'border-b text-xl'"
    @closeModal="closeModal"
  >
    <div v-if="modalType === 'update'">
      <span class="my-5 text-tiny text-gray-600">
        Are you sure you want to update the cloud platform integration?
      </span>
      <p class="my-5 text-tiny font-medium">
        Kindly verify that the provided key is for the same account. This update
        will affect all related services.
      </p>
    </div>
    <div class="mb-4">
      <label
        for="provider"
        class="block text-tiny text-neutral-800 font-medium"
      >
        <span
          class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
        >
          Provider
        </span>
      </label>
      <div class="mt-2">
        <select
          v-if="modalType === 'create'"
          v-model="createCloud.provider"
          name="provider"
          id="provider"
          class="block w-full text-tiny rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 leading-6 focus:ring-0"
        >
          <option value="" disabled>Select a Provider</option>
          <option
            :value="provider.value"
            v-for="provider in filterProvider"
            :key="provider.value"
            :disabled="providers.includes(provider.value)"
          >
            {{ provider.name }}
          </option>
        </select>

        <input
          v-if="modalType === 'update'"
          v-model="createCloud.provider"
          type="text"
          name="provider"
          id="provider"
          :readonly="modalType === 'update'"
          :class="{ readonly: modalType === 'update' }"
          class="block w-full capitalize text-tiny rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 leading-6 focus:ring-0 bg-gray-100"
          placeholder="Enter Provider Name"
        />
      </div>
      <small
        id="provider_message"
        class="error_message text-red-500 text-xs"
      ></small>
    </div>
    <div class="my-4">
      <label
        for="access_key"
        class="block text-tiny text-neutral-800 font-medium"
      >
        <span
          class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
        >
          Access Key
        </span>
      </label>
      <div class="mt-2">
        <input
          v-model="createCloud.access_key"
          type="text"
          name="access_key"
          id="access_key"
          class="block w-full text-tiny rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 leading-6 focus:ring-0"
          placeholder="Enter Access Key"
        />
      </div>
      <small
        id="access_key_message"
        class="error_message text-red-500 text-xs"
      ></small>
    </div>
    <div class="my-4" v-if="createCloud.provider === 'lightsail'">
      <label
        for="access_secret"
        class="block text-tiny text-neutral-800 font-medium"
      >
        <span
          class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
        >
          Access Secret
        </span>
      </label>
      <div class="mt-2">
        <input
          v-model="createCloud.access_secret"
          type="text"
          name="access_secret"
          id="access_secret"
          class="block w-full text-tiny rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 leading-6 focus:ring-0"
          placeholder="Enter Access Secret"
        />
      </div>
      <small
        id="access_secret_message"
        class="error_message text-red-500 text-xs"
      ></small>
    </div>
    <div class="flex justify-end items-center">
      <Button
        :disabled="processing || createCloud.provider===''"
        @click="cloudPlateForm"
      >
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Save" }}
      </Button>
    </div>
  </Modal>

  <Confirmation
    :show="openConfimationModel"
    :showLoader="showLoader"
    :submitBtnTitle="`Yes, I'm sure`"
    :confirmationTitle="'Delete Cloud Platform'"
    :btnBgColor="'bg-red-500'"
    @confirm="deleteProvider"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="text-red-500 material-symbols-outlined text-[22px]"
        >cloud_download</span
      >
    </template>
    <template v-slot:content>
      <span class="text-gray-500 text-tiny"
        >Are you sure you want to delete the {{ providerData }} from the cloud
        service providers?
      </span>
    </template>
  </Confirmation>
</template>
<script>
import { Switch } from "@headlessui/vue";
import CloudLogos from "@/CloudProviders";
export default {
  name: "Plan",
  components: { Switch },
  data() {
    return {
      breadcrumb: this.setBreadcrumb(),
      CloudLogos: CloudLogos,
      pagination: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      isOpenModal: false,
      processing: false,
      modelTitle: "",
      modalType: "",
      visible: false,
      createCloud: {
        provider: "",
        access_key: "",
        access_secret: "",
      },
      filterProvider: [
        {
          name: "Amazon Lightsail",
          value: "lightsail",
        },
        {
          name: "Digitalocean",
          value: "digitalocean",
        },
        {
          name: "Vultr",
          value: "vultr",
        },
        {
          name: "Linode",
          value: "linode",
        },
        {
          name: "Hetzner",
          value: "hetzner",
        },
      ],
      cloudPlatForms: {
        thead: [],
        data: [],
      },
      pagination: null,
      refreshing: false,
      providerId: null,
      providerData: null,
      openConfimationModel: false,
      showLoader: false,
    };
  },
  computed: {
    title() {
      if (this.$route.name === "Plan") {
        return "Plan";
      } else {
        return "Cloud Platform";
      }
    },
    providers() {
      return this.cloudPlatForms.data.map((provider) => provider.provider);
    },
    isAllProviderSet() {
      let providers = [
        "digitalocean",
        "hetzner",
        "linode",
        "vultr",
        "lightsail",
      ];
      const availableProviders = this.cloudPlatForms.data.map(
        (providerObj) => providerObj.provider
      );
      return providers.every((provider) =>
        availableProviders.includes(provider)
      );
    },
  },
  watch: {
    $route: {
      handler(val) {
        this.setTableHead();
      },
      handler() {
        this.breadcrumb = this.setBreadcrumb();
        this.setTableHead();
      },
      immediate: true,
    },
  },
  methods: {
    setBreadcrumb() {
      let breadcrumb = {};
      if (this.$route.name === "Plan") {
        breadcrumb = {
          title: "Billing",
          icon: "lab_profile",
          pages: [
            {
              name: "Plan",
            },
          ],
        };
      } else {
        breadcrumb = {
          title: "Integrations",
          icon: "rule_settings",
          pages: [
            {
              name: "Cloud Platforms",
            },
          ],
        };
      }
      return breadcrumb;
    },
    setTableHead() {
      if (this.$route.name === "Plan") {
        this.cloudPlatForms.thead = [
          {
            title: "Provider",
            classes: " pl-10 items-center font-medium",
          },
          {
            title: "Active Plans",
            classes: "font-medium",
          },
          {
            title: "Total Plans",
            classes: "text-left font-medium",
          },
          {
            title: "Plans",
            classes: "text-center  font-medium",
          },
          {
            title: "Active",
            classes: "font-medium",
          },
        ];
      } else {
        this.cloudPlatForms.thead = [
          {
            title: "Provider",
            classes: " pl-10 items-center font-medium",
          },
          {
            title: "Active",
            classes: "items-center font-medium",
          },
          {
            title: "Actions",
            classes: "text-center font-medium",
          },
        ];
      }
    },
    openModal(type, idData) {
      if (type === "create") {
        this.modelTitle = "Create Cloud Platform";
        this.createCloud.provider = "";
      } else if (type === "update") {
        this.modelTitle = "Update Cloud Platform";
        this.providerData = idData;
        this.providerId = idData.id;
        this.createCloud = { ...idData };
      }
      this.isOpenModal = true;
      this.modalType = type;
    },
    closeModal() {
      this.isOpenModal = false;
      this.openConfimationModel = false;
      this.showLoader = false;
      this.createCloud = {
        provider: "",
        access_key: "",
        access_secret: "",
      };
    },
    deleteCloud(data) {
      this.providerId = data.id;
      this.providerData = data.provider;
      this.openConfimationModel = true;
    },

    async fetchCloudPlatforms(page = "") {
      this.refreshing = true;
      let url = `/admin/cloud-providers`;
      if (page !== "") {
        url = `${url}?page=${page}`;
      }
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.cloudPlatForms.data = data.cloudProvider;
        })
        .catch(({ response }) => {
          this.$toast.error(response.cloud_provider.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },

    handlePageChange(newPage) {
      this.getUsers(newPage);
    },
    handlePerPageChange() {
      this.getUsers(1);
    },

    async cloudPlateForm() {
      this.hideError();
      this.processing = true;
      await this.$axios
        .post(
          `/admin/cloud-providers?action=${this.modalType}`,
          this.createCloud
        )
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.fetchCloudPlatforms();
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
            this.closeModal();
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async deleteProvider() {
      this.showLoader = true;
      await this.$axios
        .delete(`/admin/cloud-providers/${this.providerId}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchCloudPlatforms();
          this.closeModal();
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
          this.closeModal();
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
    async toggle(id) {
      await this.$axios
        .patch(`/admin/cloud-providers/${id}`)
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          this.$toast.errro(response.data.message);
        });
    },
  },

  mounted() {
    this.fetchCloudPlatforms();
    this.setTableHead();
  },
};
</script>