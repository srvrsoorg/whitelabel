<template>
  <div class="">
    <InstallationInfo />
    <div class="w-full 2xl:mt-36 xl:mt-20 sm:mt-14 mt-8 max-w-md mx-auto p-4">
      <div class="grid grid-cols-1 bg-[#F6F6F6A1] rounded-md p-4">
        <div class="">
          <div
            class="text-sm flex gap-3 flex-wrap justify-between items-center"
          >
            <span class="text-zinc-500">storage/app</span>
            <span
              :class="[
                processing
                  ? 'bg-gray-200'
                  : permission.app
                  ? 'bg-[#E1F2E9]'
                  : 'bg-red-200',
                'rounded-md flex gap-2 items-center px-3 py-2 text-sm',
              ]"
            >
              <i
                :class="
                  processing
                    ? 'text-gray-500 fa-solid fa-circle-notch fa-spin'
                    : permission.app
                    ? 'text-green-500 fa-square-check'
                    : 'text-red-500 fa-square-xmark'
                "
                class="fas"
              ></i>
              775
            </span>
          </div>
        </div>
        <div class="mt-5">
          <div
            class="text-sm flex gap-3 flex-wrap justify-between items-center"
          >
            <span class="text-zinc-500">storage/framework</span>
            <span
              :class="[
                processing
                  ? 'bg-gray-200'
                  : permission.framework
                  ? 'bg-[#E1F2E9]'
                  : 'bg-red-200',
                'rounded-md flex gap-2 items-center px-3 py-2 text-sm',
              ]"
            >
              <i
                :class="
                  processing
                    ? 'text-gray-500 fa-solid fa-circle-notch fa-spin'
                    : permission.framework
                    ? 'text-green-500 fa-square-check'
                    : 'text-red-500 fa-square-xmark'
                "
                class="fas"
              ></i>
              775
            </span>
          </div>
        </div>
        <div class="mt-5">
          <div
            class="text-sm flex gap-3 flex-wrap justify-between items-center"
          >
            <span class="text-zinc-500">storage/logs</span>
            <span
              :class="[
                processing
                  ? 'bg-gray-200'
                  : permission.log
                  ? 'bg-[#E1F2E9]'
                  : 'bg-red-200',
                'rounded-md flex gap-2 items-center px-3 py-2 text-sm',
              ]"
            >
              <i
                :class="
                  processing
                    ? 'text-gray-500 fa-solid fa-circle-notch fa-spin'
                    : permission.log
                    ? 'text-green-500 fa-square-check'
                    : 'text-red-500 fa-square-xmark'
                "
                class="fas"
              ></i>
              775
            </span>
          </div>
        </div>
        <div class="mt-5">
          <div
            class="text-sm flex gap-3 flex-wrap justify-between items-center"
          >
            <span class="text-zinc-500">storage/framework/cache</span>
            <span
              :class="[
                processing
                  ? 'bg-gray-200'
                  : permission.cache
                  ? 'bg-[#E1F2E9]'
                  : 'bg-red-200',
                'rounded-md flex gap-2 items-center px-3 py-2 text-sm',
              ]"
            >
              <i
                :class="
                  processing
                    ? 'text-gray-500 fa-solid fa-circle-notch fa-spin'
                    : permission.cache
                    ? 'text-green-500 fa-square-check'
                    : 'text-red-500 fa-square-xmark'
                "
                class="fas"
              ></i>
              775
            </span>
          </div>
        </div>
      </div>
      <div class="text-end">
        <Button
          @click="goToNextStep()"
          :class="['mt-4 px-5']"
          :disabled="disableButton"
        >
          Next
        </Button>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";

export default {
  data() {
    return {
      processing: false,
      disableButton: true,
      permission: {
        app: false,
        cache: false,
        framework: false,
        log: false,
      },
    };
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
  },
  created() {
    this.checkPermission();
  },
  methods: {
    async checkPermission() {
      this.processing = true;
      await this.$axios
        .get("/setup/permission")
        .then(({ data }) => {
          this.permission = data.permission;

          // Check if all properties in permission object are true
          const allPropertiesTrue = Object.values(this.permission).every(
            (value) => value === true
          );
          this.disableButton = !allPropertiesTrue;
        })
        .catch(({ response }) => {
          this.$toast.error(response.data.message);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    goToNextStep() {
      this.$router.push({
        name: "setupDatabase",
      });
    },
  },
};
</script>

<style>
</style>