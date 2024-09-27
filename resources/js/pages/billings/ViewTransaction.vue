<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="container-fluid mx-auto">
    <div class="flex justify-between items-center mb-5">
      <div class="text-[#2c3138] font-medium text-xl">Tax Invoice</div>
      <Button
        @click="print()"
        :class="[
          textColorClass,
          'bg-custom-500 px-4 py-1.5 rounded-md flex items-center gap-1 justify-center',
        ]"
      >
        <span class="material-symbols-outlined text-[18px]"> print </span>
        Print
      </Button>
    </div>

    <div
      class="px-4 py-8 sm:px-6 lg:px-8 bg-white border border-primary rounded-lg"
      id="printableArea"
      v-if="transaction"
    >
      <div
        class="xs:flex justify-between items-center gap-2 font-medium text-xl"
      >
        <img class="h-8 w-auto" v-if="logo" :src="logo" :alt="app_name" />
        <img
          v-else
          class="h-8 w-auto"
          src="/logo/whitelabel-logo.png"
          :alt="app_name"
        />
        <p class="text-xl font-semibold mt-4 xs:mt-0">TAX INVOICE</p>
      </div>
      <div class="justify-between mt-5 gap-5 sm:flex">
        <div class="">
          <div class="font-medium text-lg py-0.5">
            {{
              transaction.company_address && transaction.company_address.name
                ? transaction.company_address.name
                : ""
            }}
          </div>
          <div class="text-gray-500 text-tiny max-w-[400px]">
            {{
              transaction.company_address && transaction.company_address.address
                ? transaction.company_address.address
                : ""
            }}
          </div>
        </div>
        <div>
          <div
            class="my-2.5"
            v-for="(taxNumber, index) in transaction.company_tax_numbers"
            :key="index"
          >
            <div class="xs:flex justify-between gap-5">
              <span class="text-tiny font-medium block xl:min-w-32">
                {{ taxNumber.name ? taxNumber.name : "" }}
              </span>
              <span class="text-gray-500 text-tiny">
                {{ taxNumber.value ? taxNumber.value : "" }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div
        class="rounded-md bg-gray-100 print:bg-gray-100 print:border-t print:border-gray-300 my-5 p-4 xl:p-6"
      >
        <div class="md:flex justify-between gap-5 gap-x-10">
          <div class="">
            <p class="text-lg font-medium text-[#2c3138]">Invoice Details</p>
            <div class="mt-2">
              <div class="text-[#2c3138] font-medium gap-5 sm:flex py-1">
                <p
                  class="text-gray-500 text-tiny text-nowrap sm:grid-cols-1 min-w-36"
                >
                  Transaction ID
                </p>
                <span
                  class="text-tiny xl:max-w-[300px] sm:max-w-[200px] w-full break-all sm:truncate sm:line-clamp-none line-clamp-1"
                  v-tooltip="transaction.transaction_id"
                  >{{
                    transaction.transaction_id
                      ? transaction.transaction_id
                      : "-"
                  }}</span
                >
              </div>
              <div class="font-medium sm:flex gap-5 items-center py-1">
                <p class="text-gray-500 text-tiny whitespace-nowrap min-w-36">
                  Invoice Number
                </p>

                <span class="text-tiny">
                  {{ transaction.id ? transaction.id : "" }}</span
                >
              </div>
              <div class="font-medium sm:flex gap-5 items-center py-1">
                <p class="text-gray-500 text-tiny whitespace-nowrap min-w-36">
                  Invoice Date
                </p>
                <span class="text-tiny">
                  {{ transaction.paid_at ? transaction.paid_at : "" }}</span
                >
              </div>
            </div>
          </div>
          <div class="mt-5 md:mt-0">
            <p
              class="text-lg font-medium justify-end flex"
              v-if="transaction && transaction.address"
            >
              Billed to
            </p>
            <div class="xs:text-end mt-2" v-if="transaction">
              <div
                class="text-[#2c3138] font-medium justify-end flex text-tiny py-0.5"
              >
                {{ transaction.address?.name || "" }}
              </div>

              <div
                class="text-tiny text-gray-500 md:max-w-[400px] justify-end flex py-0.5"
              >
                {{ transaction.address?.address || "" }}
              </div>
              <div>
                <div
                  class="my-2.5 justify-between text-[16px] gap-5 xs:flex items-center"
                  v-for="(taxNumber, index) in transaction.tax_numbers"
                  :key="index"
                >
                  <span class="text-[#2c3138] text-[14px] block font-medium">
                    {{ taxNumber.name || "-" }}
                  </span>
                  <span
                    class="mt-1 text-gray-500 justify-self-end text-[15px] font-medium"
                  >
                    {{ taxNumber.value || "-" }}
                  </span>
                </div>
              </div>

              <!-- Display Status Badge -->
              <div
                :class="
                  transaction.billingDetail
                    ? 'mt-10'
                    : 'flex justify-between md:gap-10 gap-5 mt-2'
                "
              >
                <span class="text-[#2c3138] font-medium text-[15px]">
                  Status
                </span>

                <Badge
                  v-if="transaction.status == 1"
                  badgeTitle="Paid"
                  variant="success"
                  :rounded="true"
                />
                <Badge
                  v-else
                  badgeTitle="Unpaid"
                  variant="danger"
                  :rounded="true"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <hr class="mt-10" />

      <div
        class="grid xl:grid-cols-9 sm:grid-cols-12 grid-cols-1 gap-5 sm:gap-x-10 mt-5"
      >
        <div class="xl:col-span-6 md:col-span-7 sm:col-span-6">
          <span class="text-[#2c3138] font-medium text-lg">Description</span>
          <div class="my-2">
            <span class="text-gray-500 text-[15px] break-words">{{
              transaction.service
            }}</span>
          </div>
        </div>
        <div class="xl:col-span-3 md:col-span-5 sm:col-span-6">
          <div class="text-[#2c3138] font-medium flex justify-end gap-4">
            <div class="">
              <span class="text-[#2c3138] font-medium text-[18px]"
                >Net Amount</span
              >
              <div class="">
                <span class="text-[#2c3138] text-[16px] py-2 flex justify-end">
                  {{ formatCurrency(transaction.base_amount) }}
                </span>
              </div>
            </div>
          </div>
          <div class="border-dashed border-y-2 sm:pl-5 pb-3 w-full">
            <div class="pt-3 flex gap-5 justify-between text-tiny">
              <p class="text-gray-500 truncate font-medium">Sub Total</p>
              <div class="text-[#2c3138] font-medium text-end">
                {{ formatCurrency(transaction.base_amount) }}
              </div>
            </div>
            <template
              v-if="
                transaction.tax_details && transaction.tax_details.length > 0
              "
            >
              <div
                class="pt-3 flex gap-10 text-tiny"
                v-for="(tax, index) in transaction.tax_details"
                :key="index"
              >
                <div
                  class="w-1/2 text-gray-500 truncate font-medium"
                  v-tooltip="`${tax.label} (${tax.tax}%)`"
                >
                  {{ tax.label }} ({{ tax.tax }}%)
                </div>
                <div class="w-1/2 text-[#2c3138] font-medium text-end">
                  {{ formatCurrency(tax.tax_amount) }}
                </div>
              </div>
            </template>
            <div class="pt-3 flex justify-between gap-5 text-tiny">
              <div class="text-gray-500 truncate font-medium">Discount</div>
              <div class="text-[#2c3138] font-medium text-end">
                {{ formatCurrency(transaction.discount_amount) }}
              </div>
            </div>
          </div>
          <div class="pt-3 font-medium flex justify-between gap-5 w-full">
            <div
              @click="fetchBilling"
              class="text-[#2c3138] sm:pl-5 text-[16px]"
            >
              Total Amount
            </div>
            <div
              :class="[
                'text-[16px] font-semibold text-end',
                isLightColor ? 'text-custom-700' : 'text-custom-500',
              ]"
            >
              {{ formatCurrency(transaction.final_amount) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "viewTransaction",
  data() {
    return {
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [{ name: "Transactions" }, { name: "Invoice" }],
      },
      transaction: null,
      thead: [
        "#",
        "Description",
        { title: "Net Amount", classes: ["text-right"] },
      ],
    };
  },
  created() {
    this.fetchTransaction();
  },
  methods: {
    fetchTransaction() {
      this.$axios
        .get(`/user/transactions/${this.$route.params.key}`)
        .then(({ data }) => {
          this.transaction = data.transaction;
        })
        .catch((error) => {
          if (error.response) {
            this.$toast.error(error.response.data.message);
          }
        });
    },

    print() {
      var printContents = document.getElementById("printableArea").innerHTML;
      var iframe = document.createElement("iframe");
      iframe.style.position = "absolute";
      iframe.style.width = "0px";
      iframe.style.height = "0px";
      iframe.style.border = "none";

      document.body.appendChild(iframe);

      var doc = iframe.contentWindow.document;
      doc.open();
      doc.write(`
          <html>
          <head>
              <style>
                  @media print {
                      @page {
                          margin: 30px;
                      }
                      body::before {
                          content: "";
                          display: none;
                      }
                      body::after {
                          content: "";
                          display: none;
                      }
                  }
                  body {
                      padding: 30px;
                  }
              </style>
      `);

      // Copy all stylesheets from the main document
      Array.from(document.styleSheets).forEach((styleSheet) => {
        if (styleSheet.href) {
          doc.write(
            `<link rel="stylesheet" type="text/css" href="${styleSheet.href}">`
          );
        } else {
          try {
            const css = Array.from(styleSheet.cssRules)
              .map((rule) => rule.cssText)
              .join("\n");
            doc.write(`<style>${css}</style>`);
          } catch (e) {
            console.warn("Could not access stylesheet: ", styleSheet, e);
          }
        }
      });

      doc.write(`
          </head>
          <body>${printContents}</body>
          </html>
      `);
      doc.close();

      // Ensuring the print operation completes before removing the iframe
      iframe.onload = function () {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        document.body.removeChild(iframe);
      };
    },
  },
};
</script>

<style>
@media print {
  body {
    padding: 0;
    margin: 0;
  }

  .top-notice,
  .verify-notice,
  .sidebar,
  .print-btn,
  .add-credit-btn,
  .secondary-header,
  footer,
  nav {
    display: none;
  }
}
</style>