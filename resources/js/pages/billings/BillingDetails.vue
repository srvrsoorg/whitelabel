<template>
  <div class="container-fluid mx-auto my-5">
    <div class="bg-white rounded-md pb-1 shadow">
      <p class="text-xl flex gap-3 justify-between font-medium text-[#31363f]">
        Billing Details
      </p>
      <div class="my-4 px-4 grid gird-cols-1 md:grid-cols-2 gap-5">
        <div>
          <div>
            <label
              for="name"
              class="block text-tiny text-neutral-800 font-medium"
            >
              <span
                class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
              >
                Name
              </span>
            </label>
            <div class="mt-2">
              <input
                v-model="billingDetails.name"
                type="text"
                name="name"
                id="name"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="ServerAvatar Cloud Technologies PVT LTD"
              />
            </div>
            <small
              id="name_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
          <div class="sm:mt-3.5 mt-5">
            <label
              for="email"
              class="block text-tiny text-neutral-800 font-medium"
            >
              <span
                class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
              >
                Email Address
              </span>
            </label>
            <div class="mt-2">
              <input
                v-model="billingDetails.email"
                type="email"
                name="email"
                id="email"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="noreply@serveravatar.com"
              />
            </div>
            <small
              id="email_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
        </div>
        <div>
          <label
            for="address"
            class="block text-tiny text-neutral-800 font-medium"
          >
            <span
              class="after:content-['*'] after:ml-0.5 after:text-red-500 block font-medium leading-6 text-gray-900"
            >
              Address
            </span>
          </label>
          <div class="mt-2">
            <textarea
              v-model="billingDetails.address"
              rows="4"
              name="address"
              id="address"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-7 focus:ring-0"
            />
          </div>
          <small
            id="address_message"
            class="error_message text-red-500 text-xs"
          ></small>
        </div>
      </div>
      <h1 class="px-4 py-1 text-lg font-semibold">Tax Details</h1>
      <div class="mt-3 mb-5 px-4 grid gird-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="Gst" class="block text-tiny text-neutral-800 font-medium">
            GST Number
          </label>
          <div class="mt-2">
            <input
              v-model="billingDetails.tax_numbers[0].value"
              type="text"
              name="gst "
              id="tax_numbers"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter GST Number"
            />
          </div>
        </div>
        <div>
          <label
            for="VatId"
            class="block text-tiny text-neutral-800 font-medium"
          >
            VAT ID
          </label>
          <div class="mt-2">
            <input
              v-model="billingDetails.tax_numbers[1].value"
              type="text"
              name="vatId"
              id="tax_numbers"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter VAT ID"
            />
          </div>
        </div>
      </div>
      <div class="my-4 px-4">
        <div class="flex flex-wrap gap-3 justify-between items-center">
          <label for="other" class="block font-medium leading-6 text-gray-900"
            >Other</label
          >
          <button
            :class="[
              'px-4 py-1.5 rounded-md text-sm',
              isLightColor
                ? 'bg-custom-200 text-custom-700'
                : 'bg-custom-50 text-custom-500',
            ]"
            type="button"
            @click="addTax()"
          >
            <i class="las la-plus"></i>
            Add Custom Tax
          </button>
        </div>
        <span class="text-xs text-gray-500"
          >You Can Use Custom Tax Label and Tax Number Except GST Number and VAT
          ID Label and Number will display in the Device.</span
        >
        <div
          class="flex gap-3 items-center mt-5 w-full"
          v-for="(item, index) in billingDetails.tax_numbers.slice(2)"
          :key="index"
        >
          <div class="sm:flex flex-1 items-center gap-5">
            <div class="w-full">
              <input
                v-model="item.name"
                type="text"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter Label"
              />
            </div>
            <div class="w-full mt-5 sm:mt-0">
              <input
                v-model="item.value"
                type="text"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="Enter Number"
              />
            </div>
          </div>
          <div>
            <button type="button" @click="removeTax(index + 2)">
              <i class="las la-trash text-red-500 text-xl"></i>
            </button>
          </div>
        </div>
        <small
          id="tax_numbers_message"
          class="error_message text-red-500 text-xs"
        ></small>
      </div>
    </div>
    <div class="my-4 text-end">
      <Button :disabled="processing" @click="saveData">
        <i
          v-if="processing"
          class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
        ></i>
        {{ processing ? "Please wait" : "Save Settings" }}
      </Button>
    </div>
  </div>
</template>

<script>
export default {
  name: "BillingDetails",
  data() {
    return {
      processing: false,
      billingDetails: {
        name: "",
        email: "",
        address: "",
        tax_numbers: [
          { name: "GST Number", value: "" },
          { name: "VAT ID", value: "" },
        ],
      },
    };
  },
  methods: {
    addTax() {
      let obj = { name: "", value: "" };
      this.billingDetails.tax_numbers.push(obj);
    },

    removeTax(index) {
      this.billingDetails.tax_numbers.splice(index, 1);
    },
    async fetchBillingDetails() {
      await this.$axios
        .get("/billing-details")
        .then(({ data }) => {
          if (data.billingDetail !== null) {
            if (data.billingDetail.tax_numbers !== null) {
              const otherTaxs = data.billingDetail.tax_numbers.filter(
                (item) => item.name !== "GST Number" && item.name !== "VAT ID"
              );
              const notEmptyTaxs = otherTaxs.map((row) => {
                return {
                  name: row.name == "null" ? null : row.name,
                  value: row.value == "null" ? null : row.value,
                };
              });
              const GstNumber = data.billingDetail.tax_numbers.find(
                (item) => item.name === "GST Number"
              );
              const VatId = data.billingDetail.tax_numbers.find(
                (item) => item.name === "VAT ID"
              );

              if (GstNumber) {
                this.billingDetails.tax_numbers[0].value = GstNumber.value;
              }
              if (VatId) {
                this.billingDetails.tax_numbers[1].value = VatId.value;
              }
              if (notEmptyTaxs.length) {
                notEmptyTaxs.forEach((item) => {
                  const exists = this.billingDetails.tax_numbers.some(
                    (existingItem) =>
                      existingItem.name === item.name &&
                      existingItem.value === item.value
                  );
                  if (!exists) {
                    this.billingDetails.tax_numbers.push(item);
                  }
                });
              }
              this.billingDetails.tax_numbers =
                this.billingDetails.tax_numbers.filter((item) => {
                  return (
                    (item.name !== "" && item.value !== "") ||
                    item.name === "GST Number" ||
                    item.name === "VAT ID"
                  );
                });
              if (!otherTaxs.length) {
                let obj = { name: "", value: "" };
                this.billingDetails.tax_numbers.push(obj);
              }
            } else {
              this.billingDetails.tax_numbers =
                this.billingDetails.tax_numbers.filter((item) => {
                  return (
                    (item.name !== "" && item.value !== "") ||
                    item.name === "GST Number" ||
                    item.name === "VAT ID"
                  );
                });

              let obj = { name: "", value: "" };
              this.billingDetails.tax_numbers.push(obj);
            }

            this.billingDetails.name = data.billingDetail.name;
            this.billingDetails.email = data.billingDetail.email;
            this.billingDetails.address = data.billingDetail.address;
          } else {
            let obj = { name: "", value: "" };
            this.billingDetails.tax_numbers.push(obj);
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async saveData() {
      this.hideError();
      this.processing = true;
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
          this.error = [];
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
  created() {
    this.fetchBillingDetails();
  },
};
</script>
