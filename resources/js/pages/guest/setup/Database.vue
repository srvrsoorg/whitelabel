<template>
  <div class="">
    <InstallationInfo />
    <div class="w-full max-w-sm mx-auto 2xl:mt-36 sm:mt-20 mt-10 mb-4 p-4">
      <form
        action="javascript:void(0)"
        @submit.prevent="saveDatabaseDetails($event)"
      >
        <div>
          <label
            for="host"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Database Host
          </label>
          <div class="mt-1.5">
            <input
              id="host"
              name="host"
              type="text"
              placeholder="Enter Database Host"
              v-model="database.host"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small id="host_message" class="error_message text-red-500"></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="database"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Database Name
          </label>
          <div class="mt-1.5">
            <input
              id="database"
              name="database"
              type="text"
              placeholder="Enter Database Name"
              v-model="database.database"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="database_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="username"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Database Username
          </label>
          <div class="mt-1.5">
            <input
              id="username"
              name="username"
              type="text"
              placeholder="Enter Database Username"
              v-model="database.username"
              class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
            />
            <small
              id="username_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="mt-4">
          <label
            for="password"
            class="block text-tiny text-neutral-800 font-medium"
          >
            Database Password
          </label>
          <div class="mt-1.5">
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter Database Password"
                v-model="database.password"
                :class="{ 'tracking-widest': !showPassword }"
                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
              />
              <PasswordVisibility
                :showPassword="showPassword"
                @toggle="showPassword = !showPassword"
              />
            </div>
            <small
              id="password_message"
              class="error_message text-red-500"
            ></small>
          </div>
        </div>
        <div class="flex flex-wrap justify-between gap-3 mt-6">
          <router-link
            :class="[
              isLightColor
                ? 'text-custom-700 border-custom-700'
                : 'border-custom-500 text-custom-500',
              'border rounded-md px-5 py-1.5 text-sm font-medium flex items-center',
            ]"
            :to="{
              name: 'checkPermissions',
            }"
          >
            Back
          </router-link>
          <Button :disabled="processing" :class="['px-5']">
            <i
              v-if="processing"
              class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"
            ></i>
            {{ processing ? "Please Wait" : "Next" }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
import { mapState } from "pinia";
import { useSetupStore } from "@/store/setup";
import { defineAsyncComponent } from "vue";

export default {
  data() {
    return {
      database: {
        host: "",
        database: "",
        username: "",
        password: "",
      },
      processing: false,
      showPassword: false,
    };
  },
  computed: {
    ...mapState(useSetupStore, ["permissionComplete"]),
  },
  components: {
    InstallationInfo: defineAsyncComponent(() =>
      import("@/components/InstallationInfo.vue")
    ),
  },
  created() {
    if (!this.permissionComplete) {
      this.$router.push({
        name: "checkPermissions",
      });
    } else {
      this.fetchDatabaseDetails();
    }
  },
  methods: {
    async fetchDatabaseDetails() {
      await this.$axios.get("/setup/database").then(({ data }) => {
        if (data.database) {
          let { host, database, username, password } = data.database;
          Object.assign(this.database, { host, database, username, password });
        }
      });
    },
    async saveDatabaseDetails(e) {
      e.preventDefault();
      this.hideError();
      this.processing = true;

      await this.$axios
        .post("/setup/database", this.database)
        .then(({ data }) => {
          this.$toast.success(data.message);
          this.$router.push({
            name: "keyVerification",
          });
        })
        .catch(({ response }) => {
          if (response !== undefined) {
            const { status, data } = response;
            if (status === 422) {
              this.displayError(data);
            } else {
              this.$toast.error(data.message);
            }
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>