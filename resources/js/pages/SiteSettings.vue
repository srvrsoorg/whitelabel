<template>
  <InstallationInfo />
  <div class="w-full max-w-xl mx-auto p-4 2xl:mt-24 sm:mt-14 my-5">
    <form class="mt-3" action="javascript:void(0)" @submit="saveSiteSettings()">
      <div>
        <label
          for="app_name"
          class="block text-tiny text-neutral-800 font-medium"
        >
          Application Name
        </label>
        <div class="mt-1.5">
          <input
            id="app_name"
            name="app_name"
            type="text"
            placeholder="Enter Application Name"
            v-model="payload.app_name"
            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
          />
          <small
            id="app_name_message"
            class="error_message text-red-500"
          ></small>
        </div>
      </div>
      <div class="grid grid-cols-1 gap-x-5">
        <div class="mt-3">
          <label
            for="analytics"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Google Analytics ID
          </label>
          <div class="mt-1.5">
            <input
              id="analytics"
              name="analytics"
              type="text"
              placeholder="Enter Google Analytics ID"
              v-model="payload.analytics"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="analytics_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
      </div>
      <div class="grid md:grid-cols-2 grid-cols-1 gap-x-5">
        <div class="mt-3">
          <label
            for="tag_line"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Tag Line
          </label>
          <div class="mt-1.5">
            <input
              id="tag_line"
              name="tag_line"
              type="text"
              placeholder="Enter Tag Line"
              v-model="payload.tag_line"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="tag_line_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-3">
          <label
            for="color_code"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Select Your Brand Color
          </label>
          <div class="mt-1.5">
            <div
              class="rounded-md border flex items-center gap-2 border-neutral-300 focus:border-neutral-300 px-1.5 py-1.5"
            >
              <ColorPicker
                v-model:pureColor="payload.color_code"
                :format="'hex'"
                id="color_code"
                name="color_code"
                class="w-8 h-8"
              />
              <span class="text-gray-500 text-sm">{{
                payload.color_code
              }}</span>
            </div>
            <small
              id="color_code_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
      </div>
      <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-4">
        <div>
          <label
            for="app_name"
            class="block text-tiny text-neutral-800 font-medium"
            >Favicon</label
          >
          <input
            type="file"
            ref="favicon"
            style="display: none"
            accept=".png, .jpg, .jpeg, .ico"
            id="favicon"
            @change="handleFaviconUpload"
          />
          <div
            class="mt-2 py-2 px-5 border border-dashed border-gray-400 rounded-md flex flex-col items-center justify-center cursor-pointer"
            @click="
              $refs.favicon.click();
              favicon = null;
            "
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[20px]',
              ]"
              >cloud_upload</span
            >
            <p class="text-sm text-gray-500">Upload a favicon</p>
          </div>
          <div
            class="overflow-hidden mt-2 flex flex-col justify-center items-center"
            v-if="favicon !== null"
          >
            <img :src="favicon" class="h-10" />
            <div class="mt-1.5" v-if="payload.favicon && payload.favicon.name">
              <span class="text-sm">{{ payload.favicon.name }}</span>
              <button
                type="button"
                @click="
                  payload.favicon = null;
                  favicon = null;
                "
                class="text-red-500"
              >
                <i class="fa fa-times-circle text-xs ml-1"></i>
              </button>
            </div>
          </div>
          <small
            id="favicon_message"
            class="error_message text-red-500"
          ></small>
        </div>
        <div>
          <label
            for="app_name"
            class="block text-tiny text-neutral-800 font-medium"
            >Brand Logo</label
          >
          <input
            type="file"
            ref="logo"
            style="display: none"
            accept=".png, .jpg, .jpeg"
            id="logo"
            @change="handleSiteLogoUpload"
          />
          <div
            class="mt-2 py-2 px-5 border border-dashed border-gray-400 rounded-md flex flex-col items-center justify-center cursor-pointer"
            @click="
              $refs.logo.click();
              logo = null;
            "
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[20px]',
              ]"
              >cloud_upload</span
            >
            <p class="text-sm text-gray-500">Upload a Logo</p>
          </div>
          <div
            class="overflow-hidden flex mt-2 flex-col justify-center items-center"
            v-if="logo !== null"
          >
            <img :src="logo" class="h-10" />
            <div class="mt-1.5" v-if="payload.logo && payload.logo.name">
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
          <small id="logo_message" class="error_message text-red-500"></small>
        </div>
        <div>
          <label
            for="app_name"
            class="block text-tiny text-neutral-800 font-medium"
            >Logo Icon</label
          >
          <input
            type="file"
            ref="icon"
            style="display: none"
            accept=".png, .jpg, .jpeg"
            id="icon"
            @change="handleSmallLogoUpload"
          />
          <div
            class="mt-2 py-2 px-5 border border-dashed border-gray-400 rounded-md flex flex-col items-center justify-center cursor-pointer"
            @click="
              $refs.icon.click();
              icon = null;
            "
          >
            <span
              :class="[
                isLightColor ? 'text-custom-700' : 'text-custom-500',
                'material-symbols-outlined text-[20px]',
              ]"
              >cloud_upload</span
            >
            <p class="text-sm text-gray-500">Upload a Icon</p>
          </div>

          <div
            class="overflow-hidden mt-2 flex flex-col justify-center items-center"
            v-if="icon !== null"
          >
            <img :src="icon" class="h-10" />
            <div class="mt-1.5" v-if="payload.icon && payload.icon.name">
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
          <small id="icon_message" class="error_message text-red-500"></small>
        </div>
      </div>
      <div class="my-5 border rounded-md p-4">
        <h1 class="font-medium">Organization</h1>
        <p class="text-sm text-gray-500">
          In this organization, the server you create in your self-hosted panel
          will be managed through that panel.
        </p>
        <select
          v-model="payload.sa_org_id"
          name="sa_org_id"
          id="sa_org_id"
          class="block mt-3 w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-500 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
        >
          <option value="" disabled>Select an Organization</option>
          <option
            :value="organize.id"
            v-for="organize in organization"
            :key="organize"
          >
            {{ organize.name }}
          </option>
        </select>
        <small
          id="sa_org_id_message"
          class="error_message text-red-500"
        ></small>
      </div>
      <div class="text-end mt-5">
        <Button type="submit" :disabled="processing" :class="['px-5']">
          <i
            v-if="processing"
            class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
          ></i>
          {{ processing ? "Please Wait" : "Finish" }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script>
import siteMixin from "@/mixins/siteSettings";
import { mapState } from "pinia";
import { useSetupStore } from "@/store/setup.js";
import { defineAsyncComponent } from "vue";
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

export default {
  mixins: [siteMixin],
  data() {
    return {
      payload: {
        app_name: "",
        tag_line: "",
        logo: null,
        icon: null,
        favicon: null,
        color_code: "159C8C",
        analytics: "",
        sa_org_id: "",
      },
      favicon: null,
      logo: null,
      icon: null,
      processing: false,
      organization: null,
    };
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
    ColorPicker
  },
  computed: {
    ...mapState(useSetupStore, ["registerComplete"]),
  },
  created() {
    if (!this.registerComplete) {
      this.$router.push({
        name: "register",
      });
    } else {
      this.payload.app_name = this.app_name;
      this.payload.color_code = `#${this.color_code}`;
    }
    this.fetchOrganization();
  },
  methods: {
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