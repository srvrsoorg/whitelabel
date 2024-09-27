<template>
  <div
    v-for="(step, index) in steps"
    :key="step"
    class="bg-white sticky top-0 z-50 w-full"
  >
    <div
      class="flex gap-5 xl:px-24 sm:px-16 md:px-20 px-8 py-2"
      v-if="currentRouteName === step.name"
    >
      <div class="flex justify-center items-center">
        <img
          :src="step.icon"
          alt=""
          class="h-10 min-h-10"
          :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
        />
      </div>
      <div class="flex-1">
        <div class="py-4 flex flex-col space-y-0.5">
          <span
            class="text-sm"
            :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']"
            >Step {{ index + 1 }}/{{ steps.length }}</span
          >
          <span class="test-tiny font-semibold">{{ step.title }}</span>
          <span class="text-sm text-gray-500">{{ step.description }}</span>
        </div>
      </div>
    </div>
    <div v-if="currentRouteName === step.name" class="relative">
      <div class="bg-gray-200 h-[3px] w-full"></div>
      <div
        v-if="currentRouteName === step.name"
        :class="[
          isLightColor ? 'bg-custom-700' : 'bg-custom-500 ',
          'h-[3px] absolute top-0 rounded-r-full',
        ]"
        :style="{
          width: ((index + 1) / steps.length) * 100 + '%',
        }"
      ></div>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useSetupStore } from "@/store/setup";
export default {
  name: "InstallationInfo",
  data() {
    return {
      steps: [
        {
          title: "Permission",
          description:
            "Set up permissions to manage access and maintain security for the Whitelabel product.",
          icon: "/icon/Frame-1.jpg",
          name: "checkPermissions",
        },
        {
          title: "Database Credentials",
          description:
            "Enter your database credentials to setup secure data storage for the Whitelabel product.",
          icon: "/icon/Frame.jpg",
          name: "setupDatabase",
        },
        {
          title: "License Key Verification",
          description:
            "Enter your license key to securely activate and personalize the Whitelabel product.",
          icon: "/icon/square-key.jpg",
          name: "keyVerification",
        },
        {
          title: "SMTP Credentials",
          description:
            "Enter your SMTP credentials to enable email functionality for the Whitelabel product.",
          icon: "/icon/email.jpg",
          name: "setupSmtp",
        },
        {
          title: "Registration",
          description:
            "Register to get the admin side access to manage and control the Whitelabel product.",
          icon: "/icon/adopt.jpg",
          name: "setupRegister",
        },
        {
          title: "Application Setup",
          description:
            "Customize your application's branding with simple setup process.",
          icon: "/icon/settings.jpg",
          name: "setupSiteSettings",
        },
      ],
    };
  },
  computed: {
    ...mapState(useSetupStore, [
      "setupComplete",
      "smtpComplete",
      "permissionComplete",
      "databaseComplete",
      "keyVerificationComplete",
      "registerComplete",
    ]),
    currentRouteName() {
      return this.$route ? this.$route.name : null;
    },
  },
};
</script>
<style>
</style>


