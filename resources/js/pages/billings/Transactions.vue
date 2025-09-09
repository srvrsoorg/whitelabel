<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="flex justify-between items-center ">
    <div class="text-[#2c3138] font-medium text-xl">Transactions</div>
    <button
      @click="fetchTransactions(pagination.current_page)"
      :class="[
        textColorClass,
        'bg-gray-50 p-1.5 border rounded-md flex items-center ',
      ]"
    >
      <span
        :class="[
          refreshing ? 'fa-spin' : '',
          'material-symbols-outlined text-[22px] text-gray-500',
        ]"
      >
        refresh
      </span>
    </button>
  </div>

  <div class="h-full mt-5">
    <Table :head="thead" v-if="transactions.length > 0">
      <tr
        class="border-b text-[#2c3138] text-sm"
        v-for="transaction in transactions"
        :key="transaction.id"
      >
        <td class="whitespace-nowrap py-4 pl-10 px-8">
          {{ transaction.payment_gateway }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 flex max-w-[250px]">
          <span v-tooltip="transaction.service" class="truncate pt-1">{{
            transaction.service
          }}</span>
        </td>
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          {{ formatCurrency(transaction.final_amount) }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          {{ formatCurrency(transaction.discount_amount) }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          {{ transaction.created_at }}
        </td>
        <td class="whitespace-nowrap py-4 px-4 pl-10">
          <span
            class="text-xs text-green-600 py-1.5 rounded-2xl min-w-[150px] flex items-center"
            v-if="transaction.status === 1"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              check_circle
            </span>
            Success
          </span>

          <span
            class="text-xs text-gray-600 py-1.5 rounded-2xl min-w-[150px] flex items-center"
            v-if="transaction.status === 2"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              schedule
            </span>
            Pending
          </span>
          <span
            class="text-xs text-red-600 py-1.5 rounded-2xl min-w-[150px] flex items-center"
            v-if="transaction.status === 0"
          >
            <span class="material-symbols-outlined mr-1 text-[18px]">
              cancel
            </span>
            Failed
          </span>

          <span
            class="text-xs text-blue-600 py-1.5 min-w-[150px] flex items-center"
            v-if="transaction.status === 3"
          >
            <span class="material-symbols-outlined text-[15px] pr-1">
              currency_exchange
            </span>
            Refunded
          </span>
        </td>
        <td class="whitespace-nowrap py-4 px-4 pr-10 text-center">
          <button
            :class="[
              'text-sm cursor-pointer border px-2.5 py-1.5 rounded-md ',
              isLightColor
                ? 'text-custom-700 border-custom-700'
                : 'text-custom-500 border-custom-500',
            ]"
            :disabled="isProcessing && activeTransactionKey === transaction.key"
            @click="executePayment(transaction)"
            v-if="
              transaction.status === 2 && transaction.payment_gateway !== 'Cashfree' &&
              isEnabledPaymentProvider(transaction.payment_gateway) 
            "
          >
            <i
              v-if="isProcessing && activeTransactionKey === transaction.key"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{
              isProcessing && activeTransactionKey === transaction.key
                ? "Please wait"
                : "Pay Now"
            }}
          </button>

          <span
            class=""
            v-if="
              (transaction.status == 2 &&
              (transaction.payment_gateway === 'Cashfree' || !isEnabledPaymentProvider(transaction.payment_gateway))) ||
              transaction.status == 0 ||
              transaction.status == 3
            "
            >-</span
          >
          <router-link
            :to="{
              name: 'viewTransaction',
              params: {
                key: transaction.key,
              },
            }"
            v-if="transaction.status == 1"
            :class="[
              'px-4 py-2 rounded-md',
              isLightColor
                ? 'bg-custom-700 text-black'
                : 'bg-custom-500 text-white',
            ]"
            >Invoice</router-link
          >
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
          <div v-if="transactions.length > 0" class="mt-5 sm:mt-0">
            <Pagination
              :pagination="pagination"
              @page-change="handlePageChange"
            />
          </div>
        </div>
      </template>
    </Table>
    <template v-else>
      <TableSkeleton :heads="7" v-if="refreshing" />
      <Table :head="thead" v-else>
        <tr>
          <td colspan="7" class="text-center text-sm px-6 py-5">
            {{ refreshing ? "Please Wait" : "No Transactions Found" }}
          </td>
        </tr>
      </Table>
    </template>
  </div>
</template>

<script>
import executePayment from "@/mixins/executePayment";

export default {
  mixins: [executePayment],
  data() {
    return {
      breadcrumb: {
        icon: "lab_profile",
        pages: [{ name: "Billing"},{ name: "Transactions"}],
      },
      transactions: [],
      pagination: null,
      isProcessing: false,
      refreshing: false,
      enabledPaymentProviders: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      per_page: 10,
      thead: [
        { title: "Payment Gateway", classes: ["px-8 pl-10"] },
        { title: "Description" },
        { title: "Amount", classes: ["pl-10"] },
        { title: "Discount", classes: ["pl-10"] },
        { title: "Date & Time", classes: ["pl-10"] },
        { title: "Status", classes: ["pl-10"] },
        { title: "Actions", classes: ["text-center pr-10"] },
      ],
    };
  },
  created() {
    this.fetchTransactions();
    this.fetchPaymentGateway();
  },
  methods: {
    isEnabledPaymentProvider(provider) {
      return this.enabledPaymentProviders.includes(provider);
    },
    async fetchPaymentGateway() {
      await this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.enabledPaymentProviders = data.payment;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },

    async fetchTransactions(page = 1) {
      const id = this.$route.params.user;
      this.refreshing = true;
      let url = `/user/transactions?page=${page}&per_page=${this.per_page}`;
      await this.$axios
        .get(url)
        .then(({ data }) => {
          this.transactions = data.transactions.data;
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

    handlePageChange(newPage) {
      this.fetchTransactions(newPage);
    },
    handlePerPageChange() {
      this.fetchTransactions(1);
    },
  },
};
</script>