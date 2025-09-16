<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <h1 class="text-xl font-medium">Settings</h1>
  <div class="my-5 sm:py-4 mt-8 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        lab_profile
      </span>
      <p class="font-medium">Billing Details</p>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 mt-5 sm:my-5 my-3 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-20 gap-4 items-center"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Name</label
            >
          </div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.name"
              type="text"
              id="name"
              placeholder="Enter Name"
              class="w-full block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <small
              id="name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>

      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="sm:col-span-3 col-span-12">
            <label
              for="city"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >
              City
            </label>
          </div>

          <div class="2xl:col-span-9 xl:col-span-7 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.city"
              type="text"
              name="city"
              id="city"
              class="block w-full rounded-md border border-primary focus:border-primary text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm py-1.5 leading-6 focus:ring-0"
              placeholder="City"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-9 sm:col-span-8 2xl:pl-0 xl:pl-5 col-span-12">
            <small
              id="city_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-3 sm:my-5 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-20 gap-4"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500"
              >Email Address</label
            >
          </div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.email"
              type="email"
              id="email"
              name="email"
              placeholder="Enter Your Email Address"
              class="block w-full rounded-md border border-primary disabled:opacity-50 focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
            <small
              id="email_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="sm:col-span-3 col-span-12">
            <label
              for="country"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >
              Country
            </label>
          </div>
          <div class="2xl:col-span-9 xl:col-span-7 sm:col-span-8 col-span-12">
            <select
              id="country_code"
              v-model="billingDetails.country_code"
              @change="updateCountryName()"
              placeholder="Select a Country"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            >
              <option value="" disabled>Select a Country</option>
              <template v-for="country in countries" :key="country.iso2">
                <option
                  :value="country.iso2"
                >
                  {{ country.country_name }}
                </option>
              </template>
            </select>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 md:pt-0 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-9 sm:col-span-8 2xl:pl-0 xl:pl-5 col-span-12">
            <small
              id="country_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-3 sm:my-5 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-20 gap-4"
    >
      <div
        class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1"
      >
        <div class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12">
          <label
            class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >Address</label
          >
        </div>
        <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
          <textarea
            v-model="billingDetails.address"
            rows="4"
            name="address"
            id="address"
            placeholder="Enter Your Address"
            class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          ></textarea>
          <small
            id="address_message"
            class="text-red-500 error_message text-xs"
          ></small>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-x-5 gap-4">
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1"
        >
          <div class="xl:pt-2 sm:col-span-3 col-span-12">
            <label
              for="region"
              class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >
              Region
            </label>
          </div>
          <div class="2xl:col-span-9 xl:col-span-7 sm:col-span-8 col-span-12">
            <select
              id="region_code"
              name="region_code"
              @change="updateRegionName()"
              v-model="billingDetails.region_code"
              placeholder="Select a Region"
              class="block w-full rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            >
              <option value="" disabled>Select a Region</option>
              <template
                v-for="(region, key) in getRegions"
                :key="key"
              >
                <option :value="region.state_iso2">
                  {{ region.state_name }}
                </option>
              </template>
            </select>
            <small
              id="region_code_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
        <div
          class="grid 2xl:grid-cols-12 xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1"
        >
          <div class="xl:pt-2 sm:col-span-3 col-span-12">
            <label
              for="country"
              class="block text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >
              Postal Code
            </label>
          </div>
          <div class="2xl:col-span-9 xl:col-span-7 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.postal_code"
              onwheel="this.blur()"
              type="text"
              name="Postal code"
              id="postal_code"
              placeholder="Enter Postal Code"
              class="w-full block rounded-md border border-primary focus:border-primary py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="postal_code_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <hr class="mx-5 xl:mx-14 my-6 xl:mt-10 text-[#CBD5E0]" />
    <div class="px-5 xl:px-14 xl:my-4">
      <div class="xs:flex justify-between items-center gap-3">
        <div class="">
          <label for="other" class="block font-medium leading-6"
            >Tax Identification Numbers</label
          >

          <p class="text-sm text-gray-500 w-full">
            You can use any Custom Tax Label and Tax Number Except GST Number
            and VAT ID. This Custom Tax Label and Number will display in the
            Invoice.
          </p>
          <small
            id="country_name_message"
            class="text-red-500 error_message text-xs"
          ></small>
        </div>
        <div>
          <Button
            :class="[
              '',
              isLightColor
                ? 'bg-custom-200 text-custom-700'
                : 'bg-custom-50 text-custom-500',
              'mt-3 sm:mt-0 flex justify-end items-center gap-1',
            ]"
            type="button"
            @click="addTax()"
          >
            <i class="las la-plus"></i>
            Add
          </Button>
        </div>
      </div>
    </div>
    <div
      v-for="(items, index) in billingDetails.tax_numbers"
      :key="index"
      class="grid sm:grid-cols-2 grid-cols-1 mt-4 xl:my-5 xl:px-14 px-5 xl:gap-x-30 2xl:gap-x-40 sm:gap-x-20 gap-4 items-center"
    >
      <div
        class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
      >
        <div
          class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12"
        >
          <label class="font-medium text-nowrap text-tiny">Label</label>
        </div>
        <div class="xl:col-span-8 2xl:col-span-9 sm:col-span-8 col-span-12">
          <input
            v-model="items.name"
            type="text"
            placeholder="Enter Label"
            class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          />
          <small
            id="Label_message"
            class="text-red-500 error_message text-xs"
          ></small>
        </div>
      </div>

      <div
        class="grid 2xl:grid-cols-12 xl:grid-cols-10 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
      >
        <div class="sm:col-span-3 col-span-12">
          <label class="font-medium text-tiny">Number</label>
        </div>
        <div class="2xl:col-span-9 xl:col-span-7 col-span-12 flex gap-5">
          <input
            v-model="items.value"
            type="text"
            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            placeholder="Enter Number"
          />
          <button type="button" @click="removeTax(index)">
            <i class="las la-trash text-red-500 text-[22px]"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="text-end rounded-md xl:px-14 px-5 sm:my-0 my-5">
      <Button :disabled="processing" @click="openBillingUpdate"> Save </Button>
    </div>
  </div>

  <Confirmation
    :show="billingConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Update Billing Details'"
    :btnBgColor="'bg-[#FFB74D]'"
    :submitBtnTitle="`Yes, I'm Sure`"
    @confirm="saveData"
    @closeModal="closeModal"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[#FFB74D] text-2xl">
        lab_profile
      </span>
    </template>
    <template #content>
      <p class="text-tiny text-gray-600">
        Are you sure you want to update your Billing Details?
      </p>
      <div class="my-5 text-tiny font-medium flex gap-3">
        <div>
          <span
            class="material-symbols-outlined bg-[#FFB74D] text-white rounded-full font-semibold text-[20px] p-0.5 flex items-center"
          >
            check
          </span>
        </div>
        <span>
          After updating your Billing Details new invoice will be created on
          updated details.
        </span>
      </div>
    </template>
  </Confirmation>
</template>

<script>
export default {
  name: "BillingDetails",
  data() {
    return {
      breadcrumb: {
        // title: "Billing",
        icon: "lab_profile",
        pages: [{ name: "Billing" },{ name: "Settings" }],
      },
      countries: [],
      processing: false,
      billingConfirmation: false,
      showLoader: false,
      billingDetails: {
        name: "",
        city: "",
        email: "",
        address: "",
        country: "",
        country_code: "",
        region_name: "",
        region_code: "",
        tax_numbers: [
          { name: "GST Number", value: "" },
          { name: "VAT ID", value: "" },
        ],
        postal_code: "",
      },
    };
  },
  computed: {
    getRegions() {
      let country = this.countries.find(
        (country) => country.country_name === this.billingDetails.country
      );
      return country ? country.states : [];
    },
  },
  methods: {
    addTax() {
      let obj = { name: "", value: "" };
      this.billingDetails.tax_numbers.push(obj);
    },

    removeTax(index) {
      this.billingDetails.tax_numbers.splice(index, 1);
    },
    async getCountries() {
       const loader = this.$loading.show()
      await this.$axios
        .get(`/countries`)
        .then(({ data }) => {
          this.countries = data.countries;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        }).finally(()=>{
          if (loader) {
            loader.hide()
          }
        });
    },
    updateCountryName() {
      this.billingDetails.country = "";
      this.billingDetails.region_code = ""
      this.billingDetails.region = ""
      this.billingDetails.country = this.countries.find(
        (country) => country.iso2 === this.billingDetails.country_code
      ).country_name;
    },
    updateRegionName() {
      this.billingDetails.region = "";
      let country_data = this.countries.find(
        (country_data) => country_data.iso2 === this.billingDetails.country_code
      );
      let region_data = country_data.states.find(region => region.state_iso2 === this.billingDetails.region_code)
      this.billingDetails.region =  region_data ? region_data.state_name : ''
    },
    async fetchBillingDetails() {
      await this.$axios
        .get("/billing-details")
        .then(({ data }) => {
          if (data.billingDetail !== null) {
            if (data.billingDetail.tax_numbers !== null) {
              this.billingDetails.tax_numbers =
                data.billingDetail.tax_numbers.map((row) => {
                  return {
                    name: row.name == "null" ? null : row.name,
                    value: row.value == "null" ? null : row.value,
                  };
                });
            } else {
              this.billingDetails.tax_numbers = [
                { name: "GST Number", value: "" },
                { name: "VAT ID", value: "" },
              ];
            }

            this.billingDetails.name = data.billingDetail.name;
            this.billingDetails.email = data.billingDetail.email;
            this.billingDetails.address = data.billingDetail.address;
            this.billingDetails.city = data.billingDetail.city;
            this.billingDetails.country = data.billingDetail.country;
            this.billingDetails.country_code = data.billingDetail.country_code;
            this.billingDetails.region = data.billingDetail.region;
            this.billingDetails.region_code = data.billingDetail.region_code;
            this.billingDetails.postal_code = data.billingDetail.postal_code;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    openBillingUpdate() {
      this.billingConfirmation = true;
    },
    closeModal() {
      this.billingConfirmation = false;
    },
    async saveData() {
      this.showLoader = true;
      this.hideError();
      let { otherTaxs, ...otherProperties } = this.billingDetails;
      otherTaxs = this.billingDetails.tax_numbers.filter(
        (item) => item.name !== "" && item.value !== ""
      );

      let final = {
        ...otherProperties,
        tax_numbers: otherTaxs.length ? [...otherTaxs] : null,
      };
      await this.$axios
        .patch("/billing-details", final)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.fetchBillingDetails();
          this.closeModal();
          this.error = [];
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
          }
          this.closeModal();
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
  },
  created() {
    this.fetchBillingDetails();
    this.getCountries();

  },
};
</script>

