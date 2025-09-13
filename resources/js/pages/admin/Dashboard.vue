<template>
  <template v-if="processing">
    <TableSkeleton />
  </template>
  <template v-else>
    <Breadcrumb :breadcrumb="breadcrumb" />
    <h1 class="text-xl text-[#31363f] font-medium">Dashboard</h1>
    <template v-if="!isSetupComplete ">
      <div class="bg-[#F6F6F6] shadow-sm rounded-md p-10 my-5">
        <div class="xl:flex gap-5">
          <div class="flex-1">
            <p class="text-xl font-medium">Self-Hosted WhiteLabel</p>
            <div>
              <p class="text-tiny text-gray-500">
                Welcome to your self-hosted whitelabel platform, where you can
                completely customize your hosting panel, manage servers
                efficiently, and seamlessly integrate other services.
              </p>
            </div>
            <div class="flex gap-5 mt-10">
              <span
                :class="[
                  isLightColor ? 'text-custom-700' : 'text-custom-500',
                  'material-symbols-outlined  text-2xl',
                ]"
              >
                open_with
              </span>
              <div class="flex flex-col">
                <p class="text-lg font-medium">Full Customization</p>
                <p class="text-tiny text-gray-500">
                  Take full control of your hosting by customizing branding,
                  features, and settings to meet your requirements.
                </p>
              </div>
            </div>
            <div class="flex gap-5 my-6">
              <span
                :class="[
                  isLightColor ? 'text-custom-700' : 'text-custom-500',
                  'material-symbols-outlined  text-2xl',
                ]"
              >
                open_with
              </span>
              <div class="flex flex-col">
                <p class="text-lg font-medium">User-Friendly Interface</p>
                <p class="text-tiny text-gray-500">
                  Effortlessly manage your platform with a user-friendly
                  interface designed for efficient operations.
                </p>
              </div>
            </div>
            <div class="flex gap-5 mt-6">
              <span
                :class="[
                  isLightColor ? 'text-custom-700' : 'text-custom-500',
                  'material-symbols-outlined  text-2xl',
                ]"
              >
                open_with
              </span>
              <div class="flex flex-col">
                <p class="text-lg font-medium">Easy Integration</p>
                <p class="text-tiny text-gray-500">
                  Effortless integration with various platforms to enhance
                  functionality.
                </p>
              </div>
            </div>
          </div>
          <div class="xl:block hidden">
            <div class="">
              <img src="/images/Group1.png" class="w-80" />
            </div>
          </div>
        </div>
        <div class="mt-10" v-if="setup">
          <div class="text-xl font-medium flex items-center gap-2">
            <p>"Important points"</p>
            <img src="/logo/Group.png" class="w-5" />
          </div>
          <div class="sm:flex gap-5 justify-between border-b items-center py-5">
            <div class="sm:flex gap-5">
              <div>
                <div class="xs:flex items-center gap-2">
                  <div class="flex gap-2 sm:gap-5 items-center">
                    <span
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                        'material-symbols-outlined border sm:mt-2 p-1 bg-[#F6F6F6] rounded-md border-primary',
                      ]"
                    >
                      deployed_code
                    </span>
                    <div
                      class="font-medium text-lg sm:flex inline sm:gap-4 items-center gap-1"
                    >
                      <span>Application Setup</span>
                      <a
                        href="https://serveravatar.com/docs/add-on/self-hosted/site-settings"
                        target="_blank"
                        class="material-symbols-outlined cursor-pointer -rotate-45 pr-3 text-[22px] sm:pr-0 text-blue-500"
                      >
                        link
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-tiny text-gray-500 sm:pl-14 mt-2 sm:mt-0">
                  Easily configure and customize your application branding with
                  a straightforward process using the self-hosted white-label
                  panel.
                </p>
              </div>
            </div>
            <div class="flex items-center justify-end mt-3 sm:mt-0">
              <div
                v-if="setup.application_setup"
                class="flex xs:justify-center justify-end items-center min-w-28"
              >
                <Button
                  :class="[
                    '!rounded-full !p-1 flex justify-center items-center',
                  ]"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    check
                  </span>
                </Button>
              </div>
              <router-link
                :to="{ name: 'billingSetings' }"
                v-else
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-black'
                    : 'bg-custom-500 text-white',
                  'px-3 py-1.5 text-sm   whitespace-nowrap rounded-md',
                ]"
              >
                <span>Site Settings</span>
              </router-link>
            </div>
          </div>
          <div class="sm:flex gap-5 justify-between border-b items-center py-5">
            <div class="sm:flex gap-5">
              <div>
                <div class="flex items-center gap-2">
                  <div class="flex sm:gap-5 gap-2 items-center">
                    <span
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                        'material-symbols-outlined border sm:mt-2 p-1 bg-[#F6F6F6] rounded-md border-primary',
                      ]"
                    >
                      lab_profile
                    </span>
                    <div
                      class="font-medium sm:flex inline items-center sm:gap-4 text-lg"
                    >
                      <span>Billing Management</span>
                      <a
                        href="https://serveravatar.com/docs/add-on/self-hosted/billing/settings"
                        target="_blank"
                        class="material-symbols-outlined cursor-pointer -rotate-45 pr-2 text-[22px] md:pr-0 text-blue-500"
                      >
                        link
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-tiny text-gray-500 sm:pl-14 mt-2 sm:mt-0">
                  Easily customize and manage your billing details, tax details
                  and other billing settings.
                </p>
              </div>
            </div>
            <div class="flex items-center justify-end mt-3 sm:mt-0">
              <div
                class="flex xs:justify-center justify-end items-center min-w-28"
                v-if="setup.billing_management"
              >
                <Button
                  :class="[
                    '!rounded-full !p-1 flex justify-center items-center',
                  ]"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    check
                  </span>
                </Button>
              </div>

              <router-link
                :to="{ name: 'Settings' }"
                v-else
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-black'
                    : 'bg-custom-500 text-white',
                  'px-3 py-1.5 text-sm   whitespace-nowrap rounded-md',
                ]"
              >
                <span>Billing Management</span>
              </router-link>
            </div>
          </div>
          <div class="sm:flex gap-5 justify-between border-b items-center py-5">
            <div class="sm:flex gap-5">
              <div>
                <div class="flex items-center gap-2">
                  <div class="flex gap-2 sm:gap-5 items-center">
                    <span
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                        'material-symbols-outlined border sm:mt-2 p-1 bg-[#F6F6F6] rounded-md border-primary',
                      ]"
                    >
                      mail
                    </span>
                    <div class="font-medium sm:flex items-center gap-4 text-lg">
                      <span> SMTP Setup </span>
                      <a
                        href="https://serveravatar.com/docs/add-on/self-hosted/integrations/smtp"
                        target="_blank"
                        class="material-symbols-outlined cursor-pointer -rotate-45 pr-2 text-[22px] md:pr-0 text-blue-500"
                      >
                        link
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-tiny text-gray-500 sm:pl-14 mt-2 sm:mt-0">
                  Enable and configure your email functionality easily by adding
                  the SMTP credentials to the self-hosted white-label product.
                </p>
              </div>
            </div>
            <div class="flex items-center justify-end mt-3 sm:mt-0">
              <div
                v-if="setup.smtp_setup"
                class="min-w-28 flex xs:justify-center justify-end items-center"
              >
                <Button
                  :class="[
                    '!rounded-full !p-1 flex justify-center items-center',
                  ]"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    check
                  </span>
                </Button>
              </div>
              <router-link
                :to="{ name: 'SMTP' }"
                v-else
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-black'
                    : 'bg-custom-500 text-white',
                  'px-3 py-1.5 text-sm   whitespace-nowrap rounded-md',
                ]"
              >
                <span>SMTP Setup</span>
              </router-link>
            </div>
          </div>
          <div class="sm:flex gap-5 justify-between border-b items-center py-5">
            <div class="sm:flex gap-5">
              <div>
                <div class="flex items-center gap-2">
                  <div class="flex gap-2 sm:gap-5 items-center">
                    <span
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                        'material-symbols-outlined border sm:mt-2 p-1 bg-[#F6F6F6] rounded-md border-primary',
                      ]"
                    >
                      rule_settings
                    </span>
                    <div
                      class="font-medium sm:flex items-center sm:gap-4 text-lg"
                    >
                      <span> Payment Integrations </span>
                      <a
                        href="https://serveravatar.com/docs/add-on/self-hosted/integrations/payments"
                        target="_blank"
                        class="material-symbols-outlined cursor-pointer -rotate-45 pr-2 text-[22px] md:pr-0 text-blue-500"
                      >
                        link
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-tiny text-gray-500 sm:pl-14 mt-2 sm:mt-0">
                  The self-hosted white-label platform supports multiple payment
                  methods, allowing users to effortlessly adjust their plans
                  with various payment integration methods.
                </p>
              </div>
            </div>
            <div class="flex items-center justify-end mt-3 sm:mt-0">
              <div
                class="flex xs:justify-center justify-end items-center min-w-28"
                v-if="setup.payment_integration"
              >
                <Button
                  :class="[
                    '!rounded-full !p-1 flex justify-center items-center',
                  ]"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    check
                  </span>
                </Button>
              </div>
              <router-link
                :to="{ name: 'payment' }"
                v-else
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-black'
                    : 'bg-custom-500 text-white',
                  'px-3 py-1.5 text-sm   whitespace-nowrap rounded-md',
                ]"
              >
                <span>Payment Integration </span>
              </router-link>
            </div>
          </div>
          <div class="sm:flex gap-5 justify-between items-center py-5">
            <div class="sm:flex gap-5">
              <div>
                <div class="flex items-center gap-2">
                  <div class="flex sm:gap-5 gap-x-2 items-center">
                    <span
                      :class="[
                        isLightColor ? 'text-custom-700' : 'text-custom-500',
                        'material-symbols-outlined border sm:mt-2 p-1 bg-[#F6F6F6] rounded-md border-primary',
                      ]"
                    >
                      credit_card
                    </span>
                    <div
                      class="font-medium sm:flex items-center sm:gap-4 text-lg"
                    >
                      <span> Plan Management </span>
                      <a
                        href="https://serveravatar.com/docs/add-on/self-hosted/billing/plan"
                        target="_blank"
                        class="material-symbols-outlined cursor-pointer -rotate-45 xs:pr-2 text-[22px] md:pr-0 text-blue-500"
                      >
                        link
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-tiny text-gray-500 sm:pl-14 mt-2 sm:mt-0">
                  Easily customize and manage your plans and subscriptions with
                  various flexible options and different payment integration
                  methods.
                </p>
              </div>
            </div>
            <div class="flex items-center justify-end mt-3 sm:mt-0">
              <div
                v-if="setup.plan_management"
                class="flex xs:justify-center justify-end items-center min-w-28"
              >
                <Button
                  :class="[
                    '!rounded-full !p-1 flex justify-center items-center',
                  ]"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    check
                  </span>
                </Button>
              </div>
              <router-link
                :to="{ name: 'Plan' }"
                v-else
                :class="[
                  isLightColor
                    ? 'bg-custom-200 text-black'
                    : 'bg-custom-500 text-white',
                  'px-3 py-1.5 text-sm   whitespace-nowrap rounded-md',
                ]"
              >
                <span>Plan Management</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="flex gap-3 items-center mt-5">
        <span
          class="material-symbols-outlined"
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'text-custom-500 bg-custom-50',
            'p-1.5 rounded-md text-[22px] flex items-center justify-center',
          ]"
        >
          person
        </span>
        <span class="font-medium text-lg">Users</span>
      </div>
      <div
        class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-3"
        v-if="userSummary"
      >
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
        >
          <div class="flex gap-2.5 w-full">
            <div
              class="min-h-full min-w-[4px] rounded-xl"
              :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
            ></div>
            <div class="w-full">
              <span class="text-gray-500">Today</span>
              <div class="flex justify-between w-full mt-1.5">
                <span class="text-xl font-medium truncate pr-2">{{
                  userSummary.user_today
                }}</span>
                <div
                  :class="[
                    'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                    userSummary.user_today_percentage > 0
                      ? 'bg-green-100'
                      : 'bg-red-100',
                  ]"
                >
                  <span>{{ userSummary.user_today_percentage }}%</span>
                  <span
                    :class="[
                      'material-symbols-outlined text-base',
                      userSummary.user_today_percentage > 0
                        ? 'text-green-500'
                        : 'text-red-500',
                    ]"
                  >
                    {{
                      userSummary.user_today_percentage > 0
                        ? "arrow_upward"
                        : "arrow_downward"
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
        >
          <div class="flex gap-2.5 w-full">
            <div
              class="min-h-full min-w-[4px] rounded-xl"
              :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
            ></div>
            <div class="w-full">
              <span class="text-gray-500">Last 7 Days</span>
              <div class="flex justify-between w-full mt-1.5">
                <span class="text-xl font-medium">{{
                  userSummary.user_last7Days
                }}</span>
                <div
                  :class="[
                    'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                    userSummary.user_last7Days_percentage > 0
                      ? 'bg-green-100'
                      : 'bg-red-100',
                  ]"
                >
                  <span>{{ userSummary.user_last7Days_percentage }}%</span>
                  <span
                    :class="[
                      'material-symbols-outlined text-base',
                      userSummary.user_last7Days_percentage > 0
                        ? 'text-green-500'
                        : 'text-red-500',
                    ]"
                  >
                    {{
                      userSummary.user_last7Days_percentage > 0
                        ? "arrow_upward"
                        : "arrow_downward"
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
        >
          <div class="flex gap-2.5 w-full">
            <div
              class="min-h-full min-w-[4px] rounded-xl"
              :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
            ></div>
            <div class="w-full">
              <span class="text-gray-500">Last 30 Days</span>
              <div class="flex justify-between w-full mt-1.5">
                <span class="text-xl font-medium">{{
                  userSummary.user_last30Days
                }}</span>
                <div
                  :class="[
                    'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                    userSummary.user_last30Days_percentage > 0
                      ? 'bg-green-100'
                      : 'bg-red-100',
                  ]"
                >
                  <span>{{ userSummary.user_last30Days_percentage }}%</span>
                  <span
                    :class="[
                      'material-symbols-outlined text-base',
                      userSummary.user_last30Days_percentage > 0
                        ? 'text-green-500'
                        : 'text-red-500',
                    ]"
                  >
                    {{
                      userSummary.user_last30Days_percentage > 0
                        ? "arrow_upward"
                        : "arrow_downward"
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
        >
          <div class="flex gap-2.5 w-full">
            <div
              class="min-h-full min-w-[4px] rounded-xl"
              :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
            ></div>
            <div class="w-full">
              <span class="text-gray-500">6 Months</span>
              <div class="flex justify-between w-full mt-1.5">
                <span class="text-xl font-medium">{{
                  userSummary.user_last6Months
                }}</span>
                <div
                  :class="[
                    'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                    userSummary.user_last6Months_percentage > 0
                      ? 'bg-green-100'
                      : 'bg-red-100',
                  ]"
                >
                  <span>{{ userSummary.user_last6Months_percentage }}%</span>
                  <span
                    :class="[
                      'material-symbols-outlined text-base',
                      userSummary.user_last6Months_percentage > 0
                        ? 'text-green-500'
                        : 'text-red-500',
                    ]"
                  >
                    {{
                      userSummary.user_last6Months_percentage > 0
                        ? "arrow_upward"
                        : "arrow_downward"
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
        >
          <div class="flex gap-2.5 w-full">
            <div
              class="min-h-full min-w-[4px] rounded-xl"
              :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
            ></div>
            <div class="w-full">
              <span class="text-gray-500">Total</span>
              <div class="flex justify-between w-full mt-1.5">
                <span class="text-xl font-medium">{{
                  userSummary.user_total
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-3"
        v-else
      >
        <template v-for="i in 5" :key="i">
          <div
            class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
          >
            <Skeleton :count="2" />
          </div>
        </template>
      </div>
      <div
        class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-5"
      >
        <div class="md:col-span-3 sm:col-span-2 col-span-1">
          <div
            class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3"
          >
            <div class="flex justify-between items-center flex-wrap gap-5 px-3">
              <span class="font-medium">Users Overview</span>
              <div class="bg-gray-100 rounded-lg px-4 py-2 flex gap-4">
                <button
                  @click="
                    currentTab = 'sevenDay';
                    fetchChartData('user', 'sevenDay');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    currentTab == 'sevenDay'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Week
                </button>
                <button
                  @click="
                    currentTab = 'last30Days';
                    fetchChartData('user', 'last30Days');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    currentTab == 'last30Days'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Month
                </button>
                <button
                  @click="
                    currentTab = 'thisYear';
                    fetchChartData('user', 'thisYear');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    currentTab == 'thisYear'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Year
                </button>
              </div>
            </div>
            <UsersChart
              v-if="userChartData"
              ref="chart"
              :chartData="userChartData"
              :type="currentTab"
            ></UsersChart>
          </div>
        </div>
        <div
          class="xl:col-span-2 md:col-span-3 sm:col-span-2 col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3 h-full"
        >
          <span class="font-medium">Users from Country</span>
          <hr class="mt-3" />
          <template
            v-for="(data, index) in userCountryCount.slice(0, 5)"
            :key="index"
          >
            <div class="flex items-center gap-4 mt-5">
              <div class="xl:w-20 md:w-32 w-10">
                <span
                  class="truncate block text-tiny text-gray-500"
                  v-tooltip="data.country_name"
                  >{{ data.country_name }}</span
                >
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2 flex-1">
                <div
                  class="bg-blue-600 h-2 rounded-full"
                  :style="{ width: data.percentage + '%' }"
                ></div>
              </div>
              <div class="w-10">
                <span class="truncate block text-tiny text-gray-500">{{
                  data.total
                }}</span>
              </div>
            </div>
          </template>
          <!-- </div> -->
        </div>
      </div>
      <div class="flex gap-3 items-center mt-5">
        <span
          class="material-symbols-outlined"
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'text-custom-500 bg-custom-50',
            'p-1.5 rounded-md text-[22px] flex items-center justify-center',
          ]"
        >
          attach_money
        </span>
        <span class="font-medium text-lg">Earnings</span>
      </div>
      <div class="mt-3">
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
          v-if="transactionSummary"
        >
          <div
            class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          >
            <div class="sm:border-r border-gray-300 pr-4">
              <div class="flex gap-2.5 w-full">
                <div class="w-full">
                  <span class="text-gray-500">Today</span>
                  <div class="flex justify-between w-full mt-2.5">
                    <span
                      class="text-xl font-medium truncate pr-2"
                      v-tooltip="
                        formatCurrency(transactionSummary.transaction_today)
                      "
                    >
                      {{ formatCurrency(transactionSummary.transaction_today) }}
                    </span>
                    <div
                      :class="[
                        'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                        transactionSummary.transaction_today_percentage > 0
                          ? 'bg-green-100'
                          : 'bg-red-100',
                      ]"
                    >
                      <span
                        >{{
                          transactionSummary.transaction_today_percentage
                        }}%</span
                      >
                      <span
                        :class="[
                          'material-symbols-outlined text-base',
                          transactionSummary.transaction_today_percentage > 0
                            ? 'text-green-500'
                            : 'text-red-500',
                        ]"
                      >
                        {{
                          transactionSummary.transaction_today_percentage > 0
                            ? "arrow_upward"
                            : "arrow_downward"
                        }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="sm:border-r border-gray-300 pr-4">
              <div class="flex gap-2.5 w-full">
                <div class="w-full">
                  <span class="text-gray-500">Last 7 Days</span>
                  <div class="flex justify-between w-full mt-2.5">
                    <span
                      class="text-xl font-medium truncate pr-2"
                      v-tooltip="
                        formatCurrency(transactionSummary.transaction_last7Days)
                      "
                      >{{
                        formatCurrency(transactionSummary.transaction_last7Days)
                      }}</span
                    >
                    <div
                      :class="[
                        'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                        transactionSummary.transaction_last7Days_percentage > 0
                          ? 'bg-green-100'
                          : 'bg-red-100',
                      ]"
                    >
                      <span
                        >{{
                          transactionSummary.transaction_last7Days_percentage
                        }}%</span
                      >
                      <span
                        :class="[
                          'material-symbols-outlined text-base',
                          transactionSummary.transaction_last7Days_percentage >
                          0
                            ? 'text-green-500'
                            : 'text-red-500',
                        ]"
                      >
                        {{
                          transactionSummary.transaction_last7Days_percentage >
                          0
                            ? "arrow_upward"
                            : "arrow_downward"
                        }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="sm:border-r border-gray-300 pr-4">
              <div class="flex gap-2.5 w-full">
                <div class="w-full">
                  <span class="text-gray-500">Last 30 Days</span>
                  <div class="flex justify-between w-full mt-2.5">
                    <span
                      class="text-xl font-medium truncate pr-2"
                      v-tooltip="
                        formatCurrency(
                          transactionSummary.transaction_last30Days
                        )
                      "
                      >{{
                        formatCurrency(
                          transactionSummary.transaction_last30Days
                        )
                      }}</span
                    >
                    <div
                      :class="[
                        'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                        transactionSummary.transaction_last30Days_percentage > 0
                          ? 'bg-green-100'
                          : 'bg-red-100',
                      ]"
                    >
                      <span
                        >{{
                          transactionSummary.transaction_last30Days_percentage
                        }}%</span
                      >
                      <span
                        :class="[
                          'material-symbols-outlined text-base',
                          transactionSummary.transaction_last30Days_percentage >
                          0
                            ? 'text-green-500'
                            : 'text-red-500',
                        ]"
                      >
                        {{
                          transactionSummary.transaction_last30Days_percentage >
                          0
                            ? "arrow_upward"
                            : "arrow_downward"
                        }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="sm:border-r border-gray-300 pr-4">
              <div class="flex gap-2.5 w-full">
                <div class="w-full">
                  <span class="text-gray-500">6 Months</span>
                  <div class="flex justify-between w-full mt-2.5">
                    <span
                      class="text-xl font-medium truncate pr-2"
                      v-tooltip="
                        formatCurrency(
                          transactionSummary.transaction_last6Months
                        )
                      "
                      >{{
                        formatCurrency(
                          transactionSummary.transaction_last6Months
                        )
                      }}</span
                    >
                    <div
                      :class="[
                        'flex gap-1 items-center text-xs rounded-md h-fit py-0.5 px-2',
                        transactionSummary.transaction_last6Months_percentage >
                        0
                          ? 'bg-green-100'
                          : 'bg-red-100',
                      ]"
                    >
                      <span
                        >{{
                          transactionSummary.transaction_last6Months_percentage
                        }}%</span
                      >
                      <span
                        :class="[
                          'material-symbols-outlined text-base',
                          transactionSummary.transaction_last6Months_percentage >
                          0
                            ? 'text-green-500'
                            : 'text-red-500',
                        ]"
                      >
                        {{
                          transactionSummary.transaction_last6Months_percentage >
                          0
                            ? "arrow_upward"
                            : "arrow_downward"
                        }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="pr-4">
              <div class="flex gap-2.5 w-full">
                <div class="w-full">
                  <span class="text-gray-500">Total</span>
                  <div class="flex justify-between w-full mt-2.5">
                    <span
                      class="text-xl font-medium truncate"
                      v-tooltip="
                        formatCurrency(transactionSummary.transaction_total)
                      "
                      >{{
                        formatCurrency(transactionSummary.transaction_total)
                      }}</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
          v-else
        >
          <div
            class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          >
            <template v-for="i in 5" :key="i">
              <div class="sm:border-r border-gray-300 pr-4">
                <Skeleton :count="2" />
              </div>
            </template>
          </div>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3 mt-5"
        >
          <div class="flex justify-between items-center flex-wrap gap-5 px-3">
            <span class="font-medium">Earnings</span>
            <div class="bg-gray-100 rounded-lg px-4 py-2 flex gap-4">
              <button
                @click="
                  earningCurrentTab = 'sevenDay';
                  fetchChartData('transaction', 'sevenDay');
                "
                class="text-sm text-gray-500"
                :class="
                  earningCurrentTab == 'sevenDay'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Week
              </button>
              <button
                @click="
                  earningCurrentTab = 'last30Days';
                  fetchChartData('transaction', 'last30Days');
                "
                class="text-sm text-gray-500"
                :class="
                  earningCurrentTab == 'last30Days'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Month
              </button>
              <button
                @click="
                  earningCurrentTab = 'thisYear';
                  fetchChartData('transaction', 'thisYear');
                "
                class="text-sm text-gray-500"
                :class="
                  earningCurrentTab == 'thisYear'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Year
              </button>
            </div>
          </div>
          <EarningChart
            class="w-full"
            v-if="transactionChartData"
            ref="chart"
            :chartData="transactionChartData"
            :type="earningCurrentTab"
          ></EarningChart>
        </div>
      </div>
      <div class="flex gap-3 items-center mt-5">
        <span
          class="material-symbols-outlined"
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'text-custom-500 bg-custom-50',
            'p-1.5  rounded-md text-[22px] flex items-center justify-center ',
          ]"
        >
          dns
        </span>
        <span class="font-medium text-lg">Servers</span>
      </div>
      <div
        class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3 mt-3"
        v-if="serverSummary"
      >
        <div
          class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
        >
          <div class="sm:border-r border-gray-200 pr-3">
            <div class="flex gap-5 justify-between items-center w-full">
              <span class="text-gray-500">Today</span>
              <div
                class="rounded-md flex items-center justify-center px-1 py-0.5"
                :class="
                  isLightColor
                    ? 'bg-custom-300 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                "
              >
                <span class="material-symbols-outlined text-xl"> event </span>
              </div>
            </div>
            <div class="w-full">
              <p class="text-xl font-medium truncate">
                {{ serverSummary.server_today }}
              </p>
            </div>
          </div>
          <div class="sm:border-r border-gray-200 pr-3">
            <div class="flex gap-5 justify-between items-center w-full">
              <span class="text-gray-500">Last 7 Days</span>
              <div
                class="rounded-md flex items-center justify-center px-1 py-0.5"
                :class="
                  isLightColor
                    ? 'bg-custom-300 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                "
              >
                <span class="material-symbols-outlined text-xl">
                  date_range
                </span>
              </div>
            </div>
            <div class="w-full">
              <p class="text-xl font-medium truncate">
                {{ serverSummary.server_last7Days }}
              </p>
            </div>
          </div>
          <div class="sm:border-r border-gray-200 pr-3">
            <div class="flex gap-5 justify-between items-center w-full">
              <span class="text-gray-500">Last 30 Days</span>
              <div
                class="rounded-md flex items-center justify-center px-1 py-0.5"
                :class="
                  isLightColor
                    ? 'bg-custom-300 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                "
              >
                <span class="material-symbols-outlined text-xl">
                  calendar_month
                </span>
              </div>
            </div>
            <div class="w-full">
              <p class="text-xl font-medium truncate">
                {{ serverSummary.server_last30Days }}
              </p>
            </div>
          </div>
          <div class="sm:border-r border-gray-200 pr-3">
            <div class="flex gap-5 justify-between items-center w-full">
              <span class="text-gray-500">6 Months</span>
              <div
                class="rounded-md flex items-center justify-center px-1 py-0.5"
                :class="
                  isLightColor
                    ? 'bg-custom-300 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                "
              >
                <span class="material-symbols-outlined text-xl">
                  event_repeat
                </span>
              </div>
            </div>
            <div class="w-full">
              <p class="text-xl font-medium truncate">
                {{ serverSummary.server_last6Months }}
              </p>
            </div>
          </div>
          <div class="pr-3">
            <div class="flex gap-5 justify-between items-center w-full">
              <span class="text-gray-500">Total</span>
              <div
                class="rounded-md flex items-center justify-center px-1 py-0.5"
                :class="
                  isLightColor
                    ? 'bg-custom-300 text-custom-700'
                    : 'bg-custom-50 text-custom-500'
                "
              >
                <span class="material-symbols-outlined text-xl">
                  event_available
                </span>
              </div>
            </div>
            <div class="w-full">
              <p class="text-xl font-medium truncate">
                {{ serverSummary.server_total }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div
        class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
        v-else
      >
        <div
          class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
        >
          <template v-for="i in 5" :key="i">
            <div class="sm:border-r border-gray-300 pr-4">
              <Skeleton :count="2" />
            </div>
          </template>
        </div>
      </div>
      <div
        class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-5"
      >
        <div class="md:col-span-3 sm:col-span-2 col-span-1">
          <div
            class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
          >
            <div
              class="flex justify-between items-center flex-wrap gap-5 px-3 py-1"
            >
              <span class="font-medium">Servers Overview</span>
              <div class="bg-gray-100 rounded-lg px-4 py-2 flex gap-4">
                <button
                  @click="
                    serverCurrentTab = 'sevenDay';
                    fetchChartData('server', 'sevenDay');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    serverCurrentTab == 'sevenDay'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Week
                </button>
                <button
                  @click="
                    serverCurrentTab = 'last30Days';
                    fetchChartData('server', 'last30Days');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    serverCurrentTab == 'last30Days'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Month
                </button>
                <button
                  @click="
                    serverCurrentTab = 'thisYear';
                    fetchChartData('server', 'thisYear');
                  "
                  class="text-sm text-gray-500"
                  :class="
                    serverCurrentTab == 'thisYear'
                      ? isLightColor
                        ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                        : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                      : ''
                  "
                >
                  Year
                </button>
              </div>
            </div>
            <ServerChart
              class="w-full"
              v-if="serverChartData"
              ref="chart"
              :chartData="serverChartData"
              :type="serverCurrentTab"
            ></ServerChart>
          </div>
        </div>
        <div class="xl:col-span-2 md:col-span-3 sm:col-span-2 col-span-1">
          <div
            class="flex xl:flex-col sm:flex-row flex-col gap-5 w-full min-w-full"
            v-if="connectedServerCount"
          >
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
            >
              <div class="flex gap-2.5 w-full items-center">
                <div
                  class="min-h-full h-10 min-w-[4px] rounded-xl bg-green-500"
                ></div>
                <span class="flex-1 sm:text-xl text-lg">Connected Servers</span>
                <span class="relative flex items-center justify-center h-3 w-3 mr-2">
                    <span
                    class="absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75 animate-ping"
                    ></span>
                    <!-- Actual dot (stable) -->
                    <i class="relative fa-solid fa-circle text-green-500 text-sm"></i>
                </span>
              </div>
              <hr class="my-3" />
              <div class="w-full">
                <span class="text-xl text-gray-500 font-medium">{{
                  connectedServerCount.connected
                }}</span>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
            >
              <div class="flex gap-2.5 w-full items-center">
                <div
                  class="min-h-full h-10 min-w-[4px] rounded-xl bg-red-500"
                ></div>
                <span class="flex-1 sm:text-xl text-lg"
                  >Disconnected Servers</span
                  >
                    <span class="relative flex items-center justify-center h-3 w-3 mr-2">
                        <span
                        class="absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75 animate-ping"
                        ></span>
                        <i class="fa-solid fa-circle text-red-500 text-sm px-2"></i>
                    </span>
              </div>
              <hr class="my-3" />
              <div class="w-full">
                <span class="text-xl font-medium text-gray-500">{{
                  connectedServerCount.disconnected
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex gap-3 items-center mt-5">
        <span
          class="material-symbols-outlined"
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'text-custom-500 bg-custom-50',
            'p-1.5 rounded-md text-[22px] flex items-center justify-center',
          ]"
        >
          loyalty
        </span>
        <span class="font-medium text-lg">Subscriptions</span>
      </div>
      <div class="mt-3">
        <div class="h-fit" v-if="subscriptionSummary">
          <div
            class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          >
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Today</span>
                  <p class="text-xl font-medium block truncate">
                    {{ formatCurrency(subscriptionSummary.subscription_today) }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Last 7 Days</span>
                  <p class="text-xl font-medium block truncate">
                    {{
                      formatCurrency(
                        subscriptionSummary.subscription_last7Days
                      )
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Last 30 Days</span>
                  <p class="text-xl font-medium block truncate">
                    {{
                      formatCurrency(
                        subscriptionSummary.subscription_last30Days
                      )
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">6 Months</span>
                  <p class="text-xl font-medium block truncate">
                    {{
                      formatCurrency(
                        subscriptionSummary.subscription_last6Months
                      )
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Total</span>
                  <p class="text-xl font-medium block truncate">
                    {{ formatCurrency(subscriptionSummary.subscription_total) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          v-else
        >
          <template v-for="i in 5" :key="i">
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
            >
              <Skeleton :count="2" />
            </div>
          </template>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3 mt-5"
        >
          <div
            class="flex justify-between items-center flex-wrap gap-5 px-3 py-1"
          >
            <span class="font-medium">Overview</span>
            <div class="bg-gray-100 rounded-lg px-4 py-2 flex gap-4">
              <button
                @click="
                  subscriptionCurrentTab = 'sevenDay';
                  fetchChartData('subscription', 'sevenDay');
                "
                class="text-sm text-gray-500"
                :class="
                  subscriptionCurrentTab == 'sevenDay'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Week
              </button>
              <button
                @click="
                  subscriptionCurrentTab = 'last30Days';
                  fetchChartData('subscription', 'last30Days');
                "
                class="text-sm text-gray-500"
                :class="
                  subscriptionCurrentTab == 'last30Days'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Month
              </button>
              <button
                @click="
                  subscriptionCurrentTab = 'thisYear';
                  fetchChartData('subscription', 'thisYear');
                "
                class="text-sm text-gray-500"
                :class="
                  subscriptionCurrentTab == 'thisYear'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Year
              </button>
            </div>
          </div>
          <SubscriptionChart
            class="w-full"
            v-if="subscriptionChartData"
            ref="chart"
            :chartData="subscriptionChartData"
            :type="subscriptionCurrentTab"
          ></SubscriptionChart>
        </div>
      </div>
      <div class="flex gap-3 items-center mt-5">
        <span
          class="material-symbols-outlined"
          :class="[
            isLightColor
              ? 'text-custom-700 bg-custom-300'
              : 'text-custom-500 bg-custom-50',
            'p-1.5 rounded-md text-[22px] flex items-center justify-center',
          ]"
        >
          local_activity
        </span>
        <span class="font-medium text-lg">Tickets</span>
      </div>
      <div class="mt-3">
        <div class="h-fit" v-if="ticketSummary">
          <div
            class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          >
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Today</span>
                  <p class="text-xl font-medium block truncate">
                    {{ ticketSummary.ticket_today }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Last 7 Days</span>
                  <p class="text-xl font-medium block truncate">
                    {{ ticketSummary.ticket_last7Days }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Last 30 Days</span>
                  <p class="text-xl font-medium block truncate">
                    {{ ticketSummary.ticket_last30Days }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">6 Months</span>
                  <p class="text-xl font-medium block truncate">
                    {{ ticketSummary.ticket_last6Months }}
                  </p>
                </div>
              </div>
            </div>
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3"
            >
              <div class="flex gap-3">
                <div
                  class="min-h-full min-w-[4px] rounded-xl"
                  :class="isLightColor ? 'bg-custom-700' : 'bg-custom-500'"
                ></div>
                <div class="flex-1 max-w-full pr-2">
                  <span class="text-gray-500 block">Total</span>
                  <p class="text-xl font-medium block truncate">
                    {{ ticketSummary.ticket_total }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5"
          v-else
        >
          <template v-for="i in 5" :key="i">
            <div
              class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-5"
            >
              <Skeleton :count="2" />
            </div>
          </template>
        </div>
        <div
          class="bg-white rounded-lg border border-gray-200 shadow-sm px-3 py-3 mt-5"
        >
          <div
            class="flex justify-between items-center flex-wrap gap-5 px-3 py-1"
          >
            <span class="font-medium">Tickets</span>
            <div class="bg-gray-100 rounded-lg px-4 py-2 flex gap-4">
              <button
                @click="
                  ticketCurrentTab = 'sevenDay';
                  fetchChartData('ticket', 'sevenDay');
                "
                class="text-sm text-gray-500"
                :class="
                  ticketCurrentTab == 'sevenDay'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Week
              </button>
              <button
                @click="
                  ticketCurrentTab = 'last30Days';
                  fetchChartData('ticket', 'last30Days');
                "
                class="text-sm text-gray-500"
                :class="
                  ticketCurrentTab == 'last30Days'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Month
              </button>
              <button
                @click="
                  ticketCurrentTab = 'thisYear';
                  fetchChartData('ticket', 'thisYear');
                "
                class="text-sm text-gray-500"
                :class="
                  ticketCurrentTab == 'thisYear'
                    ? isLightColor
                      ? 'bg-custom-700 text-white rounded-md px-1.5 py-0.5'
                      : 'bg-custom-500 text-white rounded-md px-1.5 py-0.5'
                    : ''
                "
              >
                Year
              </button>
            </div>
          </div>
          <TicketsChart
            class="w-full"
            v-if="ticketChartData"
            ref="chart"
            :chartData="ticketChartData"
            :type="ticketCurrentTab"
          ></TicketsChart>
        </div>
      </div>
    </template>
  </template>
</template>

<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/store/auth.js";
import { defineAsyncComponent } from "vue";

export default {
  name: "Dashboard",
  data() {
    return {
      breadcrumb: {
        icon: "grid_view",
        pages: [{ name: "Dashboard" }],
      },
      processing: false,
      setup: null,
      currentTab: "sevenDay",
      earningCurrentTab: "sevenDay",
      serverCurrentTab: "sevenDay",
      subscriptionCurrentTab: "sevenDay",
      ticketCurrentTab: "sevenDay",
      userSummary: null,
      userCountryCount: [],
      userChartData: null,
      transactionSummary: null,
      transactionChartData: null,
      serverSummary: null,
      serverChartData: null,
      connectedServerCount: {
        connected: 0,
        disconnected: 0,
      },
      subscriptionSummary: null,
      subscriptionChartData: null,
      ticketSummary: null,
      ticketChartData: null,
    };
  },
  components: {
    UsersChart: defineAsyncComponent(() =>
      import("@/components/admin/charts/UsersChart.vue")
    ),
    EarningChart: defineAsyncComponent(() =>
      import("@/components/admin/charts/EarningChart.vue")
    ),
    ServerChart: defineAsyncComponent(() =>
      import("@/components/admin/charts/ServerChart.vue")
    ),
    SubscriptionChart: defineAsyncComponent(() =>
      import("@/components/admin/charts/SubscriptionChart.vue")
    ),
    TicketsChart: defineAsyncComponent(() =>
      import("@/components/admin/charts/TicketsChart.vue")
    ),
  },
  mounted() {
    this.fetchSetup();
    this.fetchSummaryData("user");
    this.fetchUserCountryCount();
    this.fetchChartData("user", this.currentTab);
    this.fetchSummaryData("transaction");
    this.fetchChartData("transaction", this.currentTab);
    this.fetchSummaryData("server");
    this.fetchChartData("server", this.currentTab);
    this.fetchConnectedServer();
    this.fetchSummaryData("subscription");
    this.fetchChartData("subscription", this.currentTab),
      this.fetchSummaryData("ticket");
    this.fetchChartData("ticket", this.currentTab);
  },
  computed: {
    ...mapState(useAuthStore, ["user"]),
    isSetupComplete() {
      if (this.setup) {
        return Object.values(this.setup).every((value) => value === true);
      } else {
        return false;
      }
    },
  },
  methods: {
    async fetchSetup() {
      this.processing = true;
      await this.$axios
        .get("/admin/setup")
        .then(({ data }) => {
          this.setup = data.setup;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async fetchSummaryData(type) {
      await this.$axios
        .get(`/admin/dashboard/summary/${type}`)
        .then(({ data }) => {
          this[`${type}Summary`] = data;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async fetchUserCountryCount() {
      await this.$axios
        .get(`/admin/dashboard/users-by-country`)
        .then(({ data }) => {
          this.userCountryCount = data.usersByCountry;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async fetchChartData(type, filter) {
      await this.$axios
        .get(`/admin/dashboard/chart/${type}/${filter}`)
        .then(({ data }) => {
          const chartDataKey = `${
            type.charAt(0).toUpperCase() + type.slice(1)
          }_chart_data`;
          this[`${type}ChartData`] = data[chartDataKey];
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
    async fetchConnectedServer() {
      await this.$axios
        .get(`/admin/dashboard/server-connection-counts `)
        .then(({ data }) => {
          if (data.serverStatusCounts || data.serverStatusCounts.length) {
            this.connectedServerCount.connected = data.serverStatusCounts
              .connected
              ? data.serverStatusCounts.connected
              : 0;
            this.connectedServerCount.disconnected = data.serverStatusCounts
              .disconnected
              ? data.serverStatusCounts.disconnected
              : 0;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
};
</script>
