<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="sm:flex justify-between items-center gap-4">
    <p class="text-xl font-medium">Transactions</p>
    <div class="xs:flex justify-end sm:mt-0 mt-3 items-center gap-5">
      <div class="relative xl:min-w-60 w-full">
        <select
        class="w-full min-w-40 text-sm border-gray-200 bg-white text-gray-700 rounded-md focus:outline-none"
        @change="fetchAdminTransaction(current_page)"
        v-model="filter.status"
      >
        <option value="0">Failed</option>
        <option value="1">Success</option>
        <option value="2">Pending</option>
        <option value="3">Refunded</option>
      </select>
       
      </div>
      <div class="flex justify-end gap-5 items-center mt-5 xs:mt-0">
       
        <button
            @click="fetchAdminTransaction(pagination.current_page)"
          :class="[
            textColorClass,
            'bg-gray-50 p-1.5 border rounded-md justify-self-end flex items-center ',
          ]"
        >
          <span
            :class="[
              refreshing ? 'fa-spin' : '',
              'material-symbols-outlined text-[22px] text-gray-400',
            ]"
          >
            refresh
          </span>
        </button>
      </div>
    </div>
  </div>

  <div class="h-full mt-5">
    <Table :head="thead" v-if="AdminTransaction.length > 0">
      <tr
        class="border-b border-primary text-[#2c3138] text-sm"
        v-for="admin_transaction in AdminTransaction"
        :key="admin_transaction"
      >
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          <p>#{{ admin_transaction.id }}</p>
        </td>
        <td class="whitespace-nowrap py-4 px-4 truncate max-w-[170px]">
          <div v-tooltip="admin_transaction.transaction_id">
          <p class="truncate max-w-[170px]">
            {{
              admin_transaction.transaction_id
                ? admin_transaction.transaction_id
                : "-"
            }}
          </p>
        </div>
        </td>
        <td class="whitespace-nowrap xl:max-w-[300px] truncate py-4 px-4">
          <div
            v-if="admin_transaction.user"
            class="flex items-center gap-3 w-full"
          >
            <div class="truncate">
              <router-link
                :to="{
                  name: 'userProfile',
                  params: {
                    user: admin_transaction.user.id,
                  },
                }"
              >
                <td
                  class="whitespace-nowrap truncate text-tiny font-medium max-w-[200px]"
                >
                  <span v-tooltip="admin_transaction.user.name">
                    {{
                      admin_transaction.user.name
                        ? admin_transaction.user.name
                        : "-"
                    }}
                  </span>
                </td>
              </router-link>

              <td
                class="whitespace-nowrap text-gray-500 truncate items-center max-w-[150px]"
              >
                <div class="flex items-center">
                  <span
                    v-tooltip="admin_transaction.user.email"
                    class="ma-w-[130px] truncate"
                  >
                    {{
                      admin_transaction.user.email
                        ? admin_transaction.user.email
                        : "-"
                    }}
                  </span>
                  <span
                    @click="copyToClipboard(admin_transaction.user.email)"
                    class="material-symbols-outlined px-1 text-blue-500 cursor-pointer text-[16px] py-1"
                  >
                    content_copy
                  </span>
                </div>
              </td>
            </div>
          </div>
          <span v-else>-</span>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          <p>
            {{
              admin_transaction.final_amount
                ? formatCurrency(admin_transaction.final_amount)
                : "-"
            }}
          </p>
        </td>
        <td class="whitespace-nowrap py-4 px-4">
          <p>
            {{ admin_transaction.payment_gateway }}
          </p>
        </td>

        <td class="whitespace-nowrap py-4 px-4 text-left">
          <span
            v-if="admin_transaction.status === 1"
            class="flex items-center gap-1 text-green-600"
          >
            <span class="material-symbols-outlined text-[18px]">
              check_circle
            </span>
            <p>Success</p>
          </span>

          <span
            v-if="admin_transaction.status === 2"
            class="flex items-center gap-1 text-gray-600"
          >
            <span class="material-symbols-outlined text-[18px]">
              schedule
            </span>
            <p>Pending</p>
          </span>
          <span
            v-if="admin_transaction.status === 3"
            class="flex items-center text-blue-600 gap-1"
          >
            <span class="material-symbols-outlined text-[15px]">
              currency_exchange
            </span>
            Refunded
          </span>
          <span
            v-if="admin_transaction.status === 0"
            class="flex items-center gap-1 text-red-500"
          >
            <span class="material-symbols-outlined text-[18px]"> cancel </span>
            Failed
          </span>
        </td>

        <td class="whitespace-nowrap py-4 px-4">
          {{
            admin_transaction.created_at ? admin_transaction.created_at : "-"
          }}
        </td>
        <td class="whitespace-nowrap cursor-pointer py-4 px-4 text-center">
          <button
            v-if="admin_transaction.user"
            v-tooltip="'Edit'"
            @click="openModalWithTransactionData(admin_transaction)"
            class="material-symbols-outlined text-[20px] p-1.5 rounded-md bg-green-100 text-green-600"
          >
            edit_square
          </button>
          <span v-else class="text-[20px] p-2 mr-1">-</span>
          <button
            v-tooltip="'View'"
            @click="openViewTransactionDetail(admin_transaction)"
            class="material-symbols-outlined text-[20px] p-1.5 rounded-md ml-2"
            :class="
              isLightColor
                ? 'text-custom-700 bg-custom-200'
                : 'text-custom-500 bg-custom-50'
            "
          >
            visibility
          </button>
        </td>
      </tr>
      <template #pagination>
        <div
          v-if="pagination.total > 10"
          :class="[
            pagination.total > 10 ? 'justify-between' : 'justify-end',
            'sm:flex gap-3  py-5 px-4',
          ]"
        >
          <div v-if="pagination.total > 10">
            <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
          </div>
          <div v-if="AdminTransaction.length > 0" class="mt-5 sm:mt-0">
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
          <td colspan="8" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Transactions Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>

  <Modal
    :show="openModal"
    @closeModal="closeModal"
    :modalTitle="'Update Transaction'"
    :modelIcon="'universal_currency'"
    :titleClass="'text-lg font-font-semibold border-b'"
    :customClass="['md:max-w-xl']"
  >
    <form>
      <div class="grid md:grid-cols-2 grid-cols-1 gap-2.5 md:gap-4">
        <div class="">
          <label for="transaction_id" class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Transaction ID</label
          >
          <input
            v-model="transaction.transaction_id"
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
            v-model="transaction.service"
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
      <div class="sm:flex gap-x-4 mt-3">
        <div class="sm:flex gap-x-4">
          <div class="sm:w-1/2 w-full">
            <label
              for="amount"
              class="text-tiny font-medium"
              >Amount</label
            >
            <div class="flex">
              <div
                class="pointer-events-none flex items-center mt-2 pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
              >
                <span class="text-tiny pr-0.5">{{ siteSettings.currency_symbol }}</span>
              </div>
              <input
                v-model="transaction.amount"
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
          <div class="sm:w-1/2 w-full mt-4 sm:mt-0">
            <label
              for="coupon_code"
              class="text-tiny font-medium"
              >Coupon Code</label
            >
            <select
              v-model="transaction.coupon_code"
              id="promo_code"
              name="promo_code"
              class="block w-full flex-1 rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
            >
              <option value="">Select a Coupon</option>
              <option v-for="code in promocodes" :key="code.id">{{ code.code }}</option>
            </select>
            <small
              class="text-red-500 error_message text-xs"
              id="promo_code_message"
            ></small>
          </div>
        </div>
        <Button class="max-w-fit min-w-fit sm:mt-8 mt-4" @click="getTotal" :disabled="gettingTotal">{{gettingTotal ? 'Please wait' : 'Get a Total'}}</Button>
      </div>
      <div class="border rounded-md px-3 py-2 mt-4" v-if="showCostOverview">
        <span class="text-tiny font-medium">Final Cost Overview</span>
        <span class="text-[11px] mt-0.5 text-gray-500 block">
          Review your total cost, including taxes. Ensure all details are correct  before proceeding with payment.
        </span>
        <hr class="my-2"/>
        <div class="flex justify-between gap-5">
          <span class="text-sm text-gray-500 font-medium">Base Amount</span>
          <span class="text-sm font-medium">{{ formatCurrency(parseFloat(transaction.base_amount)) }}</span>
        </div>
        <div class="flex justify-between gap-5 mt-1">
          <span class="text-sm text-gray-500 font-medium">Discount Amount</span>
          <span class="text-sm font-medium">{{ formatCurrency(transaction.discount_amount) }}</span>
        </div>
        <template v-if="transaction.tax_details.length > 0">   
          <div class="flex justify-between gap-5 mt-1" v-for="tax in transaction.tax_details" :key="tax">
            <span class="text-sm text-gray-500 font-medium">{{ tax.label }} ({{ tax.tax }}%)</span>
            <span class="text-sm font-medium">{{ formatCurrency(tax.tax_amount) }}</span>
          </div>
        </template>
        <hr class="my-2" />
        <div class="flex justify-between gap-5">
          <span class="text-sm text-gray-500 font-medium">Final Amount</span>
          <span :class="[isLightColor ? 'text-custom-700' : 'text-custom-500', 'font-medium']">{{ formatCurrency(transaction.final_amount) }}</span>
        </div>
      </div>
      <p class="font-medium mt-4">Payment Information</p>
      <div class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 gap-x-2.5 md:gap-x-4 gap-y-2.5 mt-2">
        <div class="">
          <label
            for="payment_gateway"
            class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
            >Gateway</label
          >
          <select
            v-model="transaction.payment_gateway"
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
            v-model="transaction.paid_at_human"
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
            v-model="transaction.status"
            @change="setRefundData()"
            id="status"
            name="status"
            class="block w-full rounded-md capitalize border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 mt-2 focus:ring-0"
          >
            <option value="">Select Status</option>
            <option value="0">Failed</option>
            <option value="1">Success</option>
            <option value="2">Pending</option>
            <option value="3">Refunded</option>
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
            v-model="transaction.payment_link"
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
      <template v-if="transaction.status == 3">
        <hr class="mt-4 mb-3"/>
        <p class="font-medium">Refund Details</p>
        <div class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 gap-x-2.5 md:gap-x-4 gap-y-2.5 mt-2">
          <div class="">
              <label
                for="refund_id"
                class="text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 font-medium"
                >Refund ID</label
              >
              <input
                v-model="transaction.refund_id"
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
              v-model="transaction.refunded_at_human"
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
              v-model="transaction.refund_reason"
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

      <div class="flex flex-row-reverse mt-5">
        <Button type="submit" :disabled="processing || !showCostOverview" @click="updateTransaction">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please wait" : "Save" }}
        </Button>
      </div>
    </form>
  </Modal>
  <Modal
    :show="viewTransactionModal"
    @closeModal="closeViewTransactionModal"
    :modalTitle="'Transaction Details'"
    :modelIcon="'universal_currency'"
  >
    <template v-if="viewTransaction">
      <div class="flex justify-between items-center gap-5 text-sm">
        <span class="text-gray-500 min-w-fit">Transaction ID</span>
        <span
          class="truncate font-medium text-sm"
          v-tooltip="viewTransaction.transaction_id" v-if="viewTransaction.transaction_id">#{{ viewTransaction.transaction_id }}</span>
        <span
          class="truncate font-medium text-sm"
          v-tooltip="viewTransaction.transaction_id" v-else>-</span>
      </div>
      <div class="flex justify-between items-center gap-5 text-sm mt-2">
        <span class="text-gray-500 min-w-fit">User</span>
        <span class="truncate text-sm font-medium" v-if="viewTransaction.user"
          >{{ viewTransaction.user.name }}
          <label v-tooltip="viewTransaction.user.email"
            >({{ viewTransaction.user.email }})</label
          ></span
        >
        <span v-else>-</span>
      </div>
      <div class="flex justify-between items-center gap-5 text-sm mt-2">
        <span class="text-gray-500 min-w-fit">Date</span>
        <span class="truncate font-medium text-sm">{{
          viewTransaction.created_at
        }}</span>
      </div>
      <div class="flex justify-between items-center gap-5 text-sm mt-2">
        <span class="text-gray-500 min-w-fit">Payment Method</span>
        <span class="truncate font-medium text-sm">{{
          viewTransaction.payment_gateway
        }}</span>
      </div>
      <div class="border border-gray-300 rounded-md px-3 py-2.5 mt-5">
        <span class="text-tiny font-medium">Amount Overview</span>
        <hr class="my-2" />
        <div class="flex justify-between items-center gap-5 text-tiny mt-2">
          <span class="text-gray-500 min-w-fit">Base Amount</span>
          <span class="truncate font-medium">
            {{ formatCurrency(viewTransaction.base_amount) }}
          </span>
        </div>
        <template v-if="viewTransaction.tax_details && viewTransaction.tax_details.length > 0">
          <div class="flex justify-between items-center gap-5 text-tiny mt-2" v-for="(tax, index) in viewTransaction.tax_details" :key="index">
            <span class="text-gray-500 min-w-fit">{{ tax.label }} ({{ tax.tax }}%)</span>
            <span class="truncate font-medium">
              {{ formatCurrency(tax.tax_amount) }}
            </span>
          </div>
        </template>
        <div class="flex justify-between items-center gap-5 text-tiny mt-2">
          <span class="text-gray-500 min-w-fit">Discount</span>
          <span class="truncate font-medium">
            {{ formatCurrency(viewTransaction.discount_amount) }}
          </span>
        </div>
        <hr class="my-2" />
        <div class="flex justify-between items-center gap-5 text-tiny mt-2">
          <span class="font-medium">Final Amount</span>
          <span
            class="truncate font-medium"
            :class="isLightColor ? 'text-custom-700' : 'text-custom-500'"
            >
            {{ formatCurrency(viewTransaction.final_amount) }}
          </span>
        </div>
      </div>
      <div class="flex justify-between items-center gap-5 text-tiny mt-3">
        <span>Transaction Status</span>
        <span
          v-if="viewTransaction.status === 1"
          class="flex items-center justify-center gap-1 text-green-600"
        >
          <p>Success</p>
        </span>

        <span
          v-if="viewTransaction.status === 2"
          class="flex items-center justify-center gap-1 text-gray-600"
        >
          <p>Pending</p>
        </span>
        <span
          v-if="viewTransaction.status === 3"
          class="flex text-blue-600 items-center justify-center gap-1"
        >
          Refunded
        </span>
        <span
          v-if="viewTransaction.status === 0"
          class="flex items-center justify-center gap-1 text-red-500"
        >
          Failed
        </span>
      </div>
    </template>
  </Modal>
</template>

<script>
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
export default {
  name: "AdminTransaction",
  components: {
    Datepicker,
  },
  data() {
    return {
      breadcrumb: {
        title: "User Management",
        icon: "groups",
        pages: [
          {
            name: "Transactions",
          },
        ],
      },
      openModal: false,
      processing: false,
      credit: false,
      isOpenModal: false,
      current_page: 1,
      open: false,
      AdminTransaction: "",
      userId: null,
      transaction_id: null,
      thead: [
        { title: "ID", classes: "font-medium pl-10" },
        {
          title: "Transaction ID",
          classes: "font-medium whitespace-nowrap",
        },
        { title: "User", classes: "font-medium" },
        {
          title: "Final Amount",
          classes: " whitespace-nowrap font-medium",
        },
        {
          title: "Gateway",
          classes: "font-medium",
        },
        {
          title: "Status",
          classes: "font-medium",
        },
        {
          title: "Created At",
          classes: "font-medium whitespace-nowrap",
        },
        { title: "Actions", classes: "font-medium text-center" },
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      refreshing: false,
      per_page: 10,

      filter: {
        status: "1",
      },
      transaction: {
        id: null,
        service: "",
        base_amount: 0,
        coupon_code: "",
        promo_code_id: '',
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
        tax_details: []
      },
      viewTransaction: null,
      viewTransactionModal: false,
      promocodes: [],
      gettingTotal: false,
      showCostOverview: false
    };
  },
  methods: {
    openModalWithTransactionData(transaction) {
      this.openModal = true;
      this.showCostOverview = true
      this.transaction = {
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
        tax_details: transaction.tax_details
      };
      this.transaction_id = transaction.id;
      this.userId = transaction.user_id;
    },
    openViewTransactionDetail(transaction) {
      this.viewTransaction = transaction;
      this.viewTransactionModal = true;
    },
    clearFilter() {
      this.fetchAdminTransaction();
    },
    closeModal() {
      this.userConfirmation = false;
      this.reason = "";
      this.openModal = false;
      this.processing = false;
      this.showCostOverview = false;
      this.userInfo = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        country_name: "",
        country_code: "",
        region_name: "",
        region_code: "",
        timezone: "",
      };
    },
    closeViewTransactionModal() {
      this.viewTransactionModal = false;
      this.viewTransaction = null;
    },
    setRefundData() {
      if (this.transaction.status !== 3) {
        this.transaction.refund_id = null;
        this.transaction.refunded_at = null;
        this.transaction.refund_reason = null;
      }
    },
    async fetchPromocodes(){
      await this.$axios.get('/admin/promo-codes/available').then(({data}) => {
        this.promocodes = data.promoCodes
      }).catch((error) => {
        this.$toast.error(error.response.data.message);
      })
    },
    async getTotal(){
      this.gettingTotal = true
      this.hideError()
      await this.$axios.patch(`/admin/users/${this.userId}/transactions/tax-details`, {
        amount: this.transaction.amount,
        promo_code: this.transaction.coupon_code
      }).then(({data}) => {
        this.transaction.base_amount = data.checkout.base_amount
        this.transaction.discount_amount = data.checkout.discount_amount
        this.transaction.tax_amount = data.checkout.tax_amount
        this.transaction.final_amount = data.checkout.final_amount
        this.transaction.tax_details = data.checkout.tax_detail
        this.transaction.promo_code_id = data.promo_code ? data.promo_code.id : null
        this.showCostOverview = true
      }).catch((error) => {
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
    async fetchAdminTransaction(page = 1) {
      this.refreshing = true;
      let url = `/admin/transactions?status=${this.filter.status}&page=${page}&per_page=${this.per_page}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.AdminTransaction = data.transactions.data;
          this.pagination = {
            current_page: data.transactions.current_page,
            last_page: data.transactions.last_page,
            per_page: data.transactions.per_page,
            total: data.transactions.total,
            next_page_url: data.transactions.next_page_url,
            prev_page_url: data.transactions.prev_page_url,
          };
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.refreshing = false;
        });
    },

    handlePerPageChange() {
      this.fetchAdminTransaction(1);
    },
    handlePageChange(newPage) {
      this.fetchAdminTransaction(newPage);
    },

    async updateTransaction() {
      this.processing = true;
      this.hideError();
      await this.$axios
        .patch(
          `/admin/users/${this.userId}/transactions/${this.transaction_id}`,
          this.transaction
        )
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          this.fetchAdminTransaction();
        })
        .catch(({ response }) => {
          if (response.status !== 422) {
            this.closeModal();
            this.$toast.error(response.data.message);
          } else {
            this.displayError(response.data);
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
  mounted() {
    this.fetchAdminTransaction();
    this.fetchPromocodes()
  },
};
</script>

