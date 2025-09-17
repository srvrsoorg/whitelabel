<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="bg-white border p-5 rounded-md shadow-sm mt-5">
    <div class="grid grid-cols-4 gap-4">
      <div class="sm:col-span-3 col-span-4">
        <div class="flex items-center gap-3">
          <img src="/logo/alert.png" class="w-7" />
          <p class="font-medium text-xl">Integrations Required</p>
        </div>
        <div class="flex gap-4 my-4 xl:items-center mt-7">
          <div>
            <span
              :class="[
                'material-symbols-outlined p-1.5 rounded-md',
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
              ]"
            >
              integration_instructions
            </span>
          </div>
          <div>
            <p class="text-gray-500">
              Certain required configurations have not been set up by an
              administrator. As a result, you are currently unable to create a
              server.
            </p>
          </div>
        </div>

        <div class="flex gap-4 xl:items-center my-4">
          <div>
            <span
              :class="[
                'material-symbols-outlined p-1.5 rounded-md',
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
              ]"
            >
              integration_instructions
            </span>
          </div>
          <div>
            <p class="text-gray-500">
              Please reach out to support to ensure that all necessary
              requirements are properly configured.
            </p>
          </div>
        </div>

        <div class="flex gap-4 xl:items-center my-4">
          <div>
            <span
              :class="[
                'material-symbols-outlined p-1.5 rounded-md',
                isLightColor
                  ? 'bg-custom-200 text-custom-700'
                  : 'bg-custom-50 text-custom-500',
              ]"
            >
              integration_instructions
            </span>
          </div>
          <div>
            <p class="text-gray-500">
              Once all the required configurations are completed, you will be
              able to create a server.
            </p>
          </div>
        </div>
      </div>
      <div class="hidden sm:block">
        <img src="/logo/server.png" class="2xl:w-64 w-full" />
      </div>
    </div>
    <template v-if="is_admin">
      <div class="text-[16px] font-medium xl:py-0 mt-5 mb-3">
        <p>
          Click on the links below to access the settings and complete the setup
          process:
        </p>
      </div>
      <div class="mt-2 sm:mt-0 inline-block" v-if="!isSetProviders">
        <router-link
          :to="{ name: 'integrateCloudPlatForms' }"
          :class="[
            'font-medium text-tiny xs:flex items-center gap-1',
            isLightColor ? 'text-custom-700' : 'text-custom-500',
          ]"
        >
          <span>Manage Cloud Platform Integrations</span>
          <span
            :class="[
              'text-blue-600 material-symbols-outlined cursor-pointer -rotate-45 pr-1.5 xs:pr-0 text-[20px] ',
              
            ]"
          >
            link
          </span>
        </router-link>
      </div>
      <br>
      <div class="mt-2 sm:mt-0 inline-block" v-if="!isSetBilling">
        <router-link
          :to="{ name: 'Settings' }"
          :class="[
            'font-medium text-tiny xs:flex items-center gap-1',
            isLightColor ? 'text-custom-700' : 'text-custom-500',
          ]"
        >
          <span>Setup Billing Details</span>
          <span
            :class="[
               'text-blue-600 material-symbols-outlined cursor-pointer -rotate-45 pr-1.5 xs:pr-0 text-[20px] ',
              
            ]"
          >
            link
          </span>
        </router-link>
      </div>
    </template>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/store/auth.js";

export default {
  data() {
    return {
      breadcrumb: {
        icon: "integration_instructions",
        pages: [
        { name: "Integrations Required" }
        ],
      },
    };
  },
  props: ["isSetProviders", "isSetBilling"],
  computed: {
    ...mapState(useAuthStore, ["is_admin"]),
  },
};
</script>