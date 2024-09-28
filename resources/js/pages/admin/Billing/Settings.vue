<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <p class="text-xl flex gap-3 justify-between font-medium text-[#31363f]">
    Settings
  </p>
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
        prescriptions
      </span>
      <p class="font-medium">Billing Details</p>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 my-5 mb-0 xl:px-10 px-5 2xl:gap-40 xl:gap-28 sm:gap-x-14 gap-x-1 "
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Full Name</label
            >
          </div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <input
              type="text"
              name="name"
              v-model="billingDetails.name"
              id="name"
              placeholder="ServerAvatar Cloud Technologies PVT LTD"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1"
        >
          <div
            class="xl:col-span-3 2xl:grid-cols-4 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-9 2xl:grid-cols-10 sm:col-span-8 col-span-12">
            <small
              id="name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >City</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="billingDetails.city"
              type="text"
              name="city"
              id="city"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="City"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 "
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="city_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 sm:my-3.5 xl:my-5 xl:px-10 px-5 2xl:gap-40 xl:gap-28 sm:gap-x-14 gap-1"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div class="xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Email</label
            >
          </div>
          <div class="xl:col-span-9 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.email"
              type="email"
              name="email"
              id="email"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="noreply@serveravatar.com"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-9 sm:col-span-8 col-span-12">
            <small
              id="email_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny text-nowrap after:content-['*'] after:ml-0.5 after:text-red-500"
            >
              Postal Code</label
            >
          </div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <input
              v-model="billingDetails.postal_code"
              type="text"
              name="postal"
              id="postal_code"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="395006"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div
            class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
          >
            <small
              id="postal_code_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-5 mb-5 xl:px-10 px-5 2xl:gap-40 xl:gap-28 sm:gap-14 gap-1"
    >
      <div
        class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1"
      >
        <div class="xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12">
          <label
            class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >Address</label
          >
        </div>
        <div class="xl:col-span-9 sm:col-span-8 col-span-12">
          <textarea
            v-model="billingDetails.address"
            rows="4"
            name="address"
            id="address"
            placeholder="Enter Your Address"
            class="block w-full rounded-md border border-primary focus:border-primary py-2 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          ></textarea>
          <small
            id="address_message"
            class="text-red-500 error_message text-xs"
          ></small>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:gap-0 gap-3">
        <div>
          <div
            class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
          >
            <div
              class="xl:col-span-4 2xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12"
            >
              <label
                for="region"
                class="block text-tiny after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
              >
                Country
              </label>
            </div>
            <div
              class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 pt-1 xl:pt-0 col-span-12"
            >
              <select
                id="country_code"
                v-model="billingDetails.country_code"
                @change="updateCountryName()"
                placeholder="Select a Country"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
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
              class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 hidden xl:block"
            ></div>
            <div
              class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
            >
              <small
                id="country_code_message"
                class="text-red-500 error_message text-xs"
              ></small>
            </div>
          </div>
        </div>
        <div>
          <div
            class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1 items-center"
          >
            <div class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12">
              <label
                for="country"
                class="block text-tiny whitespace-nowrap after:content-['*'] after:ml-0.5 after:text-red-500 text-neutral-800 font-medium"
              >
                Region
              </label>
            </div>
            <div
              class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 pt-1 xl:pt-0 sm:col-span-9 col-span-12"
            >
              <select
                id="region_code"
                name="region_code"
                @change="updateRegionName()"
                v-model="billingDetails.region_code"
                placeholder="Select a Region"
                class="border text-gray-900 text-sm rounded-lg border-gray-300 focus:border-sa-500 focus:ring-0 block w-full p-2"
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
            </div>
          </div>
          <div
            class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1 items-center"
          >
            <div
              class="xl:col-span-4 2xl:col-span-3 sm:col-span-3 col-span-12 hidden xl:block"
            ></div>
            <div
              class="xl:col-span-8 2xl:col-span-9 md:grid-cols-4 sm:col-span-9 col-span-12"
            >
              <small
                id="region_code_message"
                class="text-red-500 error_message text-xs"
              ></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="my-5 sm:py-4 mt-10 ml-3 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        settings_applications
      </span>
      <p class="font-medium">Billing Settings</p>
    </div>
    <div class="xl:px-10 px-5">
      <div
        class="bg-[#FFFAE5] mt-5 text-[#EB9A49] text-tiny px-4 py-2 rounded-md"
      >
        <b class="font-medium"> Note:</b> Once you set the currency, you can't
        update it.
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 my-3 xl:px-10 px-5 2xl:gap-40 xl:gap-28 sm:gap-14 gap-1"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-6 2xl:col-span-5 items-center flex gap-1 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:text-red-500"
            >
              Currency</label
            >
            <span
              v-tooltip="
                `Select your preferred currency from the dropdown. The chosen currency symbol will be displayed across the panel, and all payments will be processed in that currency.`
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <select
              id="currency"
              name="currency"
              :disabled="isDisableCurrency"
              v-model="billingDetails.currency"
              class="block w-full rounded-md disabled:bg-gray-50 border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            >
              <option value="">Select a Currency</option>
              <option
                :value="currency.code"
                v-for="currency in currencies"
                :key="currency.iso2"
              >
                {{ currency.name }} ({{ currency.symbol_native }})
              </option>
            </select>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-6 2xl:col-span-5 items-center sm:col-span-3 col-span-12 hidden xl:block"
          ></div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <small
              id="currency_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 mt-2 sm:gap-x-3 gap-2"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 items-center flex gap-1 sm:col-span-x-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >
              Billing Mode</label
            >
            <span
              v-tooltip="
                `Select the billing mode between prepaid or postpaid. Prepaid requires >0 credits, either free sign up credits or paid credits. Postpaid allows users to pay on the monthly basis after the services have been used.`
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="xl:col-span-5 2xl:col-span-6 col-span-9">
            <div class="flex flex-row 2xl:flex-row 2xl:gap-10 gap-5 w-full">
              <div class="flex items-center">
                <input
                  v-model="billingDetails.billing_mode"
                  id="postpaid"
                  name="billing_mode"
                  type="radio"
                  class="inline-flex h-4 w-4 border-gray-300 bg-white text-sa-500 focus:ring-0"
                  value="postpaid"
                />
                <label
                  for="postpaid"
                  class="ml-2 text-sm text-gray-500 inline-flex"
                >
                  Postpaid
                </label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="billingDetails.billing_mode"
                  id="prepaid"
                  name="billing_mode"
                  type="radio"
                  class="inline-flex h-4 w-4 border-gray-300 bg-white text-sa-500 focus:ring-0"
                  value="prepaid"
                />
                <label
                  for="prepaid"
                  class="ml-2 text-sm text-gray-500 inline-flex"
                >
                  Prepaid
                </label>
              </div>
            </div>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 mt-2 sm:gap-x-3 gap-2"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 sm:col-span-x-3 col-span-12 hidden xl:block"
          ></div>
          <div class="xl:col-span-7 2xl:col-span-6 col-span-12">
            <small
              id="billing_mode_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 mt-3 xl:px-10 px-5 2xl:gap-x-40 xl:gap-x-28 sm:gap-x-14 gap-x-1 gap-y-3"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="2xl:col-span-5 xl:col-span-6 items-center flex gap-1 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >
              Free Signup Credit</label
            >
            <span
              v-tooltip="
                'This option allows you to offer a specific amount of free credit to new users who registered for the service. This can be used as an incentive to encourage users to signup and experience the service.'
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <div class="flex">
              <div
                class="pointer-events-none flex items-center pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
              >
                <span class="text-tiny pr-0.5">{{ currencySymbol }}</span>
              </div>
              <input
                v-model="billingDetails.new_registration_free_credits"
                type="number"
                name="New Registration"
                id="new_registration_free_credits"
                class="block w-full rounded-r-md border border-l-0 border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="0"
              />
            </div>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="2xl:col-span-5 xl:col-span-6 items-center sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <small
              id="new_registration_free_credits_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 items-center flex gap-1 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Min. Recharge</label
            >
            <span
              v-tooltip="
                `Specify the minimum amount that users must recharge their account with to access the service. This ensures that users have a certain level of investment in the service and users cannot add less than the designated amount to their balance.`
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="2xl:col-span-8 xl:col-span-7 sm:col-span-8 col-span-12">
            <div class="flex">
              <div
                class="pointer-events-none flex items-center pl-3 pr-2 py-2 bg-gray-50 text-gray-500 border border-neutral-300 rounded-l-md"
              >
                <span class="text-tiny pr-0.5">{{ currencySymbol }}</span>
              </div>
              <input
                v-model="billingDetails.minimum_recharge_amount"
                type="number"
                name="minimum_recharge_amount"
                id="minimum_recharge_amount"
                class="block w-full rounded-r-md border border-l-0 border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                placeholder="10"
              />
            </div>
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 items-center sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="2xl:col-span-8 xl:col-span-7 sm:col-span-8 col-span-12">
            <small
              id="minimum_recharge_amount_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-6 my-3 xl:px-10 px-5 2xl:gap-40 xl:gap-28 sm:gap-14 gap-3"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-6 2xl:col-span-5 items-center flex gap-1 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >
              Payment Due</label
            >
            <span
              v-tooltip="
                `Set a deadline for users to make a payment before their service gets suspended or terminated. This sets the due date for invoices, ensuring timely payments and preventing service disruptions.`
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.due_days"
              type="number"
              name="due_days"
              id="due_days"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="3"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-6 2xl:col-span-5 items-center sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="2xl:col-span-7 xl:col-span-6 sm:col-span-8 col-span-12">
            <small
              id="due_days_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 items-center gap-1 flex sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
            >
              Retention Time</label
            >
            <span
              v-tooltip="
                `This option allows you to set the duration for how long your user's servers and data will remain after the due date has passed. The server and data will be permanently deleted after the retention period ends.`
              "
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[16px] cursor-pointer',
              ]"
            >
              info
            </span>
          </div>
          <div class="2xl:col-span-8 xl:col-span-7 sm:col-span-8 col-span-12">
            <input
              v-model="billingDetails.retention_period"
              type="number"
              name="retention "
              id="retention_period"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="5"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 items-center sm:col-span-3 col-span-12 xl:block hidden"
          ></div>
          <div class="2xl:col-span-8 xl:col-span-7 sm:col-span-8 col-span-12">
            <small
              id="retention_period_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div
    class="my-10 mb-5 sm:py-4 mt-10 ml-3 rounded-md bg-white border pb-3 relative"
  >
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        contract
      </span>
      <p class="font-medium">Tax Identification Numbers</p>
    </div>

    <div class="xl:px-10 px-5 sm:my-5 mt-3">
      <div class="sm:flex gap-8">
        <p class="text-sm text-gray-500 pt-0.5 flex-1">
          You can use any Custom Tax Label and Tax Number like GST Number and
          VAT ID. This Custom Tax Label and Number will display in the Invoice.
        </p>
      </div>
    </div>
    <div
      v-for="(items, index) in billingDetails.tax_numbers"
      :key="index"
      class="grid sm:grid-cols-2 grid-cols-1 xl:my-5 sm:my-3 xl:px-10 px-5 2xl:gap-x-40 xl:gap-x-28 sm:gap-x-14 gap-x-1 gap-y-3 "
    >
      <div
        class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
      >
        <div class="xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12">
          <label class="font-medium text-nowrap text-tiny">Label</label>
        </div>
        <div class="xl:col-span-9 sm:col-span-8 col-span-12">
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
        class="grid xl:grid-cols-12 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 sm:gap-x-3 gap-1 items-center"
      >
        <div class="xl:col-span-3 md:pt-0 pt-2 sm:col-span-3 col-span-12">
          <label class="font-medium text-tiny">Number</label>
        </div>
        <div class="xl:col-span-9 sm:col-span-9 col-span-12 flex gap-5">
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
    <div class="flex justify-end mr-5">
      <Button
        :class="[
          '',
          isLightColor
            ? 'bg-custom-200 text-custom-700'
            : 'bg-custom-50 text-custom-500',
          'mt-2 sm:mt-0 flex justify-end items-center gap-1',
        ]"
        type="button"
        @click="addTax()"
      >
        <i class="las la-plus"></i>
        Add
      </Button>
    </div>
  </div>

  <div class="text-end">
    <Button @click="updateBillingManagment"> Save Settings </Button>
  </div>
  <Confirmation
    @closeModal="closeModal"
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Update Billing Management'"
    :btnBgColor="'bg-[#FFB74D]'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="saveData"
  >
    <template #icon>
      <span class="material-symbols-outlined text-[#FFB74D] text-[22px]"
        >subheader</span
      >
    </template>

    <template v-slot:content
      ><span class="text-tiny text-gray-600"
        >Are you sure you want to update your billing details?
      </span>
      <div class="my-5 text-tiny font-medium flex gap-3">
        <div>
          <span
            class="material-symbols-outlined bg-[#FFB74D] text-white rounded-full font-semibold text-[20px] p-0.5 flex items-center"
          >
            check
          </span>
        </div>
        <span>
          The changes for billing settings will be applied to all the users.
          Kindly review the details carefully before saving the updates.
        </span>
      </div>
    </template>
  </Confirmation>
</template>
<script>
export default {
  name: "Settings",
  data() {
    return {
      breadcrumb: {
        title: "Billing",
        icon: "lab_profile",
        pages: [{ name: "Settings" }],
      },
      currencies: [],
      countries: [],
      processing: false,
      showLoader: false,
      openConfirmation: false,
      isDisableCurrency: false,
      billingDetails: {
        billing_mode: "",
        name: "",
        email: "",
        address: "",
        city: "",
        region: "",
        region_code: "",
        country: "",
        country_code: "",
        postal_code: "",
        tax_numbers: [
          { name: "GST Number", value: "" },
          { name: "VAT ID", value: "" },
        ],
        created_at: "",
        update_at: "",
        price_per_gb: "",
        free_backups: "",
        free_backup_size: "",
        new_registration_free_credits: "",
        minimum_recharge_amount: "",
        due_days: "",
        currency: "",
        currency_symbol: "",
        invoice_prefix: "",
        refund_period: "",
        retention_period: "",
      },
    };
  },
  computed: {
    currencySymbol() {
      let currency_name = this.billingDetails.currency
        ? this.billingDetails.currency
        : window.siteSettings.currency;
      let currency = this.currencies.find(
        (currency) => currency.code === currency_name
      );
      return currency ? currency.symbol_native : "$";
    },
    getRegions() {
      let country = this.countries.find(
        (country) => country.country_name === this.billingDetails.country
      );
      return country ? country.states : [];
    },
  },
  mounted() {
    this.fetchBillingManagement();
    this.fetchCurrency();
    this.getCountries();
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
      await this.$axios
        .get(`/countries`)
        .then(({ data }) => {
          this.countries = data.countries;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
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
    async fetchCurrency() {
      await this.$axios
        .get(`/currencies`)
        .then(({ data }) => {
          this.currencies = Object.keys(data.currencies).map((key) => ({
            code: key,
            ...data.currencies[key],
          }));
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
        });
    },
    async fetchBillingManagement() {
      await this.$axios
        .get("/admin/billing-details")
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
            this.billingDetails = {
              ...data.billingDetail,
              tax_numbers: this.billingDetails.tax_numbers,
            };
            if (
              data.billingDetail.currency !== "" &&
              data.billingDetail.currency_symbol !== ""
            ) {
              this.isDisableCurrency = true;
            }
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    updateBillingManagment() {
      this.openConfirmation = true;
    },
    closeModal() {
      this.openConfirmation = false;
    },
    async saveData() {
      this.hideError();
      this.showLoader = true;

      let { otherTaxs, ...otherProperties } = this.billingDetails;
      otherTaxs = this.billingDetails.tax_numbers.filter(
        (item) => item.name !== "" && item.value !== ""
      );

      let currency_symbol = this.currencies.find(
        (currency) => currency.code === this.billingDetails.currency
      );

      let final = {
        ...otherProperties,
        currency_symbol: currency_symbol ? currency_symbol.symbol_native : "",
        tax_numbers: otherTaxs.length ? [...otherTaxs] : null,
      };

      this.$axios
        .post(`/admin/billing-details`, final)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.closeModal();
          location.reload();
          setTimeout(() => {
            this.fetchBillingManagement();
          }, 600);
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            this.displayError(response.data);
          } else {
            this.$toast.error(response.data.message);
            this.fetchBillingManagement();
          }
          this.closeModal();
        })
        .finally(() => {
          this.showLoader = false;
        });
    },
  },
};
</script>
