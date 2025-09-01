<template>
  <Breadcrumb :breadcrumb="breadcrumb" />
  <div class="text-xl whitespace-nowrap text-[#31363f] font-medium my-2.5">
    Settings
  </div>

  <div class="my-5 mt-8 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        settings
      </span>
      <p class="font-medium">Settings</p>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 md:mt-8 xl:gap-x-32 2xl:gap-x-52 sm:gap-x-14 gap-1 px-7 xl:mb-6 mb-4 mt-10 sm:pt-2"
    >
      <div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Product Name</label
            >
          </div>
          <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
            <input
              v-model="payload.app_name"
              type="text"
              name="product name"
              id="app_name"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter Product Name"
            />
          </div>
        </div>
        <div
          class="grid xl:grid-cols-12 2xl:grid-cols-11 md:grid-cols-4 sm:grid-cols-1 grid-cols-12 gap-1"
        >
          <div
            class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"
          ></div>
          <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
            <small
              id="app_name_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1 mt-2 sm:mt-0">
          <div
            class="xl:col-span-4 2xl:col-span-3 xl:pt-2 sm:col-span-3 col-span-12"
          >
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Tag Line</label
            >
          </div>
          <div class="xl:col-span-8 2xl:col-span-8 sm:col-span-12 col-span-12">
            <input
              v-model="payload.tag_line"
              type="text"
              name="tagLine"
              id="tag_line"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter Tag Line"
            />
          </div>
        </div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1 ">
          <div
            class="xl:col-span-4 2xl:col-span-3 xl:pt-2 sm:col-span-3 col-span-12"
          ></div>
          <div class="xl:col-span-8 2xl:col-span-8 sm:col-span-12 col-span-12">
            <small
              id="tag_line_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>

    <div
      class="grid sm:grid-cols-2 grid-cols-1 xl:gap-x-32 2xl:gap-x-52 sm:gap-x-14 gap-1 px-7"
    >
      <div
        class="grid 2xl:grid-cols-11 xl:grid-cols-12 md:grid-cols-4 mb-3 sm:grid-cols-1 grid-cols-12 gap-1"
      >
        <div
          class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"
        >
          <label
            class="font-medium text-tiny text-nowrap after:content-['*'] after:ml-0.5 after:text-red-500"
            >Brand Color</label
          >
        </div>

        <div
          class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12 rounded-md border items-center flex p-1.5 gap-2 border-primary focus:border-primary py-1 text-sm"
        >
          <ColorPicker
            v-model:pureColor="payload.color_code"
            :format="'hex'"
            id="color_code"
            name="color_code"
            class="w-8 h-8"
            pickerType="Chrome"
          />
          <span class="text-gray-800">{{ payload.color_code }}</span>
          <small
            id="color_code_message"
            class="text-red-500 error_message text-xs"
          ></small>
        </div>
      </div>
    </div>
    <div
      class="grid xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 xl:gap-x-5 2xl:gap-x-14 sm:gap-x-14 gap-5 px-7 xl:mb-6 my-5"
    >
      <div
        class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-0 gap-x-14 gap-y-5 xl:flex"
      >
        <div class="flex-1 mr-4">
          <p
            :class="favicon === null ? 'h-full' : ''"
            class="whitespace-nowrap mb-2 flex items-center xl:justify-center justify-center font-medium"
          >
            Favicon
          </p>
          <div v-if="favicon != null" class="flex justify-center">
            <img :src="favicon" :alt="app_name" class="h-11" />
          </div>
          <div
            class="text-xs mt-1 flex justify-center"
            v-if="payload.favicon && payload.favicon.name"
          >
            <span class="text-sm">{{ payload.favicon.name }}</span>
            <button
              type="button"
              @click="
                payload.favicon = null;
                favicon = null;
              "
              class="text-red-500"
            >
              <i class="fa fa-times-circle ml-1"></i>
            </button>
          </div>
        </div>
        <div
          class="flex-1 border-dashed border-2 p-4 rounded-md flex items-center justify-center"
        >
          <input
            type="file"
            ref="favicon"
            style="display: none"
            accept=".png, .jpg, .jpeg, .ico"
            id="favicon"
            @change="handleFaviconUpload"
          />
          <button
            class="items-center justify-center gap-2 text-gray-500"
            onclick="document.getElementById('favicon').click()"
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined  text-2xl',
              ]"
            >
              cloud_upload
            </span>
            <p class="text-tiny">Upload a Favicon</p>
          </button>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-0 gap-5 gap-x-14">
        <div class="flex-1 mr-4">
          <p
            :class="logo === null ? 'h-full' : ''"
            class="whitespace-nowrap mb-2 flex justify-center items-center font-medium"
          >
            Brand Logo
          </p>
          <div v-if="logo != null" class="flex justify-center items-center">
            <img
              :src="logo"
              :alt="app_name"
              class="h-7 xl:h-6 2xl:h-10 xl:mt-4 2xl:mt-0"
            />
          </div>
          <div
            class="text-xs mt-1 flex justify-center"
            v-if="payload.logo && payload.logo.name"
          >
            <span class="text-sm">{{ payload.logo.name }}</span>
            <button
              type="button"
              @click="
                payload.logo = null;
                logo = null;
              "
              class="text-red-500"
            >
              <i class="fa fa-times-circle text-xs ml-1"></i>
            </button>
          </div>
        </div>
        <div
          class="flex-1 border-dashed border-2 p-4 rounded-md flex items-center justify-center"
        >
          <input
            type="file"
            ref="logo"
            style="display: none"
            accept="image/jpeg, image/jpg, image/png, image/webp"
            id="logo"
            @change="handleSiteLogoUpload"
          />
          <button
            class="items-center justify-center gap-2 text-gray-500"
            onclick="document.getElementById('logo').click()"
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined  text-2xl',
              ]"
            >
              cloud_upload
            </span>
            <p class="text-tiny">Upload a Logo</p>
          </button>
        </div>
      </div>
      <div
        class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-0 gap-x-14 gap-y-5 xl:flex"
      >
        <div class="flex-1 xl:pt-0 pt-2 mr-4">
          <p
            :class="icon === null ? 'h-full' : ''"
            class="whitespace-nowrap mb-2 flex justify-center items-center font-medium"
          >
            Logo Icon
          </p>
          <div v-if="icon != null" class="flex justify-center items-center">
            <img :src="icon" :alt="app_name" class="h-11" />
          </div>
          <div
            class="text-xs mt-1 flex justify-center"
            v-if="payload.icon && payload.icon.name"
          >
            <span class="text-sm">{{ payload.icon.name }}</span>
            <button
              type="button"
              @click="
                payload.icon = null;
                icon = null;
              "
              class="text-red-500"
            >
              <i class="fa fa-times-circle text-xs ml-1"></i>
            </button>
          </div>
        </div>
        <div
          class="flex-1 border-dashed border-2 p-4 rounded-md flex items-center justify-center"
        >
          <input
            type="file"
            ref="icon"
            style="display: none"
            accept=".png, .jpg, .jpeg, .ico"
            id="icon"
            @change="handleSmallLogoUpload"
          />
          <button
            class="items-center justify-center gap-2 text-gray-500"
            onclick="document.getElementById('icon').click()"
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined  text-2xl',
              ]"
            >
              cloud_upload
            </span>
            <p class="text-tiny">Upload Only Icon</p>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="my-5 mt-8 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        groups
      </span>
      <p class="font-medium">Organization</p>
    </div>
    <div class="px-7">
      <div class="py-5 grid sm:grid-cols-3 gap-x-5 rounded-md">
        <div class="sm:col-span-2">
          <p class="text-tiny py-2">
            Every server you create in your self-hosted panel is created with
            ServerAvatar and will be managed directly through the self-hosted
            panel of your organization.
          </p>
        </div>
        <div class="flex items-center justify-end sm:col-span-1">
          <div
            class="block w-full text-sm bg-[#F6F6F6] pl-4 rounded-md border border-neutral-300 py-2 text-gray-800"
            v-if="matchingOrganization"
          >
            <span class="">{{
              matchingOrganization && matchingOrganization.name
            }}</span>
          </div>
          <select
            v-else
            id="status"
            name="status"
            v-model="payload.sa_org_id"
            class="block mt-2 w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          >
            <option
              :value="organize.id"
              v-for="organize in organization"
              :key="organize.id"
            >
              {{ organize.name }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="my-5 mt-8 rounded-md bg-white border relative">
    <div
      class="-left-5 p-2 bg-white -top-5 absolute flex justify-center items-center gap-2"
    >
      <span
        :class="[
          isLightColor ? 'text-custom-700' : 'text-custom-500',
          'material-symbols-outlined',
        ]"
      >
        manufacturing
      </span>
      <p class="font-medium">Advance Settings</p>
    </div>
    <div class="mt-8 px-7">
      <div class="mt-5 bg-red-50 text-red-500 text-tiny px-4 py-2 rounded-md">
        <b class="font-medium"> Note:</b> Ensure the Redis password is correct,
        as an incorrect one may cause site errors.
      </div>
    </div>
    <div
      class="grid sm:grid-cols-2 grid-cols-1 mt-5 xl:gap-x-32 2xl:gap-x-52 sm:gap-x-14 gap-1 px-7 mb-3 md:mb-6"
    >
      <div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1">
          <div
            class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"
          >
            <label class="font-medium text-nowrap text-tiny"
              >Google Analytics ID</label
            >
          </div>
          <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
            <input
              v-model="payload.analytics"
              type="text"
              name="google"
              id="analytics"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
              placeholder="Enter Google Analytics ID"
            />
          </div>
        </div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1">
          <div
            class="xl:col-span-5 2xl:col-span-4 xl:pt-2 sm:col-span-3 col-span-12"
          ></div>
          <div class="xl:col-span-7 2xl:col-span-7 sm:col-span-12 col-span-12">
            <small
              id="analytics_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
      <div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1 mt-4 sm:mt-0">
          <div class="2xl:col-span-3 xl:col-span-4 xl:pt-2 col-span-4">
            <label
              class="font-medium text-nowrap text-tiny after:content-['*'] after:ml-0.5 after:text-red-500"
              >Redis Password</label
            >
          </div>
          <div class="2xl:col-span-8 xl:col-span-8 sm:col-span-12 col-span-12">
            <input
              v-model="payload.redis_password"
              type="text"
              name="password"
              id="redis_password"
              placeholder="Enter Redis Password"
              class="w-full block rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
          </div>
        </div>
        <div class="grid 2xl:grid-cols-11 grid-cols-12 gap-1">
          <div class="2xl:col-span-3 xl:col-span-4 xl:pt-2 col-span-4"></div>
          <div class="2xl:col-span-8 xl:col-span-8 sm:col-span-12 col-span-12">
            <small
              id="redis_password_message"
              class="text-red-500 error_message text-xs"
            ></small>
          </div>
        </div>
      </div>
    </div>
    <div class="grid xl:grid-cols-2 grid-cols-1 gap-x-10 gap-5 px-7 mb-6">
      <div class="">
        <h1 class="mb-5 font-medium text-lg" for="loglevel">Header Code</h1>
        <div class="mt-2">
          <v-ace-editor
            v-model:value="payload.header"
            lang="sh"
            theme="terminal"
            :options="{ showPrintMargin: false, fontSize: '15px' }"
            style="height: 500px; border-radius: 6px"
          />
        </div>
        <small id="header_message" class="error_message text-red-500"></small>
      </div>
      <div class="">
        <h1 class="font-medium mb-5 text-lg" for="loglevel">Footer Code</h1>
        <div class="mt-2">
          <v-ace-editor
            v-model:value="payload.footer"
            lang="sh"
            theme="terminal"
            :options="{
              showPrintMargin: false,
              fontSize: '15px',
            }"
            style="height: 500px; border-radius: 6px"
          />
        </div>
        <small id="footer_message" class="error_message text-red-500"></small>
      </div>
    </div>
  </div>

  <div class="my-7 text-end">
    <Button @click="openConfirmationModal"> Save Settings </Button>
  </div>

  <Confirmation
    @closeModal="closeModal"
    :show="openConfirmation"
    :showLoader="showLoader"
    :confirmationTitle="'Update Site Settings'"
    :btnBgColor="'bg-yellow-500'"
    :submitBtnTitle="`Yes I'm sure`"
    @confirm="saveSiteSettings"
  >
    <template #icon>
      <span class="material-symbols-outlined text-yellow-500 text-[22px]"
        >subheader</span
      >
    </template>

    <template v-slot:content
      ><span class="text-tiny text-gray-600"
        >Are you sure you want to update the site settings?
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
          This changes will be reflected on every user's white-label panel.
          Please review and confirm the details before proceeding.
        </span>
      </div>
    </template>
  </Confirmation>
</template>
<script>
import { VAceEditor } from "vue3-ace-editor";
import ace from "ace-builds";
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import modeShUrl from "ace-builds/src-noconflict/mode-sh?url";
ace.config.setModuleUrl("ace/mode/sh", modeShUrl);

import themeTerminalUrl from "ace-builds/src-noconflict/theme-terminal?url";
ace.config.setModuleUrl("ace/theme/terminal", themeTerminalUrl);

import siteMixin from "@/mixins/siteSettings";

export default {
  name: "Settings",
  mixins: [siteMixin],
  components: {
    VAceEditor,
    ColorPicker,
  },
  data() {
    return {
      breadcrumb: {
        icon: "settings_applications",
        pages: [
          {
            name: "Site Settings",
          },
        ],
      },
      payload: {
        app_name: "",
        color_code: "159C8C",
        analytics: "",
        redis_password: "",
        sa_org_id: "",
        tag_line: "",
        favicon: null,
        logo: null,
        icon: null,
        header: "",
        footer: "",
      },
      organization: [],
      favicon: null,
      logo: null,
      icon: null,
      processing: false,
      showLoader: false,
      organizationId: "",
    };
  },
  computed: {
    matchingOrganization() {
      return this.organization.find((org) => org.id == this.organizationId);
    }
  },
  mounted() {
    this.fetchSiteSetting();
    this.fetchOrganization();
  },
  methods: {
    async fetchSiteSetting() {
      await this.$axios
        .get("/site-setting")
        .then(({ data }) => {
          if (data) {
            this.organizationId = data.sa_org_id ? data.sa_org_id : null;
            this.payload.app_name = data.app_name;
            this.payload.redis_password = data.redis_password
              ? data.redis_password
              : null;
            this.payload.sa_org_id = data.sa_org_id ? data.sa_org_id : null;
            this.payload.color_code = `#${data.color_code}`;
            this.favicon = data.favicon == "null" ? null : data.favicon;
            this.logo = data.logo == "null" ? null : data.logo;
            this.icon = data.icon == "null" ? null : data.icon;
            this.payload.tag_line = data.tag_line == null ? "" : data.tag_line;
            this.payload.analytics =
              data.analytics == "null" ? "" : data.analytics;
            this.payload.header = data.header == "null" ? "" : data.header;
            this.payload.footer = data.footer == "null" ? "" : data.footer;
          }
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          (this.processing = false), (this.showLoader = false);
        });
    },
    async fetchOrganization() {
      await this.$axios
        .get("admin/get-organizations")
        .then(({ data }) => {
          this.organization = data.organizations;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        });
    },
  },
};
</script>
