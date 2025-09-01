<template>
  <div class="flex gap-3 justify-between items-center pt-5">
    <h1
      class="flex gap-3 justify-between text-[#31363f] items-center text-xl font-medium"
    >
      Transactions
    </h1>

    <div class="flex gap-5">
      <Button @click="openModal('create')">
        <p>Create</p>
      </Button>

      <button
        @click="fetchTransaction(pagination.current_page)"
        type="button"
        class="bg-[#F6F6F6] items-center flex border-primary border rounded-lg text-gray-500 text-sm p-1.5 text-center"
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

  <div class="h-full mt-5">
    <Table :head="thead" v-if="userTransaction.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="user_transaction in userTransaction"
        :key="user_transaction.id"
      >
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          <p>#{{ user_transaction.id }}</p>
        </td>
        <td class="whitespace-nowrap py-4 truncate px-4 max-w-[200px]">
          <span v-tooltip="user_transaction.transaction_id" class="truncate block">
            {{
              user_transaction.transaction_id
                ? user_transaction.transaction_id
                : "-"
            }}
          </span>
        </td>
        <td class="whitespace-nowrap py-4 px-4 truncate max-w-[220px] ">
          <span v-tooltip="user_transaction.service" class="truncate block">
            {{ user_transaction.service ? user_transaction.service : "-" }}
          </span>
        </td>
        <td class="p-4">
          <p>
            {{
              user_transaction.final_amount
                ? formatCurrency(user_transaction.final_amount)
                : "-"
            }}
          </p>
        </td>
        <td class="p-4">
          <p>
            {{
              user_transaction.payment_gateway
                ? user_transaction.payment_gateway
                : "-"
            }}
          </p>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          <p>
            {{
              user_transaction.created_at ? user_transaction.created_at : "-"
            }}
          </p>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          <span
            class="text-xs text-green-600 rounded-2xl flex items-center"
            v-if="user_transaction.status === 1"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              check_circle
            </span>
            Success
          </span>

          <span
            class="text-xs text-gray-600 flex items-center"
            v-if="user_transaction.status === 2"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              schedule
            </span>
            Pending
          </span>
          <span
            class="text-xs text-red-600 flex items-center"
            v-if="user_transaction.status === 0"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              cancel
            </span>
            Failed
          </span>

          <span
            class="text-xs text-blue-600 flex items-center"
            v-if="user_transaction.status === 3"
          >
            <span class="material-symbols-outlined mr-1 text-[15px]">
              currency_exchange
            </span>
            Refunded
          </span>
        </td>

        <td class="whitespace-nowrap cursor-pointer py-4 px-4">
          <div class="flex gap-3 justify-center items-center">
            <span
              v-tooltip="'Update'"
              @click="openModal('edit', user_transaction)"
              :class="[
                'material-symbols-outlined p-1.5 rounded-md text-[20px] text-green-600 bg-green-100',
              ]"
            >
              edit_square
            </span>

            <span
              v-tooltip="'Delete'"
              @click="deleteModel(user_transaction.id)"
              :class="[
                'material-symbols-outlined p-1.5 rounded-md text-red-500 bg-red-50 text-[20px]',
              ]"
            >
              delete
            </span>
          </div>
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between' : 'justify-end',
            'sm:flex gap-3 py-5 px-4',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="userTransaction.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="8" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="8" class="text-center text-sm px-6 py-4">
            {{ refreshing ? "Please Wait" : "No Transactions Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
  <Modal
    :show="isOpenModal"
    :modelIcon="'universal_currency'"
    :modalTitle="modalTitle"
    :titleClass="'text-lg font-font-semibold border-b'"
    :customClass="['md:max-w-xl']"
    @closeModal="closeModal"
  >
    <form>
      <div
        class="grid xl:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-2.5 md:gap-4"
      >
        <div class="">
          <label
            v-if="isEditing"
            for="transaction_id"
            class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Transaction ID</label
          >
          <label v-else for="transaction_id" class="text-tiny font-medium"
            >Transaction ID</label
          >
          <input
            v-model="payload.transaction_id"
            type="text"
            name="transaction_id"
            id="transaction_id"
            class="border text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
            placeholder="02227184267462369"
          />
          <small
            id="transaction_id_message"
            class="error_message text-red-500 text-xs"
          ></small>
        </div>
        <div class="col-span-1">
          <label
            for="service"
            class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Description</label
          >
          <input
            v-model="payload.service"
            type="text"
            name="service"
            id="service"
            class="border flex-1 text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
            placeholder="Add wallet credits"
          />
          <small
            class="text-red-500 error_message text-xs"
            id="service_message"
          ></small>
        </div>
      </div>
      <div class="sm:flex grid grid-cols-1 gap-4 mt-3">
        <div class="sm:flex gap-4">
          <div class="sm:w-1/2">
            <label
              for="amount"
              v-if="!isEditing"
              class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Amount</label
            >
            <label for="amount" v-if="isEditing" class="text-tiny font-medium"
              >Amount</label
            >
            <div class="flex">
              <div
                class="pointer-events-none flex items-center mt-2 pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
              >
                <span class="text-tiny pr-0.5">{{ siteSettings.currency_symbol }}</span>
              </div>
              <input
                v-model="payload.amount"
                onwheel="this.blur()"
                type="number"
                id="amount"
                name="amount"
                class="border border-l-0 text-gray-900 text-sm mt-2 rounded-r-lg border-primary focus:border-primary focus:ring-transparent block w-full pr-2 py-2"
                placeholder="199"
              />
            </div>
            <small
              id="amount_message"
              class="error_message text-red-500 text-xs"
            ></small>
          </div>
          <div class="sm:w-1/2 sm:mt-0 mt-3">
            <label for="coupon_code" class="text-tiny font-medium"
              >Coupon Code</label
            >
            <select
              v-model="payload.coupon_code"
              id="promo_code"
              name="promo_code"
              class="block w-full flex-1 rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
            >
              <option value="">Select a Coupon</option>
              <option v-for="code in promocodes" :key="code.id">
                {{ code.code }}
              </option>
            </select>
            <small
              class="text-red-500 error_message text-xs"
              id="promo_code_message"
            ></small>
          </div>
        </div>
        <Button
          class="sm:max-w-fit sm:min-w-fit sm:mt-8"
          @click="getTotal"
          :disabled="gettingTotal"
          >{{ gettingTotal ? "Please wait" : "Get a Total" }}</Button
        >
      </div>
      <div class="border rounded-md px-3 py-2 mt-4" v-if="showCostOverview">
        <span class="text-tiny font-medium">Final Cost Overview</span>
        <span class="text-[11px] mt-0.5 text-gray-500 block">
          Review your total cost, including taxes. Ensure all details are
          correct before proceeding with payment.
        </span>
        <hr class="my-2" />
        <div class="flex justify-between gap-5">
          <span class="text-sm text-gray-500 font-medium">Base Amount</span>
          <span class="text-sm font-medium">{{
            formatCurrency(parseFloat(payload.base_amount))
          }}</span>
        </div>
        <div class="flex justify-between gap-5 mt-1">
          <span class="text-sm text-gray-500 font-medium">Discount Amount</span>
          <span class="text-sm font-medium">{{
            formatCurrency(payload.discount_amount)
          }}</span>
        </div>
        <template v-if="payload.tax_details && payload.tax_details.length > 0">
          <div
            class="flex justify-between gap-5 mt-1"
            v-for="tax in payload.tax_details"
            :key="tax"
          >
            <span class="text-sm text-gray-500 font-medium"
              >{{ tax.label }} ({{ tax.tax }}%)</span
            >
            <span class="text-sm font-medium">{{
              formatCurrency(tax.tax_amount)
            }}</span>
          </div>
        </template>
        <hr class="my-2" />
        <div class="flex justify-between gap-5">
          <span class="text-sm text-gray-500 font-medium">Final Amount</span>
          <span
            :class="[
              isLightColor ? 'text-custom-700' : 'text-custom-500',
              'font-medium',
            ]"
            >{{ formatCurrency(payload.final_amount) }}</span
          >
        </div>
      </div>
      <p class="font-medium mt-4">Payment Information</p>
      <div
        class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 gap-x-2.5 md:gap-x-4 gap-y-2.5 mt-2"
      >
        <div class="">
          <label
            for="payment_gateway"
            class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Gateway</label
          >
          <select
            v-model="payload.payment_gateway"
            id="payment_gateway"
            name="payment_gateway"
            class="block w-full rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
          >
            <option value="">Select Payment</option>
            <option value="Paypal">Paypal</option>
            <option value="Razorpay">Razorpay</option>
            <option value="Stripe">Stripe</option>
          </select>
          <small
            class="text-red-500 error_message text-xs"
            id="payment_gateway_message"
          ></small>
        </div>

        <div class="">
          <label for="paid_at" class="text-tiny font-medium">Pay At</label>
          <input
            type="date"
            name="paid_at"
            v-model="payload.paid_at_human"
            id="paid_at"
            class="border text-gray-800 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
          />
          <small
            class="text-red-500 error_message text-xs"
            id="paid_at_human_message"
          ></small>
        </div>

        <div class="">
          <label
            for="status"
            class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Status</label
          >
          <select
            v-model="payload.status"
            @change="setRefundData()"
            id="status"
            name="status"
            class="block w-full rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
          >
            <option value="">Select Status</option>
            <option value="0">Failed</option>
            <option value="1">Success</option>
            <option value="2">Pending</option>
            <option value="3" v-if="isEditing">Refunded</option>
          </select>
          <small
            class="text-red-500 error_message text-xs"
            id="status_message"
          ></small>
        </div>

        <div class="">
          <label for="payment_link" class="text-tiny font-medium"
            >Payment Link</label
          >
          <input
            v-model="payload.payment_link"
            type="text"
            name="payment_link"
            id="payment_link"
            class="border text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
            placeholder="Enter Payment Link..."
          />
          <small
            class="text-red-500 error_message text-xs"
            id="payment_link_message"
          ></small>
        </div>
      </div>
      <template v-if="payload.status == 3">
        <hr class="my-3" />
        <p class="font-medium mt-4">Refund Details</p>
        <div
          class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 gap-x-2.5 md:gap-x-4 gap-y-2.5 mt-2"
        >
          <div class="">
            <label
              for="refund_id"
              class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Refund ID</label
            >
            <input
              v-model="payload.refund_id"
              type="text"
              name="refund_id"
              id="refund_id"
              class="border text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
              placeholder="Enter Refunded ID"
            />
            <small
              class="text-red-500 error_message text-xs"
              id="refund_id_message"
            ></small>
          </div>

          <div class="">
            <label
              for="refunded_at"
              class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Refund At</label
            >
            <input
              type="date"
              name="refunded_at_human"
              v-model="payload.refunded_at_human"
              id="refunded_at_human"
              class="border text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
              placeholder="Enter Refunded At"
            />
            <small
              id="refunded_at_human_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>

          <div class="">
            <label
              for="service"
              class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
              >Refund Reason</label
            >
            <input
              v-model="payload.refund_reason"
              type="text"
              name="refund_reason"
              id="refund_reason"
              class="border text-gray-900 text-sm mt-2 rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
              placeholder="Enter Refund Reason"
            />
            <small
              id="refund_reason_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </template>
      <div class="flex flex-row-reverse mt-4 gap-4">
        <Button
          type="submit"
          :disabled="processing || !showCostOverview"
          @click="updateTransaction"
        >
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Create" }}
        </Button>
        <button @click="closeModal" type="button" class="rounded-md border font-medium px-4 py-2 text-center text-sm">
        Cancel
      </button>
      </div>
    </form>
  </Modal>

  <Confirmation
    :show="openConfirmation"
    :showLoader="showLoader"
    @closeModal="closeModal"
    :confirmationTitle="'Delete Transaction History'"
    :submitBtnTitle="`Yes I'm Sure`"
    @confirm="deleteHistory"
  >
    <template #icon>
      <i
        class="fas fa-warning h-6 w-6 text-xl text-red-600 text-center flex items-center justify-center"
        aria-hidden="true"
      ></i>
    </template>
    <template #content>
      <p class="text-tiny text-gray-600">
        Are you sure you want to delete this Transaction history?
      </p>
    </template>
  </Confirmation>
</template>

<script>
export default {
  data() {
    return {
      breadcrumb: {
        icon: "groups",
        pages: [{ name: "Transactions" }],
      },
      openConfirmation: false,
      showLoader: false,
      processing: false,
      refreshing: false,
      isOpenModal: false,
      userTransaction: [],
      modelTitle: "",
      thead: [
        {
          title: "ID",
          classes: "pl-10",
        },
        {
          title: "Transaction ID",
          classes: "whitespace-nowrap",
        },
        "Description",
        {
          title: "Amount",
          classes: "",
        },
        {
          title: "Gateway",
          classes: " whitespace-nowrap",
        },
        {
          title: "Date & Time",
          classes: " whitespace-nowrap",
        },
        {
          title: "Status",
          classes: "",
        },
        {
          title: "Actions",
          classes: "text-center",
        },
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      transaction_id: "",
      payload: {
        id: null,
        service: "",
        base_amount: 0,
        coupon_code: "",
        promo_code_id: "",
        refund_id: "",
        discount_amount: 0,
        final_amount: 0,
        payment_gateway: "",
        refund_reason: "",
        refunded_at_human: "",
        tax_amount: 0,
        status: "1",
        payment_link: "",
        paid_at_human: "",
        tax_details: [],
      },
      isEditing: false,
      promocodes: [],
      gettingTotal: false,
      showCostOverview: false,
    };
  },

  methods: {
    openModal(action, transaction) {
      this.isOpenModal = true;
      if (action === "create") {
        this.isEditing = false;
        this.modalTitle = "Create Transaction";
        this.payload = {
          id: null,
          service: "",
          amount: 0,
          base_amount: 0,
          coupon_code: "",
          refund_id: "",
          discount_amount: 0,
          final_amount: 0,
          payment_gateway: "",
          refund_reason: "",
          refunded_at_human: "",
          tax_amount: 0,
          status: 1,
          payment_link: "",
          paid_at_human: "",
          tax_details: [],
        };
      } else if (action === "edit") {
        this.modalTitle = " History";
        this.isEditing = true;
        this.transaction_id = transaction.id;
        this.payload = {
          transaction_id: transaction.transaction_id,
          service: transaction.service,
          amount: 0,
          base_amount: transaction.base_amount,
          coupon_code: "",
          refund_id: transaction.refund_id,
          discount_amount: transaction.discount_amount,
          final_amount: transaction.final_amount,
          payment_gateway: transaction.payment_gateway,
          refund_reason: transaction.refund_reason,
          refunded_at_human: transaction.refunded_at_human,
          tax_amount: transaction.tax_amount,
          status: transaction.status,
          payment_link: transaction.payment_link,
          paid_at_human: transaction.paid_at_human,
          tax_details: transaction.tax_details,
        };
        this.showCostOverview = true;
      }
    },
    closeModal() {
      this.isOpenModal = false;
      this.openConfirmation = false;
      this.showLoader = false;
      this.showCostOverview = false;
      this.isEditing = false;
    },
    setRefundData() {
      if (this.payload.status !== 3) {
        this.payload.refund_id = null;
        this.payload.refunded_at = null;
        this.payload.refund_reason = null;
      }
    },
    async fetchPromocodes() {
      await this.$axios
        .get("/admin/promo-codes/available")
        .then(({ data }) => {
          this.promocodes = data.promoCodes;
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        });
    },
    async getTotal() {
      this.gettingTotal = true;
      this.hideError();
      await this.$axios
        .patch(
          `/admin/users/${this.$route.params.user}/transactions/tax-details`,
          {
            amount: this.payload.amount,
            promo_code: this.payload.coupon_code,
          }
        )
        .then(({ data }) => {
          this.payload.base_amount = data.checkout.base_amount;
          this.payload.discount_amount = data.checkout.discount_amount;
          this.payload.tax_amount = data.checkout.tax_amount;
          this.payload.final_amount = data.checkout.final_amount;
          this.payload.tax_details = data.checkout.tax_detail;
          this.payload.promo_code_id = data.promo_code
            ? data.promo_code.id
            : null;
          this.showCostOverview = true;
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.displayError(error.response.data);
          } else {
            this.closeModal();
            this.$toast.error(error.response.data.message);
          }
        })
        .finally(() => {
          this.gettingTotal = false;
        });
    },
    async fetchTransaction(page = 1) {
      const id = this.$route.params.user;
      this.refreshing = true;
      let url = `/admin/users/${id}/transactions?page=${page}&per_page=${this.per_page}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.userTransaction = data.transactions.data;
          this.pagination = {
            current_page: data.transactions.current_page,
            last_page: data.transactions.last_page,
            per_page: data.transactions.per_page,
            total: data.transactions.total,
            next_page_url: data.transactions.next_page_url,
            prev_page_url: data.transactions.prev_page_url,
          };
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },

    handlePageChange(newPage) {
      this.fetchTransaction(newPage);
    },
    handlePerPageChange() {
      this.fetchTransaction(1);
    },
    deleteModel(transaction_id) {
      this.user_transaction_id = transaction_id;
      this.openConfirmation = true;
    },

    deleteHistory() {
      const id = this.$route.params.user;
      this.showLoader = true;
      this.$axios
        .delete(`admin/users/${id}/transactions/${this.user_transaction_id}`)
        .then((response) => {
          this.$toast.success(response.data.message);
          this.fetchTransaction();
          this.closeModal();
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.displayError(error.response.data);
          }
          this.closeModal();
        });
    },

    updateTransaction() {
      const id = this.$route.params.user;
      this.processing = true;
      this.hideError();
      let request;
      if (this.payload.id === null) {
        request = this.$axios.post(
          `/admin/users/${id}/transactions`,
          this.payload
        );
      } else {
        request = this.$axios.patch(
          `/admin/users/${id}/transactions/${this.transaction_id}`,
          this.payload
        );
      }
      request
        .then((response) => {
          this.$toast.success(response.data.message);
          this.closeModal();
          this.fetchTransaction();
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.displayError(error.response.data);
          } else {
            this.closeModal();
            this.$toast.error(error.response.data.message);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },

  mounted() {
    this.fetchTransaction();
    this.fetchPromocodes();
    this.$emit("pass-breadcrumb", this.breadcrumb);
  },
};
</script>
