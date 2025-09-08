<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="container-fluid mx-auto">
    <div class="text-[#2c3138] font-medium text-xl mb-5">Wallet</div>
    <div class="grid xl:grid-cols-3 grid-cols-1 xl:gap-5 gap-y-5">
    <div
      class="p-7 bg-cover rounded-xl col-span-2"
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
          :class="['rounded-l-none whitespace-nowrap py-2.5 disabled:opacity-75']"
          @click="addCredit"
          :disabled="amount == ''"
        > 
          Add Credits
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
      <div class="flex flex-col h-full">
        <div class="bg-gray-50 border-b-0 border rounded-t-xl p-3 sm:p-4 lg:px-5">
          <div class="flex items-center gap-4">
            <div class="min-w-[40px] w-10 h-10 flex justify-center items-center text-custom-500">
              <span class="material-symbols-outlined text-3xl sm:text-4xl">credit_card_clock</span>
            </div>
            <div class="flex flex-col gap-1 flex-1">
              <p class="text- font-medium leading-tight">Minimum Credit Reminder</p>
              <p class="text-xs text-gray-500 leading-tight">
                Set a management credit limit for low balance alerts.
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white border-t-0 border rounded-b-xl p-4 flex-grow flex flex-col">
          <div class="mb-4">
            <h3 class="text-tiny font-medium">Minimum Threshold</h3>
            <p class="text-gray-500 text-xs leading-relaxed">
              Get an email alert when credits drop below a set amount.
            </p>
          </div>
        
          <div class="mt-auto flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
              <input 
                type="number" 
                v-model="user.reminder_minimum_credit"
                class="w-full text-sm p-2 sm:p-2.5 rounded-lg border border-slate-300 focus:border-sa-500 focus:ring-0"
                placeholder="Enter Minimum Credit"
              >
              <div class="flex items-center">
                <small
                  id="reminder_minimum_credit_message"
                  class="text-red-500 error_message text-xs block mt-1.5"
                ></small>
              </div>
            </div>
            <Button
              class="w-full sm:w-auto px-4 py-2.5 text-sm"
              :disabled="saveDisabled"
              @click="updateReminderMinCredit()"
            >
                {{ minimumCreditReminder.processing ? 'Please Wait' : 'Save' }}
            </Button>
          </div>
        </div>
      </div>
    </div>
    <div class="my-5">
      <h1 class="text-xl font-medium">Real Time Usage</h1>
    </div>
    <div class="sm:flex justify-between items-center gap-2 sm:gap-x-10 rounded-md border border-gray-200 p-4">
      <div class="flex gap-4 items-center">
        <span class="material-symbols-outlined text-custom-500 text-3xl">
            event_upcoming
        </span>
          <div class="flex flex-col">
            <span class="text-[18px] font-medium">Current Month Usage</span>
            <span class="text-gray-500 py-0.5 text-[14px]">
              View your total credit usage for this month in this section. It
              provides a clear overview of how much credit you’ve spent, making it
              easier to manage your monthly budget.
            </span>
          </div>
        </div>
        <div class="flex flex-col text-end items-end mt-3 sm:mt-0 pr-2">
        <span class="text-xl text-custom-500 font-medium">{{ formatCurrency(monthlyAmount) }}</span>
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
        // title: "Billing",
        icon: "lab_profile",
        pages: [
        { name: "Billing" },
        { name: "Wallet" }],
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
      minimumCreditReminder: {
        processing: false,
        hasSavedValue: false,
      },
      selectedMonth: new Date().getMonth() + 1,
      year: [],
      processing: false,
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
    saveDisabled() {
      return (
        this.minimumCreditReminder.processing ||
        (this.isEmpty(this.user?.reminder_minimum_credit) && !this.minimumCreditReminder.hasSavedValue)
      )
    }
  },
  watch: {
  user: {
	  immediate: true,
      handler(u) {
		  if (u) this.minimumCreditReminder.hasSavedValue = !this.isEmpty(u.reminder_minimum_credit)
		}
	}
},
  created() {
    this.getUser();
    this.fetchUsageSummary();
    this.fetchMinAmount();
    if (this.user) {
        this.minimumCreditReminder.hasSavedValue = !this.isEmpty(this.user.reminder_minimum_credit)
    }
  },
  methods: {
    ...mapActions(useAuthStore, ["getUser"]),
	isEmpty(v) {
		return v === null || v === undefined || v === ''
	},
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
    async updateReminderMinCredit() {
      this.minimumCreditReminder.processing = true;
	  this.hideError()
      this.$axios
        .patch("/reminder-minimum-credit",{
          reminder_minimum_credit : this.user.reminder_minimum_credit
        })
        .then(({ data }) => {
			this.getUser()
          	this.$toast.success(data.message);
        	this.minimumCreditReminder.hasSavedValue = !this.isEmpty(this.user.reminder_minimum_credit)

        })
        .catch(({ response }) => {
          if (response.status !== 422) {
            this.$toast.error(response.data.message);
          }
          this.displayError(response.data);
        })
        .finally(() => {
			this.minimumCreditReminder.processing = false; 
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
