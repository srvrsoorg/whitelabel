<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="container-fluid mx-auto">
    <div class="text-[#2c3138] font-medium text-xl mb-5">Wallet</div>
    <div
      class="p-7 bg-cover rounded-b-2xl"
      :style="{ backgroundImage: 'url(/images/backgrnd.png)' }"
    >
      <div class="grid sm:grid-cols-4 grid-cols-1 mb-5">
        <div
          class="text-[#2c3138] justify-self-start font-medium text-xl sm:col-span-3"
        >
          <div class="sm:flex gap-5">
            <img src="/images/wallet1.png" class="xl:h-auto w-14 h-12" alt="" />
            <div class="justify-center sm:mt-0 mt-5">
              <p>Available Credits</p>
              <p class="text-gray-500 text-tiny">
                The up-to-date amount of funds you can use from your wallet.
              </p>
            </div>
          </div>
        </div>
        <div class="mt-3 sm:mt-0 flex sm:justify-end items-center">
          <p
            :class="[
              isLightColor ? 'text-custom-700' : 'text-custom-500',
              'text-2xl font-semibold',
            ]"
          >
            {{ formatCurrency(credits) }}
          </p>
        </div>
      </div>
      <div class="flex sm:mt-5 mt-3">
        <div
          class="flex xl:w-1/3 md:w-1/2 w-fit items-center shadow-sm rounded-l-md"
        >
          <div
            class="pointer-events-none bg-gray-50 py-2 h-full rounded-l-md flex items-center p-3"
          >
            <span class="text-tiny">{{ siteSettings.currency_symbol }}</span>
          </div>
          <input
            type="number"
            name="amount"
            v-model="amount"
            id="amount"
            class="w-full block py-2 text-gray-800 border-none ring-0 placeholder:text-gray-400 text-sm leading-6 focus:border-none focus:ring-0"
            placeholder="0.00"
          />
        </div>
        <Button
          :class="['rounded-l-none whitespace-nowrap ']"
          @click="addCredit"
          :disabled="processing || amount === ''"
        >
          <template v-if="processing">
            <span>Please Wait</span>
          </template>
          <template v-else>
            <p class="py-0.5">Add Credits</p>
          </template>
        </Button>
      </div>

      <div v-if="errorMessage" class="error-message text-red-500 pt-2 text-xs">
        {{ errorMessage }}
      </div>
      <template
        v-if="(paymentGateways || []).length === 0 && !fetchingProviders"
      >
        <div
          class="bg-[#fcf2cc] mt-5 text-[#f09030] text-tiny px-4 py-2 rounded-md"
        >
          <b class="font-medium">Note: </b>Your admin has not enabled any
          payment gateway. Please contact your admin.
        </div>
      </template>
    </div>

    <div class="my-5">
      <h1 class="text-xl font-medium">Real Time Usage</h1>
    </div>
    <div class="rounded-md p-6 px-7 border border-gray-200">
      <div class="xs:flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-gray-400 text-3xl">
            event_upcoming
          </span>
          <span class="text-[18px] font-medium">Current Month Usage</span>
        </div>
        <p class="text-[18px] font-medium xs:mt-0 mt-3">
          {{ formatCurrency(monthlyAmount) }}
        </p>
      </div>
      <div class="sm:pl-10 sm:mt-0 mt-3 sm:w-[85%]">
        <span class="text-gray-500 py-0.5 text-[14px]">
          View your total credit usage for this month in this section. It
          provides a clear overview of how much credit you’ve spent, making it
          easier to manage your monthly budget.
        </span>
      </div>
    </div>

    <div class="sm:flex gap-5 mt-5 items-center">
      <h1 class="text-xl font-medium xl:w-1/2 sm:w-2/5">Usage Summary</h1>
      <div class="flex-1">
        <div class="xs:flex items-center mt-4 sm:mt-0 sm:justify-end gap-5">
          <div>
            <select
              name=""
              id=""
              class="w-full block rounded-md border border-primary focus:border-primary py-1.5 text-sm leading-6 focus:ring-0"
              v-model="selectedMonth"
              @change="fetchUsageSummary(pagination.current_page)"
            >
              <option value="">Select Month</option>
              <option
                :value="months.id"
                v-for="months in month"
                :key="months.id"
              >
                {{ months.name }}
              </option>
            </select>
          </div>
          <div>
            <select
              name=""
              id=""
              class="w-full mt-3 xs:mt-0 block rounded-md border border-primary focus:border-primary py-1.5 text-sm leading-6 focus:ring-0"
              v-model="selectedYear"
              @change="fetchUsageSummary(pagination.current_page)"
            >
              <option value="">Select Year</option>
              <option :value="years" v-for="years in year" :key="years">
                {{ years }}
              </option>
            </select>
          </div>
          <button
            @click="fetchUsageSummary(pagination.current_page)"
            :class="[
              textColorClass,
              'bg-gray-50 mt-3 xs:mt-0 p-1.5 border border-primary rounded-md flex items-center ',
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
      </div>
    </div>
    <div class="h-full mt-5">
      <Table :head="thead" v-if="(summary || []).length > 0">
        <tr
          v-for="(item, index) in summary"
          :key="index"
          class="border-b border-primary text-[#2c3138] text-sm"
        >
          <td class="py-3 px-5 pl-10 font-medium">#{{ item.id }}</td>
          <td class="px-4 py-3 whitespace-nowrap">
            <div class="flex flex-col max-w-[200px] truncate">
              <span
                class="font-medium pb-1.5 text-tiny truncate"
                v-tooltip="item.server_name"
                >{{ item.server_name }}</span
              >
              <p class="text-gray-500">{{ item.server_ip }}</p>
            </div>
          </td>
          <td class="whitespace-nowrap px-4 py-3">
            <p class="">
              {{
                item.subscription
                  ? formatCurrency(item.subscription.monthly_price)
                  : "-"
              }}
            </p>
          </td>
          <td class="whitespace-nowrap px-4 py-3">
            <div class="">
              <span class="block">
                {{ formatCurrency(item.deduct_amount) }}
              </span>
            </div>
          </td>
          <td class="whitespace-nowrap px-4 py-3">
            <span class="">{{ item.hours }}</span>
          </td>
          <td class="whitespace-nowrap px-4 py-3">
            <p class="">{{ item.last_deduct_at }}</p>
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
              <Perpage v-model="per_page" :initialPerPage="pagination.per_page" @changePage="handlePerPageChange" />
            </div>
            <div v-if="summary.length > 0" class="mt-5 sm:mt-0">
              <Pagination
                :pagination="pagination"
                @page-change="handlePageChange"
              />
            </div>
          </div>
        </template>
      </Table>
      <template v-else>
        <TableSkeleton :heads="6" v-if="refreshing" />
        <Table :head="thead" v-else>
          <tr>
            <td colspan="6" class="text-center text-sm px-6 py-5">
              {{ refreshing ? "Please Wait" : "No Data Found" }}
            </td>
          </tr>
        </Table>
      </template>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/store/auth";
import { mapState, mapActions } from "pinia";
export default {
  data() {
    return {
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [{ name: "Wallet" }],
      },
      amount: "",
      errorMessage: "",
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      },
      monthlyAmount: 0,
      per_page: 10,
      refreshing: false,
      transactions: [],
      thead: [
        {
          title: "ID",
          classes: "pl-10 font-medium",
        },
        {
          title: "Server Details",
          classes: "font-medium whitespace-nowrap",
        },
        {
          title: "Price per Month",
          classes: "text-left font-medium",
        },
        {
          title: "Usage",
          classes: "text-left font-medium",
        },
        {
          title: "Hours",
          classes: "text-left font-medium",
        },
        { title: "Last Deducted At", classes: "" },
      ],
      summary: [],
      month: [
        {
          id: 1,
          name: "January",
        },
        {
          id: 2,
          name: "February",
        },
        {
          id: 3,
          name: "March",
        },
        {
          id: 4,
          name: "April",
        },
        {
          id: 5,
          name: "May",
        },
        {
          id: 6,
          name: "June",
        },
        {
          id: 7,
          name: "July",
        },
        {
          id: 8,
          name: "August",
        },
        {
          id: 9,
          name: "September",
        },
        {
          id: 10,
          name: "October",
        },
        {
          id: 11,
          name: "November",
        },
        {
          id: 12,
          name: "December",
        },
      ],
      selectedMonth: new Date().getMonth() + 1,
      year: [],
      selectedYear: new Date().getFullYear(),
      minimumAmount: 0,
      paymentGateways: [],
      summary: [],
      fetchingProviders: false,
    };
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
    credits() {
      if (this.user) {
        return parseFloat(this.user.credits);
      }
    },
  },
  created() {
    this.getUser();
    this.fetchUsageSummary();
    this.fetchMinAmount();
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser"]),

    addCredit() {
      const amount = parseFloat(this.amount);
      if (!this.paymentGateways.length) {
        this.errorMessage =
          "Your admin has not enabled any payment gateway. Please contact your admin.";
      } else if (amount < this.minimumAmount) {
        this.errorMessage = `The credits field must be at least ${this.minimumAmount}.`;
      } else {
        if (amount >= this.minimumAmount) {
          this.$router.push({
            name: "checkout",
            query: {
              action: "add-credits",
              credits: this.amount,
            },
          });
          this.errorMessage = "";
        }
      }
    },
    fetchUsageSummary(page = "") {
      this.refreshing = true;
      const month = this.selectedMonth;
      const year = this.selectedYear;
      let url = `usage-summaries?month=${month}&year=${year}&page=${page}&per_page=${this.per_page}`;

      this.$axios
        .get(url)
        .then((response) => {
          this.monthlyAmount = response.data.monthly_deducted_amount;
          this.summary = response.data.usageSummaries.data;
          this.pagination = {
            current_page: response.data.usageSummaries.current_page,
            last_page: response.data.usageSummaries.last_page,
            per_page: response.data.usageSummaries.per_page,
            total: response.data.usageSummaries.total,
            next_page_url: response.data.usageSummaries.next_page_url,
            prev_page_url: response.data.usageSummaries.prev_page_url,
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
      this.fetchUsageSummary(newPage);
    },
    handlePerPageChange() {
      this.fetchUsageSummary(1);
    },
    async fetchMinAmount() {
      this.fetchingProviders = true;
      this.$axios
        .get("/enable-providers")
        .then(({ data }) => {
          this.minimumAmount = data.basic_details
            ? data.basic_details.minimum_recharge_amount
            : 1;
          this.paymentGateways = data.payment;
        })
        .catch((error) => {
          // this.$toast.error(error.response.data.message);
        })
        .finally(() => {
          this.fetchingProviders = false;
        });
    },
    getYear() {
      const startYear = 2024;
      const currentYear = new Date().getFullYear();
      for (let year = startYear; year <= currentYear; year++) {
        this.year.push(year);
      }
    },
  },
  mounted() {
    this.getYear();
  },
};
</script>
