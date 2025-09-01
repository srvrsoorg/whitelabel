<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <h1 class="text-xl font-medium">Taxes</h1>
  <div class="my-5 sm:py-4 mt-8 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined ',
        ]"
      >
        contract
      </span>
      <p class="font-medium">Default Tax Settings</p>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:gap-x-20 sm:gap-x-5 gap-3 px-7 my-5"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1 2xl:gap-0 items-center"
        >
          <div class="xl:col-span-2 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Tax (%)</label
            >
          </div>
          <div class="xl:col-span-7 sm:col-span-8 col-span-12">
            <input
              onwheel="this.blur()"
              type="number"
              v-model="tax.default[0].tax"
              name="tax_default_0_tax"
              id="tax_default_0_tax"
              placeholder="18"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-2 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-10 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="tax_default_0_tax_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 2xl:col-span-2 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Tax Label</label
            >
          </div>
          <div class="sm:col-span-7 col-span-12">
            <input
              type="text"
              v-model="tax.default[0].label"
              name="tax_default_0_label"
              id="tax_default_0_label"
              placeholder="Enter Label"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-2 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-10 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="tax_default_0_label_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <template
    v-for="(country, country_index) in tax.country"
    :key="country_index"
  >
    <div class="my-5 sm:pt-5 mt-8 ml-3 rounded-md bg-white border relative">
      <div
        class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
      >
        <span
          :class="[
            isLightColor ? 'text-custom-700' : 'text-custom-500',
            'material-symbols-outlined ',
          ]"
        >
          contract
        </span>
        <p class="font-medium">
          Country Specific Settings - {{ country.country }}
        </p>
      </div>
      <h1 class="px-7 mt-5 xl:py-2 sm:mt-0 text-sm text-gray-500">
        Set tax rates for specific countries and regions. Expand a country to
        customize or add new regional tax details.
      </h1>
      <template
        v-for="(region, region_index) in country.regions"
        :key="region_index"
      >
        <div
          class="grid sm:grid-cols-3 grid-cols-1 xl:gap-x-12 2xl:gap-20 sm:gap-x-5 gap-3 px-7 xl:my-5 my-4"
        >
          <div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
            >
              <div
                class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12"
              >
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Tax (%)</label
                >
              </div>
              <div
                class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
              >
                <input
                  onwheel="this.blur()"
                  type="number"
                  v-model="tax.country[country_index].regions[region_index].tax"
                  :name="`tax_country_${country_index}_regions_${region_index}_tax`"
                  :id="`tax_country_${country_index}_regions_${region_index}_tax`"
                  placeholder="18"
                  class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                />
              </div>
            </div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1"
            >
              <div
                class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
              ></div>
              <div
                class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
              >
                <small
                  :id="`tax_country_${country_index}_regions_${region_index}_tax_message`"
                  class="text-red-500 error_message text-xs"
                ></small>
              </div>
            </div>
          </div>
          <div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-9 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
            >
              <div class="xl:col-span-3 sm:col-span-3 col-span-12">
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Tax Label</label
                >
              </div>
              <div
                class="xl:col-span-6 2xl:col-span-9 sm:col-span-8 col-span-12"
              >
                <input
                  type="text"
                  v-model="
                    tax.country[country_index].regions[region_index].label
                  "
                  :name="`tax_country_${country_index}_regions_${region_index}_label`"
                  :id="`tax_country_${country_index}_regions_${region_index}_label`"
                  placeholder="Enter Tax Label"
                  class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                />
              </div>
            </div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1"
            >
              <div
                class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
              ></div>
              <div
                class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
              >
                <small
                  :id="`tax_country_${country_index}_regions_${region_index}_label_message`"
                  class="text-red-500 error_message text-xs"
                ></small>
              </div>
            </div>
          </div>
          <div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
            >
              <div
                class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12"
              >
                <label
                  class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
                  >Region</label
                >
              </div>
              <div
                class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12 flex items-center gap-x-3"
              >
                <div class="w-full">
                  <select
                    :id="`tax_country_${country_index}_regions_${region_index}_region`"
                    :name="`tax_country_${country_index}_regions_${region_index}_region`"
                    @change="updateRegionName(country_index, region_index)"
                    v-model="
                      tax.country[country_index].regions[region_index]
                        .region_code
                    "
                    placeholder="Select a Country"
                    class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                  >
                    <option value="all">All Regions</option>
                    <template
                      v-for="(region, key) in getRegions(country.country)"
                      :key="key"
                    >
                      <option :value="region.state_iso2">
                        {{ region.state_name }}
                      </option>
                    </template>
                  </select>
                </div>
                <div class="sm:min-w-6">
                  <button
                    @click="removeRegion(country_index, region_index)"
                    type="button"
                    v-if="region.region_code !== 'all'"
                  >
                    <i class="las la-trash text-red-500 text-[22px]"></i>
                  </button>
                </div>
              </div>
            </div>
            <div
              class="grid 2xl:grid-cols-12 xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1"
            >
              <div
                class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
              ></div>
              <div
                class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12"
              >
                <small
                  :id="`tax_country_${country_index}_regions_${region_index}_region_message`"
                  class="text-red-500 error_message text-xs"
                ></small>
              </div>
            </div>
          </div>
        </div>
      </template>
      <div class="my-5 xl:my-7 px-7 xs:flex justify-between items-center gap-5">
        <button
          @click="removeCountry(country_index)"
          class="bg-red-600 flex items-center gap-0.5 text-white w-full xs:w-fit border font-medium text-sm border-red-500 px-3 justify-center py-1.5 rounded-md"
        >
        <span class="material-symbols-outlined text-[22px]"> delete </span>
          Delete Country
        </button>
        <Button
          @click="addRegion(country.country_code)"
          class="flex items-center gap-1 mt-5 xs:mt-0 w-full xs:w-fit justify-center"
        >
          <span class="material-symbols-outlined text-[18px]"> add </span>
          <span> Add Region </span>
        </Button>
      </div>
    </div>
  </template>

  <div class="my-5 flex justify-end items-center gap-5">
    <Button :disabled="processing" @click="saveTaxes">
      <i
        v-if="processing"
        class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
      ></i>
      {{ processing ? "Please wait" : "Save" }}
    </Button>
    <Button class="flex items-center gap-1" @click="openModal">
      <span class="material-symbols-outlined text-[18px]"> add </span>
      <span> Add Country </span>
    </Button>
  </div>

  <Modal
    :show="open"
    @closeModal="closeModal"
    :modalTitle="'Add New Country'"
    :modelIcon="'contract'"
  >
    <div class="">
      <label
        for="country"
        class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
        >Country
      </label>
      <select
        id="country"
        v-model="payload.country_code"
        @change="updateCountryName()"
        placeholder="Select a Country"
        class="block mt-2 w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
      >
        <option value="">Select a Country</option>
        <template v-for="country in countries" :key="country.iso2">
          <option
            :disabled="taxCountryCodes.includes(country.iso2)"
            :value="country.iso2"
          >
            {{ country.country_name }}
          </option>
        </template>
      </select>
      <small
        class="text-red-500 text-xs error_message"
        id="country_message"
      ></small>
    </div>

    <div class="my-5">
      <label
        for="rate"
        class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
      >
        Default Tax (%)
      </label>
      <input
        onwheel="this.blur()"
        type="number"
        name="tax"
        id="tax"
        v-model="payload.tax"
        class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        placeholder="10"
      />
      <small
        class="text-red-500 text-xs error_message"
        id="tax_message"
      ></small>
    </div>
    <div class="my-5">
      <label
        for="label"
        class="text-tiny font-medium after:content-['*'] after:ml-0.5 after:text-red-500"
      >
        Default Tax Label
      </label>
      <input
        type="text"
        name="label"
        id="label"
        v-model="payload.label"
        class="w-full mt-2 block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        placeholder="Enter Tax Label"
      />
      <small
        class="text-red-500 text-xs error_message"
        id="label_message"
      ></small>
    </div>
    <div class="flex flex-row-reverse bg-white rounded-md mt-3 gap-4">
      <div class="text-end rounded-md">
        <Button :disabled="processing" @click="saveCountry">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Add" }}
        </Button>
      </div>
      <button @click="closeModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
        Cancel
      </button>
    </div>
  </Modal>
</template>
<script>
export default {
  name: "Taxes",
  data() {
    return {
      open: false,
      processing: false,
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [
          {
            name: "Billing",
          },
          {
            name: "Taxes",
          },
        ],
      },
      countries: [],
      payload: {
        country: "",
        country_code: "",
        region: "All Regions",
        region_code: "all",
        tax: "",
        label: "",
      },
      tax: {
        default: [
          {
            tax: "",
            label: "",
          },
        ],
        country: [],
      },
    };
  },
  computed: {
    taxCountryCodes() {
      return this.tax.country.map((country) => country.country_code);
    },
  },
  created() {
    this.getCountries();
    this.getTaxes();
  },
  methods: {
    openModal() {
      this.open = true;
    },
    closeModal() {
      this.open = false;
      this.payload.label = "";
      this.payload.tax = "";
      this.payload.country_code = "";
    },
    updateCountryName() {
      this.payload.country = "";
      this.payload.country = this.countries.find(
        (country) => country.iso2 === this.payload.country_code
      ).country_name;
    },
    updateRegionName(country, region) {
      let countryData = this.tax.country[country];
      let stateData = this.tax.country[country].regions[region];
      let country_data = this.countries.find(
        (country_data) => country_data.iso2 === countryData.country_code
      );
      if (stateData.region_code == "all") {
        this.tax.country[country].regions[region].region = "All Regions";
      } else {
        this.tax.country[country].regions[region].region =
          country_data.states.find(
            (state) => state.state_iso2 === stateData.region_code
          ).state_name;
      }
    },
    getRegions(country_name) {
      let country = this.countries.find(
        (country) => country.country_name === country_name
      );
      return country ? country.states : [];
    },
    addRegion(country_code) {
      const countryIndex = this.tax.country.findIndex(
        (country) => country.country_code === country_code
      );
      this.tax.country[countryIndex].regions.push({
        country_code: country_code,
        country: this.countries.find((country) => country.iso2 === country_code)
          .country_name,
        region: "All Regions",
        region_code: "all",
        tax: "",
        label: "",
      });
    },
    removeRegion(countryIndex, regionIndex) {
      this.tax.country[countryIndex].regions.splice(regionIndex, 1);
    },
    removeCountry(countryIndex) {
      this.tax.country.splice(countryIndex, 1);
      this.saveTaxes();
    },
    saveCountry() {
      this.hideError();
      if (
        this.payload.country == "" ||
        this.payload.country_code == "" ||
        this.payload.tax == "" ||
        this.payload.label == ""
      ) {
        if (this.payload.country == "" || this.payload.country_code == "") {
          $(`#country_message`).html("Country field is required.").fadeIn();
        }
        if (this.payload.tax == "") {
          $(`#tax_message`).html("Default Tax field is required.").fadeIn();
        }
        if (this.payload.label == "") {
          $(`#label_message`).html("Tax Label field is required.").fadeIn();
        }
      } else {
        this.addCountry(this.payload);
      }
    },
    resetData() {
      this.payload.country = "";
      this.payload.country_code = "";
      this.payload.region = "All Regions";
      this.payload.region_code = "all";
      this.payload.tax = "";
      this.payload.label = "";
    },
    addCountry(payload) {
      let obj = {
        country: payload.country,
        country_code: payload.country_code,
        regions: [
          {
            region: payload.region,
            region_code: payload.region_code,
            tax: payload.tax,
            label: payload.label,
          },
        ],
      };
      this.tax.country.push(obj);
      this.saveTaxes();
    },
    async getCountries() {
      await this.$axios
        .get(`/countries`)
        .then(({ data }) => {
          this.countries = data.countries;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async getTaxes() {
      await this.$axios
        .get(`/admin/tax`)
        .then(({ data }) => {
          if (data.tax.default && data.tax.default.length) {
            this.tax.default = data.tax.default;
          }
          if (data.tax.country && data.tax.country.length) {
            this.tax.country = data.tax.country;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async saveTaxes() {
      this.processing = true;
      this.hideError();

      const countries = this.tax.country.map((country) => {
        country.regions = country.regions.map((region) => {
          return {
            region: region.region_code == "all" ? "All Regions" : this.countries.find((countryData) => countryData.iso2 === region.country_code).states.find((state) => state.state_iso2 === region.region_code).state_name,
            region_code: region.region_code,
            country: country.country,
            country_code: country.country_code,
            tax: region.tax,
            label: region.label,
          };
        });
        return country;
      });
      const filteredData = countries.filter(item => item.regions.length > 0);
      this.tax.country = filteredData;

      await this.$axios
        .post(`/admin/tax`, {
          tax: this.tax,
        })
        .then(({ data }) => {
          this.getTaxes();
          this.resetData();
          this.$toast.success(data.message);
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
            this.getTaxes();
          }
        })
        .finally(() => {
          this.closeModal();
          this.processing = false;
        });
    },
  },
};
</script>
