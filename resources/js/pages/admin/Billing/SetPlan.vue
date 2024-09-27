<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <p class="text-xl font-medium pt-3 pb-5 text-[#31363f]">Set Plans</p>
  <div class="w-fit">
    <div
      class="bg-white rounded-md border border-gray-200 h-full flex items-center gap-7 px-5 py-3"
      v-if="cloudProvider"
    >
      <div>
        <h2 class="text-tiny text-gray-500">Server Provider</h2>
        <h5 class="text-lg font-medium">
          {{ CloudLogos[cloudProvider.provider].title }}
        </h5>
      </div>
      <img
        v-if="CloudLogos"
        :src="CloudLogos[cloudProvider.provider].logo"
        class="w-14 h-auto"
        alt=""
      />
    </div>
  </div>

  <div
    class="grid md:grid-cols-2 grid-cols-1 my-5 mb-2 xl:gap-x-32 md:gap-x-20 gap-4"
    v-if="regions.length > 0"
  >
    <div
      class="grid grid-cols-12 sm:grid-cols-9 md:grid-cols-8 xl:grid-cols-10 sm:gap-3 gap-2"
    >
      <div
        class="xl:col-span-3 sm:pt-2 sm:col-span-4 md:col-span-4 col-span-12"
      >
        <label
          for="Server Location"
          class="block text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
        >
          Server Location
        </label>
      </div>
      <div class="xl:col-span-7 col-span-12">
        <select
          name=""
          v-model="this.plans.region"
          @change="fetchSizes"
          id=""
          class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="" disabled selected>Select Server Location</option>
          <option
            :value="region.value"
            v-for="region in regions"
            :key="region"
            :disabled="!region.available"
          >
            {{ region.name }}
            <span v-if="isInPlan(region.value)" class="text-blue-500"
              >(in Plan)</span
            >
          </option>
        </select>
        <small
          id="region_name_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
    </div>
    <div
      v-if="sizes.length > 0"
      class="grid xl:grid-cols-10 sm:grid-cols-6 md:grid-cols-9 grid-cols-12 sm:gap-3 gap-2"
    >
      <div class="sm:pt-2 sm:col-span-3 md:col-span-4 col-span-12">
        <label
          for="Server Instance Size"
          class="block text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
        >
          Server Instance Type
        </label>
      </div>
      <div class="xl:col-span-6 col-span-12">
        <select
          name=""
          v-model="this.currentTab"
          id=""
          class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0 custom-select"
        >
          <option :value="size.name" v-for="size in sizes" :key="size">
            {{ size.name }}
          </option>
        </select>
        <small
          id="password_message"
          class="text-red-500 error_message text-xs"
        ></small>
      </div>
    </div>
  </div>
  <div class="h-full mt-5">
    <Table :head="thead" :bodyPadding="'px-5'" v-if="sizes.length > 0">
      <tr
        class="border-b border-gray-200 text-[#2c3138] text-[14px]"
        v-for="(size, index) in sizeList"
        :key="size.slug"
      >
        <td
          class="whitespace-nowrap px-4 py-4 pl-10 truncate max-w-[300px]"
          v-if="
            cloudProvider.provider == 'hetzner' ||
            cloudProvider.provider == 'linode'
          "
        >
          {{ size.label ? size.label : "-" }}
        </td>
        <td :class="[cloudProvider.provider == 'hetzner' || cloudProvider.provider == 'linode' ? 'px-4 py-4' : 'py-5 px-4 pl-10' ,'whitespace-nowrap truncate max-w-[300px]']">
          {{ size.cpu_core }}
        </td>
        <td class="whitespace-nowrap py-5 px-4">
          {{ size.ram_size_in_mb }} MB
        </td>
        <td class="whitespace-nowrap py-5 px-4 truncate">
          {{ size.disk_size_in_gb }} GB
        </td>
        <td class="whitespace-nowrap py-5 px-4 truncate">
          {{ size.bandwidth }}
          {{ cloudProvider.provider === "hetzner" ? "TB" : "GB" }}
        </td>
        <td class="whitespace-nowrap px-4 py-4">
          {{ cloudProvider.provider === "hetzner" ? "€" : "$"
          }}{{ size.price }} / Monthly
        </td>
        <td class="whitespace-nowrap px-4 py-4">
          <input
            type="text"
            name="amount"
            :id="`amount_${index}`"
            v-model="size.amount"
            class="border text-sm rounded-lg border-primary focus:border-primary-500 focus:ring-0 block p-2.5"
          />
        </td>
        <td class="px-4 py-4">
          <Switch
            v-model="size.is_visible"
            :class="[
              size.is_visible
                ? isLightColor
                  ? 'bg-custom-700'
                  : 'bg-custom-500'
                : 'bg-gray-300',
              'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0',
            ]"
          >
            <span class="sr-only">Use setting</span>
            <span
              aria-hidden="true"
              :class="[
                size.is_visible ? 'translate-x-5' : 'translate-x-0',
                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
              ]"
            />
          </Switch>
        </td>
      </tr>
    </Table>

    <div class="mt-5 text-end" v-if="plans.region">
      <Button @click="save" :disabled="processing">
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Save" }}
      </Button>
    </div>
  </div>
</template>

<script>
import CloudLogos from "@/CloudProviders";
import {
  Switch,
  RadioGroup,
  RadioGroupLabel,
  RadioGroupOption,
} from "@headlessui/vue";
import { CheckCircleIcon } from "@heroicons/vue/20/solid";

export default {
  name:"setPlan",
  data() {
    return {
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [
          {
            name: "Plan",
          },
          {
            name: "Set Plans",
          },
        ],
      },
      thead: [
        { title: "Core(s)", classes: "whitespace-nowrap" },
        { title: "Memory", classes: "" },
        { title: "Storage", classes: "" },
        { title: "Bandwidth", classes: "" },
        { title: "$/Monthly", classes: "whitespace-nowrap" },
        {
          title: `${window.siteSettings.currency_symbol}/Monthly`,
          classes: "whitespace-nowrap",
          tooltip:
            "This input field allows setting a custom price that users will be charged when they create a server. The value entered here will be shown to users instead of the actual provider price and will be used for billing purposes.",
        },
        { title: "Active", classes: "" },
      ],
      CloudLogos: CloudLogos,
      cloudProvider: null,
      regions: [],
      sizes: [],
      plans: { region: "" },
      serverPlans: [],
      currentTab: "",
      processing: false,
    };
  },
  components: {
    CloudLogos,
    RadioGroup,
    RadioGroupLabel,
    RadioGroupOption,
    CheckCircleIcon,
    Switch,
  },
  computed: {
    cloudProviderId() {
      return this.$route.params.id;
    },
    sizeList() {
      const selectedSize = this.sizes.find(
        (size) => size.name === this.currentTab
      );
      return selectedSize ? selectedSize.list : [];
    },
  },
  created() {
    this.fetchCloudProvider();
    this.fetchPlans();
    this.fetchRegions();
    this.fetchSizes();
  },
  methods: {
    isInPlan(regionValue) {
      return this.serverPlans.some(
        (plan) =>
          plan.region === regionValue &&
          plan.plans.some((subPlan) => subPlan.visible === 1)
      );
    },
    fetchCloudProvider() {
      this.$axios
        .get(`/admin/cloud-providers/${this.cloudProviderId}`)
        .then(({ data }) => {
          this.cloudProvider = { id: this.cloudProviderId, ...data };
          if (
            this.cloudProvider.provider == "hetzner" ||
            this.cloudProvider.provider == "linode"
          ) {
            this.thead.splice(1, 0, { title: "Name", classes: "" });
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    fetchPlans() {
      this.$axios
        .get(`/admin/cloud-providers/${this.cloudProviderId}/plans`)
        .then(({ data }) => {
          this.serverPlans = data.plans;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    fetchRegions() {
      this.$axios
        .get(`/admin/cloud-providers/${this.cloudProviderId}/regions`)
        .then(({ data }) => {
          this.regions = data;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async fetchSizes() {
      if (this.plans.region == "") return;
      this.currentTab = "";
      this.sizes = [];
      const loader = this.$loading.show();
      this.$axios
        .get(
          `/admin/cloud-providers/${this.cloudProviderId}/sizes?region=${this.plans.region}`
        )
        .then(({ data }) => {
          var region = this.serverPlans.find((row) => {
            return row.region == this.plans.region;
          });
          if (data.sizes.length) {
            var formattedData = data.sizes.map((size) => {
              var sizes = size.list.map((row) => {
                if (region && region.plans) {
                  var plan = region.plans.find((selectedPlan) => {
                    return selectedPlan.size_slug == row.slug;
                  });

                  if (plan) {
                    row.amount = plan.price_per_month;

                    if (plan.visible) {
                      row.is_visible = true;
                    } else {
                      row.is_visible = false;
                    }

                    if (plan.is_recommended) {
                      row.is_recommended = true;
                    } else {
                      row.is_recommended = false;
                    }

                    if (plan.is_popular) {
                      row.is_popular = true;
                    } else {
                      row.is_popular = false;
                    }
                  } else {
                    row.amount = row.price;
                    row.is_visible = false;
                    row.is_popular = false;
                    row.is_recommended = false;
                  }
                } else {
                  row.amount = row.price;
                  row.is_visible = false;
                  row.is_popular = false;
                  row.is_recommended = false;
                }
                return row;
              });

              return {
                name: size.name,
                list: sizes,
              };
            });

            this.sizes = formattedData;
            this.currentTab = formattedData[0].name;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          loader.hide();
        });
    },
    save() {
      this.processing = true;
      const sizesArr = this.sizes.flatMap((size) => size.list);
      const region = this.regions.find(
        (row) => row.value === this.plans.region
      );
      const payload = {
        provider: this.cloudProvider.provider,
        region: this.plans.region,
        region_code: region?.country_code || "",
        plans: sizesArr,
      };

      this.$axios
        .post(`/admin/cloud-providers/${this.cloudProviderId}/plans`, payload)
        .then(({ data }) => {
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
          this.fetchPlans();
        }); 
    },
  },
};
</script>